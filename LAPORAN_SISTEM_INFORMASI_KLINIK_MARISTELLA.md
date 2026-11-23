# LAPORAN TUGAS BESAR PENGEMBANGAN SISTEM INFORMASI

## PERANCANGAN DAN IMPLEMENTASI SISTEM INFORMASI KLINIK TERINTEGRASI BERBASIS WEB (KLINIK MARISTELLA)

**Mata Kuliah:** Pemrograman dan Pengujian Web (PPW)  
**Program Studi:** Sarjana Sistem Informasi  
**Fakultas:** Informatika dan Teknik Elektro, Institut Teknologi Del  
**Tahun Akademik:** 2026/2027  

**Disusun Oleh (Kelompok):**
1. **12S24035** - Josua Sianturi
2. **12S24014** - Arion Manurung
3. **12S24019** - Winda N.V. Sitorus
4. **12S24058** - Airene Situmorang

---

## BAB 1: PENDAHULUAN

### 1.1 Purpose of Document
Dokumen ini disusun sebagai laporan pelaksanaan proyek tugas besar mata kuliah Pemrograman dan Pengujian Web (PPW) pada Program Studi Sarjana Sistem Informasi, Institut Teknologi Del. Dokumen ini bertujuan untuk:
1. Memberikan dokumentasi teknis dan fungsional menyeluruh terkait perancangan, implementasi arsitektur, dan pengujian sistem pada aplikasi **Klinik Maristella**.
2. Mendokumentasikan transformasi proses bisnis operasional klinik, dari sistem manual konvensional menuju sistem digital berbasis web yang terintegrasi.
3. Menjelaskan spesifikasi arsitektur Model-View-Controller (MVC) berbasis framework Laravel, struktur skema basis data relasional (10 entitas tabel), hak akses multi-peran (Multi-Role Authentication), serta siklus layanan pasien dari registrasi, antrean, pemeriksaan medis SOAP, hingga penagihan/pembayaran dan manajemen farmasi.
4. Menjadi acuan evaluasi pemenuhan kebutuhan fungsional dan pengujian otomatis (*automated feature testing*) terhadap sistem yang telah dibangun.

### 1.2 Scope (Ruang Lingkup Sistem)
Sistem Informasi Klinik Maristella mencakup ruang lingkup layanan klinik rawat jalan yang menghubungkan 4 (empat) aktor utama:
1. **Pasien:** Mengakses portal antrean, jadwal dokter, reservasi konsultasi dokter secara mandiri (*online booking*), kalender ketersediaan jadwal, memantau nomor antrean aktif, kwitansi pembayaran digital, serta mengakses riwayat Rekam Medis Elektronik (RME).
2. **Dokter:** Mengakses portal dokter dengan isolasi data antrean per dokter, melihat data demografis & riwayat medis pasien masa lalu, mengisi rekam medis berbasis format medis standar **SOAP** (*Subjective, Objective, Assessment, Plan*) termasuk tanda vital dan peresepan obat, serta menyelesaikan sesi pemeriksaan.
3. **Admin (Keuangan & Operasional):** Mengelola operasional administrasi dan rekapitulasi finansial klinik, mencakup Dashboard Keuangan, pencatatan transaksi kasir, daftar tagihan (*invoices*), riwayat pembayaran, rekapitulasi pendapatan (*revenue report*), ekspor laporan ke format CSV, dan pemantauan kalender klinik.
4. **Apoteker:** Mengelola data master obat dan inventori farmasi (*stok, satuan, harga, kategori*), serta memantau integrasi peresepan obat dari rekam medis dokter.

### 1.3 Definition, Acronym, and Abbreviation
* **MVC (Model-View-Controller):** Pola arsitektur perangkat lunak yang memisahkan logika data (Model), antarmuka (View), dan pengendali logika bisnis (Controller).
* **Laravel Framework:** Kerangka kerja aplikasi web berbasis PHP modern dengan fitur routing ekspresif, Eloquent ORM, middleware, dan sistem migrasi basis data.
* **SOAP:** Standar internasional pendokumentasian rekam medis klinis yang terdiri atas 4 komponen:
  * *Subjective (S):* Anamnesis dan keluhan pasien.
  * *Objective (O):* Hasil pemeriksaan fisik dan tanda vital (Tekanan Darah, Nadi, Laju Nafas, Suhu, SpO2, BB, TB).
  * *Assessment (A):* Diagnosis medis (kode ICD-10) dan catatan klinis.
  * *Plan (P):* Rencana terapi medis, resep obat, tindakan klinis, dan jadwal kontrol kembali.
* **MRN (Medical Record Number):** Nomor Rekam Medis unik yang melekat pada identitas setiap pasien.
* **ERD (Entity Relationship Diagram):** Pemodelan grafis yang memetakan entitas relasional, atribut kunci (*Primary Key, Foreign Key*), dan derajat kardinalitas antar entitas dalam basis data.
* **BPMN (Business Process Model and Notation):** Notasi grafis standar industri untuk menggambarkan diagram alir proses bisnis secara runtut.
* **Eloquent ORM:** Sistem pemetaan objek-relasional bawaan Laravel yang menghubungkan tabel basis data dengan kelas model PHP.
* **Tailwind CSS & Vite:** Toolkit antarmuka utilitas CSS dan *bundler* modul JavaScript modern untuk menyusun antarmuka responsif dan cepat.

### 1.4 Document Summary
Sistematika penulisan laporan ini disusun sesuai kaidah rekayasa perangkat lunak:
* **Bab 1:** Menjelaskan latar belakang, tujuan dokumen, batasan sistem, dan definisi istilah.
* **Bab 2:** Menjelaskan gambaran sistem berjalan (*current system*), sistem usulan (*target system*), perbandingan proses bisnis dengan diagram BPMN, serta kebutuhan perangkat lunak dan keras.
* **Bab 3:** Mendefinisikan use case diagram, aktor, dan skenario fungsionalitas sistem.
* **Bab 4:** Menjelaskan rancangan basis data lengkap (ERD 10 tabel relasional) dan spesifikasi struktur kamus data.
* **Bab 5:** Menampilkan implementasi antarmuka (*screen design & mockup*) setiap modul sistem.
* **Bab 6:** Menjabarkan arsitektur data, alur pertukaran informasi AJAX/JSON internal, dan logika controller.
* **Bab 7:** Menyajikan rancangan dan hasil pengujian otomatis (*Automated Feature Tests*) sistem.
* **Bab 8:** Berisi kesimpulan pencapaian proyek dan saran pengembangan lanjutan.

