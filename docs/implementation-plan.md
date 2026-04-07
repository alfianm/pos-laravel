# Implementation Plan

Status: Ready for Execution  
Primary Reference: docs/prd.md  
Execution Mode: AI Agent + Human Review  
Stack: Laravel + Livewire + PostgreSQL + Redis

---

# 1. Tujuan

Dokumen ini menerjemahkan PRD ke dalam urutan implementasi teknis yang bisa langsung dikerjakan oleh AI coding agent atau tim engineering.

---

# 2. Prinsip Eksekusi

- Kerjakan phase-by-phase
- Jangan implement seluruh PRD sekaligus
- Satu batch kerja hanya satu modul kecil
- Setiap batch harus bisa direview
- Selalu update checklist dan progress log
- Jangan pindah ke fase berikutnya tanpa stabilisasi fase aktif

---

# 3. Aturan Kerja Agent

## Agent wajib:
- membaca `docs/prd.md`
- membaca `docs/task-checklist.md`
- memilih task prioritas tertinggi yang belum selesai
- menjelaskan rencana singkat sebelum coding
- mengimplementasikan satu batch kecil
- berhenti setelah satu batch
- melaporkan file yang diubah
- mengupdate progress log

## Agent dilarang:
- mengerjakan semua fase sekaligus
- menambah fitur di luar PRD
- refactor besar tanpa diminta
- mengubah file unrelated
- membuat asumsi diam-diam tanpa menulis di assumptions.md

---

# 4. Definisi Batch Kerja

Satu batch kerja idealnya hanya salah satu dari berikut:
- satu migration + model relation
- satu CRUD module
- satu service flow
- satu dashboard page
- satu sync module kecil
- satu seeder/permission setup

Contoh batch yang baik:
- create tenants and branches migrations
- setup roles and permissions seeder
- build products CRUD
- build inventory movement service
- build purchase receiving action
- build sale complete action

Contoh batch yang terlalu besar:
- build complete POS system
- build all CRM
- build all omnichannel integrations

---

# 5. Execution Phases

# Phase 1 — Foundation Setup

## Objective
Membuat pondasi project Laravel dan security/access layer.

## Modules
- Auth
- Tenants
- Branches
- Users
- Roles & Permissions
- Audit Log Foundation
- Dashboard Basic

## Deliverables
- auth ready
- multi tenant basic ready
- branch assignment basic ready
- roles and permissions ready
- dashboard shell ready

## Dependencies
- none

## Completion Criteria
- user login berjalan
- tenant data terisolasi
- branch data tersedia
- permission dasar berjalan

---

# Phase 2 — Master Data Core

## Objective
Membuat seluruh data master untuk transaksi.

## Modules
- Customer Groups
- Customers
- Suppliers
- Brands
- Units
- Categories
- Products
- Product Variants

## Deliverables
- CRUD customer
- CRUD supplier
- CRUD category/brand/unit
- CRUD product and variants

## Dependencies
- Phase 1 selesai

## Completion Criteria
- product searchable
- customer available for sale
- supplier available for purchase

---

# Phase 3 — Inventory Core

## Objective
Menyimpan stok per branch dan mencatat mutasi stok.

## Modules
- Inventories
- Stock Movements
- Opening Stock
- Stock Adjustment Basic

## Deliverables
- inventory table and relations
- stock movement recorder
- opening stock flow
- stock adjustment flow

## Dependencies
- products tersedia

## Completion Criteria
- stok per branch terlihat
- movement tercatat
- adjustment mengubah stok dengan benar

---

# Phase 4 — Purchasing Core

## Objective
Membangun alur pembelian dan penerimaan barang.

## Modules
- Purchase Orders
- Purchase Order Items
- Purchase Payments Basic
- Receiving Flow

## Deliverables
- purchase CRUD
- receiving action
- purchase report basic

## Dependencies
- suppliers and products tersedia
- inventory core tersedia

## Completion Criteria
- PO dapat dibuat
- receiving menambah stok
- histori pembelian tersedia

---

# Phase 5 — POS MVP

## Objective
Membangun alur transaksi kasir end-to-end.

## Modules
- Cash Register Sessions
- Sales
- Sale Items
- Sale Payments
- Receipt Basic
- Expense Basic

## Deliverables
- POS screen basic
- complete sale flow
- payment recording
- receipt display/print
- shift open/close

## Dependencies
- products, inventory, customers tersedia

## Completion Criteria
- sale mengurangi stok
- payment tercatat
- receipt tersedia
- shift cash register berjalan

---

# Phase 6 — Multi Branch Advanced

