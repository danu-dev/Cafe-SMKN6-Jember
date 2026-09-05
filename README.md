# SMKN 6 Jember Cafe/Kantin

Sistem pemesanan online untuk Cafe/Kantin SMKN 6 Jember. Mendukung multi-role (Admin, Kurir, Siswa), pembayaran via saldo dan Xendit, serta sistem pengiriman ke ruangan/kelas.

## Fitur Utama

*   **Multi-Role:** Admin, Kurir, dan Siswa.
*   **Pemesanan Online:** Siswa dapat memesan makanan/minuman secara online.
*   **Pengiriman Ruangan/Kelas:** Sistem delivery pesanan langsung ke kelas/ruangan pemesan.
*   **Payment Gateway (Xendit):** Terintegrasi dengan Xendit untuk opsi pembayaran otomatis. Termasuk Webhook.
*   **Sistem Saldo:** Siswa dapat menggunakan saldo untuk pembayaran. Top-up saldo dilakukan oleh admin.
*   **Notifikasi:** Fitur Notification Bell dengan Web Audio & Auto-polling (Livewire).
*   **Verifikasi Kartu Pelajar:** Digunakan dalam proses pendaftaran dan verifikasi siswa.

## Kebutuhan Sistem

*   PHP 8.2+
*   Composer
*   Node.js & NPM
*   Database (MySQL/MariaDB)

## Instalasi

1.  Clone repositori.
2.  Jalankan `composer install`.
3.  Jalankan `npm install && npm run build`.
4.  Salin `.env.example` ke `.env` dan konfigurasikan database serta kredensial Xendit (`XENDIT_SECRET_KEY`, `XENDIT_WEBHOOK_TOKEN`).
5.  Jalankan `php artisan key:generate`.
6.  Jalankan `php artisan migrate:fresh --seed`.

## Testing

Jalankan `php artisan test`.