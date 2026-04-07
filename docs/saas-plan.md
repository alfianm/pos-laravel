# Phase 11 — SaaS Foundation & Subscription

## Objective
Transform the system into a multi-tenant SaaS with subscription management, quotas, and billing.

## 11.1 Subscription Plans Core
- [ ] Create subscription_plans migration
- [ ] Create tenant_subscriptions migration
- [ ] Create payment_methods migration
- [ ] Create payments migration
- [ ] Create SubscriptionPlan model with JSON features column
- [ ] Create TenantSubscription model with status transitions
- [ ] Create PaymentMethod model (manual + gateway support)
- [ ] Create Payment model for manual payments
- [ ] Define subscription status constants (active, expired, grace_period, cancelled)
- [ ] Define plan codes constants (free, starter, pro, enterprise)
- [ ] Seed default plans (Free, Starter 299rb, Pro 799rb, Enterprise)
- [ ] Create subscription plan policy
- [ ] Add subscription plan CRUD admin UI
- [ ] Add permissions (manage subscription plans, view tenant subscriptions)

## 11.2 Quota System Core
- [ ] Create tenant_quotas migration
- [ ] Create TenantQuota model
- [ ] Define quota types constants (branches, products, users, transactions, storage)
- [ ] Create QuotaService for checking limits
- [ ] Create CheckQuota middleware
- [ ] Implement quota enforcement on branch creation
- [ ] Implement quota enforcement on product creation
- [ ] Implement quota enforcement on user creation
- [ ] Create quota widget for dashboard (usage % per resource)
- [ ] Create quota alerts when approaching limit

## 11.3 Billing & Invoices
- [ ] Create invoices migration
- [ ] Create invoice_items migration
- [ ] Create Invoice model with state machine
- [ ] Create InvoiceItem model
- [ ] Define invoice status constants (draft, sent, paid, overdue, cancelled)
- [ ] Create InvoiceService for generation
- [ ] Create manual invoice generation flow
- [ ] Create invoice list page
- [ ] Create invoice detail page with PDF export
- [ ] Create invoice payment recording flow
- [ ] Send invoice notification email

## 11.4 Payment Gateway Foundation (Xendit Ready)
- [ ] Create payment_gateway_configs migration
- [ ] Create PaymentGatewayConfig model (Xendit, Midtrans ready)
- [ ] Define PaymentGatewayInterface
- [ ] Create XenditService skeleton (ready but disabled)
- [ ] Create webhook receiver controller for Xendit
- [ ] Add payment gateway enable/disable toggle

## 11.5 Custom Domain (White-label)
- [ ] Create tenant_domains migration
- [ ] Create TenantDomain model with SSL fields
- [ ] Create ResolveTenantByDomain middleware
- [ ] Add domain validation logic
- [ ] Create tenant domain CRUD in admin
- [ ] Implement domain-to-tenant resolution
- [ ] Add SSL certificate tracking fields
- [ ] Create custom domain setup instructions page

## 11.6 Rate Limiting & API Control
- [ ] Configure Redis-based rate limiter
- [ ] Create tenant-based rate limit key
- [ ] Apply rate limit to API routes
- [ ] Create rate limit exception handler
- [ ] Add rate limit status to tenant dashboard

## 11.7 Testing
- [ ] Subscription plan CRUD tests
- [ ] Quota enforcement tests
- [ ] Invoice generation tests
- [ ] Domain resolution tests

---

# Phase 12 — Webhooks & Integrations

## Objective
Enable event-driven integrations with external systems.

## 12.1 Webhook System Core
- [ ] Create webhooks migration
- [ ] Create webhook_deliveries migration
- [ ] Create Webhook model with event filtering
- [ ] Create WebhookDelivery model for attempt tracking
- [ ] Define webhook event types constants
- [ ] Create WebhookService for dispatching
- [ ] Create WebhookDelivery job
- [ ] Implement retry mechanism with exponential backoff
- [ ] Create webhook signature validation
- [ ] Create webhook CRUD admin UI
- [ ] Create webhook delivery log page

