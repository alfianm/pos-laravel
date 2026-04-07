# PRD — POS Multi Cabang + CRM + Omnichannel Marketplace Indonesia

Version: 1.0  
Status: Active Draft  
Owner: Product / Engineering  
Primary Stack Target: Laravel + Livewire + PostgreSQL + Redis  
Document Purpose: Single source of truth untuk kebutuhan produk, prioritas, scope, dan tahap implementasi

---

# 1. Ringkasan Produk

POS Multi Cabang + CRM + Omnichannel Marketplace Indonesia adalah aplikasi web terpusat untuk bisnis retail, grosir, F&B, dan usaha multi outlet yang membutuhkan:

- transaksi kasir
- inventory per cabang
- purchasing
- transfer stok
- customer management
- CRM follow-up
- loyalty
- reporting lintas cabang
- integrasi marketplace Indonesia

Produk ini dirancang sebagai business operating system yang menghubungkan operasional offline dan online dalam satu sistem.

---

# 2. Masalah yang Diselesaikan

Banyak bisnis multi cabang mengalami masalah berikut:

- stok antar cabang tidak sinkron
- laporan cabang tidak konsisten
- transaksi kasir terpisah dari customer management
- owner sulit memantau performa seluruh cabang
- order marketplace masuk dari banyak channel tanpa pusat kontrol
- customer data tidak dipakai untuk retensi dan follow-up
- promosi sulit ditargetkan karena histori pembelian tersebar

Produk ini menyelesaikan masalah tersebut dengan:
- central inventory
- central reporting
- branch-aware transactions
- integrated CRM
- omnichannel foundation

---

# 3. Visi Produk

Membangun platform operasional terpusat yang memungkinkan bisnis multi cabang mengelola toko fisik, customer relationship, dan order marketplace dari satu sistem yang konsisten, aman, dan scalable.

---

# 4. Tujuan Produk

## 4.1 Tujuan bisnis
- Menyatukan operasional multi cabang dalam satu platform
- Mengurangi selisih stok dan kesalahan transaksi
- Mempermudah owner mengambil keputusan berbasis data
- Meningkatkan repeat purchase dan retensi customer
- Menjadi fondasi omnichannel commerce untuk pasar Indonesia

## 4.2 Tujuan pengguna
- Kasir dapat transaksi cepat dan minim error
- Manager dapat mengelola stok dan operasional cabang
- Owner dapat melihat performa seluruh cabang
- Tim CRM dapat follow-up customer berdasarkan histori nyata
- Tim operasional online dapat mengelola order marketplace terpusat

---

# 5. Target Pengguna

## 5.1 Primary users
- Owner
- Director / business operator
- Branch manager
- Cashier
- Inventory staff
- Purchasing staff
- CRM staff
- Omnichannel admin

## 5.2 Secondary users
- Finance staff
- Customer / member
- Marketplace ops staff

---

# 6. Use Case Utama

- Menjalankan transaksi penjualan di outlet
- Mengelola stok per cabang
- Membeli barang dari supplier
- Memindahkan stok antar cabang
- Memonitor penjualan lintas cabang
- Mengelola customer dan lead
- Menjalankan loyalty dan voucher
- Menarik order marketplace ke sistem pusat
- Menyinkronkan stok internal ke marketplace

---

# 7. Scope Produk

## 7.1 In scope
- Multi tenant
- Multi branch
- User management
- Role & permission
- Customer management
- Supplier management
- Product management
- Inventory management
- Purchasing
- POS transaction
- Payment recording
- Cash register
- Expense management
- CRM
- Loyalty
- Reporting
- Omnichannel marketplace foundation
- Audit log
- Notification dasar

## 7.2 Out of scope fase awal
- Full accounting / general ledger
- Payroll / HR
- Mobile native app
- AI demand forecasting
- Franchise royalty engine
- Customer support inbox omnichannel
- Marketplace ads optimization
- Smart repricing engine

---

# 8. Prinsip Produk

- Bangun bertahap, bukan sekaligus
- POS dan inventory harus stabil sebelum CRM lanjutan
- CRM harus terhubung ke transaksi, bukan berdiri sendiri
- Omnichannel harus dibangun setelah inventory stabil
- Sistem internal adalah source of truth untuk stok
- Semua modul harus tenant-aware
- Semua modul operasional harus branch-aware
- Semua aksi penting harus dapat diaudit

---

# 9. Requirement Fungsional Global

