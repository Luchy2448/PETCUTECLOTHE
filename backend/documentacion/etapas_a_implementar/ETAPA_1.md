# 📋 Etapa 1 - CRUD Productos + Login de Usuarios

## 📅 Fecha de Inicio: 7 de Enero de 2026
## 🎯 Estado: En desarrollo
## ✅ Progreso: ░░░░░░░░░ 0%

---

## 🌟 OBJETIVO DE LA ETAPA

Crear un sistema **MUY SENCILLO** y **FUNCIONAL** con:
- ✅ CRUD de productos (Crear, Leer, Actualizar, Borrar)
- ✅ Login sencillo de usuarios (Registro, Login, Logout)
- ✅ Protección básica de rutas
- ✅ **FRONTEND CON LARAVEL BLADE Y HTML** (Sin React - Más fácil y rápido de entender)

**IMPORTANTE**: Esta es la BASE mínima para empezar. Más adelante agregaremos carrito de compras, pagos, panel de admin, etc.

---

## 📦 LO QUE YA TENEMOS (GRATIS de Laravel)

✅ Laravel 10 framework
✅ Migración de tabla `users` (ya creada por Laravel)
✅ Laravel Sanctum para autenticación (ya instalado)
✅ Estructura básica del proyecto
✅ Configuración de composer

---

## 🚀 QUÉ VAMOS A CONSTRUIR

### 1️⃣ **Categorías** (Opcional pero útil)
- Tabla `categories` con: id, nombre
- CRUD básico para gestionar categorías

### 2️⃣ **Productos**
- Tabla `products` con:
  - id, nombre, descripción, precio, stock, talla (1,2,3,4,5), categoría, imagen, timestamps
- CRUD completo (Crear, Leer, Actualizar, Borrar)

### 3️⃣ **Autenticación de Usuarios**
- Registro de nuevos usuarios
- Login (inicio de sesión)
- Logout (cerrar sesión)
- Generación de tokens con Sanctum

### 4️⃣ **Frontend con Laravel Blade**
- Página de inicio con catálogo de productos
- Página de registro de usuarios
- Página de login
- Página de administración de productos (CRUD completo)
- Diseño simple y funcional con HTML + CSS
- No React - Usaremos Blade de Laravel (más fácil y rápido)

### 5️⃣ **Datos de prueba**
- 3-5 categorías de ejemplo
- 5-10 productos de ejemplo
- 1 usuario admin para pruebas

---

## 📂 ARCHIVOS A CREAR/MODIFICAR

### Migraciones (Database/Migrations)
- [ ] `create_categories_table.php` - Tabla de categorías
- [ ] `create_products_table.php` - Tabla de productos

### Modelos (App/Models)
- [ ] `Category.php` - Modelo de categoría
- [ ] `Product.php` - Modelo de producto
- [ ] `User.php` - Ya existe

### Controladores (App/Http/Controllers)
- [ ] `AuthController.php` - Login, Registro, Logout
- [ ] `CategoryController.php` - CRUD de categorías
- [ ] `ProductController.php` - CRUD de productos

### Rutas (routes/)
- [ ] `web.php` - Rutas web para vistas Blade
- [ ] `api.php` - Rutas API para JSON

### Vistas (resources/views/)
- [ ] `layouts/app.blade.php` - Layout principal
- [ ] `home.blade.php` - Página de inicio con catálogo
- [ ] `products/index.blade.php` - Lista de productos
- [ ] `products/show.blade.php` - Detalle de producto
- [ ] `products/create.blade.php` - Formulario para crear producto
- [ ] `products/edit.blade.php` - Formulario para editar producto
- [ ] `auth/register.blade.php` - Formulario de registro
- [ ] `auth/login.blade.php` - Formulario de login
- [ ] `auth/logout.blade.php` - Cierre de sesión

### Seeders (Database/Seeders/)
- [ ] `CategorySeeder.php` - Categorías de ejemplo
- [ ] `ProductSeeder.php` - Productos de ejemplo
- [ ] `UserSeeder.php` - Usuario admin de ejemplo

---

## 🗄️ BASE DE DATOS - TABLAS PRINCIPALES

