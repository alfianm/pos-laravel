# Database Schema — POS Multi Cabang + CRM + Omnichannel Marketplace Indonesia

Version: 1.0  
Status: Full Draft  
Primary Stack: Laravel + PostgreSQL  
Purpose: Menjadi acuan utama untuk migrasi database, relasi model, dan implementasi domain

---

# 1. Prinsip Umum Schema

## 1.1 Prinsip desain
- Semua tabel bisnis utama menggunakan UUID sebagai primary key
- Semua tabel inti memiliki `tenant_id` kecuali tabel global framework/auth tertentu
- Semua tabel operasional memakai `branch_id` bila relevan
- Semua nilai uang menggunakan `decimal`, bukan `float`
- Semua quantity menggunakan `decimal`
- Semua tabel penting memiliki `created_at`, `updated_at`
- Gunakan `deleted_at` untuk soft deletes pada data master dan data transaksi yang perlu histori
- Semua aksi penting harus dapat di-audit
- Stok internal adalah source of truth
- Omnichannel sinkronisasi dilakukan berdasarkan mapping SKU/product internal

## 1.2 Naming convention
- table names: snake_case plural
- columns: snake_case
- PK: `id`
- FK: `{table_singular}_id`
- timestamps: `created_at`, `updated_at`, `deleted_at`

## 1.3 Money precision
Gunakan:
- `decimal(18,2)` untuk harga, subtotal, total, payment
- `decimal(18,4)` untuk qty, stock, avg_cost, unit_cost

## 1.4 JSON fields
Gunakan `jsonb` untuk PostgreSQL pada data yang:
- semi-structured
- berasal dari response marketplace
- metadata fleksibel
- payload audit/sync

---

# 2. Global / Identity / Access

# 2.1 tenants

## Purpose
Menyimpan business/tenant utama.

## Columns
- id uuid pk
- code varchar(50) unique
- name varchar(150)
- slug varchar(180) unique
- email varchar(150) nullable
- phone varchar(50) nullable
- logo_url varchar(255) nullable
- currency varchar(10) default 'IDR'
- timezone varchar(80) default 'Asia/Jakarta'
- tax_number varchar(100) nullable
- address text nullable
- city varchar(100) nullable
- province varchar(100) nullable
- postal_code varchar(20) nullable
- status varchar(30) default 'active'
- settings jsonb nullable
- created_at timestamp
- updated_at timestamp
- deleted_at timestamp nullable

## Indexes
- unique(code)
- unique(slug)
- index(status)

---

# 2.2 branches

## Purpose
Menyimpan cabang/outlet/gudang dalam tenant.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- code varchar(50)
- name varchar(150)
- type varchar(30) default 'store'
- email varchar(150) nullable
- phone varchar(50) nullable
- address text nullable
- city varchar(100) nullable
- province varchar(100) nullable
- postal_code varchar(20) nullable
- is_main boolean default false
- status varchar(30) default 'active'
- settings jsonb nullable
- created_at timestamp
- updated_at timestamp
- deleted_at timestamp nullable

## Constraints
- unique(tenant_id, code)

## Indexes
- index(tenant_id)
- index(status)
- index(is_main)

---

# 2.3 users

## Purpose
Menyimpan user aplikasi.

## Columns
- id uuid pk
- tenant_id uuid fk nullable -> tenants.id
- active_branch_id uuid fk nullable -> branches.id
- name varchar(150)
- email varchar(150) unique
- phone varchar(50) nullable
- avatar_url varchar(255) nullable
- email_verified_at timestamp nullable
- password varchar(255)
- is_super_admin boolean default false
- status varchar(30) default 'active'
- last_login_at timestamp nullable
- last_login_ip varchar(64) nullable
- preferences jsonb nullable
- remember_token varchar(100) nullable
- created_at timestamp
- updated_at timestamp
- deleted_at timestamp nullable

## Indexes
- unique(email)
- index(tenant_id)
- index(active_branch_id)
- index(status)
- index(is_super_admin)

---

# 2.4 branch_user

## Purpose
Relasi many-to-many user ke branch.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- user_id uuid fk -> users.id
- is_default boolean default false
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(branch_id, user_id)

## Indexes
- index(tenant_id)
- index(user_id)
- index(branch_id)

---

# 2.5 permission tables

Gunakan package permission database-driven.

## Tables
- roles
- permissions
- model_has_roles
- model_has_permissions
- role_has_permissions

## Catatan
Role minimum:
- super_admin
- owner
- branch_manager
- cashier
- inventory_staff
- purchasing_staff
- crm_staff
- omnichannel_staff

---

