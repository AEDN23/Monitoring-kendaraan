# Database Schema — Vehicle Monitoring

> Status: Draft awal  
> Tujuan: rancangan database untuk aplikasi monitoring kendaraan pribadi yang disiapkan agar dapat berkembang menjadi multi-user dan terhubung ke web serta mobile melalui backend/API yang sama.

## 1. Prinsip desain

- Gunakan PostgreSQL.
- Setiap tabel utama menggunakan primary key `BIGINT UNSIGNED`.
- Simpan waktu transaksi dan waktu pencatatan secara terpisah jika keduanya memiliki makna berbeda.
- Simpan nilai harga pada transaksi sebagai snapshot. Perubahan harga BBM atau harga komponen di kemudian hari tidak boleh mengubah transaksi lama.
- Kilometer kendaraan tidak boleh dianggap selalu sama dengan urutan input. Validasi dan perhitungan harus mengikuti tanggal/waktu transaksi.
- Data bisnis milik user harus memiliki relasi kepemilikan yang jelas.
- Gunakan `created_at` dan `updated_at`. Untuk data yang perlu dipulihkan, pertimbangkan `deleted_at` (soft delete).
- Nilai uang disimpan sebagai `DECIMAL`, bukan `FLOAT`.
- Satuan jarak disimpan dalam kilometer dan volume BBM dalam liter.

## 2. Entitas dan tabel

### 2.1 `users`

Menyimpan akun pengguna. Disiapkan untuk penggunaan multi-user.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | ID user |
| name | VARCHAR(120) | Nama pengguna |
| email | VARCHAR(190) UNIQUE | Email login |
| password | VARCHAR(255) | Password hash |
| email_verified_at | TIMESTAMP NULL | Verifikasi email, opsional |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diubah |
| deleted_at | TIMESTAMP NULL | Soft delete, opsional |

### 2.2 `vehicles`

Menyimpan data kendaraan milik user.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | ID kendaraan |
| user_id | BIGINT UNSIGNED FK | Pemilik kendaraan |
| name | VARCHAR(100) | Nama panggilan kendaraan |
| vehicle_type | ENUM('motorcycle','car','other') | Jenis kendaraan |
| brand | VARCHAR(100) NULL | Merek |
| model | VARCHAR(100) NULL | Model |
| production_year | SMALLINT UNSIGNED NULL | Tahun produksi |
| license_plate | VARCHAR(20) NULL | Nomor polisi |
| initial_odometer_km | DECIMAL(12,1) | Kilometer awal saat kendaraan didaftarkan |
| initial_odometer_date | DATE | Tanggal kilometer awal berlaku |
| notes | TEXT NULL | Catatan |
| is_active | BOOLEAN | Status aktif |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diubah |
| deleted_at | TIMESTAMP NULL | Soft delete |

Relasi: `vehicles.user_id -> users.id`.

### 2.3 `fuel_types`

Master jenis BBM, misalnya Pertalite dan Pertamax.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | ID jenis BBM |
| name | VARCHAR(80) UNIQUE | Nama BBM |
| is_active | BOOLEAN | Bisa dipilih atau tidak |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diubah |

### 2.4 `fuel_price_settings`

Menyimpan harga BBM yang berlaku berdasarkan jenis BBM, wilayah, dan periode. Harga dapat dimasukkan manual atau diperbarui melalui integrasi sumber harga jika kelak tersedia.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | ID harga |
| fuel_type_id | BIGINT UNSIGNED FK | Jenis BBM |
| region | VARCHAR(120) NULL | Wilayah harga berlaku |
| price_per_liter | DECIMAL(12,2) | Harga per liter |
| source_type | ENUM('manual','automatic') | Sumber harga |
| effective_from | DATETIME | Mulai berlaku |
| effective_until | DATETIME NULL | Akhir berlaku |
| created_by | BIGINT UNSIGNED FK NULL | User/admin yang mengatur |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diubah |

Catatan:
- Harga aktif dipilih berdasarkan jenis BBM, wilayah (jika digunakan), dan tanggal transaksi.
- Harga pada transaksi BBM tetap disimpan sebagai snapshot.
- Integrasi otomatis belum ditentukan dan tidak boleh diasumsikan tersedia sebelum sumber/API harga dipilih.

