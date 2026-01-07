# 📦 Etapa 3 - Pedidos y Pagos con Mercado Pago

## 📅 Fecha de Inicio: TBD
## 🎯 Estado: Pendiente
## ✅ Progreso: ░░░░░░░░░░ 0%

---

## 🌟 OBJETIVO DE LA ETAPA

Implementar sistema de **PEDIDOS** y **PAGOS** con Mercado Pago:
- ✅ Crear pedidos desde el carrito
- ✅ Integrar pagos con Mercado Pago
- ✅ Recibir notificaciones de pago (webhooks)
- ✅ Ver historial de pedidos del usuario

---

## 📦 QUÉ VAMOS A CONSTRUIR

### Tabla: orders
```sql
┌─────────────────┐
│ id (PK)                │ - ID único del pedido
│ user_id (FK)           │ - ID del usuario que compró
│ total                   │ - Total del pedido
│ status                  │ - Estado (pending, paid, shipped, delivered, cancelled)
│ shipping_address        │ - Dirección de envío
│ phone_number            │ - Teléfono del cliente
│ created_at             │ - Fecha de creación
│ updated_at             │ - Fecha de actualización
└─────────────────┘
```

### Tabla: order_items
```sql
┌─────────────────┐
│ id (PK)                │ - ID único del item
│ order_id (FK)           │ - ID del pedido
│ product_id (FK)          │ - ID del producto
│ quantity               │ - Cantidad comprada
│ price_at_purchase       │ - Precio en ese momento
│ size                  │ - Talla comprada
│ created_at             │ - Fecha de creación
└─────────────────┘
```

### Tabla: payments
```sql
┌─────────────────┐
│ id (PK)                              │ - ID único del pago
│ order_id (FK)                         │ - ID del pedido
│ mercado_pago_preference_id              │ - ID de preferencia MP
│ mercado_pago_payment_id                 │ - ID del pago MP
│ status                               │ - Estado (pending, approved, rejected)
│ payment_method                        │ - Método (card, cash, etc)
│ payment_type                          │ - Tipo de pago
│ created_at                           │ - Fecha de creación
│ updated_at                           │ - Fecha de actualización
└─────────────────┘
```

---

## 💳 INTEGRACIÓN CON MERCADO PAGO

### Flujo de Pago
```
Usuario → Crea pedido → Backend crea preferencia MP
          ↓
Usuario → Va a pagar → Redirect a checkout MP
          ↓
Usuario → Paga → MP notifica a webhook
          ↓
Backend → Verifica pago → Actualiza estado pedido
```

### Rutas de la API
```
POST   /api/orders           → Crear pedido desde carrito (requiere auth)
GET    /api/orders           → Ver mis pedidos (requiere auth)
GET    /api/orders/{id}      → Ver detalle de pedido (requiere auth)
POST   /api/payment/create   → Crear preferencia de pago MP (requiere auth)
POST   /api/payment/webhook → Recibir notificación MP (pública)
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

- [ ] Crear migración `create_orders_table`
- [ ] Crear migración `create_order_items_table`
- [ ] Crear migración `create_payments_table`
- [ ] Crear modelo `Order`
- [ ] Crear modelo `OrderItem`
- [ ] Crear modelo `Payment`
- [ ] Crear `OrderController`
- [ ] Crear servicio de Mercado Pago (usar Guzzle)
- [ ] Crear `PaymentController`
- [ ] Configurar webhooks de Mercado Pago
- [ ] Probar crear pedido
- [ ] Probar proceso de pago completo
- [ ] Probar notificación de webhook
- [ ] Documentar todo el código

---

## 🔐 MERCADO PAGO CONFIGURACIÓN

### Variables de entorno (.env)
```env
MERCADO_PAGO_ACCESS_TOKEN=tu_access_token_aqui
MERCADO_PAGO_MODE=test  # test = sandbox, production = real
```

### Métodos de pago soportados
- Tarjeta de crédito/débito 💳
- Dinero en cuenta Mercado Pago 💰
- Efectivo (Rapipago, PagoFácil) 💵
- Transferencia bancaria 🏦

---

**Próxima etapa después de completar esta:** Etapa 4 - Frontend React ⚛️