---

## BAB 2: WEBSITE OVERVIEW & BUSINESS PROCESS

### 2.1 Current System Overview (Sistem Berjalan)
Pada kondisi operasional konvensional sebelum digitalisasi:
1. **Alur Pendaftaran & Antrean:** Pasien harus mendatangi klinik secara fisik (walk-in) dan mengambil nomor antrean berbasis kertas, menyebabkan penumpukan antrean fisik di ruang tunggu.
2. **Penjadwalan Praktik:** Jadwal dokter dicatat secara manual menggunakan papan informasi atau buku agenda, sehingga pasien tidak mengetahui kepastian kuota dokter yang masih tersedia sebelum tiba di klinik.
3. **Pemeriksaan & Rekam Medis Kertas:** Dokter mencatat diagnosis pada lembaran rekam medis fisik yang disimpan di rak berkas. Berkas ini berisiko rusak, tercecer, serta memakan waktu lama saat pencarian riwayat penyakit pasien di kunjungan lampau.
4. **Pencatatan Pembayaran:** Pembayaran dicatat menggunakan kuitansi nota tulis tangan, mempersulit rekapitulasi keuangan harian dan berpotensi memicu kesalahan rekapitulasi pendapatan klinik.

#### 2.1.1 BPMN Sistem Berjalan (Manual)
Proses dimulai dari Pasien datang ke klinik $\rightarrow$ Mengambil nomor antrean fisik $\rightarrow$ Menunggu panggilan di loket $\rightarrow$ Petugas loket mencari berkas rekam medis manual $\rightarrow$ Pasien menunggu giliran di depan poli dokter $\rightarrow$ Dokter melakukan anamnesis dan menulis rekam medis kertas $\rightarrow$ Pasien membawa resep manual ke kasir $\rightarrow$ Kasir menghitung total dan menulis kuitansi kertas $\rightarrow$ Pasien membayar tunai dan menerima obat.

### 2.2 Target System (Sistem Usulan Klinik Maristella)
Sistem Informasi Klinik Maristella mengintegrasikan seluruh rantai operasional ke dalam satu platform web responsif berbasis peran. Alur target sistem terbagi menjadi 4 (empat) tahapan utama yang saling terhubung secara otomatis:

```
[Tahap 1: Booking Mandiri]       [Tahap 2: Antrean & Routing]
Pasien reservasi dokter via web -> Sistem generate Kode Booking
                                -> Nomor Antrean otomatis dibuat

[Tahap 3: Pemeriksaan SOAP]      [Tahap 4: Pembayaran & Selesai]
Dokter akses antrean polinya    -> Kasir/Admin proses Invoice
Input Vital Sign, Diagnosis,     -> Kwitansi otomatis diterbitkan
Peresepan Obat & Selesaikan Sesi -> Status Booking "Completed"
```

1. **Tahap 1 — Pendaftaran & Reservasi Online:**
   Pasien dapat memilih dokter spesialis, mengecek jadwal praktik real-time, memasukkan keluhan awal, dan melakukan konfirmasi booking melalui antarmuka web.
2. **Tahap 2 — Pengalokasian Antrean Otomatis:**
   Sistem secara otomatis menggenerasi kode booking unik (`BK-YYYYMMDD-XXXX`), menentukan kode poli (misal: Prefix `B` untuk Kardiologi, `A` untuk Neurologi, `C` untuk Ortopedi), dan menerbitkan nomor antrean harian dengan status `waiting`.
3. **Tahap 3 — Pemeriksaan Medis Berstandar SOAP:**
   Dokter login ke Portal Dokter dan hanya melihat antrean pasien yang terdaftar di jadwalnya. Dokter meninjau rekam medis masa lalu pasien, memeriksa tanda-tanda vital fisik (Objective), menegakkan diagnosis (Assessment), serta menyusun resep obat (Plan) dengan pencarian obat otomatis dari basis data. Penyimpanan rekam medis mengubah status antrean dan booking menjadi `completed`.
4. **Tahap 4 — Pembayaran, Kasir & Kwitansi Digital:**
   Sistem menghitung biaya berdasarkan rencana tarif (konsultasi murni atau konsultasi + obat). Admin/Kasir memproses transaksi di Portal Keuangan, menerbitkan invoice resmi bernomor unik, mencatat metode pembayaran (Tunai, Kartu, QRIS, Transfer Bank), dan sistem menyajikan kwitansi pelunasan digital kepada pasien.

### 2.3 Kebutuhan Lingkungan Perangkat Keras dan Perangkat Lunak

#### Kebutuhan Perangkat Lunak (Software)
* **Sistem Operasi:** Windows 10/11, macOS Sonoma/Sequoia, atau Linux Ubuntu 22.04 LTS.
* **Web Server & Environment:** Laragon / Apache / Nginx.
* **Bahasa Pemrograman:** PHP versi 8.3 (dengan ekstensi `pdo_sqlite`, `curl`, `mbstring`, `xml`).
* **Framework:** Laravel versi 13.x.
* **Basis Data:** SQLite (Development) / MySQL 8.0 (Production-Ready).
* **Frontend Tooling:** Node.js v18+, NPM, Tailwind CSS v4, dan Vite.
* **Pengujian:** PHPUnit 12.x / Laravel Test Runner.

#### Kebutuhan Perangkat Keras (Hardware Minimal)
* **Processor:** Intel Core i3 / AMD Ryzen 3 (Quad Core) atau lebih tinggi.
* **RAM:** Minimal 4 GB (disarankan 8 GB untuk kompilasi aset Vite & server lokal).
* **Storage:** Ruang kosong minimal 1 GB untuk repositori kode, node_modules, dan basis data.
* **Display:** Resolusi layar 1366 x 768 (Optimal 1920 x 1080 responsif untuk perangkat seluler hingga desktop).

---

## BAB 3: SYSTEM USE CASE

### 3.1 Identifikasi Aktor Sistem
Sistem Informasi Klinik Maristella memiliki 4 (empat) aktor yang berinteraksi dengan batasan wewenang (*privilege*) masing-masing:

| No | Aktor | Peran Utama | Level Autentikasi |
| :--- | :--- | :--- | :--- |
| 1 | **Pasien (User)** | Reservasi jadwal, memantau antrean, melihat kwitansi & rekam medis. | Role: `user` / `patient` |
| 2 | **Dokter** | Melihat antrean poli miliknya, melihat profil/riwayat medis pasien, mengisi SOAP medis, mencari obat. | Role: `doctor` |
| 3 | **Administrator** | Memantau dashboard keuangan, kelola transaksi, tagihan, rekapitulasi laba, ekspor CSV, pemantauan kalender. | Role: `admin` |
| 4 | **Apoteker** | Mengelola master inventori obat (CRUD obat, stok, kategori, harga, satuan). | Role: `apoteker` |

