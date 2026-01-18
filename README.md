Employee Leave Management System API
Ini adalah sistem manajemen cuti karyawan berbasis REST API yang dibangun menggunakan Laravel 11. Sistem ini dirancang untuk menangani alur pengajuan cuti dari sisi karyawan hingga proses persetujuan oleh admin secara otomatis.

Fitur Utama
1. Sistem Autentikasi Ganda
- Sanctum Authentication: Login menggunakan email dan password untuk mendapatkan Bearer Token.
-OAuth 2.0 (Google Socialite): Memungkinkan pengguna untuk masuk menggunakan akun Google.

2. Manajemen Cuti & Kuota
- Pengecekan Kuota Otomatis: Setiap karyawan mendapatkan jatah cuti 12 hari per tahun.
- Perhitungan Durasi: Sistem menghitung selisih hari antara start_date dan end_date secara akurat.
- Validasi Status: Fitur Update dan Delete hanya diizinkan jika status pengajuan masih pending.
- Auto-Deduct Quota: Kuota karyawan hanya akan berkurang saat Admin memberikan status approved.

3. Keamanan & Role
- Middleware Protection: Memastikan endpoint Admin tidak dapat diakses oleh Employee.
- Data Isolation: Karyawan hanya bisa melihat, mengedit, dan menghapus data miliknya sendiri

Arsitektur Kode
1. Controller (app/Http/Controllers): Bertanggung jawab hanya untuk menerima input, memanggil service yang relevan, dan mengembalikan response JSON. Tidak ada logika perhitungan kuota di sini.
2. Service Layer (app/Services): Inti dari aplikasi. Semua logika bisnis seperti perhitungan selisih hari cuti, validasi sisa kuota, dan logika pemotongan kuota saat approval berada di sini.
3. Eloquent Models (app/Models): Representasi struktur data dan relasi antar tabel (seperti relasi One-to-Many antara User dan LeaveRequest).
4. Middleware (app/Http/Middleware): Layer keamanan yang menyaring permintaan berdasarkan peran pengguna (Admin atau Employee).

Alur Logika

Proses Pengajuan Cuti (Employee)
- Sistem menerima start_date dan end_date.
- Service menghitung durasi hari (menggunakan library Carbon).
- Service mengecek apakah durasi tersebut < sisa kuota user.
- Jika valid, data disimpan dengan status awal pending.

2. Proses Persetujuan (Admin)
- Admin mengubah status menjadi approved atau rejected.
- Jika approved, sistem memicu fungsi Quota Deductor.
- Service melakukan atomic update pada kolom leave_quota di tabel users.


Panduan Instalasi & Konfigurasi
Ikuti langkah-langkah di bawah ini untuk menjalankan project di lingkungan lokal Anda.

1. Prasyarat Sistem
Pastikan perangkat Anda memenuhi spesifikasi berikut:
PHP: minimal versi 8.2
Composer: Versi terbaru
Database: MySQL (localhost)
Tools: Git, Postman (untuk pengujian API)

2. Langkah-Langkah Instalasi
A. Kloning Repositori
jalankan git clone https://github.com/adityabahar20/Magang_seal.git
cd Magang_seal

B. Instalasi Dependency Gunakan Composer untuk menginstal semua library Laravel, termasuk Socialite (OAuth) dan Sanctum (Auth API):
jalankan composer install
composer require laravel/socialite

C. Konfigurasi Environment Salin file template .env dan buat yang baru:
jalankan cp .env.example .env
Buka file .env menggunakan teks editor, lalu sesuaikan konfigurasi berikut:
- Database: Buat database baru di phpMyAdmin dengan nama magang_seal, lalu sesuaikan:
DB_DATABASE=magang_seal
DB_USERNAME=root
DB_PASSWORD=

- Google OAuth: Masukkan Client ID dan Secret dari Google Cloud Console:
GOOGLE_CLIENT_ID=1081220368152-rajm68i1b4bk3gcoaiknfe9n3qcchq5u.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback

D. Inisialisasi Project Jalankan perintah ini secara berurutan untuk menyiapkan aplikasi:
- Generate unique application key
php artisan key:generate

- Migrasi struktur tabel dan mengisi data awal (Seeder)
php artisan migrate --seed

- Membuat link untuk akses file upload (Lampiran Cuti)
php artisan storage:link

3. Menjalankan Aplikasi
Setelah semua siap, jalankan server lokal:
php artisan serve

Akun Uji Coba (Default Credentials)
Setelah menjalankan php artisan migrate --seed, Anda dapat menggunakan akun berikut untuk pengujian di Postman:
Admin:admin@example.com,password
Employee:employee@example.com,password

Untuk memudahkan Anda, saya telah menyertakan folder /docs yang berisi:
- Collection.json: Daftar semua endpoint API.
- Environment.json: Variabel base_url agar Anda tidak perlu mengetik URL berulang kali.

Langkah-langkah:
1. Buka Postman > Klik tombol Import.
2. Drag & drop kedua file tersebut.
3. Pilih Environment "Leave Management Env" di pojok kanan atas Postman.
4. Jalankan request Login terlebih dahulu, salin tokennya, dan masukkan ke Collection Settings > Authorization > Bearer Token.

Untuk pengujian:
1. Login konvensional: POST|{{base_url}}/login
2. Login OAuth: GET|{{base_url}}/auth/google (untuk mendapatkan link login)
3. Employee_Request: POST|{{base_url}}/leave-requests
4. Employee_viewRequest: GET|{{base_url}}/my-leaves
5. Employee_EditRequest: PUT|{{base_url}}/leaves/{id_cuti}
6. Employee_DeleteRequest: DELETE|{{base_url}}/leaves/{id_cuti}
7. Admin_view: GET|{{base_url}}/admin/leaves
8. Admin_rejected_accept: PATCH|{{base_url}}/admin/leaves/{id_cuti}/status

API Documentation
Dokumentasi API lengkap beserta contoh request dan response dapat diakses melalui link berikut:
Link Dokumentasi Postman Online https://www.postman.com/baharexius/workspace/my-workspace/collection/39315183-6f0a4109-7054-4630-8143-b57daf963efc?action=share&creator=39315183&active-environment=39315183-c513afe8-51e9-47e3-8f64-55c604c60a2a
*Atau gunakan file JSON yang tersedia di folder `/docs/postman` untuk impor manual*
