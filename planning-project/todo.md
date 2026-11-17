# TODO — Vehicle Monitoring

Daftar pekerjaan pengembangan. Status awal: belum dikerjakan.

## 1. Analisis kebutuhan
- [ ] Finalisasi Business Requirement Document (BRD).
- [ ] Tetapkan aturan status Aman, Segera, Terlambat, dan Perlu Pemeriksaan.
- [ ] Tetapkan ambang pengingat KM dan waktu.
- [ ] Tetapkan aturan tanggal jatuh tempo untuk interval bulanan.
- [ ] Tentukan sumber harga BBM otomatis atau putuskan manual untuk MVP.
- [ ] Putuskan apakah upload foto nota masuk MVP.
- [ ] Putuskan apakah pengeluaran umum masuk MVP.

## 2. Perancangan sistem
- [ ] Buat use case diagram.
- [ ] Buat flowchart alur utama.
- [ ] Buat ERD berdasarkan `database_schema.md`.
- [ ] Review normalisasi dan relasi database.
- [ ] Tentukan strategi validasi kilometer dan transaksi backdate.
- [ ] Rancang mekanisme perhitungan ulang jadwal setelah transaksi historis diedit/dihapus.
- [ ] Rancang struktur API agar web dan mobile menggunakan backend yang sama.

## 3. Persiapan project
- [x] Tentukan versi Laravel dan PHP yang kompatibel (Laravel 13 & PHP 8.5).
- [x] Buat project Laravel.
- [x] Konfigurasi PostgreSQL dan environment.
- [x] Siapkan styling (Tailwind CSS v4 & responsive layout).
- [x] Siapkan autentikasi (Login, Register, Logout).
- [x] Buat migration dan model berdasarkan schema final (10 migrasi & 9 model).
- [x] Buat seeder master jenis BBM dan komponen perawatan.

## 4. Pengembangan MVP web
- [x] Buat login/logout.
- [x] Buat dashboard (ringkasan statistik & status jadwal perawatan).
- [x] Buat CRUD kendaraan (index, create, edit, show, delete).
- [x] Buat pemantauan kilometer (odometer terkini terintegrasi).
- [x] Buat pencatatan transaksi BBM (CRUD, filter per kendaraan).
- [x] Buat input tanggal transaksi terpisah dari tanggal input (fitur backdate).
- [x] Buat kalkulasi liter otomatis (live preview di form & auto-calc di backend).
- [x] Buat pengaturan harga BBM & master jenis BBM.
- [x] Simpan snapshot harga pada transaksi BBM.
- [x] Buat master komponen perawatan (CRUD, kategori, mode tracking).
- [x] Buat konfigurasi jadwal KM dan waktu per kendaraan.
- [x] Buat pencatatan riwayat servis (multi-item dinamis).
- [x] Buat detail harga barang dan biaya jasa terpisah.
- [x] Buat reset interval otomatis setelah perawatan dicatat.
- [x] Buat status perawatan dinamis (Aman, Segera, Terlambat, Perlu Pemeriksaan).
- [x] Buat riwayat transaksi (BBM & Servis).
- [x] Buat laporan pengeluaran BBM dan servis per bulan/tahun & per kendaraan.

## 5. Pengujian
- [ ] Uji input BBM dengan tanggal transaksi mundur.
- [ ] Uji urutan kilometer yang tidak konsisten.
- [ ] Uji kalkulasi liter dari total pembayaran dan harga/liter.
- [ ] Uji perubahan harga BBM tanpa mengubah transaksi lama.
- [ ] Uji perawatan sebelum jatuh tempo.
- [ ] Uji perawatan setelah melewati batas KM.
- [ ] Uji perawatan setelah melewati batas waktu.
- [ ] Uji reset KM dan waktu secara bersamaan.
- [ ] Uji satu transaksi servis dengan beberapa komponen.
- [ ] Uji edit/hapus transaksi historis dan dampaknya pada jadwal.
- [ ] Uji isolasi data antar-user.
- [ ] Uji tampilan responsive pada desktop dan HP.

## 6. Pengembangan lanjutan
- [ ] Implementasikan notifikasi terjadwal.
- [ ] Siapkan API untuk mobile.
- [ ] Kembangkan aplikasi mobile.
- [ ] Implementasikan sinkronisasi web dan mobile.
- [ ] Pertimbangkan push notification.
- [ ] Pertimbangkan fitur berbagi kendaraan.