### 3.2 Deskripsi Rinci Fungsionalitas Aktor

#### A. Aktor Pasien
* **UC-P01: Melihat Beranda & Daftar Dokter Spesialis:** Pasien dapat melihat profil dokter, spesialisasi, rating kepuasan, tarif konsultasi, dan ketersediaan praktik.
* **UC-P02: Melihat Kalender Praktik Dokter:** Pasien dapat memilih dokter tertentu dan meninjau slot jadwal yang masih tersedia maupun yang telah penuh terisi.
* **UC-P03: Melakukan Reservasi (Booking Konsultasi Online):** Pasien mengisi form reservasi dengan menginput keluhan, durasi gejala, tingkat keparahan, serta memilih dokter dan sesi waktu.
* **UC-P04: Memantau Status Antrean & Sesi Medis:** Pasien memantau perubahan status antrean (`waiting` $\rightarrow$ `in_progress` $\rightarrow$ `completed`) secara real-time di halaman utama.
* **UC-P05: Melakukan Pembayaran Konsultasi & Menerima Kwitansi:** Pasien menyelesaikan penagihan biaya konsultasi/obat serta menerima kuitansi pelunasan berformat resmi.
* **UC-P06: Mengakses Rekam Medis Pasien (RME):** Pasien dapat melihat ringkasan hasil diagnosa, riwayat kunjungan lampau, resep obat yang diterima, dan tanda peringatan alergi aktif.

#### B. Aktor Dokter
* **UC-D01: Login Portal Dokter:** Dokter mengakses portal khusus melalui jalur otentikasi dokter terproteksi middleware `doctor`.
* **UC-D02: Melihat Antrean Pasien Harian (Terisolasi):** Dokter memantau daftar antrean pasien yang terdaftar secara eksklusif pada dirinya tanpa dapat melihat data antrean dokter lain (*privacy preservation*).
* **UC-D03: Melihat Data Demografis & Riwayat Pasien:** Dokter memeriksa data NIK, usia, golongan darah, nomor kontak, serta riwayat alergi dan penyakit terdahulu pasien sebelum tindakan.
* **UC-D04: Meninjau Riwayat Rekam Medis Lampau:** Dokter membuka linimasa riwayat pemeriksaan medis terdahulu yang pernah ditangani.
* **UC-D05: Mengisi Lembar SOAP & Tanda Vital Fisik:** Dokter mencatat data tanda vital (TD, Nadi, Suhu, SpO2, RR, BB, TB), menegakkan diagnosa medis (ICD-10), dan menyusun rencana tindakan klinis.
* **UC-D06: Mencari Data Obat untuk Resep:** Dokter memanfaatkan fitur integrasi data obat (`/doctor/obat/search`) untuk memasukkan obat ke dalam *plan* peresepan.
* **UC-D07: Menyelesaikan Sesi Pemeriksaan:** Dokter menyimpan rekam medis lengkap, yang secara otomatis memicu pembaruan status booking dan antrean menjadi selesai (`completed`).

#### C. Aktor Administrator (Keuangan & Operasional)
* **UC-A01: Memantau Dashboard Finansial Klinik:** Meninjau metrik penerimaan kotor (*gross revenue*), total transaksi, dan tagihan tertunda (*unpaid*).
* **UC-A02: Mengelola Transaksi Klinik:** Menambahkan entri transaksi layanan baru secara manual ke dalam basis data.
* **UC-A03: Mengelola Tagihan Pasien (*Invoices*):** Memeriksa daftar tagihan pasien yang terhubung dengan kode booking konsultasi.
* **UC-A04: Memantau Riwayat Pembayaran:** Menelusuri seluruh data log transaksi yang telah lunas beserta rincian metode pembayaran.
* **UC-A05: Mengunduh Laporan Pembayaran (Ekspor CSV):** Mengunduh berkas laporan pembayaran klinik ke format `.csv` untuk keperluan pembukuan eksternal.
* **UC-A06: Memantau Kalender Jadwal Praktik Klinik:** Meninjau jadwal operasional klinik lintas dokter dalam satu tampilan kalender visual.

#### D. Aktor Apoteker
* **UC-AP01: Mengakses Dashboard Apoteker:** Meninjau ringkasan ketersediaan farmasi dan status stok obat.
* **UC-AP02: Mengelola Master Data Obat (CRUD Obat):** Menambah jenis obat baru, mengubah rincian harga/satuan/kategori, memperbarui stok masuk, serta menghapus data obat.
* **UC-AP03: Manajemen Status Keaktifan Obat:** Menandai ketersediaan obat (`is_active = true/false`) agar dokter tidak meresepkan obat yang kedaluwarsa atau habis.

---

## BAB 4: DATABASE DESIGN

### 4.1 Perancangan Basis Data (Entity Relationship Diagram - ERD)
Struktur basis data Sistem Informasi Klinik Maristella dirancang berbasis prinsip normalisasi relasional untuk memastikan integritas data (*referential integrity*), konsistensi entitas, dan pencegahan redundansi data. Sistem terdiri dari **10 (sepuluh) entitas tabel utama**:

```
 [users] 1 -- 0..1 [patients] 1 ------- 0..* [bookings] 1 ----- 1 [queues]
    |                                            |
    | 1                                          | 1
    |                                            |
 [doctors] 1 -- 0..* [schedules]                 +----- 1 [medical_records]
    |                                            |
    | 0..*                                       +----- 0..* [payments]
    |                                                         |
 [polis] 1                                                    | (opsional ke
                                                              |  patients & doctors)
 [obats] (Katalog Mandiri Farmasi)
```

### 4.2 Spesifikasi dan Kamus Data Tabel

#### 1. Tabel `users` (Pusat Autentikasi Pengguna)
Menyimpan kredensial otentikasi login seluruh aktor sistem.
* `id` (BIGINT, Primary Key, Auto Increment)
* `name` (VARCHAR 255) — Nama lengkap pengguna.
* `email` (VARCHAR 255, Unique) — Alamat surel login.
* `role` (VARCHAR 255, Default: `'user'`) — Peran pengguna: `'user'`, `'admin'`, `'doctor'`, `'apoteker'`.
* `password` (VARCHAR 255) — Hash kata sandi terenkripsi (Bcrypt).
* `email_verified_at` (TIMESTAMP, Nullable)
* `remember_token` (VARCHAR 100, Nullable)
* `created_at`, `updated_at` (TIMESTAMP)

