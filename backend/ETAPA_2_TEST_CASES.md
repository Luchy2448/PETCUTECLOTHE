# 🧪 TEST CASES - ETAPA 2: Carrito de Compras
# 
# Este archivo contiene todos los test cases para validar el sistema de carrito
# Se puede ejecutar con herramientas como Postman, Insomnia o curl
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

---

## 📋 TEST CASES COMPLETOS

### 🔵 TEST 1: VER CARRITO VACÍO
# **Objetivo**: Verificar que un carrito vacío retorna estructura correcta
# **Método**: GET
# **Ruta**: /api/cart
# **Esperado**: Carrito vacío con totales en 0

```bash
curl -X GET http://127.0.0.1:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]"
```

**Respuesta esperada:**
```json
{
    "message": "Carrito obtenido exitosamente",
    "cart_items": [],
    "total": 0,
    "item_count": 0,
    "total_formatted": "$0",
    "currency": "ARS"
}
```

---

### 🛒 TEST 2: AGREGAR PRODUCTO AL CARRITO (CASO EXITOSO)
# **Objetivo**: Agregar un producto que existe y tiene stock suficiente
# **Método**: POST
# **Ruta**: /api/cart
# **Datos**: product_id=1, quantity=2

```bash
curl -X POST http://127.0.0.1:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

**Respuesta esperada:**
```json
{
    "message": "Producto agregado al carrito exitosamente",
    "cart_item": {
        "id": 1,
        "user_id": 1,
        "product_id": 1,
        "quantity": 2,
        "created_at": "2026-01-15T...",
        "updated_at": "2026-01-15T...",
        "product": {
            "id": 1,
            "name": "Suéter con corazones",
            "price": 15000.00,
            "stock": 10
        }
    },
    "cart_count": 2
}
```

---

### 🔄 TEST 3: AGREGAR PRODUCTO EXISTENTE (INCREMENTAR CANTIDAD)
# **Objetivo**: Verificar que si el producto ya está en el carrito, se incrementa la cantidad
# **Método**: POST
# **Ruta**: /api/cart
# **Datos**: product_id=1, quantity=1 (debería quedar en 3 total)

```bash
curl -X POST http://127.0.0.1:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "product_id": 1,
    "quantity": 1
  }'
```

**Respuesta esperada:**
```json
{
    "message": "Producto agregado al carrito exitosamente",
    "cart_item": {
        "id": 1,
        "quantity": 3,
        "product": {
            "id": 1,
            "name": "Suéter con corazones"
        }
    },
    "cart_count": 3
}
```

---

### ❌ TEST 4: AGREGAR PRODUCTO QUE NO EXISTE
# **Objetivo**: Validar manejo de error cuando el product_id no existe
# **Método**: POST
# **Ruta**: /api/cart
# **Datos**: product_id=999, quantity=1

```bash
curl -X POST http://127.0.0.1:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "product_id": 999,
    "quantity": 1
  }'
```

**Respuesta esperada:**
```json
{
    "error": "Producto no encontrado",
    "message": "El producto solicitado no existe"
}
```
**Código HTTP esperado**: 404

---

### ⚠️ TEST 5: AGREGAR PRODUCTO CON STOCK INSUFICIENTE
# **Objetivo**: Validar manejo de error cuando no hay stock suficiente
# **Método**: POST
# **Ruta**: /api/cart
# **Datos**: product_id=1, quantity=20 (solo hay 10 en stock)

```bash
curl -X POST http://127.0.0.1:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "product_id": 1,
    "quantity": 20
  }'
```

**Respuesta esperada:**
```json
{
    "error": "Stock insuficiente",
    "message": "Solo hay 10 unidades disponibles",
    "available_stock": 10
}
```
**Código HTTP esperado**: 400

---

### ⚠️ TEST 6: AGREGAR PRODUCTO CON STOCK INSUFICIENTE (YA EN CARRITO)
# **Objetivo**: Validar manejo cuando ya hay items en el carrito
# **Primero**: Agregar 10 unidades (llega al límite)
# **Segundo**: Intentar agregar 5 más

```bash
# Primera petición:
curl -X POST http://127.0.0.1:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "product_id": 1,
    "quantity": 10
  }'

