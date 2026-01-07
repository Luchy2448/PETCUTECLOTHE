# 🛒 Etapa 2 - Carrito de Compras

## 📅 Fecha de Inicio: TBD
## 🎯 Estado: Pendiente
## ✅ Progreso: ░░░░░░░░░░ 0%

---

## 🌟 OBJETIVO DE LA ETAPA

Implementar sistema de **CARRITO DE COMPRAS** para que los usuarios puedan:
- ✅ Agregar productos al carrito
- ✅ Ver el carrito
- ✅ Modificar cantidad de productos
- ✅ Eliminar productos del carrito
- ✅ Calcular total del carrito

---

## 📦 QUÉ VAMOS A CONSTRUIR

### Tabla: cart_items
```sql
┌─────────────────┐
│ id (PK)        │ - ID único del item del carrito
│ user_id (FK)   │ - ID del usuario (relación)
│ product_id (FK) │ - ID del producto (relación)
│ quantity       │ - Cantidad (1, 2, 3, etc)
│ created_at     │ - Fecha de creación
│ updated_at     │ - Fecha de actualización
└─────────────────┘
```

### Rutas de la API
```
GET    /api/cart              → Ver mi carrito (requiere auth)
POST   /api/cart              → Agregar producto (requiere auth)
PUT    /api/cart/{id}         → Actualizar cantidad (requiere auth)
DELETE /api/cart/{id}         → Eliminar del carrito (requiere auth)
DELETE /api/cart              → Vaciar carrito completo (requiere auth)
POST   /api/cart/calculate    → Calcular total (requiere auth)
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

- [ ] Crear migración `create_cart_items_table`
- [ ] Crear modelo `CartItem`
- [ ] Crear `CartController`
- [ ] Configurar rutas de carrito
- [ ] Probar agregar al carrito
- [ ] Probar modificar cantidad
- [ ] Probar eliminar del carrito
- [ ] Probar vaciar carrito
- [ ] Probar cálculo de total
- [ ] Documentar todo el código

---

**Próxima etapa después de completar esta:** Etapa 3 - Pedidos y Pagos con Mercado Pago 💳
