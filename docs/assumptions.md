# Assumptions

## Tujuan
Dokumen ini menyimpan asumsi yang digunakan selama implementasi agar agent tidak menebak diam-diam.

---

## Asumsi Produk
- Satu tenant dapat memiliki banyak branch
- Satu user dapat terkait ke banyak branch
- Owner dapat melihat semua branch dalam tenant
- Branch manager hanya melihat branch yang ditugaskan
- Cashier hanya bisa bertransaksi di branch aktifnya

---

## Asumsi Inventory
- Source of truth stok adalah sistem internal, bukan marketplace
- Receiving purchase menambah stok
- Sales mengurangi stok
- Return dan adjustment memengaruhi stok
- Semua perubahan stok harus tercatat di stock movements

---

## Asumsi POS
- Walk-in customer diperbolehkan
- Satu sale dapat memiliki banyak item
- Satu sale dapat memiliki banyak payment record bila split payment diaktifkan
- MVP menggunakan payment manual tanpa integrasi gateway dulu

---

## Asumsi CRM
- CRM dibangun setelah sales stabil
- Customer timeline mengambil event dari sales dan follow-up
- Lead dapat dikonversi ke customer tanpa duplikasi data penting

---

## Asumsi Omnichannel
- Omnichannel dimulai dari satu marketplace dulu
- Mapping produk dilakukan dengan SKU internal
- Sinkronisasi stok bersifat near real-time atau queue-based
- Order marketplace disimpan ke tabel internal terpisah sebelum diproses

---

## Asumsi Teknis
- Semua tabel business utama memakai UUID
- Money dan quantity memakai decimal
- Authorization memakai policies + roles/permissions
- Laravel Livewire dipakai untuk admin/internal UI
- Audit log tersedia sejak awal

---

## Catatan
Jika ada keputusan baru yang mengubah flow, tambahkan di file ini.