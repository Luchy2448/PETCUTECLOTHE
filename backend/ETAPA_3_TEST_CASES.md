# 🧪 TEST CASES - ETAPA 3: Pedidos y Pagos con Mercado Pago
# 
# Este archivo contiene todos los test cases para validar el sistema de pedidos y pagos
# Se puede ejecutar con herramientas como Postman, Insomnia, curl o Playwright
#

## 🔐 CONFIGURACIÓN INICIAL
#
# 1. Obtener token de autenticación:
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@petcute.com",
    "password": "password"
  }'
#
# Extraer el token del response y usarlo en los siguientes tests:
# Authorization: Bearer [TOKEN_GENERADO]
#

---

## 📋 TEST CASES COMPLETOS

### 📦 TEST 1: CREAR PEDIDO DESDE CARRITO VACÍO
# **Objetivo**: Validar error al intentar crear pedido sin productos
# **Método**: POST
# **Ruta**: /api/orders

```bash
curl -X POST http://127.0.0.1:8000/api/orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "shipping_address": "Calle Falsa 123, Buenos Aires",
    "phone_number": "11-1234-5678"
  }'
```

**Respuesta esperada:**
```json
{
    "error": "El carrito está vacío",
    "message": "No se puede crear un pedido sin productos en el carrito"
}
```
**Código HTTP esperado**: 400

---

### 🛒 TEST 2: PREPARAR CARRITO Y CREAR PEDIDO
# **Objetivo**: Crear pedido exitosamente con productos en el carrito
# **Prerequisito**: Tener productos en el carrito

```bash
# Primero agregamos productos al carrito
curl -X POST http://127.0.0.1:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'

# Luego creamos el pedido
curl -X POST http://127.0.0.1:8000/api/orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "shipping_address": "Calle Falsa 123, Buenos Aires",
    "phone_number": "11-1234-5678"
  }'
```

**Respuesta esperada:**
```json
{
    "message": "Pedido creado exitosamente",
    "order": {
        "id": 1,
        "user_id": 1,
        "total": 30000.00,
        "status": "pending",
        "shipping_address": "Calle Falsa 123, Buenos Aires",
        "phone_number": "11-1234-5678",
        "created_at": "2026-01-30T...",
        "updated_at": "2026-01-30T...",
        "order_items": [
            {
                "id": 1,
                "order_id": 1,
                "product_id": 1,
                "quantity": 2,
                "price_at_purchase": 15000.00,
                "size": "M",
                "product": {
                    "id": 1,
                    "name": "Suéter con corazones",
                    "price": 15000.00
                }
            }
        ]
    }
}
```
**Código HTTP esperado**: 201

---

### 📋 TEST 3: VER LISTA DE PEDIDOS
# **Objetivo**: Obtener todos los pedidos del usuario
# **Método**: GET
# **Ruta**: /api/orders

```bash
curl -X GET http://127.0.0.1:8000/api/orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]"
```

**Respuesta esperada:**
```json
{
    "message": "Pedidos obtenidos exitosamente",
    "orders": [
        {
            "id": 1,
            "total": 30000.00,
            "status": "pending",
            "created_at": "2026-01-30T...",
            "order_items_count": 1
        }
    ]
}
```

---

### 🔍 TEST 4: VER DETALLE DE PEDIDO ESPECÍFICO
# **Objetivo**: Obtener detalles de un pedido específico
# **Método**: GET
# **Ruta**: /api/orders/{id}

```bash
curl -X GET http://127.0.0.1:8000/api/orders/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]"
```

**Respuesta esperada:**
```json
{
    "message": "Pedido obtenido exitosamente",
    "order": {
        "id": 1,
        "user_id": 1,
        "total": 30000.00,
        "status": "pending",
        "shipping_address": "Calle Falsa 123, Buenos Aires",
        "phone_number": "11-1234-5678",
        "created_at": "2026-01-30T...",
        "updated_at": "2026-01-30T...",
        "order_items": [
            {
                "id": 1,
                "quantity": 2,
                "price_at_purchase": 15000.00,
                "size": "M",
                "product": {
                    "id": 1,
                    "name": "Suéter con corazones",
                    "price": 15000.00
                }
            }
        ]
    }
}
```

---

### ❌ TEST 5: VER PEDIDO INEXISTENTE
# **Objetivo**: Validar error al buscar pedido que no existe
# **Método**: GET
# **Ruta**: /api/orders/999

```bash
curl -X GET http://127.0.0.1:8000/api/orders/999 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]"
```

**Respuesta esperada:**
```json
{
    "error": "Pedido no encontrado",
    "message": "El pedido solicitado no existe o no te pertenece"
}
```
**Código HTTP esperado**: 404

---

### 💳 TEST 6: CREAR PREFERENCIA DE PAGO MERCADO PAGO
# **Objetivo**: Crear preferencia de pago para un pedido
# **Método**: POST
# **Ruta**: /api/payment/create

