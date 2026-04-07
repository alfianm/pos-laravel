# Task Checklist
Status: Execution Checklist  
Reference: docs/prd.md + docs/implementation-plan.md

---

# Phase 1 — Foundation Setup

## 1.1 Project bootstrap
- [X] initialize Laravel project
- [X] setup environment variables
- [X] setup PostgreSQL connection
- [X] setup Redis connection
- [X] setup queue driver
- [X] setup scheduler readiness
- [X] setup base admin layout
- [X] setup global navigation shell

## 1.2 Auth
- [X] install auth starter setup
- [X] login page ready
- [X] logout flow ready
- [X] password reset ready
- [X] auth middleware verified


## 1.3 Tenants
- [X] create tenants migration
- [X] create Tenant model
- [X] add tenant factory
- [X] add tenant seeder
- [X] create tenant CRUD basic
- [X] add tenant-aware scope helper

## 1.4 Branches
- [X] create branches migration
- [X] create Branch model
- [X] create branch seeder
- [X] create branch CRUD basic
- [X] add branch code uniqueness per tenant
- [X] add branch status field

## 1.5 Users
- [X] update users migration for tenant relation
- [X] optional branch_id support for active branch
- [X] create branch_user pivot migration
- [X] create user-branch assignment flow
- [X] build users CRUD basic

## 1.6 Roles and permissions
- [X] install permission package
- [X] publish permission config
- [X] run permission migrations
- [X] create role seeder
- [X] create permission seeder
- [X] assign permissions to roles
- [X] enforce permissions on menu
- [X] enforce permissions on actions

## 1.7 Audit log foundation
- [X] create audit_logs migration
- [X] create AuditLog model
- [X] create audit logging service
- [X] log auth events basic
- [X] log CRUD events basic


## 1.8 Dashboard shell
- [X] dashboard home page
- [X] tenant summary widget
- [X] branch summary widget
- [X] user summary widget

## 1.9 Point of Sale (POS) Foundation
- [X] POS screen shell (Clean UI)
- [X] POS navigation setup
- [X] Cart logic integration

## 1.10 Documentation
- [X] update progress-log after batch
- [X] update assumptions if needed

---

# Phase 2 — Master Data Core

## 2.1 Customer groups
- [X] create customer_groups migration
- [X] create CustomerGroup model
- [X] customer group CRUD

## 2.2 Customers
- [X] create customers migration
- [X] create Customer model
- [X] define customer code generator
- [X] customer CRUD list
- [X] customer create page
- [X] customer edit page
- [X] customer detail page
- [X] customer filters
- [X] customer status field
- [X] customer notes support

## 2.3 Suppliers
- [X] create suppliers migration
- [X] create Supplier model
- [X] supplier code generator
- [X] supplier CRUD list
- [X] supplier create page
- [X] supplier edit page
- [X] supplier detail page

## 2.4 Brands
- [X] create brands migration
- [X] create Brand model
- [X] brand CRUD

## 2.5 Units
- [X] create units migration
- [X] create Unit model
- [X] unit CRUD

## 2.6 Categories
- [X] create product_categories migration
- [X] create ProductCategory model
- [X] support parent category
- [X] category CRUD

## 2.7 Products
- [X] create products migration
- [X] create Product model
- [X] setup product relationships
- [X] product code generator
- [X] product SKU generator
- [X] product CRUD list
- [X] product create page
- [X] product edit page
- [X] product detail page
- [X] product search support
- [X] product barcode field
- [X] product image support optional

## 2.8 Product variants
- [X] create product_variants migration
- [X] create ProductVariant model
- [X] variant create flow
- [X] variant edit flow
- [X] variant default selection support

## 2.9 Testing
- [X] customer CRUD feature tests
- [X] supplier CRUD feature tests
- [X] product CRUD feature tests

---

# Phase 3 — Inventory Core

## 3.1 Inventory tables
- [X] create inventories migration
- [X] create Inventory model
- [X] unique inventory per branch and sku mapping

## 3.2 Stock movements
- [X] create stock_movements migration
- [X] create StockMovement model
- [X] creative movement types constants
- [X] create stock movement recorder service

## 3.3 Opening stock
- [X] build opening stock form
- [X] build opening stock action
- [X] opening stock creates stock movement
- [X] inventory qty updates after opening stock