### Tabla: users (ya existe)
```sql
┌─────────────────┐
│ id (PK)        │ - ID único del usuario
│ name           │ - Nombre del usuario
│ email          │ - Email (único)
│ password       │ - Contraseña (encriptada 🔒)
│ remember_token │ - Token para "recordarme"
│ created_at     │ - Fecha de creación
│ updated_at     │ - Fecha de actualización
└─────────────────┘
```

### Tabla: categories (nueva)
```sql
┌─────────────────┐
│ id (PK)        │ - ID único de la categoría
│ name           │ - Nombre (ej: "Casual", "Elegante")
│ created_at     │ - Fecha de creación
│ updated_at     │ - Fecha de actualización
└─────────────────┘
```

### Tabla: products (nueva)
```sql
┌────────────────────────┐
│ id (PK)                │ - ID único del producto
│ name                   │ - Nombre del producto
│ description            │ - Descripción detallada
│ price                 │ - Precio (decimal, ej: 15000.00)
│ stock                 │ - Cantidad disponible (entero)
│ size                  │ - Talla (1, 2, 3, 4, 5)
│ category_id (FK)       │ - ID de la categoría (relación)
│ image_url             │ - URL de la foto del producto
│ created_at            │ - Fecha de creación
│ updated_at            │ - Fecha de actualización
└────────────────────────┘
```

---

## 🛣️ RUTAS DEL SISTEMA

### Rutas WEB (para ver páginas HTML/Blade)
- `GET /` - Página de inicio con catálogo de productos
- `GET /login` - Página de login
- `GET /register` - Página de registro
- `GET /admin/products` - Panel de administración de productos
- `GET /admin/products/create` - Formulario para crear producto
- `GET /admin/products/{id}/edit` - Formulario para editar producto
- `GET /admin/products/{id}` - Ver detalle de producto (admin)
- `GET /products/{id}` - Ver detalle de producto (público)

### Rutas API (para operaciones JSON)
```
Públicas (sin autenticación):
- POST /api/register          → Registrar usuario nuevo
- POST /api/login             → Iniciar sesión (obtener token)
- GET  /api/products          → Listar todos los productos
- GET  /api/products/{id}     → Ver un producto específico
- GET  /api/categories        → Listar categorías

Protegidas (requieren token):
- POST /api/logout            → Cerrar sesión
- GET  /api/user             → Ver mi usuario
- GET  /api/categories        → Listar categorías
- POST /api/categories        → Crear categoría
- PUT  /api/categories/{id}   → Editar categoría
- DELETE /api/categories/{id}   → Borrar categoría
- POST /api/products          → Crear producto
- PUT  /api/products/{id}     → Editar producto
- DELETE /api/products/{id}     → Borrar producto
```

---

## 🔐 AUTENTICACIÓN CON SANCTUM

### ¿Cómo funciona?
1. Usuario se registra → Guardamos sus datos en la base
2. Usuario hace login → Le damos un TOKEN (como una llave mágica 🔑)
3. Usuario usa el TOKEN → Puede acceder a rutas protegidas
4. Usuario hace logout → Token se invalida

### Flujo de Tokens
```
Usuario → Register → Guardar en DB → Retorna usuario
         ↓
Usuario → Login   → Validar email/pass → Generar TOKEN
         ↓
Token   → Header  → Validar token → Acceder a rutas protegidas
```

---

## 💻 EJEMPLOS DE USO DEL SISTEMA

### 1. Registrar Usuario (Formulario web)
```bash
Página: http://localhost:8000/register
Llenas campos: Nombre, Email, Contraseña, Confirmar Contraseña
Botón: "Registrarse"
```

### 2. Login (Formulario web)
```bash
Página: http://localhost:8000/login
Llenas campos: Email, Contraseña
Botón: "Iniciar Sesión"
Redirige a la página de inicio con sesión iniciada
```

### 3. Ver Productos (Página web)
```bash
Página: http://localhost:8000/products
Ver catálogo de productos con fotos, nombres y precios
Sin necesidad de iniciar sesión
```

### 4. Ver Producto Detalle (Página web)
```bash
Página: http://localhost:8000/products/1
Ver detalles completos del producto
Sin necesidad de iniciar sesión
```

### 5. Administrar Productos (Página web protegida)
```bash
Página: http://localhost:8000/admin/products
REQUIERE: Estar logueado (tener token)
Ver lista de productos
Botón: "Crear Nuevo Producto" → Va a /admin/products/create
Botón: "Editar" → Va a /admin/products/{id}/edit
Botón: "Eliminar" → Borra producto (pide confirmación)
```

