# 🎨 Etapa 4 - Frontend React

## 📅 Fecha de Inicio: TBD
## 🎯 Estado: Pendiente
## ✅ Progreso: ░░░░░░░░░░ 0%

---

## 🌟 OBJETIVO DE LA ETAPA

Crear el **FRONTEND** con React para que los usuarios puedan:
- ✅ Ver el catálogo de productos
- ✅ Buscar y filtrar productos
- ✅ Agregar al carrito
- ✅ Ver el carrito
- ✅ Registrarse y hacer login
- ✅ Ver historial de pedidos

---

## 📦 QUÉ VAMOS A CONSTRUIR

### Páginas/Componentes Principales
- [ ] `App.jsx` - Componente principal
- [ ] `Navbar.jsx` - Barra de navegación
- [ ] `Footer.jsx` - Pie de página
- [ ] `Home.jsx` - Página de inicio
- [ ] `Products.jsx` - Catálogo de productos
- [ ] `ProductDetail.jsx` - Detalle de producto
- [ ] `Cart.jsx` - Carrito de compras
- [ ] `Login.jsx` - Login de usuarios
- [ ] `Register.jsx` - Registro de usuarios
- [ ] `OrderHistory.jsx` - Historial de pedidos

### Componentes Reutilizables
- [ ] `ProductCard.jsx` - Tarjeta de producto
- [ ] `CategoryFilter.jsx` - Filtro de categorías
- [ ] `SearchBar.jsx` - Barra de búsqueda
- [ ] `CartItem.jsx` - Item del carrito
- [ ] `LoadingSpinner.jsx` - Indicador de carga

### Context/State Management
- [ ] `AuthContext.jsx` - Estado de autenticación
- [ ] `CartContext.jsx` - Estado del carrito

### Servicios
- [ ] `api.js` - Conexión con backend Laravel
- [ ] `auth.js` - Servicios de autenticación
- [ ] `products.js` - Servicios de productos
- [ ] `cart.js` - Servicios de carrito

---

## 🎨 DISEÑO Y ESTILO

### Colores (PET CUTE CLOTHES - Amigable)
- Rosa pastel: `#FFB6C1` 💗
- Celeste: `#ADD8E6` 💙
- Amarillo suave: `#FFFACD` 💛
- Verde menta: `#98FF98` 💚
- Blanco: `#FFFFFF` ⚪
- Gris oscuro: `#333333` ⚫

### Tipografía
- Principal: **Poppins** (Google Fonts)
- Secundaria: **Nunito** (Google Fonts)

### Layout
- Header fijo con logo y navegación
- Contenido principal centrado
- Cards de productos con foto, nombre, precio
- Botones de acción grandes y coloridos
- Diseño responsive (mobile-friendly)

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### Configuración Inicial
- [ ] Crear proyecto React con Vite
- [ ] Instalar Tailwind CSS o Material UI
- [ ] Instalar Axios
- [ ] Instalar React Router
- [ ] Configurar rutas de la aplicación

### Componentes Base
- [ ] Navbar con logo y menú
- [ ] Footer con información
- [ ] Layout principal

### Autenticación
- [ ] Página de Login
- [ ] Página de Registro
- [ ] Context de autenticación
- [ ] Guardar token en localStorage

### Catálogo de Productos
- [ ] Página de listado de productos
- [ ] Página de detalle de producto
- [ ] Filtros de categoría
- [ ] Barra de búsqueda
- [ ] Paginación

### Carrito de Compras
- [ ] Página de carrito
- [ ] Agregar producto al carrito
- [ ] Modificar cantidad
- [ ] Eliminar del carrito
- [ ] Ver total
- [ ] Context de carrito

### Pedidos
- [ ] Página de historial de pedidos
- [ ] Detalle de pedido
- [ ] Mostrar estado del pedido

### Integración con Backend
- [ ] Conectar con API Laravel
- [ ] Implementar manejo de errores
- [ ] Implementar loading states
- [ ] Probar todas las funcionalidades

---

## 📱 ESTRUCTURA DE CARPETAS FRONTEND

```
frontend/
├── src/
│   ├── components/          # Componentes reutilizables
│   ├── pages/              # Páginas principales
│   ├── context/            # Contextos de React
│   ├── services/           # Servicios API
│   ├── styles/             # Estilos globales
│   ├── assets/             # Imágenes y recursos
│   ├── App.jsx             # Componente principal
│   └── main.jsx            # Entry point
├── public/                 # Archivos públicos
└── package.json
```

---

**Próxima etapa después de completar esta:** Etapa 5 - Panel de Administración 👩‍💻