## 9.1 Multi tenant
- Sistem mendukung banyak tenant/business
- Data tiap tenant terisolasi
- Tenant memiliki konfigurasi sendiri:
  - nama bisnis
  - logo
  - currency
  - timezone
  - tax settings
  - receipt settings

## 9.2 Multi branch
- Satu tenant dapat memiliki banyak branch
- Tiap branch memiliki:
  - nama
  - kode
  - alamat
  - status
  - optional gudang utama
- User dapat dibatasi hanya ke branch tertentu
- Laporan dapat difilter per branch atau agregat

## 9.3 User dan permission
- Role minimum:
  - super admin
  - owner
  - branch manager
  - cashier
  - inventory staff
  - purchasing staff
  - CRM staff
  - omnichannel staff
- Permission granular:
  - view
  - create
  - update
  - delete
  - export
  - approve
  - refund
  - sync marketplace

## 9.4 Product management
- Categories
- Brands
- Units
- Products
- Product variants
- SKU
- Barcode
- Purchase price
- Selling price
- Branch pricing pada fase lanjutan

## 9.5 Customer management
- Customer profile
- Customer groups
- Contact info
- Notes
- Last purchase date
- Total spent
- Membership status

## 9.6 Supplier management
- Supplier profile
- Contact person
- Payment terms
- Purchase history

## 9.7 Inventory
- Inventory per branch
- Stock movement history
- Opening stock
- Stock adjustment
- Low stock indicator
- Reorder threshold
- Stock transfer

## 9.8 Purchasing
- Purchase orders
- Purchase items
- Receiving
- Purchase payment dasar
- Purchase history
- Outstanding payable dasar

## 9.9 POS
- Product search
- Barcode input
- Add to cart
- Walk-in customer
- Registered customer
- Discount
- Tax
- Note
- Checkout
- Receipt
- Split payment optional
- Hold sale optional
- Return dasar optional bertahap

## 9.10 Cash register
- Open shift
- Close shift
- Cash in
- Cash out
- Shift summary
- Variance tracking

## 9.11 Expense
- Expense category
- Expense per branch
- Attachment optional
- Expense reporting

## 9.12 CRM
- Lead source
- Lead stage
- Leads
- Follow-up one-time
- Follow-up recurring
- Customer timeline
- Lead to customer conversion
- Proposal
- Customer portal

## 9.13 Loyalty
- Membership tier
- Points accrual
- Points redemption
- Voucher
- Promotion rules dasar

## 9.14 Omnichannel
- Marketplace account connection
- Marketplace shop connection
- Product mapping
- Order sync
- Stock sync
- Sync log
- Error handling and retry queue
- Minimal dimulai dari satu marketplace

## 9.15 Reporting
- Sales report
- Purchase report
- Inventory report
- Expense report
- Branch performance report
- Customer report
- CRM conversion report
- Marketplace sync report
- **9.16 Webhook & Event System (Phase 12)**:
  - **Outgoing Webhooks**: Konfigurasi per tenant (URL, Secret, Events) dengan HMAC-SHA256 signature. Mendukung retries otomatis via background background job.
  - **Event-Driven hooks**: Trigger otomatis untuk transaksi POS, siklus hidup produk (CRUD), peringatan stok rendah, dan alarm kuota.
  - **Inbound Stock Sync**: API khusus untuk pembaruan stok massal dari marketplace (absolute/relative) dengan validasi signature ketat.
  - **Audit & Logs**: Pencatat pengiriman (outbound) dan audit trail (inbound) untuk rekonsiliasi data eksternal.

---

# 10. Requirement Non-Fungsional

## 10.1 Performance
- POS checkout harus cepat
- Query report berat tidak boleh mengganggu transaksi
- Sync marketplace harus async jika berat

## 10.2 Security
- Auth aman
- Permissions wajib
- Audit log wajib untuk aksi penting
- Token marketplace disimpan aman
- Sensitive credentials terenkripsi

## 10.3 Scalability
- Cocok untuk puluhan branch
- Cocok untuk banyak user per tenant
- Bisa menambah marketplace connector bertahap

## 10.4 Maintainability
- Modular monolith
- Clear domain boundaries
- Business logic tidak dicampur dengan UI
- Mudah dijalankan oleh AI coding agent

## 10.5 Reliability
- Queue retries
- Failure logging
- Sync logs
- Background jobs untuk pekerjaan berat

---

# 11. Tahapan Produk

# Tahap 1 — Foundation

## Tujuan
Membuat pondasi aplikasi yang aman dan siap dikembangkan.