## Objective
Menyelesaikan kebutuhan inti multi branch.

## Modules
- Stock Transfers
- Transfer Approvals
- Branch Pricing
- Owner Summary Dashboard
- Export Basic

## Deliverables
- transfer flow
- approval flow
- branch pricing flow
- owner dashboard basic

## Dependencies
- inventory core and POS stabil

## Completion Criteria
- transfer antar branch sukses
- stok asal turun, tujuan naik
- owner dapat melihat ringkasan branch

---

# Phase 7 — CRM Core

## Objective
Membangun layer hubungan pelanggan berbasis data transaksi.

## Modules
- Lead Sources
- Lead Stages
- Leads
- Follow Ups
- Customer Timelines
- Convert Lead to Customer
- Proposals
- Customer Portal Basic

## Deliverables
- lead management
- follow-up module
- customer timeline page
- conversion flow
- customer portal shell

## Dependencies
- customers and sales stabil

## Completion Criteria
- lead bisa dibuat
- follow-up bisa dijadwalkan
- timeline customer aktif
- lead bisa dikonversi

---

# Phase 8 — Loyalty Foundation

## Objective
Membangun retensi customer dasar.

## Modules
- Membership Tiers
- Loyalty Accounts
- Loyalty Transactions
- Vouchers
- Points Accrual Basic
- Voucher Apply Basic

## Deliverables
- points system basic
- voucher application basic
- loyalty statement basic

## Dependencies
- sales stabil
- customer data stabil

## Completion Criteria
- transaksi eligible menambah poin
- voucher valid bisa diaplikasikan

---

# Phase 9 — Omnichannel Marketplace Foundation

## Objective
Membangun connector marketplace awal.

## Modules
- Marketplace Accounts
- Marketplace Shops
- Marketplace Product Maps
- Marketplace Orders
- Marketplace Order Items
- Marketplace Sync Logs
- Stock Sync Flow
- Order Sync Flow

## Deliverables
- one marketplace connector ready
- product mapping flow
- order import flow
- stock sync flow
- sync logs page basic

## Dependencies
- products, inventory, sales stabil
- queue system siap

## Completion Criteria
- minimal satu shop terhubung
- order marketplace masuk
- stock sync berjalan
- error sync tercatat

---

# Phase 11 — SaaS Foundation & Subscription

## Objective
Membangun infrastruktur SaaS untuk multi-tenant monetization.

## Modules
- Subscription Plans
- Tenant Quotas & Enforcement
- Billing & Invoices
- Payment Gateway Foundation (Xendit)
- Custom Domain (White-label)
- API Rate Limiting

## Deliverables
- subscription plans ready
- quota enforcement middleware
- invoice PDF & notification
- xendit webhook handler
- custom domain resolution

## Dependencies
- Phase 1-10 stabil

## Completion Criteria
- tenant terbatasi oleh kuota plan
- invoice ter-generate otomatis
- domain custom mengarah ke tenant yang benar

---

# Phase 12 — Webhooks & Integrations Foundation

## Objective
Membangun sistem interopabilitas real-time.

## Modules
- Outbound Webhook Management
- Event Subscriber System
- HMAC Signature Verification
- Inbound Stock Sync Endpoint
- Inbound Audit Logs

## Deliverables
- webhook admin UI
- background jobs for delivery
- delivery retry mechanism
- API for inbound stock updates

## Dependencies
- Phase 11 stabil

## Completion Criteria
- webhook berhasil terkirim ke URL eksternal
- pembaruan stok via API merubah inventory internal

---

# Phase 13 — Enhanced Operations & Accounting Foundation

## Objective
Memperkuat operasional dan menyentuh fondasi akuntansi.

## Modules
- Returns & Refunds
- Bulk Import System (CSV/Excel)
- Barcode Label Generation
- Chart of Accounts (COA)
- Journal Entry Engine (Double Entry)
- AR/AP Tracking

## Deliverables
- return module
- data importer
- barcode printing UI
- journal entry list
- AR/AP summary

## Dependencies
- Inventory and Sales stabil

## Completion Criteria
- return penjualan terekam dan mempengaruhi stok/payment
- impor data berhasil validasi
- entri jurnal terbentuk otomatis dari transaksi harian

---

# 6. Order of Execution Inside Each Phase

Untuk setiap phase, agent harus mengikuti urutan berikut:

1. migrations
2. models and relationships
3. enums/constants
4. policies and permissions
5. services/actions
6. UI pages/components
7. tests
8. documentation updates

---

# 7. Review Gate per Batch

Sebelum lanjut ke batch berikutnya, revi