# 2.6 sessions / personal_access_tokens
Gunakan sesuai kebutuhan auth Laravel.

---

# 3. Settings / System

# 3.1 settings

## Purpose
Menyimpan konfigurasi tenant atau branch.

## Columns
- id uuid pk
- tenant_id uuid fk nullable -> tenants.id
- branch_id uuid fk nullable -> branches.id
- group varchar(100)
- key varchar(150)
- value jsonb nullable
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(tenant_id, branch_id, group, key)

## Indexes
- index(tenant_id)
- index(branch_id)
- index(group)

---

# 3.2 audit_logs

## Purpose
Menyimpan jejak aksi penting sistem.

## Columns
- id uuid pk
- tenant_id uuid fk nullable -> tenants.id
- branch_id uuid fk nullable -> branches.id
- user_id uuid fk nullable -> users.id
- event varchar(100)
- auditable_type varchar(150) nullable
- auditable_id uuid nullable
- old_values jsonb nullable
- new_values jsonb nullable
- meta jsonb nullable
- ip_address varchar(64) nullable
- user_agent text nullable
- created_at timestamp
- updated_at timestamp

## Indexes
- index(tenant_id)
- index(branch_id)
- index(user_id)
- index(event)
- index(auditable_type, auditable_id)

---

# 3.3 notifications_log

## Purpose
Mencatat notifikasi internal/scheduled messages.

## Columns
- id uuid pk
- tenant_id uuid fk nullable -> tenants.id
- branch_id uuid fk nullable -> branches.id
- user_id uuid fk nullable -> users.id
- type varchar(100)
- channel varchar(50)
- title varchar(255)
- message text nullable
- payload jsonb nullable
- status varchar(30) default 'pending'
- sent_at timestamp nullable
- created_at timestamp
- updated_at timestamp

## Indexes
- index(tenant_id)
- index(branch_id)
- index(user_id)
- index(type)
- index(status)

---

# 4. Master Data

# 4.1 customer_groups

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- name varchar(100)
- description text nullable
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(tenant_id, name)

---

# 4.2 customers

## Purpose
Menyimpan customer utama.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk nullable -> branches.id
- customer_group_id uuid fk nullable -> customer_groups.id
- code varchar(50)
- name varchar(150)
- email varchar(150) nullable
- phone varchar(50) nullable
- gender varchar(20) nullable
- birth_date date nullable
- address text nullable
- city varchar(100) nullable
- province varchar(100) nullable
- postal_code varchar(20) nullable
- tax_number varchar(100) nullable
- notes text nullable
- total_spent decimal(18,2) default 0
- total_orders integer default 0
- last_purchase_at timestamp nullable
- status varchar(30) default 'active'
- source varchar(50) nullable
- meta jsonb nullable
- created_at timestamp
- updated_at timestamp
- deleted_at timestamp nullable

## Constraints
- unique(tenant_id, code)

## Indexes
- index(tenant_id)
- index(branch_id)
- index(customer_group_id)
- index(email)
- index(phone)
- index(status)
- index(last_purchase_at)

---

# 4.3 suppliers

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- code varchar(50)
- name varchar(150)
- email varchar(150) nullable
- phone varchar(50) nullable
- contact_person varchar(150) nullable
- address text nullable
- city varchar(100) nullable
- province varchar(100) nullable
- postal_code varchar(20) nullable
- payment_terms_days integer default 0
- notes text nullable
- status varchar(30) default 'active'
- meta jsonb nullable
- created_at timestamp
- updated_at timestamp
- deleted_at timestamp nullable

## Constraints
- unique(tenant_id, code)

## Indexes
- index(tenant_id)
- index(status)
- index(phone)
- index(email)

---

# 4.4 brands

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- name varchar(100)
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(tenant_id, name)

---

# 4.5 units

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- name varchar(100)
- short_name varchar(20)
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(tenant_id, name)
- unique(tenant_id, short_name)

---

# 4.6 product_categories

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- parent_id uuid fk nullable -> product_categories.id
- name varchar(100)
- slug varchar(150)
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(tenant_id, slug)

## Indexes
- index(parent_id)
- index(tenant_id)

---

# 4.7 products

## Purpose
Master product utama.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- category_id uuid fk nullable -> product_categories.id
- brand_id uuid fk nullable -> brands.id
- unit_id uuid fk nullable -> units.id
- code varchar(50)
- sku varchar(100)
- barcode varchar(100) nullable
- name varchar(200)
- type varchar(30) default 'single'
- purchase_price decimal(18,2) default 0
- selling_price decimal(18,2) default 0
- cost_price decimal(18,2) default 0
- track_stock boolean default true
- allow_decimal boolean default false
- has_expiry boolean default false
- is_active boolean default true
- description text nullable
- image_url varchar(255) nullable
- meta jsonb nullable
- created_at timestamp
- updated_at timestamp
- deleted_at timestamp nullable

