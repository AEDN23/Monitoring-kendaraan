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
- [ ] Tentukan versi Laravel dan PHP yang kompatibel.
- [ ] Buat project Laravel.
- [ ] Konfigurasi MySQL dan environment.
- [ ] Pasang dan konfigurasi Bootstrap 5.
- [ ] Siapkan autentikasi.
- [ ] Buat migration dan model berdasarkan schema final.
- [ ] Buat seeder master jenis BBM dan komponen perawatan.

## 4. Pengembangan MVP web
- [ ] Buat login/logout.
- [ ] Buat dashboard.
- [ ] Buat CRUD kendaraan.
- [ ] Buat pencatatan kilometer manual.
- [ ] Buat pencatatan transaksi BBM.
- [ ] Buat input tanggal transaksi terpisah dari tanggal input.
- [ ] Buat kalkulasi liter otomatis.
- [ ] Buat pengaturan harga BBM manual.
- [ ] Simpan snapshot harga pada transaksi BBM.
- [ ] Buat master komponen perawatan.
- [ ] Buat konfigurasi jadwal KM dan waktu per kendaraan.
- [ ] Buat pencatatan riwayat servis.
- [ ] Buat detail harga barang dan biaya jasa.
- [ ] Buat reset interval setelah perawatan.
- [ ] Buat status perawatan dan peringatan di dashboard.
- [ ] Buat riwayat transaksi.
- [ ] Buat laporan pengeluaran BBM dan servis.

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