## 3.4 Stock adjustment
- [X] create stock_adjustments migration
- [X] create stock_adjustment_items migration
- [X] create StockAdjustment model
- [X] create StockAdjustmentItem model
- [X] build adjustment create flow
- [X] build adjustment finalize flow
- [X] create movement entries after adjustment

## 3.5 Inventory pages
- [X] inventory list page
- [X] inventory detail page
- [X] stock movement history page
- [X] low stock basic indicator

## 3.6 Testing
- [X] opening stock tests
- [X] stock adjustment tests
- [X] stock movement recording tests

---

# Phase 4 — Purchasing Core

## 4.1 Purchase tables
- [X] create purchase_orders migration
- [X] create purchase_order_items migration
- [X] create purchase_payments migration
- [X] create PurchaseOrder model
- [X] create PurchaseOrderItem model
- [X] create PurchasePayment model

## 4.2 Purchase flow
- [X] purchase number generator
- [X] create purchase order page
- [X] edit purchase order page
- [X] submit purchase order flow
- [X] approve purchase order flow optional
- [X] purchase order detail page

## 4.3 Receiving
- [X] build receiving action
- [X] build receiving UI
- [X] receiving updates received_qty
- [X] receiving increases inventory
- [X] receiving records stock movement

## 4.4 Purchase payment (Basic integrated)
- [X] add purchase payment form
- [X] save purchase payment records
- [X] calculate basic payment status

## 4.5 Purchase report basic
- [X] purchase list page
- [X] supplier purchase summary basic

## 4.6 Testing
- [X] PO create tests
- [X] receiving tests
- [X] stock update after receiving tests

---

# Phase 5 — POS MVP

## 5.1 Cash register
- [X] create cash_register_sessions migration
- [X] create CashRegisterSession model
- [X] open shift flow
- [X] close shift flow
- [X] cash in flow
- [X] cash out flow
- [X] shift summary page

## 5.2 Sales tables
- [X] create sales migration
- [X] create sale_items migration
- [X] create sale_payments migration
- [X] create Sale model
- [X] create SaleItem model
- [X] create SalePayment model

## 5.3 POS interface
- [X] POS screen shell
- [X] product search on POS
- [X] add item to cart
- [X] update qty in cart
- [X] remove item from cart
- [X] attach customer to sale
- [X] support walk-in customer
- [X] discount basic (voucher + points redemption)
- [X] tax basic
- [X] transaction notes

## 5.4 Complete sale flow
- [X] sale number generator
- [X] complete sale action
- [X] validate stock before finalize
- [X] deduct inventory after sale
- [X] create stock movement after sale
- [X] create sale payment records
- [X] update customer purchase stats

## 5.5 Receipt
- [X] receipt blade/page
- [X] receipt print stylesheet
- [X] receipt accessible after sale

## 5.6 Expense basic
- [X] create expense_categories migration
- [X] create expenses migration
- [X] create ExpenseCategory model
- [X] create Expense model
- [X] expense CRUD basic
- [X] expense list page

## 5.7 Testing
- [X] POS complete sale tests
- [X] stock deduction tests
- [X] payment recording tests
- [X] cash register open close tests

---

# Phase 6 — Multi Branch Advanced

## 6.1 Stock transfer tables
- [X] create stock_transfers migration
- [X] create stock_transfer_items migration
- [X] create StockTransfer model
- [X] create StockTransferItem model

## 6.2 Transfer flow
- [X] transfer number generator
- [X] create transfer request page
- [X] approve transfer flow
- [X] send transfer flow
- [X] receive transfer flow
- [X] deduct source stock on send/approve by chosen rule
- [X] add destination stock on receive
- [X] create source stock movement
- [X] create destination stock movement

## 6.3 Branch pricing
- [X] create branch_prices migration
- [X] create BranchPrice model
- [X] branch pricing form
- [X] branch-specific price retrieval

## 6.4 Owner dashboard
- [X] total sales by branch widget
- [X] top branch widget
- [X] low stock summary widget
- [X] branch performance basic page

## 6.5 Export basic
- [X] export sales report basic
- [X] export inventory report basic

## 6.6 Testing
- [X] transfer flow tests
- [X] branch pricing tests

---

# Phase 7 — CRM Core

## 7.1 CRM tables
- [X] create lead_sources migration
- [X] create lead_stages migration
- [X] create leads migration
- [X] create follow_ups migration
- [X] create customer_timelines migration
- [X] create proposals migration
- [X] create add_portal_fields_to_customers migration

