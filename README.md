# ☕ SMKN 6 Jember Cafe & Kantin Online

Platform digital pemesanan makanan, minuman, dan snack berbasis web untuk Cafe/Kantin **SMKN 6 Jember**. Dirancang untuk memudahkan siswa melakukan order langsung dari kelas dengan dukungan multi-role (Admin, Kurir, Siswa), integrasi Payment Gateway Xendit, dompet digital/saldo mandiri, serta pengantaran langsung ke ruang teori/kelas.

---

## ✨ Fitur Utama

### 👨‍🎓 Portal Siswa
- **Katalog Menu Interaktif:** Filter kategori dinamis (dengan icon preset/gambar), pencarian real-time, status *Ready / Not Ready*, dan keranjang belanja instan.
- **Fleksibilitas Pengiriman:** Opsi diantar langsung ke meja kelas/ruang teori atau ambil mandiri di kantin (*takeaway*).
- **Multi Metode Pembayaran:**
  - **Saldo Cafe:** Bayar instan menggunakan saldo akun yang terverifikasi.
  - **Xendit Payment Gateway:** Mendukung QRIS (GoPay, OVO, ShopeePay, Dana, LinkAja) & Virtual Account (BCA, Mandiri, BNI, BRI, Permata).
  - **COD (Cash on Delivery):** Bayar tunai saat pesanan diterima.
- **Top Up Saldo Mandiri:** Otomatisasi isi saldo via Xendit invoice dengan verifikasi webhook instan.
- **Upload & Verifikasi Kartu Pelajar:** Validasi status identitas siswa untuk membuka akses pembayaran saldo online.
- **Pelacakan Pesanan Real-time:** Riwayat pesanan, status tracking (*Menunggu, Diproses, Siap, Diantar, Selesai, Dibatalkan*), dan auto-sync pembayaran.

### 🛵 Portal Kurir
- **Manajemen Pengantaran Khusus:** Menerima, mengambil, dan menyelesaikan pesanan antar.
- **Detail Lokasi Presisi:** Informasi kelas, jurusan, nomor ruangan teori, dan catatan pengantaran pemesan.
- **Statistik Harian:** Ringkasan performa pengiriman harian dan riwayat selesai.

### 🛡️ Portal Admin & Kasir
- **Dashboard Analitik:** Ringkasan pendapatan, omset hari ini/bulan ini, grafik penjualan, volume pesanan, dan menu terlaris.
- **Manajemen Menu & Kategori:** CRUD menu lengkap dengan foto, deskripsi, harga, toggle *Ready / Not Ready*, serta pengelolaan kategori dengan icon & thumbnail.
- **Manajemen Order & POS:** Monitor antrean order secara live, filter status pembayaran, update progress dapur.
- **Verifikasi Kartu Pelajar:** Review foto fisik kartu pelajar siswa sebelum menyetujui akses akun.
- **Manajemen User & Kurir:** Kelola akun siswa, kurir, hak akses, serta penyesuaian saldo manual.
- **Laporan Penjualan:** Ekspor dan rekapitulasi data transaksi per rentang tanggal.

### 🔔 Notifikasi & Real-time
- **Notification Bell & Sound Alert:** Notifikasi berbasis audio instan dengan polling Livewire saat ada pesanan baru, pembayaran masuk, atau verifikasi kartu.

---

## 🛠️ Tech Stack

- **Backend Framework:** [Laravel 12](https://laravel.com) (PHP 8.2+)
- **Fullstack Reactivity:** [Livewire 3](https://livewire.laravel.com)
- **Frontend & Styling:** [Tailwind CSS 4](https://tailwindcss.com), Alpine.js, Blade Components
- **Authentication:** Laravel Jetstream & Fortify (Two-Factor Auth & Passkeys ready)
- **Database:** MySQL / MariaDB (Eloquent ORM dengan DB Transaction safety)
- **Payment Gateway:** [Xendit API & Webhook](https://www.xendit.co/)

---

## 🚀 Panduan Instalasi

### 1. Kebutuhan Sistem
Pastikan perangkat Anda sudah terinstal:
- PHP >= 8.2 (ekstensi: `pdo`, `mbstring`, `fileinfo`, `gd`/`imagick`, `curl`)
- Composer >= 2.x
- Node.js >= 18.x & NPM
- Database MySQL / MariaDB

### 2. Langkah Setup

```bash
# 1. Clone repositori
git clone https://github.com/danu-dev/Cafe-SMKN6-Jember.git
cd Cafe-SMKN6-Jember

# 2. Install dependency PHP & Node.js
composer install
npm install

# 3. Buat file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Konfigurasi Database di file .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=cafe_smkn6
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Konfigurasi Kredensial Xendit di .env (Opsional untuk testing payment)
# XENDIT_SECRET_KEY=xnd_development_...
# XENDIT_WEBHOOK_TOKEN=...

# 7. Jalankan migrasi & seeder
php artisan migrate:fresh --seed

# 8. Link storage publik
php artisan storage:link

# 9. Build aset frontend
npm run build
```

### 3. Menjalankan Aplikasi

Jalankan server pengembangan lokal:

```bash
php artisan serve
```

Akses aplikasi di browser: `http://localhost:8000`

---

## 👥 Akun Demo / Default Seeder

Setelah menjalankan database seeder (`php artisan db:seed`), akun default berikut dapat digunakan:

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@cafe.smkn6jember.sch.id` | `password` |
| **Kurir** | `kurir@cafe.smkn6jember.sch.id` | `password` |
| **Siswa** | `siswa@cafe.smkn6jember.sch.id` | `password` |

---

## 🧪 Testing

Jalankan suite pengujian unit & feature test bawaan:

```bash
php artisan test
```

---

## 📄 Lisensi

Proyek ini dikembangkan di bawah lisensi [MIT License](LICENSE).