#### 2. Tabel `polis` (Master Data Spesialisasi Poliklinik)
Menyimpan klasifikasi poli pelayanan kesehatan di klinik.
* `id` (BIGINT, Primary Key, Auto Increment)
* `slug` (VARCHAR 255, Unique) — Kode pengenal poli (misal: `Cardiologist`, `Neurologist`, `Orthopedist`).
* `nama_poli` (VARCHAR 255) — Nama poli bahasa Indonesia (Kardiologi, Neurologi, Ortopedi).
* `name_en` (VARCHAR 255, Nullable) — Penamaan internasional.
* `deskripsi` (TEXT, Nullable) — Gambaran ruang lingkup penanganan poli.
* `created_at`, `updated_at` (TIMESTAMP)

#### 3. Tabel `doctors` (Master Data Profil Dokter)
Menyimpan profil profesional dokter dan tarif konsultasi per sesi.
* `id` (BIGINT, Primary Key, Auto Increment)
* `user_id` (BIGINT, Foreign Key $\rightarrow$ `users.id`, Nullable, Null on Delete)
* `poli_id` (BIGINT, Foreign Key $\rightarrow$ `polis.id`, Cascade on Delete)
* `nama_dokter` (VARCHAR 255) — Nama beserta gelar dokter.
* `spesialisasi` (VARCHAR 255) — Bidang subspesialis.
* `rating` (DECIMAL 2,1, Default: 4.8) — Skor kepuasan pasien (skala 5.0).
* `price` (VARCHAR 255, Default: `'$18.0'`) — Label tampilan harga.
* `price_num` (DECIMAL 8,2, Default: 18.00) — Nilai numerik tarif dasar untuk kalkulasi transaksi.
* `initials` (VARCHAR 10, Nullable) — Inisial nama dokter (misal: LW, AK, SM).
* `image` (VARCHAR 255, Nullable) — Direktori berkas foto dokter.
* `availability` (VARCHAR 255, Default: `'9AM - 8PM'`) — Jam operasional umum.
* `experience` (VARCHAR 255, Default: `'10 years'`) — Masa pengalaman kerja.
* `patients` (VARCHAR 255, Default: `'3.8k Up'`) — Rekam jejak total pasien.
* `created_at`, `updated_at` (TIMESTAMP)

#### 4. Tabel `schedules` (Jadwal Praktik Dokter)
Menyimpan kuota harian dan slot jadwal waktu praktik setiap dokter.
* `id` (BIGINT, Primary Key, Auto Increment)
* `doctor_id` (BIGINT, Foreign Key $\rightarrow$ `doctors.id`, Cascade on Delete)
* `hari` (VARCHAR 255) — Hari praktik (Senin - Sabtu).
* `jam_mulai` (VARCHAR 255) — Jam pembukaan sesi (misal: `'08:00'`).
* `jam_selesai` (VARCHAR 255) — Jam penutupan sesi (misal: `'12:00'`).
* `kuota` (INTEGER, Default: 20) — Kapasitas maksimal penerimaan pasien harian.
* `created_at`, `updated_at` (TIMESTAMP)

#### 5. Tabel `patients` (Profil Rekam Data Pasien)
Menyimpan informasi identitas kependudukan dan riwayat klinis statis pasien.
* `id` (BIGINT, Primary Key, Auto Increment)
* `user_id` (BIGINT, Foreign Key $\rightarrow$ `users.id`, Nullable, Null on Delete)
* `mrn` (VARCHAR 255, Unique) — Nomor Rekam Medis Pasien (format `#GM-XXXX`).
* `nik` (VARCHAR 255, Unique, Nullable) — Nomor Induk Kependudukan KTP.
* `full_name` (VARCHAR 255) — Nama lengkap pasien.
* `dob` (DATE, Nullable) — Tanggal lahir (*Date of Birth*).
* `gender` (VARCHAR 255, Nullable) — Jenis kelamin (Laki-laki / Perempuan).
* `blood_type` (VARCHAR 10, Nullable) — Golongan darah (A+, B+, AB+, O+, dll.).
* `phone` (VARCHAR 255, Nullable) — Nomor telepon/WhatsApp.
* `address` (TEXT, Nullable) — Alamat domisili.
* `allergies` (TEXT / JSON, Nullable) — Daftar riwayat alergi obat/makanan pasien.
* `past_illnesses` (TEXT / JSON, Nullable) — Riwayat penyakit masa lalu.
* `regular_meds` (TEXT / JSON, Nullable) — Obat rutin yang dikonsumsi.
* `created_at`, `updated_at` (TIMESTAMP)

#### 6. Tabel `bookings` (Transaksi Reservasi & Konsultasi Pasien)
Entitas sentral yang mencatat seluruh transaksi pendaftaran konsultasi klinis.
* `id` (BIGINT, Primary Key, Auto Increment)
* `booking_code` (VARCHAR 255, Unique) — Kode booking unik (`BK-YYYYMMDD-XXXX`).
* `patient_id` (BIGINT, Foreign Key $\rightarrow$ `patients.id`, Cascade on Delete)
* `doctor_id` (BIGINT, Foreign Key $\rightarrow$ `doctors.id`, Cascade on Delete)
* `schedule_id` (BIGINT, Foreign Key $\rightarrow$ `schedules.id`, Nullable, Null on Delete)
* `booking_date` (DATE) — Tanggal pelaksanaan kunjungan medis.
* `booking_time` (VARCHAR 255, Nullable) — Slot waktu yang dipilih (misal: `'09:00 AM'`).
* `booking_source` (VARCHAR 255, Default: `'online'`) — Kanal pendaftaran (`'online'` / `'walkin'`).
* `symptoms` (TEXT, Nullable) — Keluhan awal saat reservasi.
* `duration` (VARCHAR 255, Nullable) — Lama keluhan dirasakan (misal: `'1 - 3 Hari'`).
* `severity` (VARCHAR 255, Nullable) — Skala keparahan (`'Ringan'`, `'Sedang'`, `'Berat'`).
* `notes` (TEXT, Nullable) — Catatan tambahan dari pasien.
* `status` (VARCHAR 255, Default: `'pending'`) — Status reservasi: `'pending'`, `'confirmed'`, `'in_progress'`, `'completed'`, `'cancelled'`.
* `tariff_plan` (VARCHAR 255, Default: `'with_medicine'`) — Paket tarif: `'with_medicine'`, `'consultation_only'`.
* `total_amount` (DECIMAL 10,2, Default: 435.00) — Total kewajiban biaya layanan.
* `payment_status` (VARCHAR 255, Default: `'unpaid'`) — Status pelunasan: `'unpaid'` / `'paid'`.
* `created_at`, `updated_at` (TIMESTAMP)