# Segunda petición:
curl -X POST http://127.0.0.1:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "product_id": 1,
    "quantity": 5
  }'
```

**Respuesta esperada (segunda petición):**
```json
{
    "error": "Stock insuficiente",
    "message": "Solo hay 10 unidades disponibles. Ya tienes 10 en el carrito.",
    "available_stock": 10,
    "current_quantity": 10
}
```

---

### 📝 TEST 7: ACTUALIZAR CANTIDAD DE UN ITEM
# **Objetivo**: Modificar la cantidad de un producto en el carrito
# **Método**: PUT
# **Ruta**: /api/cart/{id}
# **Datos**: quantity=5

```bash
curl -X PUT http://127.0.0.1:8000/api/cart/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "quantity": 5
  }'
```

**Respuesta esperada:**
```json
{
    "message": "Cantidad actualizada exitosamente",
    "cart_item": {
        "id": 1,
        "quantity": 5,
        "product": {
            "id": 1,
            "name": "Suéter con corazones"
        }
    }
}
```

---

### ❌ TEST 8: ACTUALIZAR ITEM QUE NO ESTÁ EN CARRITO
# **Objetivo**: Validar error al intentar actualizar un item no existente
# **Método**: PUT
# **Ruta**: /api/cart/999

```bash
curl -X PUT http://127.0.0.1:8000/api/cart/999 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "quantity": 3
  }'
```

**Respuesta esperada:**
```json
{
    "error": "Item no encontrado",
    "message": "Este producto no está en tu carrito"
}
```
**Código HTTP esperado**: 404

---

### 📝 TEST 9: ACTUALIZAR CON STOCK INSUFICIENTE
# **Objetivo**: Validar error al actualizar a una cantidad mayor al stock disponible
# **Método**: PUT
# **Ruta**: /api/cart/1
# **Datos**: quantity=15 (solo hay 10 en stock)

```bash
curl -X PUT http://127.0.0.1:8000/api/cart/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "quantity": 15
  }'
```

**Respuesta esperada:**
```json
{
    "error": "Stock insuficiente",
    "message": "Solo hay 10 unidades disponibles",
    "available_stock": 10
}
```

---

### 🗑️ TEST 10: ELIMINAR UN ITEM DEL CARRITO
# **Objetivo**: Eliminar un producto específico del carrito
# **Método**: DELETE
# **Ruta**: /api/cart/{id}

```bash
curl -X DELETE http://127.0.0.1:8000/api/cart/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]"
```

**Respuesta esperada:**
```json
{
    "message": "Producto eliminado del carrito exitosamente",
    "cart_count": 2  # Si había 3 productos, ahora quedan 2
}
```

---

### 🗑️ TEST 11: ELIMINAR ITEM QUE NO EXISTE
# **Objetivo**: Validar error al intentar eliminar un item no existente
# **Método**: DELETE
# **Ruta**: /api/cart/999

```bash
curl -X DELETE http://127.0.0.1:8000/api/cart/999 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]"
```

**Respuesta esperada:**
```json
{
    "error": "Item no encontrado",
    "message": "Este producto no está en tu carrito"
}
```
**Código HTTP esperado**: 404

---

### 🧹 TEST 12: VACIAR CARRITO COMPLETO
# **Objetivo**: Eliminar todos los productos del carrito
# **Método**: DELETE
# **Ruta**: /api/cart

```bash
curl -X DELETE http://127.0.0.1:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]"
```

**Respuesta esperada:**
```json
{
    "message": "Carrito vaciado exitosamente",
    "deleted_items": 3  # Número de items eliminados
}
```

---

### 💰 TEST 13: CÁLCULAR TOTAL DEL CARRITO
# **Objetivo**: Verificar cálculo de subtotal y total
# **Método**: POST
# **Ruta**: /api/cart/calculate

```bash
curl -X POST http://127.0.0.1:8000/api/cart/calculate \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]"
```

**Respuesta esperada (carrito con múltiples items):**
```json
{
    "message": "Cálculo del carrito realizado exitosamente",
    "cart_items": [...],
    "subtotal": 75000.00,
    "total": 75000.00,
    "item_count": 5,
    "subtotal_formatted": "$75,000",
    "total_formatted": "$75,000",
    "currency": "ARS"
}
```

---

### 📊 TEST 14: CÁLCULAR CARRITO VACÍO
# **Objetivo**: Verificar cálculo cuando el carrito está vacío
# **Método**: POST
# **Ruta**: /api/cart/calculate

```bash
curl -X POST http://127.0.0.1:8000/api/cart/calculate \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]"
```

**Respuesta esperada:**
```json
{
    "message": "El carrito está vacío",
    "subtotal": 0,
    "total": 0,
    "item_count": 0,
    "currency": "ARS"
}
```

---

## 🔴 TESTS DE AUTENTICACIÓN

### TEST 15: ACCEDER SIN TOKEN
# **Objetivo**: Verificar que las rutas del carrito requieren autenticación
# **Método**: GET (sin Authorization header)

```bash
curl -X GET http://127.0.0.1:8000/api/cart
```

**Respuesta esperada:**
```json
{
    "message": "Unauthenticated.",
    "status": 401
}
```
**Código HTTP esperado**: 401

### TEST 16: ACCEDER CON TOKEN INVÁLIDO
# **Objetivo**: Verificar que el token expirado o inválido es rechazado
# **Método**: GET
# **Token**: Bearer token_invalido_prueba

```bash
curl -X GET http://127.0.0.1:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer token_invalido_prueba"
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