## Constraints
- unique(tenant_id, code)
- unique(tenant_id, sku)

## Indexes
- index(tenant_id)
- index(category_id)
- index(brand_id)
- index(unit_id)
- index(barcode)
- index(is_active)

---

# 4.8 product_variants

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- product_id uuid fk -> products.id
- name varchar(150)
- sku varchar(100)
- barcode varchar(100) nullable
- purchase_price decimal(18,2) default 0
- selling_price decimal(18,2) default 0
- cost_price decimal(18,2) default 0
- is_default boolean default false
- is_active boolean default true
- attributes jsonb nullable
- created_at timestamp
- updated_at timestamp
- deleted_at timestamp nullable

## Constraints
- unique(tenant_id, sku)

## Indexes
- index(product_id)
- index(barcode)
- index(is_default)
- index(is_active)

---

# 4.9 branch_prices

## Purpose
Harga per branch untuk product atau variant.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- product_id uuid fk nullable -> products.id
- product_variant_id uuid fk nullable -> product_variants.id
- price decimal(18,2)
- created_at timestamp
- updated_at timestamp

## Indexes
- index(tenant_id)
- index(branch_id)
- index(product_id)
- index(product_variant_id)

---

# 5. Inventory

# 5.1 inventories

## Purpose
Menyimpan saldo stok per branch.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- product_id uuid fk -> products.id
- product_variant_id uuid fk nullable -> product_variants.id
- qty_on_hand decimal(18,4) default 0
- qty_reserved decimal(18,4) default 0
- qty_available decimal(18,4) default 0
- avg_cost decimal(18,4) default 0
- reorder_level decimal(18,4) default 0
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(branch_id, product_id, product_variant_id)

## Indexes
- index(tenant_id)
- index(branch_id)
- index(product_id)
- index(product_variant_id)
- index(qty_available)

---

# 5.2 stock_movements

## Purpose
Ledger semua mutasi stok.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- product_id uuid fk -> products.id
- product_variant_id uuid fk nullable -> product_variants.id
- reference_type varchar(100)
- reference_id uuid nullable
- movement_type varchar(50)
- qty decimal(18,4)
- before_qty decimal(18,4)
- after_qty decimal(18,4)
- unit_cost decimal(18,4) default 0
- notes text nullable
- performed_by uuid fk nullable -> users.id
- meta jsonb nullable
- created_at timestamp
- updated_at timestamp

## Movement types contoh
- opening_stock
- purchase_receiving
- sale
- sale_return
- transfer_out
- transfer_in
- adjustment_add
- adjustment_subtract
- manual_fix

## Indexes
- index(tenant_id)
- index(branch_id)
- index(product_id)
- index(product_variant_id)
- index(reference_type, reference_id)
- index(movement_type)
- index(performed_by)
- index(created_at)

---

# 5.3 stock_adjustments

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- adjustment_no varchar(50)
- reason varchar(100)
- status varchar(30) default 'draft'
- notes text nullable
- performed_by uuid fk nullable -> users.id
- approved_by uuid fk nullable -> users.id
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(tenant_id, adjustment_no)

## Indexes
- index(branch_id)
- index(status)

---

# 5.4 stock_adjustment_items

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- stock_adjustment_id uuid fk -> stock_adjustments.id
- product_id uuid fk -> products.id
- product_variant_id uuid fk nullable -> product_variants.id
- before_qty decimal(18,4)
- adjusted_qty decimal(18,4)
- after_qty decimal(18,4)
- unit_cost decimal(18,4) default 0
- notes text nullable
- created_at timestamp
- updated_at timestamp

## Indexes
- index(stock_adjustment_id)
- index(product_id)
- index(product_variant_id)

---

# 5.5 stock_transfers

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- from_branch_id uuid fk -> branches.id
- to_branch_id uuid fk -> branches.id
- transfer_no varchar(50)
- status varchar(30) default 'draft'
- requested_by uuid fk nullable -> users.id
- approved_by uuid fk nullable -> users.id
- sent_at timestamp nullable
- received_at timestamp nullable
- notes text nullable
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(tenant_id, transfer_no)

## Indexes
- index(from_branch_id)
- index(to_branch_id)
- index(status)
- index(requested_by)
- index(approved_by)

---