### 2.5 `fuel_transactions`

Mencatat setiap pengisian BBM.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | ID transaksi |
| vehicle_id | BIGINT UNSIGNED FK | Kendaraan |
| fuel_type_id | BIGINT UNSIGNED FK | Jenis BBM |
| transaction_at | DATETIME | Tanggal/waktu pengisian sebenarnya |
| recorded_at | TIMESTAMP | Waktu data dicatat ke aplikasi |
| odometer_km | DECIMAL(12,1) | KM kendaraan saat pengisian |
| total_amount | DECIMAL(14,2) | Total pembayaran |
| price_per_liter | DECIMAL(12,2) | Harga per liter saat transaksi |
| volume_liter | DECIMAL(10,3) | Jumlah liter |
| price_source | ENUM('manual','configured') | Asal harga yang digunakan |
| station_name | VARCHAR(150) NULL | Nama SPBU/tempat isi |
| notes | TEXT NULL | Catatan |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diubah |
| deleted_at | TIMESTAMP NULL | Soft delete |

Relasi:
- `fuel_transactions.vehicle_id -> vehicles.id`
- `fuel_transactions.fuel_type_id -> fuel_types.id`

Aturan perhitungan:
- Jika user memasukkan total pembayaran dan harga/liter: `volume_liter = total_amount / price_per_liter`.
- Jika user mengubah harga/liter secara manual, volume dihitung ulang.
- Simpan nilai harga dan volume hasil transaksi agar riwayat tidak berubah saat harga master diperbarui.
- Validasi bahwa total pembayaran, harga/liter, volume, dan odometer bernilai positif.
- Transaksi boleh dicatat belakangan dengan `transaction_at` sesuai tanggal pengisian sebenarnya; `recorded_at` tetap menunjukkan kapan input dilakukan.

### 2.6 `maintenance_items`

Master komponen atau jenis perawatan yang dapat dipantau.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | ID komponen |
| name | VARCHAR(120) | Contoh: Oli mesin, oli gardan, ban |
| category | VARCHAR(80) NULL | Contoh: mesin, rem, ban |
| default_interval_km | INT UNSIGNED NULL | Interval KM bawaan, opsional |
| default_interval_days | INT UNSIGNED NULL | Interval waktu bawaan, opsional |
| tracking_mode | ENUM('km','time','km_or_time','condition') | Cara pemantauan |
| applies_to | ENUM('motorcycle','car','both','other') | Jenis kendaraan yang sesuai |
| description | TEXT NULL | Keterangan |
| is_active | BOOLEAN | Status aktif |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diubah |

Catatan: nilai interval bawaan hanya menjadi saran. User dapat mengatur interval sesuai kendaraan dan rekomendasi pabrikan/bengkel.

### 2.7 `vehicle_maintenance_schedules`

Konfigurasi jadwal perawatan untuk setiap kendaraan.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | ID jadwal |
| vehicle_id | BIGINT UNSIGNED FK | Kendaraan |
| maintenance_item_id | BIGINT UNSIGNED FK | Komponen |
| interval_km | INT UNSIGNED NULL | Interval KM |
| interval_days | INT UNSIGNED NULL | Interval hari |
| reminder_before_km | INT UNSIGNED NULL | Peringatan sebelum jatuh tempo KM |
| reminder_before_days | INT UNSIGNED NULL | Peringatan sebelum jatuh tempo waktu |
| last_service_record_id | BIGINT UNSIGNED FK NULL | Riwayat perawatan terakhir |
| last_service_odometer_km | DECIMAL(12,1) NULL | KM saat perawatan terakhir |
| last_service_at | DATETIME NULL | Tanggal perawatan terakhir |
| is_active | BOOLEAN | Jadwal aktif |
| notes | TEXT NULL | Catatan |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diubah |

Relasi:
- `vehicle_maintenance_schedules.vehicle_id -> vehicles.id`
- `vehicle_maintenance_schedules.maintenance_item_id -> maintenance_items.id`
- `last_service_record_id` mengacu ke riwayat perawatan, setelah tabel riwayat dibuat.