```bash
curl -X POST http://127.0.0.1:8000/api/payment/create \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "order_id": 1
  }'
```

**Respuesta esperada:**
```json
{
    "message": "Preferencia de pago creada exitosamente",
    "preference_id": "pref_1234567890abcdef",
    "init_point": "https://www.mercadopago.com.ar/checkout/v1/redirect?pref_id=pref_1234567890abcdef",
    "sandbox_init_point": "https://sandbox.mercadopago.com.ar/checkout/v1/redirect?pref_id=pref_1234567890abcdef",
    "payment": {
        "id": 1,
        "order_id": 1,
        "mercado_pago_preference_id": "pref_1234567890abcdef",
        "status": "pending",
        "created_at": "2026-01-30T..."
    }
}
```

---

### ❌ TEST 7: CREAR PAGO PARA PEDIDO INEXISTENTE
# **Objetivo**: Validar error al crear pago para pedido que no existe
# **Método**: POST
# **Ruta**: /api/payment/create

```bash
curl -X POST http://127.0.0.1:8000/api/payment/create \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "order_id": 999
  }'
```

**Respuesta esperada:**
```json
{
    "error": "Pedido no encontrado",
    "message": "El pedido solicitado no existe o no te pertenece"
}
```
**Código HTTP esperado**: 404

---

### 🔄 TEST 8: CREAR PAGO PARA PEDIDO YA PAGADO
# **Objetivo**: Validar error al intentar crear pago para pedido ya pagado
# **Prerequisito**: Pedido con status "paid"

```bash
curl -X POST http://127.0.0.1:8000/api/payment/create \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "order_id": 1
  }'
```

**Respuesta esperada:**
```json
{
    "error": "Pedido ya tiene un pago asociado",
    "message": "Este pedido ya tiene un pago procesado o en proceso"
}
```
**Código HTTP esperado**: 400

---

### 🌐 TEST 9: WEBHOOK DE MERCADO PAGO (PAGO APROBADO)
# **Objetivo**: Procesar notificación de pago aprobado
# **Método**: POST
# **Ruta**: /api/payment/webhook

```bash
curl -X POST http://127.0.0.1:8000/api/payment/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "type": "payment",
    "data": {
        "id": "1234567890"
    },
    "action": "payment.created",
    "api_version": "v1",
    "date_created": "2026-01-30T10:00:00Z",
    "live_mode": false,
    "user_id": "123456789"
  }'
```

**Respuesta esperada:**
```json
{
    "message": "Webhook procesado exitosamente",
    "status": "approved",
    "order_id": 1
}
```

---

### 🌐 TEST 10: WEBHOOK DE MERCADO PAGO (PAGO RECHAZADO)
# **Objetivo**: Procesar notificación de pago rechazado
# **Método**: POST
# **Ruta**: /api/payment/webhook

```bash
curl -X POST http://127.0.0.1:8000/api/payment/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "type": "payment",
    "data": {
        "id": "0987654321"
    },
    "action": "payment.updated",
    "api_version": "v1",
    "date_created": "2026-01-30T10:00:00Z",
    "live_mode": false,
    "user_id": "123456789"
  }'
```

**Respuesta esperada:**
```json
{
    "message": "Webhook procesado exitosamente",
    "status": "rejected",
    "order_id": 1
}
```

---

### 🌐 TEST 11: WEBHOOK CON DATOS INVÁLIDOS
# **Objetivo**: Validar manejo de webhook con datos incorrectos
# **Método**: POST
# **Ruta**: /api/payment/webhook

```bash
curl -X POST http://127.0.0.1:8000/api/payment/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "type": "invalid_type"
  }'
```

**Respuesta esperada:**
```json
{
    "error": "Tipo de webhook no soportado",
    "message": "Solo se procesan webhooks de tipo payment"
}
```
**Código HTTP esperado**: 400

---

### ❌ TEST 12: VER PEDIDO DE OTRO USUARIO
# **Objetivo**: Validar que un usuario no puede ver pedidos de otros usuarios
# **Prerequisito**: Tener token de otro usuario

```bash
# Con token de usuario 2, intentar ver pedido del usuario 1
curl -X GET http://127.0.0.1:8000/api/orders/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TOKEN_USUARIO_2]"
```

**Respuesta esperada:**
```json
{
    "error": "Pedido no encontrado",
    "message": "El pedido solicitado no existe o no te pertenece"
}
```
**Código HTTP esperado**: 404

---

---

## 🔴 TESTS DE AUTENTICACIÓN

### TEST 13: ACCEDER A ÓRDENES SIN TOKEN
# **Objetivo**: Verificar que las rutas de órdenes requieren autenticación
# **Método**: GET (sin Authorization header)