#### 7. Tabel `queues` (Antrean Layanan Poliklinik)
Menyimpan urutan nomor antrean harian poliklinik hasil konfirmasi pendaftaran.
* `id` (BIGINT, Primary Key, Auto Increment)
* `booking_id` (BIGINT, Foreign Key $\rightarrow$ `bookings.id`, Unique, Cascade on Delete) — Relasi One-to-One dengan booking.
* `queue_number` (VARCHAR 255) — Nomor urut poliklinik (misal: `'B-01'`, `'A-02'`, `'C-01'`).
* `queue_date` (DATE) — Tanggal aktif antrean.
* `time_slot` (VARCHAR 255, Nullable) — Perkiraan waktu pelayanan.
* `status` (VARCHAR 255, Default: `'waiting'`) — Siklus antrean: `'waiting'`, `'in_progress'`, `'completed'`.
* `created_at`, `updated_at` (TIMESTAMP)

#### 8. Tabel `medical_records` (Rekam Medis Digital SOAP)
Menyimpan berkas pemeriksaan klinis dokter berbasis standar SOAP.
* `id` (BIGINT, Primary Key, Auto Increment)
* `booking_id` (BIGINT, Foreign Key $\rightarrow$ `bookings.id`, Unique, Cascade on Delete) — Relasi One-to-One dengan booking.
* `subjective` (TEXT, Nullable) — Keluhan utama, riwayat perjalanan penyakit, dan anamnesis mendalam.
* `objective` (TEXT / JSON, Nullable) — Data pemeriksaan fisik dan tanda vital: Tekanan Darah (BP), Nadi (HR), Laju Respirasi (RR), Suhu Tubuh, SpO2, BB, TB.
* `assessmen` (TEXT / JSON, Nullable) — Diagnosis utama (kode ICD-10), diagnosis sekunder, dan catatan evaluasi klinis.
* `plan` (TEXT / JSON, Nullable) — Terapi farmakologi (nama obat, dosis, frekuensi), tindakan klinis, dan instruksi tanggal kontrol ulang (*return date*).
* `created_at`, `updated_at` (TIMESTAMP)

#### 9. Tabel `payments` (Catatan Transaksi & Pembayaran Keuangan)
Menyimpan riwayat faktur tagihan, metode pembayaran kasir, serta verifikasi pembayaran.
* `id` (BIGINT, Primary Key, Auto Increment)
* `booking_id` (BIGINT, Foreign Key $\rightarrow$ `bookings.id`, Nullable, Null on Delete)
* `patient_id` (BIGINT, Foreign Key $\rightarrow$ `patients.id`, Nullable, Null on Delete)
* `doctor_id` (BIGINT, Foreign Key $\rightarrow$ `doctors.id`, Nullable, Null on Delete)
* `invoice_number` (VARCHAR 50, Unique) — Nomor faktur resmi (format `INV-YYYYMMDD-XXXX`).
* `patient_name` (VARCHAR 255, Nullable) — Nama pembayar/pasien.
* `doctor_name` (VARCHAR 255, Nullable) — Dokter pemeriksa.
* `service_type` (VARCHAR 255, Default: `'General Examination'`) — Jenis perlakuan medis.
* `payment_method` (VARCHAR 255, Default: `'cash'`) — Metode bayar: `'cash'`, `'credit_card'`, `'bank_transfer'`, `'qris'`.
* `tariff_plan` (VARCHAR 255, Default: `'with_medicine'`) — Rencana paket tarif.
* `amount` (DECIMAL 10,2) — Nilai kotor tagihan.
* `tax_amount` (DECIMAL 10,2, Default: 0) — Beban pajak.
* `discount_amount` (DECIMAL 10,2, Default: 0) — Potongan diskon.
* `net_amount` (DECIMAL 10,2) — Total bersih yang dibayarkan.
* `status` (VARCHAR 255, Default: `'paid'`) — Status pembayaran: `'paid'`, `'confirmed'`, `'pending'`, `'canceled'`.
* `notes` (TEXT, Nullable) — Catatan kasir.
* `paid_at` (TIMESTAMP, Nullable) — Waktu pelunasan transaksi.
* `created_at`, `updated_at` (TIMESTAMP)

#### 10. Tabel `obats` (Katalog Master Obat & Farmasi)
Menyimpan master inventori obat untuk pelayanan resep dan farmasi klinik.
* `id` (BIGINT, Primary Key, Auto Increment)
* `kode_obat` (VARCHAR 30, Unique) — Kode barang obat (misal: `'OBT-001'`).
* `nama_obat` (VARCHAR 150) — Nama generik atau paten obat (misal: *Amoxicillin 500mg*).
* `kategori` (VARCHAR 100) — Golongan obat (Antibiotik, Analgesik, Vitamin, Antihipertensi, dll.).
* `satuan` (VARCHAR 50) — Satuan kemasan (Tablet, Strip, Botol, Kapsul, Ampul).
* `harga` (DECIMAL 12,2, Default: 0) — Harga jual satuan per obat.
* `stok` (UNSIGNED INTEGER, Default: 0) — Jumlah persediaan fisik obat di farmasi klinik.
* `deskripsi` (TEXT, Nullable) — Indikasi penggunaan dan aturan umum obat.
* `is_active` (BOOLEAN, Default: `true`) — Status ketersediaan peredaran obat.
* `created_at`, `updated_at` (TIMESTAMP)

---

## BAB 5: SCREEN DESIGN (MOCKUP & IMPLEMENTASI UI)

Sistem Informasi Klinik Maristella dibangun menggunakan tema warna korporat klinis modern: **Navy Blue Gabe Medica (`#012D68`)** dipadukan dengan permukaan netral bersih (`#F8FAFC` dan `#FFFFFF`) untuk menciptakan kesan tenang, profesional, dan tepercaya bagi pasien dan tenaga medis.

