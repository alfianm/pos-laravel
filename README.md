# POS Multi Cabang + CRM + Omnichannel Marketplace Indonesia 🛍️

[![Laravel 13](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Livewire 3](https://img.shields.io/badge/Livewire-3-4e5ee4?style=for-the-badge&logo=livewire)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?style=for-the-badge&logo=postgresql)](https://www.postgresql.org)

**Sistem Operasi Bisnis (Business Operating System)** yang mengintegrasikan transaksi kasir (POS), manajemen stok multi-cabang, Customer Relationship Management (CRM), dan konektivitas Omnichannel ke marketplace Indonesia (Shopee, Tokopedia, dll) dalam satu platform terpusat.

---

## ✨ Fitur Utama

-   🏬 **Multi-Tenant & Multi-Branch**: Isolasi data antar bisnis dan manajemen banyak cabang/outlet dalam satu akun.
-   🛒 **Modern POS**: Interface kasir cepat berbasis Livewire dengan dukungan barcode, diskon, dan cetak struk PDF/Thermal.
-   📦 **Inventory Management**: Stok real-time per cabang, mutasi stok antar cabang, dan histori pergerakan barang.
-   🤝 **CRM & Leads**: Kelola data pelanggan, follow-up prospek, loyalty program (poin/voucher), dan membership tier.
-   🛍️ **Omnichannel Foundation**: Pemetaan SKU internal ke marketplace Indonesia (Shopee, Tokopedia, dll) beserta sinkronisasi stok dan order.
-   📊 **Reporting & Analytics**: Laporan penjualan, pembelian, laba-rugi, dan performa cabang yang komprehensif.
-   🛡️ **Sistem Audit & Izin**: Role & Permission yang granular (Spatie) dan audit log untuk setiap aksi kritikal.

---

## 🛠️ Tech Stack

-   **Backend**: Laravel 13 (PHP 8.3+)
-   **Frontend**: Livewire 3 + Volt, Alpine.js, Tailwind CSS
-   **Database**: PostgreSQL
-   **Caching & Queue**: Redis
-   **Media**: Spatie MediaLibrary
-   **Permissions**: Spatie Laravel Permission
-   **Exports/Imports**: Maatwebsite Excel, Laravel DomPDF

---

## 🚀 Panduan Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan project di environment lokal Anda.

### 1. Prasyarat (Prerequisites)

Pastikan Anda sudah menginstal:
- PHP >= 8.3
- Composer
- Node.js & NPM
- PostgreSQL
- Redis Server

### 2. Clone Repository

```bash
git clone https://github.com/alfianm/pos-laravel.git
cd pos-laravel
```

### 3. Instal Dependensi

```bash
# Instal dependensi PHP
composer install

# Instal dependensi Frontend
npm install
```

### 4. Konfigurasi Environment

Salin file `.env.example` menjadi `.env` dan sesuaikan kredensial database & Redis Anda:

```bash
cp .env.example .env
```

**Penting**: Pastikan `DB_CONNECTION` diatur ke `pgsql` dan sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.

### 5. Setup Project

Jalankan perintah berikut untuk meng-generate app key, menjalankan migrasi, dan seeder:

```bash
# Generate Key
php artisan key:generate

# Migrasi Database & Seeding Data Awal
php artisan migrate --seed

# Link Storage
php artisan storage:link
```

Atau gunakan script shortcut:
```bash
npm run setup
```

---

## 🖥️ Menjalankan Aplikasi

Project ini menggunakan `concurrently` untuk mempermudah menjalankan server pengembangan, queue, dan vite secara bersamaan.

```bash
# Menjalankan Server, Vite, Queue, dan Pail (Logging)
npm run dev
```

Akses aplikasi di browser melalui: [http://localhost:8000](http://localhost:8000)

---

## 📦 Pengembangan & Produksi

### Background Jobs (Queues)
Beberapa fitur seperti sinkronisasi marketplace dan pengiriman Webhook berjalan di background. Pastikan queue worker aktif:
```bash
php artisan queue:work
```

### Penjadwalan (Scheduler)
Fitur laporan otomatis dan pembersihan data membutuhkan scheduler:
```bash
# Lokal
php artisan schedule:work

# Produksi (Crontab)
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📄 Dokumentasi Tambahan

Detail spesifikasi fitur dan rencana pengembangan dapat ditemukan di folder `docs/`:
- [Product Requirement Document (PRD)](docs/prd.md)
- [Architecture Design](docs/architecture.md)
- [Database Schema](docs/database-schema.md)
- [Implementation Plan](docs/implementation-plan.md)

---

## 🛡️ License

Project ini dikembangkan untuk internal bisnis dan dilisensikan di bawah [MIT License](LICENSE).