Aturan:
- Target KM berikutnya = `last_service_odometer_km + interval_km`.
- Target tanggal berikutnya = `last_service_at + interval_days`.
- Jika salah satu batas yang dikonfigurasi tercapai, jadwal dapat berstatus segera atau terlambat.
- Saat perawatan baru dicatat, titik awal KM dan tanggal untuk jadwal terkait diperbarui dari transaksi tersebut.
- Jika hanya satu jenis interval yang diisi, hanya jenis interval itu yang dipakai.
- Perawatan berbasis kondisi (misalnya kondisi ban/kampas rem) perlu pemeriksaan manual, bukan dianggap pasti aus berdasarkan KM.

### 2.8 `maintenance_records`

Mencatat kejadian perawatan yang benar-benar dilakukan.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | ID riwayat perawatan |
| vehicle_id | BIGINT UNSIGNED FK | Kendaraan |
| maintenance_item_id | BIGINT UNSIGNED FK NULL | Komponen utama, opsional jika transaksi berisi banyak item |
| service_at | DATETIME | Tanggal/waktu perawatan |
| recorded_at | TIMESTAMP | Waktu input |
| odometer_km | DECIMAL(12,1) | KM saat perawatan |
| workshop_name | VARCHAR(150) NULL | Bengkel/tempat perawatan |
| notes | TEXT NULL | Catatan |
| total_parts_cost | DECIMAL(14,2) DEFAULT 0 | Total harga barang |
| total_labor_cost | DECIMAL(14,2) DEFAULT 0 | Total jasa |
| total_cost | DECIMAL(14,2) DEFAULT 0 | Total transaksi |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diubah |
| deleted_at | TIMESTAMP NULL | Soft delete |

### 2.9 `maintenance_record_items`

Detail komponen/barang dalam satu transaksi perawatan. Tabel ini memungkinkan satu kunjungan bengkel mencatat beberapa pekerjaan.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | ID detail |
| maintenance_record_id | BIGINT UNSIGNED FK | Transaksi perawatan |
| maintenance_item_id | BIGINT UNSIGNED FK NULL | Komponen yang dipantau |
| item_name | VARCHAR(150) | Nama barang/pekerjaan sebagai snapshot |
| quantity | DECIMAL(10,2) DEFAULT 1 | Jumlah |
| unit_price | DECIMAL(14,2) DEFAULT 0 | Harga barang satuan |
| parts_cost | DECIMAL(14,2) DEFAULT 0 | Total biaya barang |
| labor_cost | DECIMAL(14,2) DEFAULT 0 | Biaya jasa untuk item ini |
| notes | TEXT NULL | Catatan |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diubah |

Aturan:
- `parts_cost` dapat dihitung dari `quantity * unit_price`, tetapi nilai final disimpan agar transaksi tetap stabil.
- Total biaya transaksi merupakan penjumlahan biaya barang dan jasa seluruh detail.
- Jika satu transaksi mengganti oli dan kampas rem, setiap item dapat memperbarui jadwal perawatan masing-masing.
- Setelah transaksi disimpan, jadwal komponen yang sesuai di-reset menggunakan tanggal dan KM transaksi perawatan tersebut.

### 2.10 `expenses` (opsional untuk fase berikutnya)

Tabel pengeluaran umum di luar BBM dan detail servis, misalnya parkir, pajak, cuci kendaraan, atau aksesori. Hindari mencatat biaya BBM/servis dua kali jika keduanya sudah punya tabel transaksi khusus.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | ID pengeluaran |
| vehicle_id | BIGINT UNSIGNED FK | Kendaraan |
| expense_date | DATETIME | Tanggal pengeluaran |
| category | VARCHAR(80) | Kategori |
| description | VARCHAR(255) | Keterangan |
| amount | DECIMAL(14,2) | Nominal |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diubah |

### 2.11 `notification_logs` (fase notifikasi)