### API JSON (para integraciones futuras)

#### Registrar usuario:
```bash
POST http://localhost:8000/api/register

Body (JSON):
{
  "name": "Admin",
  "email": "admin@petcute.com",
  "password": "password123",
  "password_confirmation": "password123"
}

Respuesta Exitosa (200):
{
  "message": "Usuario registrado exitosamente",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@petcute.com"
  }
}
```

#### Login:
```bash
POST http://localhost:8000/api/login

Body (JSON):
{
  "email": "admin@petcute.com",
  "password": "password123"
}

Respuesta Exitosa (200):
{
  "message": "Login exitoso",
  "token": "1|rAbCdEfGhIjKlMnOpQrStUvWxYz",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@petcute.com"
  }
}
```

#### Listar productos:
```bash
GET http://localhost:8000/api/products

Respuesta (200):
[
  {
    "id": 1,
    "name": "Suéter con corazones",
    "description": "Lindo suéter para gatito",
    "price": 15000.00,
    "stock": 10,
    "size": 3,
    "category_id": 1,
    "image_url": "https://ejemplo.com/sueter.jpg",
    "category": {
      "id": 1,
      "name": "Casual"
    }
  }
]
```

#### Crear producto (requiere token):
```bash
POST http://localhost:8000/api/products

Headers (desde login):
  Authorization: Bearer 1|rAbCdEfGhIjKlMnOpQrStUvWxYz

Body (JSON):
{
  "name": "Suéter con corazones",
  "description": "Suéter de gatito con corazones",
  "price": 15000,
  "stock": 10,
  "size": 3,
  "category_id": 1,
  "image_url": "https://ejemplo.com/sueter.jpg"
}

Respuesta Exitosa (201):
{
  "message": "Producto creado exitosamente",
  "product": { ...producto creado... }
}
```

---

## 📝 DATOS DE PRUEBA (SEEDERS)

### Categorías a Crear:
1. "Casual" - Ropa para el día a día 🌞
2. "Elegante" - Ropa para eventos especiales ✨
3. "Cumpleaños" - Ropa para fiestas 🎂

### Productos a Crear:

**Categoría: Casual (3 productos)**
1. "Suéter con corazones" - $15,000 - Talla 3 - Stock 10
2. "Camiseta básica" - $8,000 - Talla 2 - Stock 15
3. "Chaqueta ligera" - $20,000 - Talla 4 - Stock 5

**Categoría: Elegante (3 productos)**
4. "Vestido de gala" - $25,000 - Talla 3 - Stock 3
5. "Corbata elegante" - $5,000 - Talla 1 - Stock 8
6. "Sombrero de fiesta" - $7,000 - Talla 2 - Stock 6

**Categoría: Cumpleaños (2 productos)**
7. "Disfraz de superhéroe" - $18,000 - Talla 3 - Stock 4
8. "Tutu rosa" - $12,000 - Talla 2 - Stock 7

### Usuario Admin:
- Name: "Admin"
- Email: "admin@petcute.com"
- Password: "password123"

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### Backend Laravel
- [ ] Crear migración de categorías
- [ ] Crear migración de productos
- [ ] Crear modelo Category
- [ ] Crear modelo Product
- [ ] Crear AuthController (register, login, logout)
- [ ] Crear CategoryController (CRUD)
- [ ] Crear ProductController (CRUD)
- [ ] Configurar rutas web (para Blade)
- [ ] Configurar rutas API (para JSON)
- [ ] Crear CategorySeeder
- [ ] Crear ProductSeeder
- [ ] Crear UserSeeder
- [ ] Ejecutar migraciones
- [ ] Ejecutar seeders

### Frontend Blade + HTML
- [ ] Crear layout principal
- [ ] Crear página de inicio (catálogo)
- [ ] Crear páginas de productos (lista, detalle, crear, editar)
- [ ] Crear página de login
- [ ] Crear página de registro
- [ ] Crear página de administración de productos
- [ ] Agregar estilos CSS para diseño atractivo
- [ ] Integrar PHP con Blade para mostrar datos