# 5.6 stock_transfer_items

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- stock_transfer_id uuid fk -> stock_transfers.id
- product_id uuid fk -> products.id
- product_variant_id uuid fk nullable -> product_variants.id
- qty decimal(18,4)
- received_qty decimal(18,4) default 0
- notes text nullable
- created_at timestamp
- updated_at timestamp

## Indexes
- index(stock_transfer_id)
- index(product_id)
- index(product_variant_id)

---

# 6. Purchasing

# 6.1 purchase_orders

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- supplier_id uuid fk -> suppliers.id
- purchase_no varchar(50)
- order_date date
- expected_date date nullable
- status varchar(30) default 'draft'
- subtotal decimal(18,2) default 0
- discount_amount decimal(18,2) default 0
- tax_amount decimal(18,2) default 0
- shipping_amount decimal(18,2) default 0
- grand_total decimal(18,2) default 0
- payment_status varchar(30) default 'unpaid'
- notes text nullable
- created_by uuid fk nullable -> users.id
- approved_by uuid fk nullable -> users.id
- created_at timestamp
- updated_at timestamp
- deleted_at timestamp nullable

## Constraints
- unique(tenant_id, purchase_no)

## Indexes
- index(branch_id)
- index(supplier_id)
- index(status)
- index(payment_status)
- index(order_date)

---

# 6.2 purchase_order_items

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- purchase_order_id uuid fk -> purchase_orders.id
- product_id uuid fk -> products.id
- product_variant_id uuid fk nullable -> product_variants.id
- qty decimal(18,4)
- received_qty decimal(18,4) default 0
- purchase_price decimal(18,2)
- discount_amount decimal(18,2) default 0
- tax_amount decimal(18,2) default 0
- line_total decimal(18,2)
- created_at timestamp
- updated_at timestamp

## Indexes
- index(purchase_order_id)
- index(product_id)
- index(product_variant_id)

---

# 6.3 purchase_payments

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- purchase_order_id uuid fk -> purchase_orders.id
- branch_id uuid fk -> branches.id
- payment_no varchar(50)
- payment_date timestamp
- amount decimal(18,2)
- payment_method varchar(50)
- reference_no varchar(100) nullable
- notes text nullable
- created_by uuid fk nullable -> users.id
- created_at timestamp
- updated_at timestamp

## Indexes
- index(purchase_order_id)
- index(branch_id)
- index(payment_date)
- index(payment_method)

---

# 7. POS / Sales

# 7.1 cash_register_sessions

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- user_id uuid fk -> users.id
- opening_balance decimal(18,2) default 0
- closing_balance decimal(18,2) nullable
- cash_sales_total decimal(18,2) default 0
- non_cash_sales_total decimal(18,2) default 0
- refund_total decimal(18,2) default 0
- cash_in_total decimal(18,2) default 0
- cash_out_total decimal(18,2) default 0
- expected_cash_balance decimal(18,2) default 0
- actual_cash_balance decimal(18,2) nullable
- difference_amount decimal(18,2) nullable
- opened_at timestamp
- closed_at timestamp nullable
- status varchar(30) default 'open'
- notes text nullable
- created_at timestamp
- updated_at timestamp

## Indexes
- index(branch_id)
- index(user_id)
- index(status)
- index(opened_at)
- index(closed_at)

---

# 7.2 cash_register_movements

## Purpose
Cash in/out selama shift.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- cash_register_session_id uuid fk -> cash_register_sessions.id
- type varchar(30)
- amount decimal(18,2)
- notes text nullable
- created_by uuid fk nullable -> users.id
- created_at timestamp
- updated_at timestamp

## Types
- cash_in
- cash_out

## Indexes
- index(cash_register_session_id)
- index(type)

---

# 7.3 sales

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- cash_register_session_id uuid fk nullable -> cash_register_sessions.id
- customer_id uuid fk nullable -> customers.id
- sale_no varchar(50)
- sale_date timestamp
- status varchar(30) default 'completed'
- subtotal decimal(18,2) default 0
- discount_amount decimal(18,2) default 0
- tax_amount decimal(18,2) default 0
- service_amount decimal(18,2) default 0
- rounding_amount decimal(18,2) default 0
- grand_total decimal(18,2) default 0
- paid_amount decimal(18,2) default 0
- change_amount decimal(18,2) default 0
- payment_status varchar(30) default 'paid'
- source varchar(30) default 'pos'
- notes text nullable
- performed_by uuid fk nullable -> users.id
- meta jsonb nullable
- created_at timestamp
- updated_at timestamp
- deleted_at timestamp nullable

## Constraints
- unique(tenant_id, sale_no)

