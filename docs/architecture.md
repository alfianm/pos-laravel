# Architecture

## Arsitektur Utama
Aplikasi menggunakan pendekatan modular monolith berbasis Laravel.

---

## Stack
- Laravel 13+
- PHP 8.4
- Livewire 4
- Blade
- Tailwind CSS 
- PostgreSQL
- Redis
- Laravel Queue
- Laravel Scheduler
- Spatie Laravel Permission

---

## Prinsip Arsitektur
- tenant-aware
- branch-aware
- modular by domain
- transaction-safe untuk inventory dan payment
- audit-first
- queue untuk proses berat
- business logic tidak ditaruh penuh di UI component

---

## Domain Utama
- Auth
- Tenant
- Branch
- User
- Customer
- Supplier
- Product
- Inventory
- Purchase
- Sale
- Payment
- Expense
- CRM
- Loyalty
- Omnichannel
- Reports
- AuditLog
- Settings

---

## Struktur Folder Utama
- app/Domain
- app/Actions
- app/Data
- app/Enums
- app/Events
- app/Jobs
- app/Livewire
- app/Models
- app/Policies
- app/Queries
- app/Services
- app/Support

---

## Flow Penting

### Flow Stok
- purchase receiving → tambah stok
- sale complete → kurangi stok
- return → koreksi stok
- transfer branch → stok asal turun, stok tujuan naik
- adjustment → stok menyesuaikan alasan

### Flow CRM
- sale complete → update customer stats
- sale complete → append customer timeline
- follow-up complete → append customer timeline
- lead converted → create/update customer

### Flow Omnichannel
- internal product ↔ marketplace product mapping
- marketplace order → normalized order
- internal stock update → queue sync marketplace
- sync result → marketplace_sync_logs

---

## Batasan Arsitektur
- Jangan membuat microservices di fase awal
- Jangan membuat omnichannel sebelum inventory stabil
- Jangan membuat advanced reports sebelum transaksi inti stabil