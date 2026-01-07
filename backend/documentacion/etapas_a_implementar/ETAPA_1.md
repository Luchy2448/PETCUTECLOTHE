# 📋 Etapa 1 - CRUD Productos + Login Sencillo

## 📅 Fecha de Inicio: 7 de Enero de 2026
## 🎯 Estado: Pendiente de inicio
## ✅ Progreso: ░░░░░░░░░░ 0%

---

## 🌟 OBJETIVO DE LA ETAPA

Crear un sistema **MUY SENCILLO** y **FUNCIONAL** con:
- ✅ CRUD de productos (Crear, Leer, Actualizar, Borrar)
- ✅ Login sencillo de usuarios
- ✅ Protección básica de rutas con Sanctum

**IMPORTANTE**: Esta es la BASE mínima para empezar. Más adelante agregaremos carrito, pagos, frontend, etc.

---

## 📦 LO QUE YA TENEMOS (GRATIS de Laravel)

✅ Laravel 10 framework
✅ Migración de tabla `users` (ya creada por Laravel)
✅ Laravel Sanctum para autenticación (ya instalado)
✅ Estructura básica del proyecto
✅ Configuración de composer

---

## 🚀 QUÉ VAMOS A CONSTRUIR

### 1️⃣ **Categorías**
- Tabla `categories` para organizar productos
- Campos: id, nombre, timestamps

### 2️⃣ **Productos**
- Tabla `products` con toda la información de la ropa
- Campos: id, nombre, descripción, precio, stock, talla, categoría, imagen, timestamps
- CRUD completo (Crear, Leer, Actualizar, Borrar)

### 3️⃣ **Autenticación de Usuarios**
- Registro de nuevos usuarios
- Login (inicio de sesión)
- Logout (cerrar sesión)
- Generación de tokens con Sanctum

### 4️⃣ **Datos de Prueba**
- 3-5 categorías de ejemplo (Casual, Elegante, Cumpleaños)
- 5-10 productos de ejemplo
- 1 usuario admin para pruebas

---

## 📂 ESTRUCTURA DE BASE DE DATOS

### Tabla: users (ya existe)
```sql
┌─────────────────┐
│ id (PK)        │ - ID único del usuario
│ name           │ - Nombre del usuario
│ email          │ - Email (único)
│ password       │ - Contraseña encriptada
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
┌─────────────────┐
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
└─────────────────┘
```

**Leyenda**:
- `PK` = Primary Key (Clave Primaria - identificador único)
- `FK` = Foreign Key (Clave Foránea - relación con otra tabla)

---

## 📂 ARCHIVOS A CREAR/MODIFICAR

### Migraciones (Database/Migrations)
- [ ] `create_categories_table.php` - Tabla de categorías
- [ ] `create_products_table.php` - Tabla de productos

### Modelos (App/Models)
- [ ] `Category.php` - Modelo de categoría
- [ ] `Product.php` - Modelo de producto

### Controladores (App/Http/Controllers)
- [ ] `AuthController.php` - Login, Registro, Logout
- [ ] `CategoryController.php` - CRUD de categorías
- [ ] `ProductController.php` - CRUD de productos

### Rutas (routes/)
- [ ] Modificar `api.php` - Configurar todas las rutas API

### Seeders (Database/Seeders/)
- [ ] `CategorySeeder.php` - Datos de categorías de ejemplo
- [ ] `ProductSeeder.php` - Datos de productos de ejemplo

---

## 🛣️ RUTAS DE LA API

### RUTAS PÚBLICAS (sin autenticación)
```
POST   /api/register          → Registrar usuario nuevo
POST   /api/login             → Iniciar sesión (obtener token)
GET    /api/products          → Listar todos los productos
GET    /api/products/{id}     → Ver un producto específico
```

### RUTAS PROTEGIDAS (requieren autenticación)
```
POST   /api/logout            → Cerrar sesión
GET    /api/categories        → Listar categorías
POST   /api/categories        → Crear categoría
PUT    /api/categories/{id}   → Editar categoría
DELETE /api/categories/{id}   → Borrar categoría
POST   /api/products          → Crear producto
PUT    /api/products/{id}     → Editar producto
DELETE /api/products/{id}     → Borrar producto
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

## 💻 EJEMPLOS DE USO DE LA API

### 1. Registrar Usuario
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

### 2. Iniciar Sesión (Login)
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

### 3. Listar Productos (Público)
```bash
GET http://localhost:8000/api/products

Respuesta (200):
[
  {
    "id": 1,
    "name": "Suéter con corazones",
    "description": "Lindo suéter para gatitos con diseño de corazones",
    "price": 15000.00,
    "stock": 10,
    "size": 3,
    "image_url": "https://ejemplo.com/sueter.jpg",
    "category": {
      "id": 1,
      "name": "Casual"
    },
    "created_at": "2026-01-07T12:00:00.000000Z",
    "updated_at": "2026-01-07T12:00:00.000000Z"
  }
]
```

### 4. Crear Producto (Requiere Token)
```bash
POST http://localhost:8000/api/products

Headers:
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
  "product": {
    "id": 1,
    "name": "Suéter con corazones",
    ... resto de campos
  }
}
```

### 5. Editar Producto (Requiere Token)
```bash
PUT http://localhost:8000/api/products/1

Headers:
  Authorization: Bearer 1|rAbCdEfGhIjKlMnOpQrStUvWxYz

Body (JSON):
{
  "price": 18000,
  "stock": 15
}

Respuesta Exitosa (200):
{
  "message": "Producto actualizado exitosamente",
  "product": { ...producto actualizado... }
}
```

### 6. Borrar Producto (Requiere Token)
```bash
DELETE http://localhost:8000/api/products/1

