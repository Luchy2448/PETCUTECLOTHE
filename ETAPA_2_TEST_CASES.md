# Test Cases - PET CUTE CLOTHES ETAPA 2

## 📋 **Test Matrix - Cart Functionality**

| Test ID | Feature | User State | Expected Result | Status ✅/❌ | Notes |
|---------|----------|------------|----------------|----------------|-------|
| **TC-001** | Home Page - Product Cards | Not logged in | ✅ Only "Ver Detalles" button visible<br>❌ No "Añadir al Carrito" button |  | Users should see product info but not cart options |
| **TC-002** | Home Page - Product Cards | Logged in | ✅ "Añadir al Carrito" button visible<br>✅ "Ver Detalles" button visible<br>✅ Stock badge shows correctly |  | Both buttons should be present when authenticated |
| **TC-003** | Home Page - Add to Cart | Logged in, Product in stock | ✅ Button shows "🔄 Agregando..." temporarily<br>✅ Redirects to cart page<br>✅ Shows success message "¡Producto agregado al carrito exitosamente!"<br>✅ Product appears in cart |  | Full add-to-cart flow should work |
| **TC-004** | Home Page - Out of Stock | Logged in, Product out of stock | ✅ Button shows "📦 Producto Agotado" (disabled)<br>❌ No add to cart possible |  | Should prevent adding out-of-stock items |
| **TC-005** | Product Detail Page | Not logged in | ✅ Only "← Volver a Productos" button<br>❌ No "Añadir al Carrito" button |  | Should behave like home page for guests |
| **TC-006** | Product Detail Page | Logged in, Product in stock | ✅ "Añadir al Carrito" button enabled<br>✅ Stock shows "Disponible (X unidades)" |  | Should allow adding from product page |
| **TC-007** | Product Detail Page - Add to Cart | Logged in | ✅ Button works like home page<br>✅ Redirects to cart with success message |  | Same functionality as home page |
| **TC-008** | Product Detail Page - Out of Stock | Logged in, Product out of stock | ✅ Button shows "📦 Producto Agotado" (disabled) |  | Should match home page behavior |
| **TC-009** | Navigation - Cart Icon | Not logged in | ✅ Cart icon visible in navbar<br>❌ Cart badge hidden<br>❌ Cart link redirects to login |  | Should handle unauthenticated access |
| **TC-010** | Navigation - Cart Icon | Logged in | ✅ Cart icon visible<br>✅ Cart badge shows item count<br>✅ Badge hidden when cart empty<br>✅ Clicking goes to /cart |  | Proper cart navigation and counter |
| **TC-011** | Cart Page - Empty | Logged in, No items | ✅ Shows "Tu carrito está vacío" message<br>✅ Shows "🛒 Ir a Comprar" button<br>✅ Links to products page |  | Proper empty state handling |
| **TC-012** | Cart Page - With Items | Logged in, Has items | ✅ Shows product list correctly<br>✅ Displays item images, names, prices<br>✅ Shows quantities and subtotals<br>✅ Shows total correctly<br>✅ Item count matches navbar badge |  | Complete cart display |
| **TC-013** | Cart Page - Update Quantity | Logged in, Has items | ✅ + and - buttons work<br>✅ Manual quantity input works<br>✅ Validates 1-10 range<br>✅ Shows success message on update<br>✅ Total updates automatically |  | Quantity management |
| **TC-014** | Cart Page - Remove Item | Logged in, Has items | ✅ Delete button (🗑️) works<br>✅ Shows confirmation modal<br>✅ Item removed from list<br>✅ Total updates<br>✅ Success message shown |  | Item removal functionality |
| **TC-015** | Cart Page - Clear Cart | Logged in, Has items | ✅ "Vaciar Carrito" button works<br>✅ All items removed<br>✅ Shows empty state<br>✅ Success message shown |  | Full cart clearing |
| **TC-016** | Cart Page - Checkout | Logged in, Has items | ✅ "Proceed to Checkout" button visible<br>✅ Clicking goes to /checkout<br>✅ Shows "Checkout Próximamente" page<br>✅ Can return to cart or products |  | ETAPA 2 checkout placeholder |
| **TC-017** | Authentication - Login Redirect | Not logged in, attempts cart access | ✅ Redirects to /login<br>✅ After login, should work normally |  | Protect cart routes properly |
| **TC-018** | Authentication - Session Management | Logged in user | ✅ Cart persists across page refresh<br>✅ Cart items belong to correct user<br>✅ Logout clears cart access |  | Proper session handling |

---

## 🔧 **Test Data Setup**

### Users for Testing:
```
User 1: test@example.com / password123
User 2: user2@example.com / password123  
```

### Products for Testing:
```
Product A (ID: 1): Suéter con corazones - Stock: 10 - Price: $15.000
Product B (ID: 2): Camisa elegante - Stock: 0 - Price: $12.000  
Product C (ID: 3): Vestido casual - Stock: 5 - Price: $8.000
```

---

## 📝 **Test Execution Checklist**

### Before Testing:
- [ ] Clear browser cache and cookies
- [ ] Ensure database has test data
- [ ] Verify server is running correctly
- [ ] Check routes are properly loaded

### During Testing:
- [ ] Document any errors or unexpected behavior
- [ ] Take screenshots of UI states
- [ ] Test in multiple browsers if possible
- [ ] Verify console for JavaScript errors

### After Testing:
- [ ] Update Status column with ✅ or ❌
- [ ] Add detailed notes in Notes column
- [ ] Report any blockers or critical issues
- [ ] Suggest improvements for next iteration

---

## 🐛 **Known Issues & Limitations (ETAPA 2)**

| Issue | Description | Impact | Workaround |
|-------|-------------|---------|------------|
| Checkout | Payment not implemented | Cannot complete purchase | Shows "coming soon" page |
| Stock Validation | Real-time stock not checked | Race conditions possible | Manual stock management |
| Cart Persistence | Cart lost on browser close | User experience | Should implement session storage |
| Mobile UI | Not fully responsive | Poor mobile experience | CSS improvements needed |

---

## 📊 **Success Criteria for ETAPA 2**

A test case is **PASSED** when:
1. ✅ Expected Result matches Actual Result exactly
2. ✅ No JavaScript console errors
3. ✅ No PHP/Laravel errors in logs
4. ✅ User experience is smooth and intuitive
5. ✅ Data integrity is maintained

**Overall ETAPA 2 Success**: At least 15/18 test cases pass ✅

---

## 🎯 **Priority Test Cases (Must Pass)**

1. **TC-003**: Home page add to cart (Core functionality)
2. **TC-007**: Product page add to cart (Core functionality)  
3. **TC-010**: Cart navigation and badge (User experience)
4. **TC-012**: Cart display with items (Core feature)
5. **TC-017**: Authentication protection (Security)

These 5 test cases are critical for ETAPA 2 success.