Mencatat notifikasi yang dibuat/dikirim agar notifikasi tidak dikirim berulang tanpa kontrol.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | ID notifikasi |
| user_id | BIGINT UNSIGNED FK | Penerima |
| vehicle_id | BIGINT UNSIGNED FK | Kendaraan |
| schedule_id | BIGINT UNSIGNED FK NULL | Jadwal terkait |
| notification_type | VARCHAR(60) | Jenis notifikasi |
| title | VARCHAR(180) | Judul |
| message | TEXT | Isi |
| triggered_at | DATETIME | Waktu pemicu |
| read_at | DATETIME NULL | Waktu dibaca |
| delivery_channel | VARCHAR(40) NULL | Web, email, push, dan lain-lain |
| created_at | TIMESTAMP | Waktu dibuat |

## 3. Relasi utama

```text
users
  └── vehicles
       ├── fuel_transactions
       │    └── fuel_types
       ├── vehicle_maintenance_schedules
       │    └── maintenance_items
       ├── maintenance_records
       │    └── maintenance_record_items
       │         └── maintenance_items
       ├── expenses
       └── notification_logs

fuel_types
  └── fuel_price_settings
```

## 4. Indeks yang disarankan

- `vehicles`: index `(user_id, is_active)`, unique opsional `(user_id, license_plate)` jika nomor polisi diisi.
- `fuel_transactions`: index `(vehicle_id, transaction_at)`, index `(vehicle_id, odometer_km)`.
- `fuel_price_settings`: index `(fuel_type_id, region, effective_from, effective_until)`.
- `vehicle_maintenance_schedules`: unique `(vehicle_id, maintenance_item_id)` jika satu jadwal aktif per komponen/kendaraan.
- `maintenance_records`: index `(vehicle_id, service_at)`, index `(vehicle_id, odometer_km)`.
- `maintenance_record_items`: index `(maintenance_record_id)`, index `(maintenance_item_id)`.
- `notification_logs`: index `(user_id, read_at)`, index `(schedule_id, triggered_at)`.

## 5. Aturan validasi dan konsistensi data

1. Odometer tidak boleh negatif.
2. Sistem memberi peringatan jika KM transaksi lebih kecil daripada transaksi sebelumnya dalam urutan tanggal.
3. Input transaksi mundur tanggal diperbolehkan, tetapi dapat memengaruhi urutan dan hasil perhitungan histori. Sistem harus menghitung ulang proyeksi KM/jadwal yang terdampak.
4. Jangan mengubah `recorded_at` ketika user mengedit tanggal transaksi.
5. Harga BBM dan harga barang disimpan sebagai snapshot transaksi.
6. Penghapusan transaksi perawatan tidak boleh diam-diam meninggalkan jadwal yang salah. Jadwal perlu dihitung ulang dari riwayat perawatan valid terakhir.
7. Status jadwal sebaiknya dihitung dari data terbaru, bukan disimpan sebagai nilai permanen yang mudah kedaluwarsa.
8. Simpan zona waktu aplikasi secara konsisten; tampilkan waktu sesuai zona waktu user.
9. Perhitungan interval bulanan perlu menetapkan aturan tanggal akhir bulan (misalnya tanggal 31 ke bulan yang tidak memiliki tanggal 31). Aturan ini perlu diputuskan sebelum implementasi.

## 6. Catatan keputusan yang belum final

- Apakah harga BBM otomatis memakai sumber nasional atau berdasarkan wilayah/SPBU.
- Apakah kendaraan menggunakan satuan KM bulat atau mendukung satu angka desimal.
- Ambang status “Segera” untuk KM dan waktu.
- Aturan perhitungan interval bulanan pada tanggal akhir bulan.
- Apakah biaya umum (`expenses`) masuk MVP atau fase berikutnya.
- Kanal notifikasi MVP: notifikasi di dashboard/web dahulu atau email juga.
- Apakah user dapat membagikan satu kendaraan kepada user lain pada fase multi-user lanjutan.

## 7. Catatan implementasi

Dokumen ini adalah rancangan konseptual, belum merupakan migration SQL final. Sebelum migration dibuat, validasi kembali tipe data, foreign key, kebijakan soft delete, dan aturan reset jadwal. Untuk Laravel, setiap tabel nantinya dibuat melalui migration dan relasi model Eloquent.