Headers:
  Authorization: Bearer 1|rAbCdEfGhIjKlMnOpQrStUvWxYz

Respuesta Exitosa (200):
{
  "message": "Producto eliminado exitosamente"
}
```

### 7. Cerrar Sesión (Logout)
```bash
POST http://localhost:8000/api/logout

Headers:
  Authorization: Bearer 1|rAbCdEfGhIjKlMnOpQrStUvWxYz

Respuesta Exitosa (200):
{
  "message": "Logout exitoso"
}
```

---

## 📝 DATOS DE PRUEBA (SEEDERS)

### Categorías a Crear:
1. "Casual" - Ropa para el día a día 🌞
2. "Elegante" - Ropa para eventos especiales ✨
3. "Cumpleaños" - Ropa para fiestas 🎂

### Productos a Crear:

**Categoría: Casual**
1. "Suéter con corazones" - $15,000 - Talla 3 - Stock 10
2. "Camiseta básica" - $8,000 - Talla 2 - Stock 15
3. "Chaqueta ligera" - $20,000 - Talla 4 - Stock 5

**Categoría: Elegante**
4. "Vestido de gala" - $25,000 - Talla 3 - Stock 3
5. "Corbata elegante" - $5,000 - Talla 1 - Stock 8
6. "Sombrero de fiesta" - $7,000 - Talla 2 - Stock 6

**Categoría: Cumpleaños**
7. "Disfraz de superhéroe" - $18,000 - Talla 3 - Stock 4
8. "Tutu rosa" - $12,000 - Talla 2 - Stock 7

### Usuario Admin:
- Name: "Admin"
- Email: "admin@petcute.com"
- Password: "password123"

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### Backend Laravel
- [ ] **Migraciones**
  - [ ] Crear migración `create_categories_table`
  - [ ] Crear migración `create_products_table`
  - [ ] Ejecutar migraciones (`php artisan migrate`)

- [ ] **Modelos**
  - [ ] Crear modelo `Category.php`
  - [ ] Crear modelo `Product.php`
  - [ ] Configurar relaciones entre modelos

- [ ] **Controladores**
  - [ ] Crear `AuthController.php`
    - [ ] Método `register`
    - [ ] Método `login`
    - [ ] Método `logout`
  - [ ] Crear `CategoryController.php`
    - [ ] Método `index` (listar)
    - [ ] Método `store` (crear)
    - [ ] Método `show` (ver uno)
    - [ ] Método `update` (editar)
    - [ ] Método `destroy` (borrar)
  - [ ] Crear `ProductController.php`
    - [ ] Método `index` (listar)
    - [ ] Método `show` (ver uno)
    - [ ] Método `store` (crear)
    - [ ] Método `update` (editar)
    - [ ] Método `destroy` (borrar)

- [ ] **Rutas API**
  - [ ] Configurar rutas en `routes/api.php`
  - [ ] Rutas públicas (register, login, productos)
  - [ ] Rutas protegidas con middleware auth:sanctum

- [ ] **Seeders**
  - [ ] Crear `CategorySeeder.php`
  - [ ] Crear `ProductSeeder.php`
  - [ ] Ejecutar seeders (`php artisan db:seed`)

### Pruebas
- [ ] Probar registro de usuario
- [ ] Probar login (obtener token)
- [ ] Probar listar productos (sin token)
- [ ] Probar crear categoría (con token)
- [ ] Probar crear producto (con token)
- [ ] Probar editar producto (con token)
- [ ] Probar borrar producto (con token)
- [ ] Probar logout (con token)

### Documentación
- [ ] Crear archivo de explicación detallada de código
- [ ] Explicar cada archivo creado con analogías y emojis
- [ ] Documentar rutas de la API
- [ ] Documentar ejemplos de uso

---

## 🎯 CRITERIOS DE FINALIZACIÓN

La Etapa 1 estará **COMPLETA** cuando:
- ✅ Todas las migraciones estén ejecutadas
- ✅ Todas las rutas de la API funcionen correctamente
- ✅ Se pueda registrar, loguear y desloguear usuarios
- ✅ Se pueda hacer CRUD completo de productos
- ✅ Se pueda hacer CRUD completo de categorías
- ✅ Todos los datos de prueba estén cargados
- ✅ La documentación esté completa
- ✅ Se hayan probado todas las funcionalidades

---

## 📊 PROGRESO DETALLADO

| Componente | Estado | Porcentaje |
|------------|---------|-----------|
| Migraciones | ⏳ Pendiente | 0% |
| Modelos | ⏳ Pendiente | 0% |
| AuthController | ⏳ Pendiente | 0% |
| CategoryController | ⏳ Pendiente | 0% |
| ProductController | ⏳ Pendiente | 0% |
| Rutas API | ⏳ Pendiente | 0% |
| Seeders | ⏳ Pendiente | 0% |
| Pruebas | ⏳ Pendiente | 0% |
| Documentación | ⏳ Pendiente | 0% |
| **TOTAL** | **⏳ Pendiente** | **0%** |

---

## 📝 NOTAS Y OBSERVACIONES

- Esta etapa NO incluye frontend (solo API)
- Esta etapa NO incluye carrito de compras
- Esta etapa NO incluye pagos con Mercado Pago
- El objetivo es tener una API funcional y probada
- Todos los datos se guardan en la base de datos configurada en `.env`

---

## 🔄 ETAPAS FUTURAS (NO implementar aún)

- Etapa 2: Carrito de compras
- Etapa 3: Pedidos y pagos con Mercado Pago
- Etapa 4: Frontend React
- Etapa 5: Panel de administración
- Etapa 6: Deploy y pruebas finales

---

**Última actualización:** 7 de Enero de 2026
**Estado:** Pendiente de inicio
**Próximo paso:** Crear migración de categorías
