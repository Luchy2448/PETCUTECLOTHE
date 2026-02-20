# 📧 CONFIGURACIÓN DE MAILTRAP PARA PET CUTE CLOTHES

## 🔑 Cómo obtener credenciales de Mailtrap

1. **Ir a Mailtrap**: https://mailtrap.io
2. **Crear cuenta** (gratis) o iniciar sesión
3. **Crear un inbox** para testing
4. **Ir a Settings > SMTP Settings**
5. **Copiar las credenciales** que te proporcionan

## ⚙️ Configuración del archivo .env

Abre el archivo `backend/.env` y configura estas variables:

```env
# ========================================
# 📧 CONFIGURACIÓN DE EMAIL (MAILTRAP)
# ========================================
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=TU_USERNAME_AQUI
MAIL_PASSWORD=TU_PASSWORD_AQUI
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="pedidos@petcuteclothes.com"
MAIL_FROM_NAME="PET CUTE CLOTHES"
```

## 📝 Instrucciones paso a paso

### Paso 1: Obtener credenciales
1. Ve a https://mailtrap.io
2. Crea una cuenta gratuita
3. Crea un nuevo inbox llamado "PET CUTE CLOTHES"
4. En el inbox, ve a la pestaña "SMTP Settings"
5. Copia los valores de:
   - **Username**
   - **Password**

### Paso 2: Actualizar .env
Reemplaza en el archivo `backend/.env`:
- `TU_USERNAME_AQUI` → Tu username de Mailtrap
- `TU_PASSWORD_AQUI` → Tu password de Mailtrap

### Paso 3: Probar la configuración
Ejecuta este comando para probar:

```bash
php artisan tinker
```

Y dentro de tinker:

```php
Mail::raw('Test email from PET CUTE CLOTHES', function($message) {
    $message->to('test@example.com')->subject('Test');
});
```

### Paso 4: Verificar en Mailtrap
1. Ve a tu inbox en Mailtrap.io
2. Deberías ver el email de prueba

## 🎨 Plantillas de Email Configuradas

### Emails disponibles:
1. **OrderConfirmation** - Confirmación de pedido
   - Ubicación: `app/Mail/OrderConfirmation.php`
   - Vista: `resources/views/emails/order-confirmation.blade.php`

## 🔧 Comandos útiles

```bash
# Limpiar caché de configuración
php artisan config:clear

# Ver configuración de mail actual
php artisan tinker
>>> config('mail')

# Enviar email de prueba
php artisan tinker
>>> Mail::to('test@test.com')->send(new App\Mail\OrderConfirmation(App\Models\Order::first()));
```

## 📱 Datos de contacto configurados

- **WhatsApp**: 3814459268 (Natalia Cabral)
- **Email**: pedidos@petcuteclothes.com
- **Dirección**: Av. Sarmiento 1234, San Miguel de Tucumán

## ⚠️ Importante

- **NUNCA** subas las credenciales reales a Git
- El archivo `.env` está en `.gitignore` por seguridad
- Para producción, usar un servicio real como:
  - Mailgun
  - SendGrid
  - Amazon SES
  - SMTP propio

## 🧪 Testing en desarrollo

Cuando hagas un pedido de prueba:
1. El email se enviará a Mailtrap (no al cliente real)
2. Puedes ver el email en tu inbox de Mailtrap
3. Puedes verificar que el diseño se vea correcto
4. Puedes reenviar el email las veces que necesites

¡Listo! Tu sistema de emails está configurado para testing.