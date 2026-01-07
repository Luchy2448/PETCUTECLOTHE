# 👩‍💻 Etapa 5 - Panel de Administración

## 📅 Fecha de Inicio: TBD
## 🎯 Estado: Pendiente
## ✅ Progreso: ░░░░░░░░░░ 0%

---

## 🌟 OBJETIVO DE LA ETAPA

Crear un **PANEL DE ADMINISTRACIÓN** donde el admin pueda:
- ✅ Ver estadísticas básicas
- ✅ Gestionar productos (CRUD)
- ✅ Gestionar categorías (CRUD)
- ✅ Ver y gestionar pedidos
- ✅ Ver lista de usuarios

---

## 📦 QUÉ VAMOS A CONSTRUIR

### Páginas de Admin
- [ ] `AdminDashboard.jsx` - Dashboard con estadísticas
- [ ] `AdminProducts.jsx` - Gestión de productos
- [ ] `AdminCategories.jsx` - Gestión de categorías
- [ ] `AdminOrders.jsx` - Gestión de pedidos
- [ ] `AdminUsers.jsx` - Lista de usuarios

### Funcionalidades

#### Dashboard
- [ ] Mostrar total de productos
- [ ] Mostrar total de pedidos
- [ ] Mostrar productos más vendidos
- [ ] Mostrar ventas totales
- [ ] Gráfico simple de ventas

#### Gestión de Productos
- [ ] Lista de todos los productos
- [ ] Crear nuevo producto
- [ ] Editar producto existente
- [ ] Eliminar producto
- [ ] Formulario con validación
- [ ] Subida de imágenes (o URL)

#### Gestión de Categorías
- [ ] Lista de todas las categorías
- [ ] Crear nueva categoría
- [ ] Editar categoría
- [ ] Eliminar categoría

#### Gestión de Pedidos
- [ ] Lista de todos los pedidos
- [ ] Ver detalle de pedido
- [ ] Cambiar estado del pedido (pending → paid → shipped → delivered → cancelled)
- [ ] Filtros por estado
- [ ] Filtros por fecha

#### Usuarios
- [ ] Lista de todos los usuarios registrados
- [ ] Ver información básica del usuario
- [ ] Ver pedidos del usuario

---

## 🔐 ROLES DE USUARIO

### Admin
- Puede hacer TODO en el panel de admin
- Puede crear, editar y borrar productos
- Puede crear, editar y borrar categorías
- Puede cambiar estados de pedidos

### Cliente (usuario normal)
- Solo puede ver productos
- Solo puede hacer compras
- Solo puede ver su historial de pedidos
- NO puede acceder al panel de admin

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### Backend (Laravel)
- [ ] Crear middleware para verificar rol de admin
- [ ] Crear rutas protegidas de admin
- [ ] Crear `AdminStatsController` para estadísticas
- [ ] Modificar `ProductController` con más funcionalidades
- [ ] Modificar `OrderController` con gestión de estados

### Frontend (React)
- [ ] Crear ruta protegida de admin
- [ ] Crear página de Dashboard
- [ ] Crear página de gestión de productos
- [ ] Crear página de gestión de categorías
- [ ] Crear página de gestión de pedidos
- [ ] Crear página de lista de usuarios
- [ ] Verificar permisos antes de acceder

### Funcionalidades de Admin
- [ ] Ver estadísticas en tiempo real
- [ ] CRUD completo de productos
- [ ] CRUD completo de categorías
- [ ] Cambiar estado de pedidos
- [ ] Ver lista de usuarios
- [ ] Implementar gráfico de ventas

### UI/UX
- [ ] Diseño limpio y profesional
- [ ] Tablas con datos ordenados
- [ ] Formularios con validación
- [ ] Mensajes de confirmación para acciones destructivas (borrar)
- [ ] Loading states
- [ ] Manejo de errores

---

## 📊 DASHBOARD - ESTADÍSTICAS A MOSTRAR

### KPIs (Key Performance Indicators)
```
📦 Total de productos: 15
🛒 Total de pedidos: 42
💰 Ventas totales: $630,000
👥 Total de usuarios: 28
```

### Productos Más Vendidos (Top 5)
```
1. Suéter con corazones - 12 ventas
2. Vestido de gala - 10 ventas
3. Camiseta básica - 8 ventas
4. Corbata elegante - 6 ventas
5. Disfraz superhéroe - 5 ventas
```

### Pedidos Recientes (Últimos 10)
```
| ID | Cliente | Total | Estado | Fecha |
|----|---------|--------|--------|
| 1  | María   | $25,000| Delivered | 2026-01-07|
| 2  | Carlos  | $15,000| Shipped   | 2026-01-06|
| 3  | Ana     | $8,000 | Paid      | 2026-01-06|
```

---

## 🎨 DISEÑO DEL PANEL DE ADMIN

### Colores (Profesional y limpio)
- Primario: `#4F46E5` (Azul índigo)
- Secundario: `#10B981` (Verde esmeralda)
- Warning: `#F59E0B` (Naranja)
- Danger: `#EF4444` (Rojo)
- Background: `#F9FAFB` (Gris claro)
- Card: `#FFFFFF` (Blanco)

### Layout
- Sidebar con navegación
- Header con logo y perfil de usuario
- Contenido principal con cards y tablas
- Responsive (mobile sidebar)

---

## 📱 ESTRUCTURA DE MENÚ DEL PANEL DE ADMIN

```
📊 Dashboard
📦 Productos
📁 Categorías
🛒 Pedidos
👥 Usuarios
🚪 Salir
```

---

**Próxima etapa después de completar esta:** Etapa 6 - Deploy y Pruebas Finales 🚀
