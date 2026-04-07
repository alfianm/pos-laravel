# Critical Flow Regression Checklist

This checklist should be performed manually or verified via automation before every major release to ensure core business functions are intact.

## 1. POS & Sales
- [ ] Product search and add to cart (Desktop & Mobile).
- [ ] Category filtering.
- [ ] Customer selection and loyalty points display.
- [ ] Voucher application and discount calculation.
- [ ] Points redemption and boundary checks.
- [ ] Checkout process (Cash, Transfer, QRIS).
- [ ] Receipt generation and thermal print format.
- [ ] Cash register opening and closing with summary.

## 2. Inventory & Purchasing
- [ ] Stock adjustment recording.
- [ ] Purchase order creation and receiving (manual stock update).
- [ ] Cross-branch stock transfer (if applicable).
- [ ] Low stock alert generation.

## 3. CRM & Leads
- [ ] Lead creation (manual and potential API).
- [ ] Follow-up recording (Notes, Next date).
- [ ] Lead to Customer conversion.
- [ ] Activity timeline verification after sale.

## 4. Multi-tenancy
- [ ] Data isolation: User A from Tenant 1 cannot see Tenant 2 data.
- [ ] Branch isolation: User at Branch X sees only Branch X stock/sales by default.

## 5. Marketplace Sync
- [ ] Product mapping integrity.
- [ ] Forced stock sync from POS to Marketplace.
- [ ] Order import from Marketplace creating internal Sales (if implemented).