## Indexes
- index(branch_id)
- index(customer_id)
- index(cash_register_session_id)
- index(status)
- index(payment_status)
- index(sale_date)

---

# 7.4 sale_items

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- sale_id uuid fk -> sales.id
- product_id uuid fk -> products.id
- product_variant_id uuid fk nullable -> product_variants.id
- product_name_snapshot varchar(200)
- sku_snapshot varchar(100) nullable
- qty decimal(18,4)
- unit_price decimal(18,2)
- discount_amount decimal(18,2) default 0
- tax_amount decimal(18,2) default 0
- cost_amount decimal(18,2) default 0
- line_total decimal(18,2)
- created_at timestamp
- updated_at timestamp

## Indexes
- index(sale_id)
- index(product_id)
- index(product_variant_id)

---

# 7.5 sale_payments

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- sale_id uuid fk -> sales.id
- branch_id uuid fk -> branches.id
- payment_no varchar(50)
- payment_date timestamp
- payment_method varchar(50)
- amount decimal(18,2)
- reference_no varchar(100) nullable
- meta jsonb nullable
- created_by uuid fk nullable -> users.id
- created_at timestamp
- updated_at timestamp

## Indexes
- index(sale_id)
- index(branch_id)
- index(payment_method)
- index(payment_date)

---

# 7.6 sale_holds

## Purpose
Menyimpan cart yang ditahan.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- customer_id uuid fk nullable -> customers.id
- hold_no varchar(50)
- cart_payload jsonb
- notes text nullable
- held_by uuid fk nullable -> users.id
- held_at timestamp
- expired_at timestamp nullable
- created_at timestamp
- updated_at timestamp

## Indexes
- index(branch_id)
- index(customer_id)
- index(held_by)

---

# 7.7 sale_returns

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- sale_id uuid fk -> sales.id
- return_no varchar(50)
- return_date timestamp
- subtotal decimal(18,2) default 0
- tax_amount decimal(18,2) default 0
- grand_total decimal(18,2) default 0
- refund_amount decimal(18,2) default 0
- status varchar(30) default 'completed'
- notes text nullable
- created_by uuid fk nullable -> users.id
- created_at timestamp
- updated_at timestamp

## Indexes
- index(branch_id)
- index(sale_id)
- index(return_date)
- index(status)

---

# 7.8 sale_return_items

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- sale_return_id uuid fk -> sale_returns.id
- sale_item_id uuid fk nullable -> sale_items.id
- product_id uuid fk -> products.id
- product_variant_id uuid fk nullable -> product_variants.id
- qty decimal(18,4)
- unit_price decimal(18,2)
- line_total decimal(18,2)
- reason varchar(100) nullable
- created_at timestamp
- updated_at timestamp

## Indexes
- index(sale_return_id)
- index(product_id)
- index(product_variant_id)

---

# 8. Expenses

# 8.1 expense_categories

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- name varchar(100)
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(tenant_id, name)

---

# 8.2 expenses

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- expense_category_id uuid fk nullable -> expense_categories.id
- expense_no varchar(50)
- expense_date timestamp
- amount decimal(18,2)
- description text nullable
- attachment_url varchar(255) nullable
- status varchar(30) default 'approved'
- created_by uuid fk nullable -> users.id
- approved_by uuid fk nullable -> users.id
- created_at timestamp
- updated_at timestamp
- deleted_at timestamp nullable

## Constraints
- unique(tenant_id, expense_no)

## Indexes
- index(branch_id)
- index(expense_category_id)
- index(status)
- index(expense_date)

---

# 9. CRM

# 9.1 lead_sources

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- name varchar(100)
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(tenant_id, name)

---

# 9.2 lead_stages

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- name varchar(100)
- sort_order integer default 0
- is_won boolean default false
- is_lost boolean default false
- created_at timestamp
- updated_at timestamp

## Indexes
- index(tenant_id)
- index(sort_order)

---

# 9.3 leads

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk nullable -> branches.id
- lead_source_id uuid fk nullable -> lead_sources.id
- lead_stage_id uuid fk nullable -> lead_stages.id
- customer_id uuid fk nullable -> customers.id
- code varchar(50)
- name varchar(150)
- email varchar(150) nullable
- phone varchar(50) nullable
- company_name varchar(150) nullable
- estimated_value decimal(18,2) default 0
- status varchar(30) default 'open'
- owner_id uuid fk nullable -> users.id
- notes text nullable
- converted_at timestamp nullable
- meta jsonb nullable
- created_at timestamp
- updated_at timestamp
- deleted_at timestamp nullable

## Constraints
- unique(tenant_id, code)