## Scope
- Auth
- Tenants
- Branches
- Users
- Roles
- Permissions
- Dashboard basic
- Audit log foundation

## Outcome
- user bisa login
- tenant tersedia
- branch tersedia
- role dan permission aktif
- project structure siap

## Acceptance Criteria
- user dapat login/logout
- satu tenant tidak bisa mengakses data tenant lain
- branch manager hanya dapat melihat branch terkait
- role owner dan cashier memiliki akses berbeda

---

# Tahap 2 — Master Data

## Tujuan
Menyiapkan seluruh data inti sebelum transaksi.

## Scope
- Customer groups
- Customers
- Suppliers
- Brands
- Units
- Categories
- Products
- Product variants

## Outcome
- master data siap dipakai transaksi
- produk dapat dicari
- customer dan supplier siap digunakan

## Acceptance Criteria
- produk memiliki SKU
- customer dapat dibuat
- supplier dapat dibuat
- produk varian dapat didukung

---

# Tahap 3 — Inventory Foundation

## Tujuan
Menyimpan stok per branch dan mutasi stok secara benar.

## Scope
- Inventories
- Stock movements
- Opening stock
- Stock adjustment basic

## Outcome
- stok per branch tersimpan
- histori pergerakan stok ada
- stok dapat disesuaikan secara manual

## Acceptance Criteria
- opening stock berhasil menambah inventory
- adjustment menghasilkan stock movement
- inventory list per branch tersedia

---

# Tahap 4 — Purchasing

## Tujuan
Mengelola pembelian barang dari supplier dan receiving.

## Scope
- Purchase orders
- Purchase order items
- Purchase payments basic
- Receiving

## Outcome
- barang dapat dipesan dan diterima
- receiving menambah stok

## Acceptance Criteria
- PO bisa dibuat
- receiving bisa dilakukan
- stok berubah saat receiving
- histori pembelian tersimpan

---

# Tahap 5 — POS MVP

## Tujuan
Membuat sistem kasir usable end-to-end.

## Scope
- Cash register sessions
- Sales
- Sale items
- Sale payments
- Receipt basic
- Expense basic

## Outcome
- kasir dapat bertransaksi
- stok berkurang saat penjualan
- pembayaran tercatat

## Acceptance Criteria
- sale complete mengurangi stok
- payment tercatat ke sale
- shift kasir dapat dibuka dan ditutup
- receipt dapat ditampilkan/cetak

---

# Tahap 6 — Multi Branch Advanced

## Tujuan
Menguatkan operasional multi branch.

## Scope
- Stock transfers
- Transfer approval
- Branch pricing
- Owner summary dashboard
- Basic export report

## Outcome
- transfer stok antar branch berjalan
- owner dapat melihat gambaran semua branch

## Acceptance Criteria
- transfer approved mengurangi stok branch asal
- receiving transfer menambah stok branch tujuan
- owner dapat melihat performa cabang

---

# Tahap 7 — CRM Core

## Tujuan
Membuat customer relationship layer berbasis data transaksi.

## Scope
- Lead sources
- Lead stages
- Leads
- Follow ups
- Customer timelines
- Convert lead to customer
- Proposals
- Customer portal basic

## Outcome
- customer bisa di-follow-up
- lead bisa dikelola
- timeline customer tersedia

## Acceptance Criteria
- lead dapat dipindah stage
- follow-up dapat dibuat dan diselesaikan
- timeline customer memuat event penting
- lead bisa dikonversi ke customer

---

# Tahap 8 — Loyalty & Campaign Foundation

## Tujuan
Mendorong repeat order dan retensi.

## Scope
- Membership tiers
- Loyalty accounts
- Loyalty transactions
- Vouchers
- Points accrual basic
- Voucher apply basic

## Outcome
- customer bisa mendapatkan poin
- voucher bisa digunakan di POS

## Acceptance Criteria
- transaksi eligible menambah poin
- voucher valid mengurangi nilai transaksi sesuai rule

---

# Tahap 9 — Omnichannel Marketplace Foundation

## Tujuan
Menghubungkan sistem internal dengan marketplace Indonesia.

## Scope
- Marketplace accounts
- Marketplace shops
- Product mapping
- Marketplace order sync
- Marketplace stock sync
- Sync logs
- Retry queue

## Outcome
- minimal satu marketplace bisa terhubung
- order marketplace bisa masuk ke sistem
- stok internal bisa didorong ke marketplace

