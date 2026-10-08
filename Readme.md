# Sistem Informasi Web Desa 🏡

Aplikasi berbasis web ini dirancang untuk mempermudah tata kelola administrasi desa, pelayanan publik, dan transparansi informasi bagi masyarakat. Melalui portal ini, masyarakat dapat mengakses berita desa, mengajukan surat administrasi, dan melihat profil desa secara real-time.

## 🚀 Fitur Utama
*   **Profil Desa:** Menampilkan sejarah, visi misi, struktur organisasi, dan demografi desa.
*   **Berita & Pengumuman:** Sistem manajemen konten (CMS) untuk publikasi kegiatan desa.
*   **Layanan Administrasi:** Pengajuan dan pencetakan surat keterangan (misal: SKTM, Surat Pengantar, dll).
*   **Transparansi Dana Desa:** Publikasi infografis APBDes.
*   **Galeri:** Dokumentasi kegiatan masyarakat dan aparatur desa.

## 🛠️ Teknologi yang Digunakan
*(Silakan sesuaikan bagian ini dengan teknologi yang Anda gunakan)*
*   **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript (jQuery)
*   **Backend:** PHP 8.x / Framework (misal: CodeIgniter / Laravel)
*   **Database:** MySQL / MariaDB

## 📋 Persyaratan Sistem
Sebelum menginstal aplikasi ini, pastikan Anda telah menginstal perangkat lunak berikut:
*   Web Server lokal (XAMPP / Laragon / WAMP)
*   PHP versi 7.4 atau lebih baru (disarankan versi 8.x)
*   MySQL atau MariaDB
*   Web Browser (Chrome, Firefox, atau Edge)

## ⚙️ Panduan Instalasi & Konfigurasi

### 1. Menyiapkan File Proyek
1. Unduh atau *clone* repositori ini.
2. Ekstrak file dan pindahkan folder proyek ke dalam direktori web server lokal Anda:
   * Jika menggunakan **XAMPP**: pindahkan ke `C:\xampp\htdocs\`
   * Jika menggunakan **Laragon**: pindahkan ke `C:\laragon\www\`
3. Ubah nama folder menjadi `web-desa` (atau sesuai keinginan Anda).

### 2. Setup Database (Menggunakan File yang Dilampirkan)
1. Buka aplikasi XAMPP/Laragon dan jalankan modul **Apache** dan **MySQL**.
2. Buka browser dan akses `http://localhost/phpmyadmin`.
3. Buat database baru dengan nama `db_web_desa` (atau sesuaikan dengan kebutuhan).
4. Klik database yang baru dibuat, lalu pilih tab **Import**.
5. Klik **Choose File** dan pilih file database berekstensi `.sql` yang terlampir pada proyek ini.
6. Scroll ke bawah dan klik tombol **Go** atau **Import**. Tunggu hingga proses selesai.

### 3. Konfigurasi Koneksi Database
Agar aplikasi terhubung ke database, Anda perlu menyesuaikan file konfigurasi.
*(Sesuaikan path file di bawah ini berdasarkan struktur aplikasi Anda, misalnya `config/database.php` atau file `.env`)*

Buka file konfigurasi database dan ubah parameternya menjadi:
```php
// Contoh konfigurasi PHP Native
$host = "localhost";
$user = "root";
$pass = ""; // Kosongkan jika default XAMPP
$db   = "db_web_desa"; // Sesuaikan dengan nama database yang Anda buat
```

### 4. Menjalankan Aplikasi
1. Buka web browser.
2. Ketikkan URL berikut: `http://localhost/web-desa`
3. Untuk masuk ke halaman admin, silakan akses: `http://localhost/web-desa/admin` (sesuaikan dengan rute Anda).

**Akun Administrator Default:**
*   **Username / Email:** admin@desa.id
*   **Password:** admin123
*(Pastikan untuk segera mengubah password default ini setelah aplikasi berjalan di server produksi!)*

## 🤝 Berkontribusi
Jika Anda ingin mengembangkan fitur atau memperbaiki *bug*, silakan buat *Pull Request* atau laporkan masalah melalui *Issues*.

## 📄 Lisensi
Proyek ini bersifat *Open Source* dan didistribusikan di bawah lisensi MIT. Anda bebas menggunakan dan memodifikasinya untuk kebutuhan desa Anda.