## Indexes
- index(branch_id)
- index(lead_source_id)
- index(lead_stage_id)
- index(customer_id)
- index(owner_id)
- index(status)

---

# 9.4 follow_ups

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk nullable -> branches.id
- lead_id uuid fk nullable -> leads.id
- customer_id uuid fk nullable -> customers.id
- supplier_id uuid fk nullable -> suppliers.id
- assigned_to uuid fk nullable -> users.id
- title varchar(200)
- description text nullable
- follow_up_at timestamp
- is_recurring boolean default false
- recurring_rule varchar(100) nullable
- status varchar(30) default 'pending'
- completed_at timestamp nullable
- created_by uuid fk nullable -> users.id
- created_at timestamp
- updated_at timestamp

## Indexes
- index(lead_id)
- index(customer_id)
- index(supplier_id)
- index(assigned_to)
- index(status)
- index(follow_up_at)

---

# 9.5 customer_timelines

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk nullable -> branches.id
- customer_id uuid fk -> customers.id
- event_type varchar(100)
- reference_type varchar(100) nullable
- reference_id uuid nullable
- title varchar(255)
- description text nullable
- meta jsonb nullable
- created_by uuid fk nullable -> users.id
- created_at timestamp
- updated_at timestamp

## Indexes
- index(customer_id)
- index(event_type)
- index(reference_type, reference_id)
- index(created_at)

---

# 9.6 proposals

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk nullable -> branches.id
- lead_id uuid fk nullable -> leads.id
- customer_id uuid fk nullable -> customers.id
- proposal_no varchar(50)
- title varchar(200)
- status varchar(30) default 'draft'
- amount decimal(18,2) default 0
- valid_until date nullable
- content text nullable
- sent_at timestamp nullable
- approved_at timestamp nullable
- created_by uuid fk nullable -> users.id
- created_at timestamp
- updated_at timestamp

## Indexes
- index(lead_id)
- index(customer_id)
- index(status)
- index(valid_until)

---

# 9.7 customer_portal_accounts

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- customer_id uuid fk -> customers.id
- email varchar(150)
- password varchar(255)
- last_login_at timestamp nullable
- status varchar(30) default 'active'
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(tenant_id, email)
- unique(customer_id)

## Indexes
- index(status)

---

# 10. Loyalty

# 10.1 membership_tiers

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- name varchar(100)
- min_total_spent decimal(18,2) default 0
- point_multiplier decimal(8,2) default 1
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(tenant_id, name)

---

# 10.2 loyalty_accounts

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- customer_id uuid fk -> customers.id
- membership_tier_id uuid fk nullable -> membership_tiers.id
- points_balance decimal(18,2) default 0
- lifetime_points decimal(18,2) default 0
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(customer_id)

## Indexes
- index(membership_tier_id)

---

# 10.3 loyalty_transactions

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- customer_id uuid fk -> customers.id
- sale_id uuid fk nullable -> sales.id
- type varchar(30)
- points decimal(18,2)
- description text nullable
- expires_at timestamp nullable
- created_at timestamp
- updated_at timestamp

## Types
- earn
- redeem
- expire
- manual_adjustment

## Indexes
- index(customer_id)
- index(sale_id)
- index(type)
- index(expires_at)

---

# 10.4 vouchers

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- code varchar(50)
- name varchar(150)
- type varchar(30)
- value decimal(18,2)
- min_purchase decimal(18,2) default 0
- start_at timestamp nullable
- end_at timestamp nullable
- usage_limit integer nullable
- used_count integer default 0
- status varchar(30) default 'active'
- rules jsonb nullable
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(tenant_id, code)

## Indexes
- index(status)
- index(start_at)
- index(end_at)

---

# 11. Omnichannel Marketplace

# 11.1 marketplace_accounts

## Purpose
Menyimpan koneksi account marketplace per tenant.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- provider varchar(50)
- account_name varchar(150)
- external_account_id varchar(150) nullable
- access_token text nullable
- refresh_token text nullable
- token_expires_at timestamp nullable
- status varchar(30) default 'active'
- credentials jsonb nullable
- created_by uuid fk nullable -> users.id
- created_at timestamp
- updated_at timestamp

## Providers contoh
- shopee
- tokopedia
- tiktok_shop
- lazada

## Indexes
- index(tenant_id)
- index(provider)
- index(status)
- index(external_account_id)

---

# 11.2 marketplace_shops

## Purpose
Satu account marketplace bisa punya satu atau banyak shop.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- marketplace_account_id uuid fk -> marketplace_accounts.id
- branch_id uuid fk nullable -> branches.id
- provider varchar(50)
- external_shop_id varchar(150)
- shop_name varchar(150)
- region_code varchar(50) nullable
- status varchar(30) default 'active'
- settings jsonb nullable
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(provider, external_shop_id)