### 5.1 Halaman Beranda Pasien (Home & Active Queue)
* **Tujuan Antarmuka:** Menyambut pasien dengan kartu salam ramah, memaparkan status antrean aktif (menampilkan nomor antrean, nama dokter, dan sesi waktu), kartu pengingat jadwal temu janji terdekat (*Next Appointment*), serta kartu kategori spesialisasi poli (*Kardiologi, Neurologi, Ortopedi*).
* **Komponen:**
  * Header navigasi dengan foto profil pengguna dan penanda peran.
  * Kartu antrean dinamis dengan status bertahap: *Menunggu Giliran*, *Sedang Diperiksa*, atau *Pemeriksaan Selesai*.
  * Tautan cepat menuju pendaftaran konsultasi baru dan kalender dokter.

### 5.2 Pencarian Dokter & Modal Booking Interaktif
* **Tujuan Antarmuka:** Memfasilitasi pasien dalam mencari dokter spesialis berdasarkan kriteria keahlian dan langsung membuka form pendaftaran reservasi tanpa meninggalkan halaman.
* **Komponen:**
  * Grid kartu profil dokter lengkap dengan foto, nama, gelar, rating bintang, tarif per sesi, dan tahun pengalaman kerja.
  * Modal formulir pendaftaran konsultasi klinis:
    * Input nama lengkap dan nomor telepon aktif.
    * Pemilihan dokter spesialis yang menangani.
    * Input gejala/keluhan utama pasien (*Symptoms*), durasi gejala (*1-3 hari, >1 minggu*), dan tingkat keparahan (*Ringan, Sedang, Berat*).
    * Pemilihan slot tanggal dan waktu kunjungan.
  * Tombol submit AJAX yang secara instan menerbitkan kode booking dan nomor antrean baru tanpa reload halaman secara penuh.

### 5.3 Kalender Jadwal Praktik Terpadu (`/calendar`)
* **Tujuan Antarmuka:** Menyajikan matriks jadwal dokter secara visual lintas hari dalam sepekan.
* **Komponen:**
  * Filter dropdown dokter spesialis (Dr. Lina Wijaya, Dr. Anaya Kapoor, Dr. Saran Mitchell).
  * Kisi-kisi (*grid*) slot waktu harian: slot yang telah terisi ditandai secara visual dengan warna abu-abu bertuliskan **"Sudah Terbook"** untuk mencegah konflik reservasi ganda.
  * Widget mini-kalender di panel samping kanan untuk navigasi cepat tanggal operasional.

### 5.4 Portal Dokter & Lembar Pemeriksaan SOAP (`/doctor`)
* **Tujuan Antarmuka:** Workspace dokter terpadu untuk melakukan evaluasi klinis pasien.
* **Komponen:**
  * **Sidebar Antrean:** Daftar antrean pasien spesifik dokter yang bersangkutan beserta indikator status (*Waiting*, *In Progress*, *Completed*).
  * **Tab Demografis Pasien:** Menampilkan identitas pasien lengkap (NIK, No. RM, usia, golongan darah, nomor kontak) disertai kartu peringatan merah mencolok (*Alert Box*) untuk riwayat alergi berat.
  * **Tab Riwayat Medis:** Linimasa riwayat kunjungan lampau pasien bersama diagnosa dan terapi sebelumnya.
  * **Formulir SOAP Interaktif:**
    * *Subjective:* Keluhan utama dan anamnesis lanjutan.
    * *Objective:* Grid input tanda vital medis (Tekanan Darah, Denyut Nadi, Laju Nafas, Suhu, SpO2, Berat Badan, Tinggi Badan) dan catatan pemeriksaan fisik lokal.
    * *Assessment:* Kolom input diagnosis utama dengan standar kode ICD-10 dan diagnosis sekunder.
    * *Plan:* Kolom rencana terapi dan peresepan obat yang dilengkapi fitur auto-search obat dari database, tindakan klinis, dan tanggal jadwal kontrol ulang.
  * **Tombol Selesaikan Pemeriksaan:** Menyimpan berkas SOAP ke basis data dan menyelesaikan antrean secara otomatis.

### 5.5 Halaman Rekam Medis Pasien (`/rekam-medis`)
* **Tujuan Antarmuka:** Halaman personal pasien untuk melihat catatan kesehatan digital pribadi.
* **Komponen:**
  * Ringkasan nomor rekam medis unik (*MRN*) dan identitas pasien.
  * Tabulasi riwayat:
    * **Tab Riwayat Klinis:** Catatan kunjungan, nama dokter pemeriksa, tanggal periksa, dan diagnosa akhir.
    * **Tab Resep Obat:** Rincian obat yang pernah diresepkan beserta aturan pakai.
    * **Tab Peringatan Medis:** Riwayat alergi obat dan penyakit kronis penyerta untuk menjaga kewaspadaan pasien.

### 5.6 Portal Admin Keuangan & Transaksi (`/admin`)
* **Tujuan Antarmuka:** Pusat kendali keuangan dan akuntansi operasional klinik.
* **Komponen:**
  * **Kartu Metrik KPI:** Total pendapatan kotor (*Gross Revenue*), total transaksi tercatat, serta akumulasi tagihan belum lunas (*Unpaid*).
  * **Daftar Tagihan & Riwayat Pembayaran:** Tabel komprehensif memuat Nomor Invoice, Nama Pasien, Dokter yang Bertugas, Jenis Layanan, Metode Bayar, Tanggal Pelunasan, dan Status Faktur.
  * **Fitur Ekspor CSV:** Tombol ekspor satu klik (`/admin/export-csv`) untuk mengunduh log transaksi kasir ke dalam format spreadsheet.
  * **Form Input Transaksi:** Modal untuk memasukkan entri transaksi klinik secara langsung ke basis data.

### 5.7 Portal Apoteker & Kelola Inventori Obat (`/apoteker` & `/obat`)
* **Tujuan Antarmuka:** Manajemen persediaan obat-obatan dan suplai farmasi klinik.
* **Komponen:**
  * **Tabel Master Obat:** Menampilkan Kode Obat, Nama Obat, Kategori Farmasi, Satuan, Harga Jual, dan Jumlah Stok Tersedia.
  * **Form Tambah / Edit Obat:** Antarmuka CRUD untuk memutakhirkan ketersediaan stok fisik dan harga satuan.
  * **Status Ketersediaan:** Tombol kendali *toggle* status aktif/non-aktif untuk menjaga peresepan dokter tetap selaras dengan stok fisik di instalasi farmasi.