## Acceptance Criteria
- account marketplace dapat dikoneksikan
- product internal bisa dimapping
- order marketplace tersimpan ke sistem
- sync logs merekam hasil sukses/gagal

---

# Tahap 10 — Reporting & Hardening

## Tujuan
Membuat sistem siap digunakan secara lebih serius.

## Scope
- Advanced reports
- Queue jobs
- Scheduled reports
- Index optimization
- Monitoring
- Deployment readiness

## Outcome
- report penting tersedia
- performa membaik
- release lebih aman

## Acceptance Criteria
- laporan dapat difilter
- job berat dijalankan async
- monitoring dasar tersedia
- dokumen deploy tersedia

# Tahap 11 — SaaS Foundation & Subscription

## Tujuan
Membangun infrastruktur SaaS (Software as a Service) untuk monetisasi platform.

## Scope
- Subscription plans (plans, features, pricing)
- Tenant subscriptions tracking
- Quota system (branch limit, product limit, etc)
- Billing & Invoices foundation
- Custom Domain (White-label) support foundation
- API Rate Limiting per tenant

## Outcome
- Platform siap menerima pembayaran langganan
- Pembatasan penggunaan resource per tenant aktif
- Tenant dapat menggunakan domain sendiri

## Acceptance Criteria
- Tenant tidak bisa menambah branch/produk melebihi kuota plan
- Invoice ter-generate saat subscription diperbarui
- Rate limit mencegah abuse API per tenant

# Tahap 12 — Webhooks & Integrations Foundation

## Tujuan
Menghubungkan ekosistem internal dengan dunia luar (integrasi eksternal).

## Scope
- Outbound Webhook Management
- Event subscriber pattern
- Background delivery with retries (HMAC-SHA256)
- Inbound marketplace stock sync endpoint
- Inbound audit logging

## Outcome
- Tenant dapat mengirim data real-time ke sistem luar
- Marketplace dapat mendorong pembaruan stok ke sistem pusat

## Acceptance Criteria
- Webhook terkirim dengan signature valid
- Inbound stock update mengubah stok internal dengan audit trail

# Tahap 13 — Enhanced Operations & Accounting Foundation

## Tujuan
Memperkuat operasional harian dan membangun fondasi akuntansi.

## Scope
- Returns & Refunds (Penanganan pengembalian barang/uang)
- Bulk Import System (Impor massal produk/customer)
- Barcode & Label Printing
- Chart of Accounts (COA) foundation
- Journal Entries foundation (Auto-journal sales/purchase)
- Accounts Receivable / Payable tracking basis

## Outcome
- Kasir dapat menangani return penjualan
- Tim gudang dapat mencetak barcode label
- Sistem mulai merekam entri jurnal akuntansi dasar

## Acceptance Criteria
- Return penjualan mengembalikan stok (optional) dan tercatat di timeline
- Entri jurnal otomatis terbentuk saat transaksi POS/Purchase
- Laporan piutang/hutang tersedia

# Tahap 14 — Advanced CRM & Loyalty Hardening
(Draft - detail menyusul sesuai progress task checklist)

---

# 12. Marketplace Target

Target awal integrasi omnichannel:
- Shopee
- Tokopedia
- TikTok Shop / Tokopedia Shop ecosystem bila relevan
- Lazada (opsional setelah connector awal stabil)

Catatan:
- Implementasi dimulai dari satu marketplace terlebih dahulu
- Stok internal tetap menjadi source of truth
- Product mapping wajib berbasis SKU internal

---

# 13. KPI Produk

## Operasional
- waktu checkout kasir
- akurasi stok
- jumlah selisih stok
- jumlah transaksi harian
- nilai penjualan per branch

## CRM
- jumlah follow-up selesai
- lead conversion rate
- repeat purchase rate
- active customer rate

## Omnichannel
- jumlah shop connected
- sync success rate
- sync failure rate
- order import success rate
- stock sync latency

---

# 14. Risiko Utama

- inventory logic salah akan merusak seluruh sistem
- CRM tidak berguna jika tidak terhubung ke transaksi
- omnichannel terlalu cepat dibangun dapat merusak akurasi stok
- permission yang lemah akan berbahaya untuk multi branch
- report berat dapat mengganggu performa transaksi

---

# 15. Keputusan Produk yang Wajib Dipertahankan

- Source of truth stok adalah sistem internal
- CRM dibangun setelah sales stabil
- Omnichannel dibangun setelah inventory stabil
- Satu task implementasi harus kecil dan dapat direview
- Semua modul harus dapat dijalankan bertahap