## Indexes
- index(tenant_id)
- index(marketplace_account_id)
- index(branch_id)
- index(status)

---

# 11.3 marketplace_product_maps

## Purpose
Memetakan product internal ke listing marketplace.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- marketplace_shop_id uuid fk -> marketplace_shops.id
- product_id uuid fk nullable -> products.id
- product_variant_id uuid fk nullable -> product_variants.id
- provider varchar(50)
- external_product_id varchar(150)
- external_variant_id varchar(150) nullable
- external_sku varchar(150) nullable
- sync_price boolean default true
- sync_stock boolean default true
- is_active boolean default true
- meta jsonb nullable
- created_at timestamp
- updated_at timestamp

## Indexes
- index(marketplace_shop_id)
- index(product_id)
- index(product_variant_id)
- index(provider)
- index(external_product_id)
- index(external_variant_id)

---

# 11.4 marketplace_orders

## Purpose
Menyimpan order import dari marketplace.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk nullable -> branches.id
- marketplace_shop_id uuid fk -> marketplace_shops.id
- customer_id uuid fk nullable -> customers.id
- provider varchar(50)
- external_order_id varchar(150)
- external_order_no varchar(150) nullable
- status varchar(50)
- order_date timestamp nullable
- buyer_name varchar(150) nullable
- buyer_phone varchar(50) nullable
- subtotal decimal(18,2) default 0
- shipping_amount decimal(18,2) default 0
- discount_amount decimal(18,2) default 0
- grand_total decimal(18,2) default 0
- raw_payload jsonb nullable
- imported_at timestamp nullable
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(provider, external_order_id)

## Indexes
- index(tenant_id)
- index(branch_id)
- index(marketplace_shop_id)
- index(provider)
- index(status)
- index(order_date)

---

# 11.5 marketplace_order_items

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- marketplace_order_id uuid fk -> marketplace_orders.id
- marketplace_product_map_id uuid fk nullable -> marketplace_product_maps.id
- product_id uuid fk nullable -> products.id
- product_variant_id uuid fk nullable -> product_variants.id
- external_product_id varchar(150) nullable
- external_variant_id varchar(150) nullable
- external_sku varchar(150) nullable
- name_snapshot varchar(255)
- qty decimal(18,4)
- unit_price decimal(18,2)
- line_total decimal(18,2)
- raw_payload jsonb nullable
- created_at timestamp
- updated_at timestamp

## Indexes
- index(marketplace_order_id)
- index(product_id)
- index(product_variant_id)
- index(marketplace_product_map_id)

---

# 11.6 marketplace_sync_logs

## Purpose
Mencatat hasil sinkronisasi.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk nullable -> branches.id
- marketplace_shop_id uuid fk nullable -> marketplace_shops.id
- provider varchar(50)
- sync_type varchar(50)
- direction varchar(20)
- entity_type varchar(100)
- entity_id uuid nullable
- external_entity_id varchar(150) nullable
- status varchar(30)
- request_payload jsonb nullable
- response_payload jsonb nullable
- error_message text nullable
- synced_at timestamp nullable
- created_at timestamp
- updated_at timestamp

## Sync types contoh
- product_sync
- stock_sync
- order_import
- order_status_update
- price_sync

## Direction contoh
- push
- pull

## Status contoh
- pending
- success
- failed

## Indexes
- index(tenant_id)
- index(branch_id)
- index(marketplace_shop_id)
- index(provider)
- index(sync_type)
- index(status)
- index(entity_type, entity_id)
- index(synced_at)

---

# 11.7 marketplace_webhook_logs

## Purpose
Menyimpan webhook raw dari marketplace.

## Columns
- id uuid pk
- tenant_id uuid fk nullable -> tenants.id
- marketplace_shop_id uuid fk nullable -> marketplace_shops.id
- provider varchar(50)
- event_type varchar(100) nullable
- payload jsonb
- processed boolean default false
- processed_at timestamp nullable
- error_message text nullable
- created_at timestamp
- updated_at timestamp

## Indexes
- index(provider)
- index(event_type)
- index(processed)
- index(created_at)

---

# 12. Reporting / Summary Tables (Opsional Bertahap)

# 12.1 daily_branch_sales_summaries
Digunakan bila report mulai berat.

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- summary_date date
- total_sales decimal(18,2) default 0
- total_transactions integer default 0
- total_items decimal(18,4) default 0
- total_discount decimal(18,2) default 0
- total_tax decimal(18,2) default 0
- total_refunds decimal(18,2) default 0
- created_at timestamp
- updated_at timestamp

