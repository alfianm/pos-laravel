# Smoke Test List

Smoke tests are high-level sanity checks to ensure the application is basically functional on a fresh environment.

## 1. Authentication & Access
- [ ] Login with multiple tenants (UUID verification).
- [ ] Login with Super Admin credentials.
- [ ] Logout and session invalidation.
- [ ] Password reset flow.

## 2. Dashboard Rendering
- [ ] Main dashboard loads within 3s.
- [ ] Summary widgets (Sales Today, Orders Today) show meaningful numbers (or zero).
- [ ] Low stock alerts sidebar shows correct count.
- [ ] Latest activity feed shows recent actions.

## 3. Core Page Connectivity
- [ ] Product List: search for a product.
- [ ] Customer List: view a customer profile.
- [ ] Sales History: see list of recent sales.
- [ ] Inventory/Branches: verify branch settings.

## 4. Basic Interaction
- [ ] Create a new dummy customer.
- [ ] Create a new dummy product.
- [ ] Open a POS session and add 1 item to cart. (No checkout needed for smoke test).

## 5. API/External Status
- [ ] Check `/up` endpoint.
- [ ] Verify Marketplace settings page accessibility.
- [ ] Verify CRM/Leads detail page can load.
