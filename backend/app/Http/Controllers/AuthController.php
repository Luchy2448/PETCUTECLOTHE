<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Sanctum;

/**
 * 🔐 AuthController - El Guardia de Seguridad
 *
 * Este controlador maneja todo lo relacionado con la AUTENTICACIÓN:
 * - Registro de usuarios nuevos
 * - Login (inicio de sesión)
 * - Logout (cerrar sesión)
 *
 * Piensa en esto como un GUARDIA DE SEGURIDAD que:
 * - Verifica quién eres
 * - Te da una llave (token) para entrar
 * - Saca a las personas que no están autorizadas
 */
class AuthController extends Controller
{
    /**
     * 📝 MÉTODO: register - Registrar usuario nuevo
     *
     * Este método recibe los datos del usuario (nombre, email, contraseña)
     * y crea un nuevo usuario en la base de datos.
     *
     * Ruta: POST /api/register
     * 
     * Analogía: Es como "registrarse" en una escuela nueva.
     * Llenas un formulario y te dan un carnet de estudiante.
     *
     * @param Request $request - Los datos enviados por el usuario
     * @return JsonResponse - La respuesta (éxito o error)
     */
    public function register(Request $request): JsonResponse
    {
        // 🛡️ VALIDAR los datos antes de guardar
        //
        // Laravel revisa que:
        // - El nombre no esté vacío
        // - El email sea válido (ej: usuario@ejemplo.com)
        // - La contraseña tenga al menos 6 caracteres
        // - La confirmación de contraseña sea igual a la contraseña
        //
        // Si algo falla, Laravel devuelve un error automático
        $validated = $request->validate([
            'name' => 'required|string|max:255',        // Nombre: obligatorio, texto, máx 255 caracteres
            'email' => 'required|string|email|max:255|unique:users,email',  // Email: obligatorio, email válido, único en tabla users
            'password' => 'required|string|min:6',     // Contraseña: obligatoria, texto, mínimo 6 caracteres
            'password_confirmation' => 'required|string|same:password'  // Confirmación: debe ser igual a password
        ], [
            // Mensajes de error personalizados en español
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password_confirmation.required' => 'Debes confirmar tu contraseña',
            'password_confirmation.same' => 'Las contraseñas no coinciden'
        ]);

        // 📝 CREAR el nuevo usuario
        //
        // Creamos un objeto de la clase User
        // y llenamos los campos con los datos validados
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            // 🔒 ENCRIPTAR la contraseña antes de guardar
            //
            // IMPORTANTE: NUNCA guardamos contraseñas en texto plano.
            // Siempre usamos Hash::make() para encriptarlas.
            // De esta forma, ni nosotros podemos ver la contraseña original.
            'password' => Hash::make($validated['password'])
        ]);

        // 🎉 RETORNAR respuesta exitosa
        //
        // Devolvemos:
        // - Un mensaje de éxito
        // - El usuario creado (sin contraseña por seguridad)
        // - Un código HTTP 201 (Created = Creado exitosamente)
        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ], 201);
    }

    /**
     * 🔑 MÉTODO: login - Iniciar sesión
     *
     * Este método recibe email y contraseña,
     * verifica si son correctos y genera un TOKEN.
     *
     * Ruta: POST /api/login
     *
     * Analogía: Es como "presentar tu carnet" para entrar a un edificio.
     * Si el carnet es válido, te dejan entrar y te dan una tarjeta.
     *
     * @param Request $request - Los datos enviados (email y password)
     * @return JsonResponse - El token de acceso o error
     */
    public function login(Request $request): JsonResponse
    {
        // 🛡️ VALIDAR los datos
        $validated = $request->validate([
            'email' => 'required|string|email',    // Email: obligatorio, email válido
            'password' => 'required|string'          // Contraseña: obligatoria
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'password.required' => 'La contraseña es obligatoria'
        ]);

        // 🔐 INTENTAR iniciar sesión
        //
        // Auth::attempt() intenta verificar las credenciales
        // 1. Busca el usuario por email
        // 2. Verifica que la contraseña coincida (usando el hash)
        // 3. Si todo está bien, inicia sesión y devuelve true
        // 4. Si algo falla, devuelve false
        if (!Auth::attempt($validated)) {
            // ❌ ERROR: Credenciales incorrectas
            //
            // Si Auth::attempt() devuelve false, significa que:
            // - El email no existe, o
            // - La contraseña es incorrecta
            //
            // Por seguridad, no decimos EXACTAMENTE qué falló.
            // Solo decimos "credenciales incorrectas" para que
            // los malos no sepan si el email existe o no.
            throw ValidationException::withMessages([
                'email' => ['Las credenciales son incorrectas']
            ]);
        }

        // ✅ ÉXITO: Usuario autenticado
        //
        // Si llegamos aquí, el usuario inició sesión correctamente
        $user = Auth::user();  // Obtenemos el usuario autenticado

        // 🔑 CREAR un TOKEN para este usuario
        //
        // Este token es como una "llave mágica" que el usuario
        // usará para acceder a rutas protegidas.
        //
        // Primero generamos un string aleatorio
        $plainTextToken = Str::random(40);
        
        // Creamos el token en la base de datos
        $token = $user->tokens()->create([
            'name' => 'auth-token',
            'token' => hash('sha256', $plainTextToken),
            'abilities' => ['*'],
        ]);
        
        // El texto plano del token es lo que devolvemos al usuario
        // Formato: ID_del_usuario|token_plano
        $tokenParaEnviar = $user->id . '|' . $plainTextToken;

        // 🎉 RETORNAR respuesta exitosa con el TOKEN
        return response()->json([
            'message' => 'Login exitoso',
            'token' => $tokenParaEnviar,  // 🔑 Esta es la LLAVE mágica
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ], 200);
    }

    /**
     * 🚪 MÉTODO: logout - Cerrar sesión
     *
     * Este método elimina el token del usuario,
     * revocando su acceso a rutas protegidas.
     *
     * Ruta: POST /api/logout
     * Requiere: Token de autenticación (middleware auth:sanctum)
     *
     * Analogía: Es como "devolver la tarjeta" cuando sales del edificio.
     * Sin la tarjeta, ya no puedes entrar a las áreas protegidas.
     *
     * @param Request $request - El request con el usuario autenticado
     * @return JsonResponse - Mensaje de éxito
     */
    public function logout(Request $request): JsonResponse
    {
        // 🔑 ELIMINAR el token actual del usuario
        //
        // $request->user() devuelve el usuario autenticado
        // ->currentAccessToken() devuelve el token que se está usando
        // ->delete() borra ese token de la base de datos
        $request->user()->currentAccessToken()->delete();

        // 🎉 RETORNAR respuesta exitosa
        return response()->json([
            'message' => 'Logout exitoso. Hasta pronto!'
        ], 200);
    }

    /**
     * 👤 MÉTODO: me - Ver mi usuario actual
     *
     * Este método devuelve la información del usuario autenticado.
     *
     * Ruta: GET /api/user
     * Requiere: Token de autenticación (middleware auth:sanctum)
     *
     * Analogía: Es como "mirar mi carnet" para ver mis datos.
     * Solo puedo ver mi carnet si estoy dentro del edificio (autenticado).
     *
     * @param Request $request - El request con el usuario autenticado
     * @return JsonResponse - Datos del usuario
     */
    public function me(Request $request): JsonResponse
    {
        // 📋 RETORNAR los datos del usuario autenticado
        //
        // $request->user() devuelve el usuario que inició sesión
        return response()->json($request->user(), 200);
    }
}