### TEST 17: DATOS INCOMPLETOS EN STORE
# **Objetivo**: Verificar validación de campos requeridos
# **Método**: POST sin product_id

```bash
curl -X POST http://127.0.0.1:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "quantity": 2
  }'
```

**Respuesta esperada:**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "product_id": [
            "The product id field is required."
        ]
    }
}
```
**Código HTTP esperado**: 422

### TEST 18: DATOS INVÁLIDOS EN STORE
# **Objetivo**: Verificar validación de tipos de datos
# **Método**: POST con quantity = "dos"

```bash
curl -X POST http://127.0.0.1:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer [TU_TOKEN]" \
  -d '{
    "product_id": 1,
    "quantity": "dos"
  }'
```

**Respuesta esperada:**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "quantity": [
            "The quantity must be an integer."
        ]
    }
}
```
**Código HTTP esperado**: 422

---

## 📊 ESCENARIOS DE TESTING

### Escenario 1: Flujo Completo de Compra
1. **Ver carrito vacío** → TEST 1
2. **Agregar 3 productos diferentes** → TEST 2 (3 veces)
3. **Ver carrito con items** → TEST 1 (debería mostrar 3 items)
4. **Modificar cantidad de un item** → TEST 7
5. **Calcular total** → TEST 13
6. **Vaciar carrito** → TEST 12
7. **Ver carrito vacío** → TEST 1

### Escenario 2: Manejo de Errores
1. **Intentar agregar producto inexistente** → TEST 4
2. **Intentar agregar sin stock** → TEST 5
3. **Intentar actualizar item no existente** → TEST 8
4. **Intentar eliminar item no existente** → TEST 11

### Escenario 3: Casos Límite
1. **Llenar carrito hasta el límite de stock**
2. **Intentar agregar más** → TEST 6
3. **Intentar actualizar más del stock** → TEST 9

---

## 🎯 CRITERIOS DE ÉXITO

La ETAPA 2 estará **COMPLETA** cuando todos los tests anteriores pasen:

✅ **Funcionalidad Básica**:
- Agregar productos al carrito
- Ver items del carrito
- Modificar cantidades
- Eliminar items individuales
- Vaciar carrito completo
- Calcular totales

✅ **Manejo de Errores**:
- Validación de datos de entrada
- Manejo de stock insuficiente
- Productos no encontrados
- Autenticación requerida

✅ **Consistencia de Datos**:
- Cálculos correctos de subtotal
- Contadores de items funcionales
- Relaciones con productos mantenidas

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

---

**¡Estos tests cubren el 100% de la funcionalidad del carrito de compras!** 🛒🛍️