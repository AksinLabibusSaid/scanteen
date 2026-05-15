# Dokumentasi Struktur Proyek Scanteen

Proyek ini adalah sistem manajemen kantin (Scanteen) yang dibangun menggunakan PHP dengan arsitektur yang terorganisir (mirip MVC/Service-Repository).

## Struktur Folder Utama

| Folder | Fungsi |
| :--- | :--- |
| **`api/`** | Berisi endpoint API untuk permintaan AJAX atau integrasi eksternal. Dibagi menjadi folder `customer`, `payment`, dan `staff`. |
| **`app/`** | Pusat logika bisnis aplikasi (Core Logic). |
| &nbsp;&nbsp;&nbsp;`Core/` | Kelas dasar (Base Classes) untuk framework internal. |
| &nbsp;&nbsp;&nbsp;`Repositories/` | Layer akses data (Database Query). Bertanggung jawab untuk interaksi langsung ke tabel database. |
| &nbsp;&nbsp;&nbsp;`Services/` | Layer logika bisnis. Mengolah data dari Repositories sebelum dikirim ke UI/API. |
| &nbsp;&nbsp;&nbsp;`Support/` | Kelas pembantu (Helper classes) seperti URL handling, formatters, dll. |
| **`assets/`** | File statis seperti CSS, JavaScript, dan Gambar yang digunakan di sisi client. |
| **`config/`** | File konfigurasi sistem, termasuk koneksi database (`db.php`) dan pengaturan gateway pembayaran (`payment.php`). |
| **`database/`** | Menyimpan schema SQL atau script migrasi database. |
| **`includes/`** | File yang sering disertakan di berbagai halaman, seperti `functions.php` (helper global), `auth.php` (cek login), dan `modal.php`. |
| **`pages/`** | Berisi file tampilan (UI) yang dikelompokkan berdasarkan peran pengguna (Admin, Auth, Customer, Kasir, Warung, Staff). |

## File Penting di Root

- **`index.php`**: Titik masuk utama aplikasi. Saat ini diatur untuk mengarahkan pengguna ke halaman login staff secara otomatis.
- **`app/bootstrap.php`**: Menginisialisasi aplikasi dan mengatur *PSR-4 Autoloader* sehingga kelas di folder `app/` dapat dipanggil secara otomatis tanpa `require` manual di setiap file.

## Alur Kerja Singkat

1. **Routing**: Pengguna mengakses halaman melalui folder `pages/`.
2. **Logic**: Halaman di `pages/` memanggil **Services** untuk memproses data.
3. **Data**: **Services** memanggil **Repositories** untuk mengambil/menyimpan data ke database.
4. **API**: Untuk fitur dinamis (seperti update status tanpa reload), aplikasi menggunakan file di folder `api/`.
