Test Cases - PET CUTE CLOTHES ETAPA 3

Test ID | Feature | User State | Expected Result | Status | Notes
---|---|---|---|---
TC-ET3-001 | Create Order from Cart | Logged in, Has items in cart | ✅ Order created successfully from cart items<br>✅ Cart items converted to order<br>✅ Cart emptied after order creation<br>✅ Order saved with pending status<br>✅ User receives success message | | Core order creation functionality |
TC-ET3-002 | Create Order - Empty Cart | Logged in, Empty cart | ✅ Shows error message "Tu carrito está vacío"<br>❌ Cannot create order<br>✅ Redirects back to cart | | Should prevent empty cart orders |
TC-ET3-003 | Create Order - Invalid Cart Data | Logged in, Corrupted cart data | ✅ Shows error message about invalid cart data<br>✅ Order not created<br>✅ Cart items preserved | | Error handling for corrupted data |
TC-ET3-004 | View Orders List | Logged in | ✅ Shows list of user's orders<br>✅ Orders display with status badges<br>✅ Shows order dates and totals<br>✅ Order details viewable | | Order history functionality |
TC-ET3-005 | View Orders - No Orders | Logged in, No orders | ✅ Shows "No tienes pedidos aún" message<br>✅ Shows suggestion to add products to cart | | Empty state handling |
TC-ET3-006 | View Order Details | Logged in, Has orders | ✅ Shows complete order information<br>✅ Lists all items with quantities and prices<br>✅ Shows shipping address<br>✅ Shows order status with appropriate color<br>✅ Shows payment method and status | | Detailed order view |
TC-ET3-007 | View Order Details - Not Owner | Logged in, Wrong user | ✅ Shows "Pedido no encontrado" error<br>❌ Cannot view other users' orders | | Security - users can only see their orders |
TC-ET3-008 | Mercado Pago Integration | Logged in, Creating order | ✅ Mercado Pago payment preference created<br>✅ User redirected to Mercado Pago checkout<br>✅ Order data passed correctly to MP<br>✅ Payment ID saved to order | | Payment gateway integration |
TC-ET3-009 | Mercado Pago - Payment Success | After completing MP payment | ✅ Webhook receives payment confirmation<br>✅ Order status updated to 'paid'<br>✅ Payment record created with 'approved' status<br>✅ User can view updated order status | | Payment success flow |
TC-ET3-010 | Mercado Pago - Payment Failure | After MP payment failure | ✅ Webhook receives payment rejection<br>✅ Order status updated to 'cancelled'<br>✅ Payment record created with 'rejected' status<br>✅ User notified of payment failure | | Payment failure handling |
TC-ET3-011 | Mercado Pago - Webhook Security | External MP webhook call | ✅ Webhook validates MP signature<br>✅ Only processes legitimate MP notifications<br>✅ Logs webhook reception for debugging<br>❌ Rejects invalid/fake webhooks | | Security verification |
TC-ET3-012 | Order Status Progression | Logged in, Pending order | ✅ Order progresses: pending → processing → shipped → delivered<br>✅ Status changes are properly dated<br>✅ User receives notifications for status changes<br>✅ Each status shows appropriate icon/color | | Full order lifecycle |
TC-ET3-013 | Cancel Order | Logged in, Pending order | ✅ Cancel button appears for pending orders only<br>✅ Order status updated to 'cancelled'<br>✅ Success message "Pedido cancelado exitosamente"<br>✅ Cannot cancel paid/shipped orders | | Order cancellation |
TC-ET3-014 | Cancel Order - Already Paid | Logged in, Paid order | ✅ No cancel button shown for paid orders<br>✅ Error message if trying to cancel via URL<br>✅ Cannot cancel completed orders | | Business rule enforcement |
TC-ET3-015 | Order Items Accuracy | Any order | ✅ Order items match original cart items<br>✅ Quantities preserved correctly<br>✅ Prices match cart prices at order time<br>✅ Product details (name, size) preserved | | Data integrity |
TC-ET3-016 | Order Calculations | Any order | ✅ Subtotal calculated correctly (items × quantities)<br>✅ Shipping cost calculated if applicable<br>✅ Total matches subtotal + shipping<br>✅ Currency formatting correct (ARS) | | Financial accuracy |
TC-ET3-017 | Email Notifications | Order status changes | ✅ User receives email for order creation<br>✅ User receives email for payment confirmation<br>✅ User receives email for shipping status<br>✅ User receives email for delivery | | Communication system |
TC-ET3-018 | Order Search/Filter | Orders list page | ✅ Search by order ID works<br>✅ Filter by status works<br>✅ Filter by date range works<br>✅ Results update dynamically | | User experience features |
TC-ET3-019 | Order Pagination | Many orders | ✅ Shows 10 orders per page<br>✅ Pagination controls work correctly<br>✅ Page numbers accurate<br>✅ Maintains search/filters across pages | | Performance/usability |
TC-ET3-020 | Payment Methods Display | Order details | ✅ Shows payment type (card, cash, etc.)<br>✅ Shows payment gateway (Mercado Pago)<br>✅ Shows last 4 digits of card number<br>✅ Shows transaction ID | | Payment information display |
TC-ET3-021 | Order PDF Download | Order details | ✅ Download button appears for completed orders<br>✅ PDF contains order information<br>✅ PDF is properly formatted<br>✅ File downloads correctly | | Document generation |
TC-ET3-022 | Mobile Responsive | Order pages | ✅ Orders list works on mobile<br>✅ Order details view works on mobile<br>✅ Checkout mobile-friendly<br>✅ All buttons/touch targets appropriate size | | Mobile optimization |
TC-ET3-023 | Error Handling | Invalid requests | ✅ 404 page for non-existent orders<br>✅ Graceful error messages<br>✅ Error pages maintain navigation<br>✅ No sensitive data leaked in errors | | Robust error handling |
TC-ET3-024 | Performance | Large order database | ✅ Orders list loads quickly (<2 seconds)<br>✅ Search results are instant<br>✅ Database queries optimized with proper indexes<br>✅ No N+1 queries | | Performance optimization |