## Constraints
- unique(branch_id, summary_date)

---

# 12.2 daily_product_sales_summaries

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- branch_id uuid fk -> branches.id
- product_id uuid fk -> products.id
- product_variant_id uuid fk nullable -> product_variants.id
- summary_date date
- total_qty decimal(18,4) default 0
- total_sales decimal(18,2) default 0
- created_at timestamp
- updated_at timestamp

---

# 12.3 daily_customer_sales_summaries

## Columns
- id uuid pk
- tenant_id uuid fk -> tenants.id
- customer_id uuid fk -> customers.id
- summary_date date
- total_orders integer default 0
- total_spent decimal(18,2) default 0
- created_at timestamp
- updated_at timestamp

---

# 13. Relasi Inti Antar Tabel

## Tenant
- hasMany branches
- hasMany users
- hasMany customers
- hasMany suppliers
- hasMany products
- hasMany sales
- hasMany purchase_orders
- hasMany leads
- hasMany marketplace_accounts

## Branch
- belongsTo tenant
- belongsToMany users
- hasMany inventories
- hasMany sales
- hasMany purchase_orders
- hasMany expenses
- hasMany stock_transfers as source
- hasMany stock_transfers as destination

## Customer
- belongsTo tenant
- belongsTo branch nullable
- belongsTo customer_group nullable
- hasMany sales
- hasMany follow_ups
- hasMany customer_timelines
- hasOne loyalty_account
- hasOne customer_portal_account

## Product
- belongsTo tenant
- belongsTo category
- belongsTo brand
- belongsTo unit
- hasMany product_variants
- hasMany inventories
- hasMany stock_movements
- hasMany sale_items
- hasMany purchase_order_items

---

# 14. Index Strategy yang Wajib

Tambahkan index pada field berikut di hampir semua tabel:
- tenant_id
- branch_id
- status
- created_at

Tambahkan index khusus pada:
- sku
- barcode
- sale_date
- order_date
- follow_up_at
- external_order_id
- external_product_id
- sync_type
- payment_status
- reference_type + reference_id

---

# 15. Enum / Status yang Direkomendasikan di Level Aplikasi

## Branch status
- active
- inactive

## User status
- active
- inactive
- suspended

## Product type
- single
- variant
- service
- bundle

## Purchase status
- draft
- submitted
- approved
- partially_received
- received
- cancelled

## Sale status
- draft
- completed
- void
- refunded

## Payment status
- unpaid
- partial
- paid
- refunded

## Transfer status
- draft
- approved
- sent
- received
- cancelled

## Follow-up status
- pending
- completed
- missed
- cancelled

## Lead status
- open
- won
- lost
- archived

## Voucher status
- active
- inactive
- expired

## Sync status
- pending
- success
- failed

---

# 16. Aturan Data Integrity

- `tenant_id` wajib konsisten di seluruh relasi bisnis
- `branch_id` harus sesuai branch user aktif saat transaksi jika flow branch-restricted
- product variant tidak boleh berdiri tanpa parent product
- stock movement wajib dibuat setiap mutasi stok
- sale complete wajib berada dalam transaction DB
- receiving purchase wajib berada dalam transaction DB
- transfer stock wajib terdiri dari movement source dan destination
- order marketplace tidak langsung mengurangi inventory kecuali ada flow fulfilment yang disetujui sistem
- mapping product marketplace sebaiknya berbasis SKU internal

---

# 17. Catatan Implementasi Laravel

- Gunakan migrations bertahap sesuai phase
- Jangan membuat semua tabel sekaligus jika agent sulit fokus
- Phase implementasi schema yang direkomendasikan:
  1. tenants, branches, users, pivot, audit
  2. customers, suppliers, brands, units, categories, products, variants
  3. inventories, stock_movements, adjustments
  4. purchase tables
  5. sales, payments, cash register, expenses
  6. transfers, branch prices
  7. CRM tables
  8. loyalty tables
  9. marketplace tables
  10. summary/reporting tables

---

# 18. Minimal Viable Schema untuk Start Cepat

Jika ingin memulai cepat, tabel minimal yang dibuat dulu:
- tenants
- branches
- users
- branch_user
- customers
- suppliers
- products
- product_variants
- inventories
- stock_movements
- purchase_orders
- purchase_order_items
- sales
- sale_items
- sale_payments
- cash_register_sessions
- audit_logs

Setelah itu baru bertahap ke:
- expenses
- transfers
- CRM
- loyalty
- omnichannel