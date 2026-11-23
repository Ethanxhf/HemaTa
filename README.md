# HemaTa - Sistem Pemantauan Pemakaian Air dan Listrik untuk Mendukung Efisiensi Sumber Daya

Aplikasi web untuk mencatat dan memantau pemakaian **listrik** serta **air** dalam satu dashboard, lengkap dengan perhitungan biaya otomatis dalam rupiah dan laporan rekap per bulan. Dirancang untuk mendukung program SDGs terutama **SDG 6 Clean Water and Sanitation** dan **SDG 7 Affordable and Clean Energy**.

---

## 1. Latar Belakang dan Tujuan

Konsumsi listrik dan air di rumah tangga sering tidak terpantau sehingga pemborosan sulit terdeteksi dan tagihan sulit dibagi secara adil. HemaTa hadir sebagai sistem pemantauan sederhana yang:

- mencatat pemakaian listrik berdasarkan **daya terpasang** dan pemakaian **kWh**;
- mencatat pemakaian air berdasarkan **nominal rupiah** yang otomatis dikonversi menjadi **Liter**;
- menampilkan **4 nilai utama di dashboard**: total kWh, total Liter, total biaya listrik, dan total biaya air;
- menyajikan **laporan rekap per bulan** untuk pemakaian listrik dan air, lengkap dengan grafik tren, unduhan CSV, dan fitur cetak;
- memberi **perkiraan tagihan bulan depan** dari rata-rata bulan-bulan sebelumnya;
- membantu upaya efisiensi sumber daya sehingga sejalan dengan **SDG 6** dan **SDG 7**.

**Tujuan:** menghasilkan aplikasi web utuh yang mendukung program SDGs terutama **SDG 6 Clean Water and Sanitation** dan **SDG 7 Affordable and Clean Energy**.

---

## 2. Modul dan Fitur Aplikasi

Aplikasi ini dirancang untuk membantu pengguna mencatat, memantau, dan menganalisis pemakaian listrik serta air dalam satu platform. Berikut adalah modul dan fitur utama yang tersedia:

| # | Modul Aplikasi | Deskripsi |
|---|----------------|-----------|
| 1 | Autentikasi Pengguna | Menyediakan fitur registrasi, login, logout, serta perlindungan halaman berdasarkan session pengguna. |
| 2 | Input Pemakaian Listrik | Form pencatatan pemakaian listrik dengan dropdown pilihan daya 450–2200 VA dan isian jumlah pemakaian dalam kWh. |
| 3 | Kalkulator Tarif Listrik | Menghitung biaya listrik secara otomatis berdasarkan jumlah pemakaian kWh dan dua kelompok tarif tetap, yaitu 450–900 VA dan 1300–2200 VA. |
| 4 | Input Pemakaian Air | Form pencatatan biaya pemakaian air dalam nominal rupiah yang dikonversi secara otomatis menjadi satuan liter. |
| 5 | Dashboard 4 Metrik | Menampilkan empat ringkasan utama, yaitu total pemakaian listrik (kWh), pemakaian air (liter), biaya listrik, dan biaya air dalam rupiah. |
| 6 | Riwayat Catatan | Menampilkan seluruh catatan pemakaian yang tersimpan, dilengkapi filter berdasarkan jenis pemakaian dan ringkasan data. |
| 7 | Ubah Catatan | Memungkinkan pengguna mengubah data pemakaian yang telah dicatat, termasuk melakukan perhitungan ulang secara otomatis setelah perubahan data. |
| 8 | Hapus Catatan | Memungkinkan pengguna menghapus catatan pemakaian dengan konfirmasi sebelum penghapusan dilakukan. |
| 9 | Laporan Pemakaian Listrik dan Air | Menyediakan rekapitulasi pemakaian listrik dan air berdasarkan periode bulanan dari catatan milik akun yang sedang login, serta fitur cetak laporan. |
| 10 | Validasi Form | Menerapkan validasi input di sisi klien menggunakan JavaScript dan menyediakan pratinjau hasil perhitungan secara real-time sebelum data disimpan. |
| 11 | Grafik Tren Bulanan | Menampilkan diagram batang perbandingan biaya listrik dan air selama enam bulan terakhir pada halaman laporan. |
| 12 | Perkiraan Tagihan Bulan Depan | Menampilkan estimasi biaya listrik dan air untuk bulan berikutnya berdasarkan rata-rata biaya dari maksimal tiga bulan terakhir. |
| 13 | Ekspor CSV | Memungkinkan pengguna mengunduh rekapitulasi data berdasarkan periode bulanan atau seluruh catatan. |


---

## 3. Logika Perhitungan

### 3.1 Tarif Listrik

| Pilihan pada Dropdown | Tarif |
|---|---|
| 450 VA (subsidi) | **Rp 415 / kWh** |
| 900 VA (subsidi) | **Rp 605 / kWh** |
| 900 VA (non-subsidi) | **Rp 1.352 / kWh** |
| 1300 VA | **Rp 1.444,70 / kWh** |
| 2200 VA | **Rp 1.444,70 / kWh** |

Tarif dapat berubah mengikuti ketetapan PLN, tarif dapat disesuaikan berdasarkan tarif terkini `includes/fungsi.php` di fungsi `daftarGolongan()`.

```
Biaya Listrik = kWh terpakai × tarif golongan daya
Contoh : 2200 VA, 12,5 kWh  →  12,5 × 1.444,70 = Rp 18.058,75
```

### 3.2 Konversi Air (rupiah → liter)