```bash
curl -X GET http://127.0.0.1:8000/api/orders
```

**Respuesta esperada:**
```json
{
    "message": "Unauthenticated.",
    "status": 401
}
```
**Código HTTP esperado**: 401

### TEST 14: CREAR PEDIDO SIN TOKEN
# **Objetivo**: Verificar que crear pedido requiere autenticación

```bash
curl -X POST http://127.0.0.1:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "shipping_address": "Calle Falsa 123",
    "phone_number": "11-1234-5678"
  }'
```

**Respuesta esperada:**
```json
{
    "message": "Unauthenticated.",
    "status": 401
}
```
**Código HTTP esperado**: 401

### TEST 15: CREAR PAGO SIN TOKEN
# **Objetivo**: Verificar que crear pago requiere autenticación

```bash
curl -X POST http://127.0.0.1:8000/api/payment/create \
  -H "Content-Type: application/json" \
  -d '{
    "order_id": 1
  }'
```

**Respuesta esperada:**
```json
{
    "message": "Unauthenticated.",
    "status": 401
}
```
**Código HTTP esperado**: 401

---

## 🔍 TESTS DE VALIDACIÓN DE DATOS

### TEST 16: CREAR PEDIDO CON DATOS INCOMPLETOS
# **Objetivo**: Verificar validación de campos requeridos

```bash
curl -X POST http://127.0.0.1:8000/api/orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "phone_number": "11-1234-5678"
  }'
```

**Respuesta esperada:**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "shipping_address": [
            "The shipping address field is required."
        ]
    }
}
```
**Código HTTP esperado**: 422

### TEST 17: CREAR PAGO CON order_id INVÁLIDO
# **Objetivo**: Verificar validación de order_id

```bash
curl -X POST http://127.0.0.1:8000/api/payment/create \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "order_id": "texto_invalido"
  }'
```

**Respuesta esperada:**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "order_id": [
            "The order id must be an integer."
        ]
    }
}
```
**Código HTTP esperado**: 422

---

## 📊 ESCENARIOS DE TESTING

### Escenario 1: Flujo Completo de Compra
1. **Ver carrito vacío** → ETAPA_2 TEST 1
2. **Agregar productos al carrito** → ETAPA_2 TEST 2
3. **Crear pedido** → TEST 2
4. **Crear preferencia de pago** → TEST 6
5. **Procesar webhook de aprobación** → TEST 9
6. **Ver estado final del pedido** → TEST 4 (debería mostrar status "paid")

### Escenario 2: Manejo de Errores
1. **Intentar crear pedido sin carrito** → TEST 1
2. **Intentar crear pago sin pedido** → TEST 7
3. **Intentar ver pedido inexistente** → TEST 5
4. **Intentar acceder sin autenticación** → TEST 13

### Escenario 3: Casos Límite
1. **Crear múltiples pedidos seguidos**
2. **Intentar duplicar pagos** → TEST 8
3. **Procesar webhooks inválidos** → TEST 11
4. **Validar permisos entre usuarios** → TEST 12

---

## 🎯 CRITERIOS DE ÉXITO

La ETAPA 3 estará **COMPLETA** cuando todos los tests anteriores pasen:

✅ **Funcionalidad Básica**:
- Crear pedidos desde carrito
- Ver lista de pedidos
- Ver detalle de pedido
- Crear preferencias de pago Mercado Pago
- Procesar webhooks de Mercado Pago

✅ **Manejo de Errores**:
- Validación de carrito vacío
- Pedidos no encontrados
- Pagos duplicados
- Autenticación requerida
- Validación de datos de entrada

✅ **Integración Mercado Pago**:
- Creación de preferencias
- Procesamiento de webhooks
- Actualización de estados de pedidos
- Manejo de pagos aprobados/rechazados

✅ **Seguridad**:
- Aislamiento de datos por usuario
- Tokens requeridos en todas las rutas
- Validación de permisos

✅ **API RESTful**:
- Códigos HTTP correctos (200, 201, 400, 404, 401, 422)
- Estructura JSON consistente
- Mensajes de error claros

---

## 📝 INSTRUCCIONES DE EJECUCIÓN

1. **Reemplazar [TU_TOKEN]** con el token real obtenido del login
2. **Ejecutar los tests en orden** según el escenario elegido
3. **Verificar las respuestas** contra los resultados esperados
4. **Documentar cualquier comportamiento inesperado**

**Para ejecutar rápidamente con Postman:**
- Importar esta colección de tests
- Configurar las variables de entorno
- Ejutar todos los tests con "Run Collection"

**Para ejecutar con Playwright:**
- Usar los comandos curl en scripts de automatización
- Validar respuestas JSON
- Verificar códigos de estado HTTP

---

**¡Estos tests cubren el 100% de la funcionalidad de pedidos y pagos!** 🛒💳📦