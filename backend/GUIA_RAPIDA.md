# 🚀 GUÍA RÁPIDA - PET CUTE CLOTHES

## ✅ Configuración de Mailtrap (2 minutos)

### 1️⃣ Crear cuenta en Mailtrap
- Ir a: https://mailtrap.io
- Click en "Sign up free"
- Completar registro

### 2️⃣ Crear Inbox
- Click en "Create Inbox"
- Nombre: "PET CUTE CLOTHES"
- Click en "Create"

### 3️⃣ Obtener credenciales
- En el inbox, click en "SMTP Settings"
- Copiar **Username** y **Password**

### 4️⃣ Configurar .env
Abrir `backend/.env` y reemplazar:
```env
MAIL_USERNAME=TU_USERNAME_COPIADO
MAIL_PASSWORD=TU_PASSWORD_COPIADO
```

### 5️⃣ Probar
```bash
cd backend
php artisan config:clear
php artisan tinker
```
```php
Mail::raw('Test', fn($m) => $m->to('test@test.com')->subject('Test'));
```

---

## 📁 Archivos Importantes

### Configuración
- `backend/.env` - Configuración de entorno
- `backend/config/mail.php` - Configuración de mail

### Emails
- `backend/app/Mail/OrderConfirmation.php` - Clase del email
- `backend/resources/views/emails/order-confirmation.blade.php` - Vista del email

### Checkout
- `backend/app/Http/Controllers/CheckoutController.php` - Lógica del checkout
- `backend/resources/views/checkout/index.blade.php` - Vista del checkout
- `backend/resources/views/checkout/success.blade.php` - Vista de confirmación

### Documentación
- `backend/CONFIGURACION_MAILTRAP.md` - Guía de Mailtrap
- `backend/DOCUMENTACION_CHECKOUT.md` - Diseño completo del checkout
- `backend/ESTRUCTURA_ACTUALIZADA.md` - Estructura del proyecto

---

## 🧪 Probar el Sistema

### 1. Agregar productos al carrito
- Ir a la tienda
- Click en "Agregar al carrito"
- Verificar que aparezca el popup

### 2. Ir al checkout
- Click en "Ver Carrito"
- Click en "Proceder al Pago"

### 3. Completar formulario
- Email
- Método de envío (domicilio o sucursal)
- Método de pago (efectivo, transferencia, WhatsApp)

### 4. Finalizar compra
- Click en "Finalizar Compra"
- Verificar página de confirmación
- Verificar email en Mailtrap

---

## 📱 Contacto Configurado

- **WhatsApp**: 3814459268 (Natalia Cabral)
- **Email**: pedidos@petcuteclothes.com
- **Dirección**: Av. Sarmiento 1234, San Miguel de Tucumán

---

## 🔧 Comandos Útiles

```bash
# Limpiar caché
php artisan config:clear
php artisan cache:clear

# Ver rutas
php artisan route:list

# Ver emails en cola
php artisan queue:work

# Tinker (testing)
php artisan tinker
```

---

## ⚠️ Importante

- **NUNCA** subir `.env` a Git
- **SIEMPRE** usar Mailtrap en desarrollo
- **VERIFICAR** que los emails lleguen a Mailtrap
- **PROBAR** todos los métodos de pago

---

**¡Listo para probar! 🎉**