```
Liter = Nominal Rp ÷ tarif air per m³ × 1000
Tarif air bawaan : Rp 5.000 per m³ (tarif per meter kubik dapat disesuaikan di `includes/fungsi.php`)
Contoh : Rp 100.000 → 100.000 ÷ 5.000 × 1000 = 20.000 Liter
         Rp  50.000 → 10.000 Liter
         Rp 200.000 → 40.000 Liter
```

### 3.3 Empat Nilai Dashboard

| Nilai | Rumus query |
|---|---|
| Total kWh | `SUM(kwh) WHERE jenis='listrik'` |
| Total Liter | `SUM(liter) WHERE jenis='air'` |
| Total biaya listrik | `SUM(total_rupiah) WHERE jenis='listrik'` |
| Total biaya air | `SUM(total_rupiah) WHERE jenis='air'` |

### 3.4 Pengeluaran Bulan Ini

```
Pengeluaran bulan ini = SUM(total_rupiah) untuk catatan dengan tanggal di bulan berjalan
```

---

## 4. Teknologi yang Digunakan

- **HTML5** - struktur halaman, form, tabel
- **CSS3** - stylesheet eksternal, flexbox, grid, responsive, print style
- **JavaScript** - validasi form dan pratinjau perhitungan realtime
- **PHP 8** - logika bisnis, include, function, array
- **MySQL** - database, operasi CRUD
- **Session PHP** - autentikasi dan penjaga halaman
- **XAMPP / Laragon** - lingkungan server lokal (Apache, MariaDB/MySQL, PHP)

---

## 5. Struktur Folder

```
hemata/
├── index.php               # Pintu masuk otomatis (ke dashboard / login)
├── register.php            # Registrasi akun
├── login.php               # Login
├── logout.php              # Logout
├── dashboard.php           # 4 nilai utama + total + pengeluaran bulan ini
├── catat_listrik.php       # Input listrik (dropdown daya + kWh)
├── catat_air.php           # Input air (rupiah -> liter)
├── riwayat.php             # Read + filter + aksi
├── edit_catatan.php        # Update
├── hapus_catatan.php       # Delete
├── laporan.php             # Rekap pemakaian per bulan + grafik tren + cetak
├── export_csv.php          # Ekspor rekap / seluruh catatan ke CSV
├── includes/
│   ├── koneksi.php         # Koneksi MySQL
│   ├── fungsi.php          # Fungsi logika bisnis (tarif, konversi, format)
│   ├── header.php          # Layout atas + penjaga session
│   └── footer.php          # Layout bawah
├── css/style.css           # Seluruh tampilan
├── js/app.js               # Validasi + pratinjau hitungan
├── database/hemata.sql     # Skema + data awal database
├── docs/PENJELASAN_KODE.txt # Penjelasan tiap file, fungsi, dan alur kode
└── README.md               # Dokumen ini
```

---

## 6. Panduan Instalasi dan Konfigurasi

1. **Salin folder** `hemata` ke folder web server:
   - XAMPP: `C:\xampp\htdocs\hemata`
   - Laragon: `C:\laragon\www\hemata`
2. **Jalankan Apache dan MySQL** dari XAMPP/Laragon Control Panel.
3. **Buka phpMyAdmin** (`http://localhost/phpmyadmin`), pilih tab **Import**, lalu impor file `database/db_hemata.sql`. Database `db_hemata` akan dibuat otomatis.
4. **Sesuaikan koneksi** bila diperlukan di `includes/koneksi.php`:
   ```php
   $koneksi = mysqli_connect("localhost", "root", "", "db_hemata");
   ```

---

## 7. Panduan Menjalankan Aplikasi

1. Pastikan Apache dan MySQL menyala.
2. Buka browser, akses `http://localhost/hemata/`.
3. Anda akan diarahkan ke halaman **Login**.
4. Masuk memakai akun demo:

   | Username | Password | Nama |
   |---|---|---|
   | `kos_melati` | `hemata123` | Kos Melati |
   | `rumah_cempaka` | `hemata123` | Rumah Cempaka |
   | `kos_mawar` | `hemata123` | Kos Mawar |

   Atau buat akun baru lewat menu **Daftar**.
5. Alur penggunaan:
   - **Dashboard** → lihat 4 nilai utama (kWh, Liter, biaya listrik, biaya air), perbandingan dengan bulan lalu, dan peringatan jika pemakaian naik lebih dari 20%.
   - **Catat Listrik** → pilih golongan daya (450/900 subsidi/900/1300/2200 VA), isi kWh, simpan.
   - **Catat Air** → isi nominal rupiah, sistem menghitung Liter.
   - **Riwayat** → lihat/filter data, ubah, atau hapus (penghapusan lewat tombol dengan konfirmasi).
   - **Dashboard** juga menampilkan perkiraan tagihan bulan depan (rata-rata maksimal 3 bulan terakhir).
   - **Laporan** → rekap per bulan, grafik tren 6 bulan, unduh CSV, dan cetak.
   - **Logout** untuk keluar.

---

## 8. Our Team

| Name | Jobdesk |
|---|---|
| Muhammad Hanif Muhyidin | Autentikasi (register/login/logout), dashboard, styling |
| Muhammad Fajri Hamsah | Input listrik & air, logika tarif dan konversi |
| Radithya Agung Anugrah | CRUD riwayat/edit/hapus, laporan pemakaian |


---

## 9. SDGs Contribute

- **SDG 6 - Clean Water and Sanitation:** pemantauan volume air  dan biaya air mendorong penggunaan air secara terukur dan efisien.
- **SDG 7 - Affordable and Clean Energy:** pemantauan penggunaan listrik berdasarkan daya terpasang membantu mengenali pola konsumsi energi dan menekan pemborosan listrik.

---

## 10. Dokumentasi

- `database/hemata.sql` - skema tabel, relasi, dan inisiasi data awal.