### Funcionalidades del Sistema
- [ ] Ver catálogo de productos (público)
- [ ] Ver detalle de producto (público)
- [ ] Registrarse como usuario
- [ ] Iniciar sesión
- [ ] Administrar productos (requiere login)
  - [ ] Ver lista de productos
  - [ ] Crear nuevo producto
  - [ ] Editar producto existente
  - [ ] Borrar producto
  - [ ] Ver categorías
  - [ ] Crear/editar categorías

---

## 📊 CRITERIOS DE FINALIZACIÓN

La Etapa 1 estará **COMPLETA** cuando:
- ✅ Todas las migraciones estén ejecutadas
- ✅ Todas las rutas web funcionen correctamente
- ✅ Todas las rutas de API funcionen correctamente
- ✅ Se pueda registrar, loguear y desloguear usuarios
- ✅ Se pueda hacer CRUD completo de productos (vía web y API)
- ✅ Se pueda hacer CRUD completo de categorías (vía web y API)
- ✅ Todos los datos de prueba estén cargados
- ✅ El frontend Blade esté funcionando y sea atractivo
- ✅ El diseño sea responsive (funcione en móvil)
- ✅ Se hayan probado todas las funcionalidades

---

## 📝 NOTAS IMPORTANTES

- **NO** incluye carrito de compras en esta etapa
- **NO** incluye pagos con Mercado Pago en esta etapa
- **NO** usa React - Usamos Laravel Blade (más simple para entender)
- **NO** incluye panel de administración avanzado en esta etapa
- El objetivo es tener un sistema **SIMPLE** y **FUNCIONAL** con productos y usuarios
- Tallas son numéricas: 1, 2, 3, 4, 5
- Diseño amigable con colores pasteles (rosa, celeste, amarillo, verde menta)
- Puedes copiar los archivos a Notion para tu documentación

---

## 📊 PROGRESO DETALLADO

| Componente | Estado | Porcentaje |
|------------|---------|-----------|
| Migraciones | ⏳ Pendiente | 0% |
| Modelos | ⏳ Pendiente | 0% |
| Controladores | ⏳ Pendiente | 0% |
| Rutas | ⏳ Pendiente | 0% |
| Vistas Blade | ⏳ Pendiente | 0% |
| Seeders | ⏳ Pendiente | 0% |
| Backend listo | ⏳ Pendiente | 0% |
| Frontend Blade | ⏳ Pendiente | 0% |
| Pruebas | ⏳ Pendiente | 0% |
| **TOTAL** | **⏳ Pendiente** | **0%** |

---

## 🔄 ETAPAS FUTURAS (NO implementar aún)

- Etapa 2: Carrito de compras
- Etapa 3: Pedidos y pagos con Mercado Pago
- Etapa 4: Panel de administración avanzado
- Etapa 5: Deploy y pruebas finales

---

## 🎨 DISEÑO Y BRANDING

### Colores (PET CUTE CLOTHES - Amigable)
- 🎨 Rosa pastel: `#FFB6C1`
- 💙 Celeste: `#ADD8E6`
- 💛 Amarillo suave: `#FFFACD`
- 💚 Verde menta: `#98FF98`
- ⚪ Blanco: `#FFFFFF`
- ⚫ Gris oscuro: `#333333`

### Tipografía
- Principal: **Poppins** (Google Fonts)
- Secundaria: **Nunito** (Google Fonts)

### Estilo
- Amigable y colorido
- Diseño responsive (funciona en móvil, tablet y desktop)
- Emojis en la documentación
- Explicaciones sencillas para principiantes

---

## 🎯 CRITERIOS DE FINALIZACIÓN DEL PROYECTO COMPLETO

El proyecto estará **100% COMPLETO** cuando:
- ✅ Backend Laravel funcionando con productos y usuarios
- ✅ Frontend Blade mostrando el catálogo de productos
- ✅ Sistema de login/registro/logout funcionando
- ✅ Panel de administración para gestionar productos
- ✅ Diseño atractivo y responsive
- ✅ Todos los datos de prueba cargados
- ✅ Sistema probado y listo para usar
- ✅ Documentación completa
- ✅ Usuario pueda ver catálogo, registrarse, hacer login y administrar productos

---

**Última actualización:** 7 de Enero de 2026
**Estado del proyecto:** Modificado - Ahora con Blade en lugar de React
**Próximo paso:** Crear vistas Blade y estilos para el frontend