## 12.2 Event System
- [ ] Document all available webhook events
- [ ] Create event dispatcher for tenant lifecycle events
- [ ] Create event dispatcher for subscription events
- [ ] Create event dispatcher for invoice events
- [ ] Create event dispatcher for quota threshold events

## 12.3 Stock Sync Webhook (Marketplace → Internal)
- [ ] Create webhook endpoint for marketplace stock updates
- [ ] Implement stock sync validation
- [ ] Create stock sync conflict resolution
- [ ] Add stock sync audit logging

## 12.4 Testing
- [ ] Webhook delivery tests
- [ ] Webhook retry mechanism tests
- [ ] Signature validation tests

---

# Phase 13 — Enhanced Operations & Accounting Foundation

## Objective
Add operational enhancements and accounting foundation.

## 13.1 Returns & Refunds
- [ ] Create return_reasons migration
- [ ] Create returns migration
- [ ] Create return_items migration
- [ ] Create ReturnReason model
- [ ] Create Return model (sales returns)
- [ ] Create ReturnItem model
- [ ] Define return status constants
- [ ] Create ReturnService for processing
- [ ] Implement return flow with inventory reversal
- [ ] Create return list page
- [ ] Create return form page
- [ ] Create refund recording flow

## 13.2 Bulk Import System
- [ ] Create import_batches migration
- [ ] Create import_errors migration
- [ ] Create ImportBatch model
- [ ] Create ImportError model for tracking failures
- [ ] Create ProductImportService
- [ ] Create CustomerImportService
- [ ] Create bulk product import UI
- [ ] Create import progress tracking
- [ ] Create import error report download

## 13.3 Barcode & Label Printing
- [ ] Create barcode_label_templates migration
- [ ] Create BarcodeLabelTemplate model
- [ ] Create barcode generation service (Code128, EAN13)
- [ ] Create product label print page
- [ ] Create shelf label print page
- [ ] Add support for thermal printer format

## 13.4 Chart of Accounts Foundation
- [ ] Create account_categories migration
- [ ] Create chart_of_accounts migration
- [ ] Create AccountCategory model
- [ ] Create ChartOfAccount model
- [ ] Define account types constants (asset, liability, equity, revenue, expense)
- [ ] Seed default COA for retail business
- [ ] Create COA CRUD admin UI

## 13.5 Journal Entries Foundation
- [ ] Create journal_entries migration
- [ ] Create journal_entry_lines migration
- [ ] Create JournalEntry model
- [ ] Create JournalEntryLine model
- [ ] Define journal entry source constants (manual, sales, purchase, adjustment)
- [ ] Create JournalEntryService
- [ ] Implement auto-journal for sales
- [ ] Implement auto-journal for purchases
- [ ] Create journal entry list page
- [ ] Create journal entry detail page

## 13.6 Accounts Receivable / Payable Tracking
- [ ] Create ar_ap_records migration
- [ ] Create ArApRecord model (AR/AP tracking)
- [ ] Define record types constants (receivable, payable)
- [ ] Create ArApService for balance calculation
- [ ] Create customer AR summary page
- [ ] Create supplier AP summary page
- [ ] Create AR aging report
- [ ] Create AP aging report

## 13.7 Testing
- [ ] Return processing tests
- [ ] Bulk import tests
- [ ] Journal entry generation tests
- [ ] AR/AP calculation tests

---

## Summary

**Phase 11 (8 modules):** Subscription, Quota, Billing, Payment Gateway, Custom Domain, Rate Limiting + Tests
**Phase 12 (4 modules):** Webhook System, Event System, Stock Sync Webhook + Tests  
**Phase 13 (7 modules):** Returns, Bulk Import, Barcode Printing, COA, Journal Entries, AR/AP + Tests

**Total: 35+ new tasks**

**Recommended Start: Phase 11.1 — Subscription Plans Core**