### 5.8 Halaman Login & Multi-Role Authentication Switcher (`/login`)
* **Tujuan Antarmuka:** Gerbang autentikasi terpusat yang aman dengan fasilitas *Quick-Login Role Switcher* untuk kemudahan demonstrasi pengujian sistem.
* **Komponen:**
  * Form login standar (*Email* dan *Password*).
  * Panel pintasan cepat (*Quick Login*) untuk beralih peran instan:
    * **Pasien:** `user@aihealth.com`
    * **Admin:** `admin@aihealth.com`
    * **Dokter Kardiologi:** `lina@aihealth.com`
    * **Dokter Neurologi:** `anaya@aihealth.com`
    * **Apoteker:** `apoteker@aihealth.com`

---

## BAB 6: ARSITEKTUR SISTEM & ENDPOINT INTERAKSI DATA

Sistem Informasi Klinik Maristella dibangun menggunakan arsitektur web modern berbasis MVC Laravel di mana antarmuka Blade berkomunikasi secara responsif dengan Controller backend melalui kombinasi rute HTTP web dan asynchronous AJAX/JSON requests.

### 6.1 Pemetaan Rute dan Fungsi Controller

| Rute Web | Metode HTTP | Controller & Action | Middleware | Fungsi Utama |
| :--- | :--- | :--- | :--- | :--- |
| `/login` | `GET`, `POST` | `AuthController@showLoginForm`, `login` | `guest` | Autentikasi pengguna |
| `/login/dokter` | `GET` | `AuthController@showDoctorLoginForm` | `guest` | Portal login khusus dokter |
| `/quick-login/{role}` | `GET` | `AuthController@quickLogin` | - | Pintasan login pengujian peran |
| `/logout` | `POST` | `AuthController@logout` | `auth` | Mengakhiri sesi login |
| `/` | `GET` | `HomeController@index` | - | Beranda antrean & booking pasien |
| `/appointment-status` | `GET` | `HomeController@getAppointmentStatus` | - | JSON status antrean aktif |
| `/booking` | `POST` | `HomeController@storeBooking` | - | Input pendaftaran booking via JSON |
| `/payment` | `POST` | `HomeController@processPayment` | - | Pemrosesan pembayaran & invoice |
| `/calendar` | `GET` | `HomeController@calendar` | - | Tampilan kalender jadwal praktik |
| `/rekam-medis` | `GET` | `HomeController@medicalRecords` | - | Lembar rekam medis pasien (RME) |
| `/doctor` | `GET` | `DoctorController@index` | `doctor` | Workspace pemeriksaan dokter |
| `/doctor/soap/{queueId}` | `POST` | `DoctorController@saveSoap` | `doctor` | Simpan rekam medis SOAP |
| `/doctor/obat/search` | `GET` | `DoctorController@searchObat` | `doctor` | Pencarian master obat live |
| `/admin` | `GET` | `AdminController@index` | `admin` | Dashboard finansial klinik |
| `/admin/transactions` | `GET` | `AdminController@transactions` | `admin` | Daftar log transaksi |
| `/admin/invoices` | `GET` | `AdminController@invoices` | `admin` | Daftar tagihan pasien |
| `/admin/payment-history`| `GET` | `AdminController@paymentHistory` | `admin` | Rekapitulasi pembayaran lunas |
| `/admin/revenue` | `GET` | `AdminController@revenue` | `admin` | Laporan pendapatan berkala |
| `/admin/reports` | `GET` | `AdminController@reports` | `admin` | Pusat laporan manajerial |
| `/admin/calendar` | `GET` | `AdminController@calendar` | `admin` | Kalender dokter versi admin |
| `/admin/export-csv` | `GET` | `AdminController@exportCsv` | `admin` | Download rekapitulasi file CSV |
| `/admin/transaction` | `POST` | `AdminController@storeTransaction` | `admin` | Input transaksi manual kasir |
| `/apoteker` | `GET` | `ApotekerController@index` | `apoteker` | Dashboard inventori farmasi |
| `/obat` | Resource | `ObatController` (CRUD) | `obat.manager` | Kelola master katalog obat |

### 6.2 Mekanisme Keamanan dan Isolasi Data
1. **Middleware Perlindungan Peran:**
   * `AdminMiddleware`: Memverifikasi kepemilikan peran `admin`. Akses tidak sah secara otomatis dialihkan kembali ke halaman utama dengan pesan penolakan.
   * `DoctorMiddleware`: Memeriksa bahwa pengguna yang masuk memiliki peran `doctor` dan memiliki catatan dokter terikat pada tabel `doctors`.
   * `ApotekerMiddleware` & `ObatManagerMiddleware`: Membatasi wewenang perubahan data farmasi hanya untuk pengguna apoteker atau admin.
2. **Aturan Privasi Medis Dokter (*Privacy Preservation*):**
   Pada `DoctorController`, data antrean disaring secara ketat berdasarkan klausa relasional:
   ```php
   Queue::whereHas('booking', function ($q) use ($doctor) {
       $q->where('doctor_id', $doctor->id);
   })->get();
   ```
   Aturan ini menjamin seorang dokter tidak dapat melihat rekam medis atau antrean pasien yang sedang diperiksa oleh dokter lain di poliklinik berbeda.

---

## BAB 7: PENGUJIAN SISTEM (AUTOMATED TEST CASES)

Pengujian kualitas perangkat lunak Sistem Informasi Klinik Maristella dilakukan secara otomatis menggunakan framework pengujian terintegrasi **PHPUnit** pada Laravel. Rangkaian pengujian mencakup verifikasi logika bisnis, integritas data, keamanan hak akses, dan pengujian siklus hidup layanan ujung-ke-ujung (*end-to-end lifecycle*).

### 7.1 Daftar Skenario Pengujian Otomatis

Rangkaian unit dan feature test diimplementasikan pada direktori `tests/Feature/`:

#### 1. Berkas Pengujian: `AuthRoleTest.php` (Pengujian Keamanan & Autentikasi)
* `test_login_page_renders_successfully`: Memastikan halaman login terbuka dengan status HTTP 200 OK.
* `test_user_login_redirects_to_home`: Memastikan pengguna dengan peran pasien dialihkan ke beranda pasien setelah login berhasil.
* `test_admin_login_redirects_to_admin_portal`: Memastikan akun administrator diarahkan langsung ke dashboard admin.
* `test_guest_is_redirected_from_admin_dashboard`: Memastikan pengguna anonim (tamu) yang mencoba mengakses portal admin ditolak dan dialihkan ke login.
* `test_regular_user_cannot_access_admin_dashboard`: Memastikan pengguna dengan peran biasa tidak memiliki izin membuka dashboard keuangan admin (akses terproteksi).
* `test_quick_login_for_user` & `test_quick_login_for_admin`: Memastikan pengalihan otomatis fitur quick-login berjalan valid sesuai sesi peran.
* `test_logout_redirects_to_login`: Memastikan proses keluar sesi menghapus token otentikasi dan mengembalikan pengguna ke halaman awal.