## 7.2 Lead sources and stages
- [X] lead source CRUD (Auto-seeded)
- [X] lead stage CRUD (Auto-seeded)
- [X] stage ordering support

## 7.3 Leads
- [X] create Lead model
- [X] lead code generator
- [X] lead CRUD list
- [X] lead create page
- [X] lead edit page
- [X] lead detail page
- [X] lead owner support
- [X] lead branch support

## 7.4 Follow-ups
- [X] create FollowUp model
- [X] follow-up create flow (Integrated in Lead Show)
- [X] follow-up list component
- [X] follow-up complete action for scheduled ones
- [X] recurring follow-up support basic
- [X] reminder-ready fields

## 7.5 Customer timeline
- [X] create CustomerTimeline model
- [X] append sale events to timeline
- [X] append follow-up events to timeline (Leads)
- [X] customer timeline page (In Lead Detail)

## 7.6 Lead conversion
- [X] convert lead to customer action
- [X] prevent duplicate customer logic (Implicit via status)
- [X] conversion event logging

## 7.7 Proposal
- [X] create Proposal model
- [X] create ProposalItem model
- [X] proposal CRUD basic
- [X] proposal status support (Draft defaults)
- [X] total calculations auto

## 7.8 Customer portal basic
- [X] customer portal login shell (via custom guard)
- [X] customer dashboard shell
- [X] customer orders page basic
- [X] customer profile page basic

## 7.9 Testing
- [X] lead CRUD tests
- [X] follow-up tests
- [X] lead conversion tests
- [X] timeline append tests

---

# Phase 8 — Loyalty Foundation

## 8.1 Loyalty tables
- [X] create membership_tiers migration
- [X] create loyalty_accounts migration
- [X] create loyalty_transactions migration
- [X] create vouchers migration

## 8.2 Membership tiers
- [X] membership tier CRUD

## 8.3 Loyalty accounts
- [X] create loyalty account per customer flow
- [X] points balance support

## 8.4 Points accrual
- [X] define points earning rule basic
- [X] points added after eligible sale
- [X] record loyalty transaction

## 8.5 Voucher basic
- [X] voucher CRUD
- [X] voucher validation basic
- [X] voucher apply in POS

## 8.6 Testing
- [X] points accrual tests
- [X] voucher apply tests

---

# Phase 9 — Omnichannel Marketplace Foundation

## 9.1 Marketplace core tables
- [X] create marketplace_accounts migration
- [X] create marketplace_shops migration
- [X] create marketplace_product_maps migration
- [X] create marketplace_orders migration
- [X] create marketplace_order_items migration
- [X] create marketplace_sync_logs migration

## 9.2 Marketplace models
- [X] create MarketplaceAccount model
- [X] create MarketplaceShop model
- [X] create MarketplaceProductMap model
- [X] create MarketplaceOrder model
- [X] create MarketplaceOrderItem model
- [X] create MarketplaceSyncLog model

## 9.3 Connection flow
- [X] marketplace account connect UI shell
- [X] save credentials/token securely
- [X] shop retrieval flow basic
- [X] shop select/assign flow

## 9.4 Product mapping
- [X] product mapping page
- [X] map internal product to marketplace listing
- [X] map variants by SKU
- [X] save mapping records

## 9.5 Order sync
- [X] create order import service
- [X] normalize marketplace order shape
- [X] save imported order records
- [X] duplicate import prevention basic

## 9.6 Stock sync
- [X] create stock sync service
- [X] push internal stock to mapped listing
- [X] log sync result
- [X] queue-ready stock sync job

## 9.7 Sync logs
- [X] sync logs page
- [X] filter by marketplace/shop/status

## 9.8 Testing
- [X] product mapping tests
- [X] order import tests
- [X] stock sync log tests

---

# Phase 10 — Reporting & Hardening

## 10.1 Reporting
- [X] sales by branch report
- [X] sales by cashier report
- [X] inventory movement report
- [X] purchase summary report
- [X] customer spending report
- [X] CRM conversion report
- [X] marketplace sync report

## 10.2 Queue & scheduler
- [X] configure queue jobs for heavy tasks
- [X] scheduled report job basic
- [X] scheduled sync retry basic

## 10.3 Performance
- [X] add database indexes
- [X] optimize hot queries
- [X] review pagination across modules
- [X] optimize dashboard summary queries