Database Schema Requirements

Tables to Create for ETAPA 3:

Table: orders
```sql
CREATE TABLE orders (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL FOREIGN KEY (users.id),
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    phone_number VARCHAR(20),
    mercado_pago_preference_id VARCHAR(255) NULL,
    mercado_pago_payment_id VARCHAR(255) NULL,
    payment_method VARCHAR(50),
    payment_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_orders (user_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);
```

Table: order_items
```sql
CREATE TABLE order_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT NOT NULL FOREIGN KEY (orders.id) ON DELETE CASCADE,
    product_id BIGINT NOT NULL FOREIGN KEY (products.id),
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    size INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order_items (order_id),
    INDEX idx_product (product_id)
);
```

Table: payments
```sql
CREATE TABLE payments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT NOT NULL FOREIGN KEY (orders.id),
    mercado_pago_preference_id VARCHAR(255) NULL,
    mercado_pago_payment_id VARCHAR(255) NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_payments (order_id),
    INDEX idx_status (status)
);
```

API Endpoints to Implement:

POST /api/orders - Create order from cart
GET /api/orders - List user orders
GET /api/orders/{id} - Get order details
PUT /api/orders/{id}/cancel - Cancel order
POST /api/payment/create - Create Mercado Pago preference
POST /api/payment/webhook - Receive Mercado Pago notifications

Environment Variables:
MERCADO_PAGO_ACCESS_TOKEN=your_access_token_here
MERCADO_PAGO_MODE=test # test=sandbox, production=live

Success Criteria for ETAPA 3

Core Functionality (Must Pass):
- Order creation from cart works correctly
- Mercado Pago integration functional
- Payment webhook processing works
- Order history and details display
- Order status management works

User Experience (Should Pass):
- Mobile responsive design
- Proper error handling
- Email notifications
- Search and pagination
- Performance acceptable

Security & Data Integrity (Must Pass):
- Users can only access their own orders
- Payment webhook validation
- Proper foreign key constraints
- SQL injection protection
- Data validation on all inputs

Overall ETAPA 3 Success: At least 18/24 test cases pass with all core functionality working.

Priority Test Cases (Must Pass):

1. TC-ET3-001: Create Order from Cart (Core functionality)
2. TC-ET3-008: Mercado Pago Integration (Payment gateway) 
3. TC-ET3-009: Payment Success Flow (Critical path)
4. TC-ET3-011: Webhook Security (Essential for MP)
5. TC-ET3-015: Order Items Accuracy (Data integrity)

These 5 test cases are critical for ETAPA 3 success.