#### 2. Berkas Pengujian: `DoctorPortalTest.php` (Pengujian Portal Dokter & Rekam Medis)
* `test_doctor_login_page_renders_successfully`: Memverifikasi ketersediaan portal login khusus dokter (`/login/dokter`).
* `test_doctor_can_login_and_is_redirected_to_doctor_dashboard`: Memverifikasi kredensial dokter valid mengarahkan sesi ke `/doctor`.
* `test_guest_cannot_access_doctor_portal` & `test_standard_user_cannot_access_doctor_portal`: Memverifikasi ketegasan `DoctorMiddleware` dalam memblokir tamu dan pasien biasa dari portal dokter.
* `test_doctor_dashboard_renders_all_4_use_cases`: Memverifikasi tampilan dashboard dokter memuat keempat fungsionalitas utama: Daftar Antrean, Profil Pasien, Riwayat Medis, dan Lembar SOAP.
* `test_doctor_can_save_soap_record`: Memverifikasi dokter dapat menyimpan data tanda vital, diagnosis ICD-10, dan resep obat ke tabel `medical_records`, serta memastikan status antrean berubah menjadi `completed`.

#### 3. Berkas Pengujian: `DatabaseIntegrationTest.php` (Integritas Basis Data & Sinkronisasi)
* `test_database_seeder_populates_all_tables`: Memastikan seeder berhasil mengisi 10 tabel basis data dengan relasi yang konsisten.
* `test_patient_can_store_booking_via_api`: Memverifikasi request `POST /booking` berhasil membuat entitas pasien baru, entitas booking, dan nomor antrean poliklinik secara terpadu.
* `test_appointment_mode_automatically_flips_when_doctor_examines`: Menguji pergantian mode tampilan antrean secara dinamis saat dokter memulai dan menyelesaikan pemeriksaan.
* `test_admin_dashboard_riwayat_pembayaran_and_csv_export`: Menguji rekapitulasi data pembayaran pada dashboard admin serta memastikan endpoint `/admin/export-csv` menghasilkan stream file CSV yang valid.
* `test_admin_can_store_transaction_integrated_with_database`: Memverifikasi fungsionalitas pencatatan transaksi manual kasir terhubung langsung dengan tabel `payments`.
* `test_each_doctor_has_account_isolated_patient_history_and_admin_sees_all`: Menguji aturan isolasi privasi medis dokter dan memastikan administrator memiliki hak tinjau menyeluruh.

#### 4. Berkas Pengujian: `PatientDoctorPaymentLifecycleTest.php` (Pengujian Siklus End-to-End)
* `test_complete_five_step_lifecycle_works_end_to_end`: Menguji siklus operasional 5 tahap klinis tanpa terputus:
  1. *Step 1:* Pasien melakukan booking online dan menerima nomor antrean.
  2. *Step 2:* Antrean tampil di portal dokter yang bersangkutan.
  3. *Step 3:* Dokter memeriksa pasien dan menyimpan data rekam medis SOAP lengkap.
  4. *Step 4:* Pasien/Kasir menyelesaikan tagihan pembayaran via endpoint `/payment` dan menerima faktur invoice.
  5. *Step 5:* Transaksi tercatat lunas di laporan keuangan admin dan riwayat rekam medis tampil di portal pasien.

---

## BAB 8: KESIMPULAN DAN SARAN

### 8.1 Kesimpulan
Berdasarkan perancangan, implementasi, dan pengujian yang telah dilaksanakan pada proyek pengembangan sistem ini, dapat ditarik kesimpulan sebagai berikut:
1. **Penyelesaian Permasalahan Operasional:** Sistem Informasi Klinik Maristella berhasil mentransformasikan proses manual konvensional pendaftaran pasien, pencatatan antrean, dan rekam medis kertas menjadi alur digital yang terpusat dan terotomatisasi.
2. **Kesesuaian Arsitektur dan Data:** Sistem berhasil menerapkan arsitektur MVC berbasis Laravel dengan skema basis data relasional 10 entitas tabel yang terhubung secara konsisten, mencakup relasi otentikasi pengguna, data dokter, poli, jadwal, booking, antrean, rekam medis SOAP, pencatatan transaksi pembayaran, dan katalog obat.
3. **Pemisahan Peran dan Keamanan Privasi:** Implementasi multi-role authentication (`patient`, `doctor`, `admin`, `apoteker`) berjalan dengan efektif. Portal dokter berhasil menerapkan isolasi privasi data medis antrean, dan portal admin keuangan dilengkapi modul pelaporan finansial serta ekspor dokumen CSV.
4. **Verifikasi Kualitas:** Seluruh komponen inti fungsionalitas telah divalidasi melalui rangkaian pengujian otomatis (*automated feature tests*) dengan pengujian siklus ujung-ke-ujung (*end-to-end lifecycle*) yang membuktikan sistem bebas dari kegagalan logika kritis.

### 8.2 Saran Pengembangan Lanjutan
Untuk meningkatkan kematangan sistem pada pengembangan rilis berikutnya, disarankan beberapa poin perbaikan:
1. **Integrasi Notifikasi Real-time:** Menambahkan integrasi pengiriman notifikasi pemanggilan nomor antrean dan kwitansi pembayaran via WhatsApp Gateway atau SMS API langsung ke nomor telepon pasien.
2. **Validasi Kuota Jadwal Dokter Real-time:** Memperketat validasi pemilihan tanggal dan jam konsultasi pada form booking agar secara ketat memotong kuota harian pada tabel `schedules` serta mencegah pasien memilih tanggal lampau.
3. **Integrasi Rekam Medis Nasional (SatuSehat Kemenkes):** Mengembangkan standarisasi format rekam medis elektronik agar kompatibel dengan FHIR (*Fast Healthcare Interoperability Resources*) untuk integrasi platform SatuSehat Kementerian Kesehatan Republik Indonesia.
4. **Modul Pengurangan Stok Farmasi Otomatis:** Mengintegrasikan resep obat yang disimpan dokter pada rekam medis SOAP agar otomatis memotong jumlah persediaan stok pada tabel `obats` di instalasi farmasi apotek.