## 10.4 Hardening
- [X] permission review across modules
- [X] audit log review
- [X] failure handling review
- [X] validation review

## 10.5 Deployment readiness
- [X] production env doc
- [X] queue worker doc
- [X] scheduler setup doc
- [X] backup checklist
- [X] release checklist

## 10.6 Testing
- [X] comprehensive test suite (160+ tests across all modules)
- [X] Master Data tests (ProductManagementTest)
- [X] Inventory tests (StockManagementTest)
- [X] Purchasing tests (PurchaseOrderTest)
- [X] POS tests (POSTest)
- [X] CRM tests (LeadManagementTest)
- [X] Membership tests (LoyaltyTest)
- [X] Expenses tests (ExpenseTest)
- [X] Marketplace tests (MarketplaceTest)
- [X] critical flow regression checklist
- [X] smoke test list

---

# Phase 11 — SaaS Foundation & Subscription

## 11.1 Subscription Plans Core ✅
- [X] create subscription_plans migration
- [X] create tenant_subscriptions migration
- [X] create payment_methods migration
- [X] create payments migration
- [X] create SubscriptionPlan model with JSON features
- [X] create TenantSubscription model
- [X] create PaymentMethod model (manual + gateway ready)
- [X] create Payment model for manual payments
- [X] define subscription status constants
- [X] define plan codes constants
- [X] seed default plans (Free, Starter 299rb, Pro 799rb, Enterprise)
- [X] create subscription plan policy
- [X] add subscription plan CRUD admin UI
- [X] add navigation menu in sidebar (Administration section)
- [X] add permissions (manage subscription plans, view tenant subscriptions)

## 11.2 Quota System Core ✅
- [X] create tenant_quotas migration
- [X] create TenantQuota model
- [X] define quota types constants
- [X] create QuotaService for checking limits
- [X] create CheckQuota middleware
- [X] implement quota enforcement on branch creation
- [X] implement quota enforcement on product creation
- [X] implement quota enforcement on user creation
- [X] create quota widget for dashboard
- [X] create quota alerts when approaching limit

## 11.3 Billing & Invoices ✅
- [X] create invoices migration
- [X] create invoice_items migration
- [X] create Invoice model with state machine
- [X] create InvoiceItem model
- [X] define invoice status constants
- [X] create InvoiceService for generation
- [X] create manual invoice generation flow
- [X] create invoice list page
- [X] create invoice detail page with PDF export
- [X] create invoice payment recording flow
- [X] send invoice notification email

## 11.4 Payment Gateway Foundation (Xendit Ready) ✅
- [X] create payment_gateway_configs migration
- [X] create PaymentGatewayConfig model
- [X] define PaymentGatewayInterface
- [X] create XenditService skeleton (ready but disabled)
- [X] create webhook receiver controller for Xendit
- [X] add payment gateway enable/disable toggle

## 11.5 Custom Domain (White-label)
- [X] create tenant_domains migration
- [X] create TenantDomain model with SSL fields
- [X] create ResolveTenantByDomain middleware
- [X] add domain validation logic (Partial)
- [X] create tenant domain CRUD in admin
- [X] implement domain-to-tenant resolution
- [X] add SSL certificate tracking fields (Migration & Model)
- [X] create custom domain setup instructions page

## 11.6 Rate Limiting & API Control
- [X] configure Redis-based rate limiter
- [X] create tenant-based rate limit key
- [X] apply rate limit to API routes
- [X] create rate limit exception handler
- [X] add rate limit status to tenant dashboard

## 11.7 Testing
- [ ] subscription plan CRUD tests
- [ ] quota enforcement tests
- [ ] invoice generation tests
- [X] domain resolution tests
- [X] domain CRUD tests
- [X] rate limit tests

---

# Phase 12 — Webhooks & Integrations

## 12.1 Webhook System Core
- [X] create webhooks migration
- [X] create webhook_deliveries migration
- [X] create Webhook model with event filtering
- [X] create WebhookDelivery model
- [X] define webhook event types constants
- [X] create WebhookService for dispatching
- [X] create WebhookDelivery job
- [X] implement retry mechanism with exponential backoff
- [X] create webhook signature validation
- [X] create webhook CRUD admin UI
- [X] create webhook delivery log page

## 12.2 Event System
- [X] document all available webhook events
- [X] create event dispatcher for tenant lifecycle events
- [X] create event dispatcher for subscription events
- [X] create event dispatcher for invoice events
- [X] create event dispatcher for quota threshold events

## 12.3 Stock Sync Webhook
- [X] create webhook endpoint for marketplace stock updates
- [X] implement stock sync validation
- [X] create stock sync conflict resolution
- [X] add stock sync audit logging

## 12.4 Testing
- [X] webhook delivery tests
- [X] webhook retry mechanism tests
- [X] signature validation tests

---

# Phase 13 — Enhanced Operations & Accounting Foundation

## 13.1 Returns & Refunds
- [X] create return_reasons migration
- [X] create returns migration
- [X] create return_items migration
- [X] create ReturnReason model
- [X] create Return model
- [X] create ReturnItem model
- [X] define return status constants
- [X] create ReturnService for processing
- [X] implement return flow with inventory reversal
- [X] create return list page
- [X] create return form page
- [X] create refund recording flow

## 13.2 Bulk Import System
- [X] create import_batches migration
- [X] create ImportBatch model
- [X] create ProductImportService
- [X] create CustomerImportService
- [X] create bulk product import UI
- [X] create import progress tracking
- [X] create import error report summary

## 13.3 Barcode & Label Printing
- [X] create barcode_label_templates migration
- [X] create BarcodeLabelTemplate model
- [X] create barcode generation service
- [X] create product label print page
- [X] create shelf label print page
- [X] add support for thermal printer format

## 13.4 Chart of Accounts Foundation
- [X] create account_categories migration
- [X] create chart_of_accounts migration
- [X] create AccountCategory model
- [X] create ChartOfAccount model
- [X] define account types constants
- [X] seed default COA for retail business
- [X] create COA CRUD admin UI

## 13.5 Journal Entries Foundation
- [X] create journal_entries migration
- [X] create journal_entry_lines migration
- [X] create JournalEntry model
- [X] create JournalEntryLine model
- [X] define journal entry source constants
- [X] create JournalEntryService
- [X] implement auto-journal for sales
- [X] implement auto-journal for purchases
- [X] create journal entry list page
- [X] create journal entry detail page

## 13.6 Accounts Receivable / Payable Tracking
- [X] create ar_ap_records migration
- [X] create ArApRecord model
- [X] define record types constants
- [X] create ArApService for balance calculation
- [X] create customer AR summary page
- [X] create supplier AP summary page
- [X] create AR aging report
- [X] create AP aging report

## 13.7 Testing & Finalization (IN PROGRESS)
- [ ] return processing tests
- [ ] bulk import tests
- [X] journal entry generation tests (Sales & Purchases)
- [ ] AR/AP calculation tests

## 13.8 Financial Reporting Foundation
- [X] implement Trial Balance logic in TrialBalanceService
- [X] create TrialBalanceReport Livewire component
- [X] add Trial Balance route in web.php
- [X] create Income Statement (Laba Rugi) logic & UI
- [X] create Balance Sheet (Neraca) logic & UI
- [X] create Cash Flow report basic

# Phase 14 — Advanced CRM & Loyalty Hardening ✅

## 14.1 Membership & Points Hardening
- [X] create loyalty configuration file
- [X] implement points expiration with FIFO logic
- [X] implement points transfer between customers
- [X] create Loyalty/ExpirePoints scheduled command
- [X] create Loyalty/BirthdayRewards scheduled command
- [X] add birthday & anniversary fields to customers
- [X] refactor POS to use centralized LoyaltyService

## 14.2 Tier Automation
- [X] implement Membership Tier Engine multiplier logic
- [X] create Loyalty/CheckTiers scheduled command
- [X] add membership_tier_id filter to vouchers

## 14.3 Customer Intelligence (RFM Analysis)
- [X] add RFM score & segment fields to customers
- [X] create CustomerSegmentationService (quantiles)
- [X] create CRM/CalculateRFM scheduled command
- [X] create RFM segment visualization in CRM dashboard

## 14.4 Advanced Voucher & Campaign
- [X] create VoucherService with code generator
- [X] create Campaign Management UI
- [X] implement targeted promos by RFM segment
- [X] implement promo rules engine (min qty, bundle)

## 14.5 Customer 360° View
- [X] create Customer Detail (360°) page
- [X] implement interaction timeline with auto-events
- [X] show RFM health & Loyalty status in 360° view

