# Project Context — Vehicle Monitoring

## 1. Ringkasan proyek

Vehicle Monitoring adalah aplikasi untuk mencatat dan memantau kendaraan pribadi, terutama kilometer, pengisian BBM, jadwal perawatan, riwayat servis, dan seluruh biaya kendaraan.

Masalah utama yang ingin diselesaikan adalah pemilik kendaraan sering lupa kapan terakhir mengganti oli, memeriksa atau mengganti ban, melakukan servis rutin, serta tidak memiliki catatan biaya kendaraan yang rapi.

Aplikasi dimulai sebagai web app. Arsitektur harus memungkinkan pengembangan mobile app di tahap berikutnya dengan backend/API dan database yang sama agar data web dan mobile tersinkronisasi.

## 2. Tujuan produk

1. Membantu pemilik kendaraan mengetahui kilometer terakhir kendaraan.
2. Mencatat pengisian BBM beserta tanggal, jenis BBM, harga, liter, dan biaya.
3. Memantau jadwal perawatan berdasarkan kilometer dan/atau waktu.
4. Memberi peringatan saat perawatan mendekati atau melewati batas.
5. Menyimpan riwayat servis dan rincian harga barang serta jasa.
6. Menampilkan total pengeluaran kendaraan, termasuk BBM dan perawatan.
7. Menyiapkan fondasi multi-user untuk kemungkinan aplikasi digunakan banyak orang.

## 3. Target pengguna dan ruang lingkup

### Tahap awal
- Digunakan untuk kebutuhan pribadi.
- Satu akun dapat mengelola satu atau beberapa kendaraan.
- Jenis kendaraan awal: motor dan mobil.
- Platform: web responsive.

### Pengembangan lanjutan
- Multi-user dengan data terisolasi per akun.
- Mobile app.
- Sinkronisasi web dan mobile melalui backend/API yang sama.
- Notifikasi tambahan seperti push notification atau email.
- Kemungkinan fitur berbagi kendaraan atau akses keluarga, belum masuk ruang lingkup awal.

## 4. Alur bisnis utama

### 4.1 Registrasi kendaraan

User menambahkan kendaraan dengan data seperti:
- Nama kendaraan.
- Jenis kendaraan (motor/mobil).
- Merek dan model.
- Tahun produksi.
- Nomor polisi (opsional).
- Kilometer awal.
- Tanggal kilometer awal berlaku.
- Catatan.

Kilometer awal dan tanggalnya menjadi dasar pembacaan riwayat kendaraan. Jika user sudah memiliki catatan sebelumnya, data awal dapat disesuaikan dengan kondisi sebenarnya.

### 4.2 Pencatatan pengisian BBM

Setiap kali mengisi BBM, user dapat mencatat:
- Tanggal/waktu pengisian sebenarnya.
- Tanggal/waktu input ke aplikasi (dicatat otomatis oleh sistem).
- Kilometer kendaraan saat pengisian.
- Jenis BBM, misalnya Pertalite atau Pertamax.
- Total pembayaran.
- Harga per liter.
- Jumlah liter.
- Tempat pengisian dan catatan (opsional).

Jumlah liter dihitung otomatis dari total pembayaran dibagi harga per liter. Jika harga atau total pembayaran diedit, jumlah liter dihitung ulang.

Harga per liter dapat berasal dari:
- Pengaturan harga otomatis jika integrasi sumber harga tersedia.
- Input manual oleh user.

Harga BBM perlu dapat diperbarui di pengaturan. Harga yang tersimpan di transaksi lama tidak boleh berubah ketika harga terbaru diperbarui.

#### Input terlambat / backdate
User boleh mengisi data setelah hari transaksi. Contoh:
- Pengisian sebenarnya tanggal 10 November.
- User baru mencatat tanggal 12 November.
- User mengisi tanggal transaksi 10 November.
- Sistem otomatis mencatat kapan data dimasukkan pada tanggal 12 November.

Sistem mengurutkan riwayat berdasarkan tanggal transaksi, bukan tanggal input. Jika ada transaksi lama yang baru dimasukkan, sistem memvalidasi urutan kilometer dan menghitung ulang data turunan yang terdampak.

### 4.3 Monitoring kilometer

Kilometer terbaru ditampilkan berdasarkan kronologi catatan odometer yang valid. Input kilometer dapat berasal dari:
- Transaksi BBM.
- Pencatatan kilometer manual tanpa transaksi BBM.

Fitur pencatatan manual diperlukan jika user lupa mencatat pengisian BBM atau ingin memperbarui kilometer kendaraan di waktu lain.

Sistem harus memberi peringatan jika kilometer baru lebih rendah dari catatan berikutnya/sebelumnya dalam urutan waktu. User perlu dapat memperbaiki data yang salah tanpa membuat histori menjadi tidak konsisten.

### 4.4 Konfigurasi jadwal perawatan

User memilih komponen yang ingin dipantau, lalu mengatur:
- Interval kilometer, misalnya setiap 2.000 km.
- Interval waktu, misalnya setiap 1 bulan.
- Pengingat sebelum jatuh tempo, misalnya 100 km atau 7 hari sebelumnya.
- Status jadwal aktif/nonaktif.

Tidak semua komponen harus menggunakan interval yang sama. Oli mesin dapat dipantau berdasarkan KM dan waktu. Ban atau kampas rem dapat menggunakan pemeriksaan kondisi secara manual, dengan interval inspeksi opsional.

### 4.5 Perhitungan jatuh tempo

Contoh konfigurasi oli mesin:
- Interval: 2.000 km.
- Interval waktu: 1 bulan.
- Terakhir diganti: 21 November pada 15.000 km.

Target berikutnya:
- KM: 17.000 km.
- Tanggal: 21 Desember.

Contoh perawatan lebih awal:
- Pada 11 Desember, kendaraan sudah mencapai 16.500 km.
- User mengganti oli pada tanggal tersebut.
- Jadwal baru dihitung dari 11 Desember dan 16.500 km.
- Target baru: 18.500 km dan 11 Januari.

Dengan demikian, kedua interval di-reset dari tanggal dan kilometer aktual perawatan terakhir. Jadwal lama tidak terus berjalan setelah perawatan baru dicatat.

Jika interval KM dan waktu sama-sama dikonfigurasi, perawatan jatuh tempo ketika salah satu batas tercapai lebih dahulu.

Status yang direncanakan:
- Aman: belum mendekati batas.
- Segera: sudah masuk ambang pengingat.
- Terlambat: salah satu batas telah terlewati.
- Perlu pemeriksaan: komponen dipantau berdasarkan kondisi dan belum ada hasil inspeksi terbaru.

Ambang “Segera” perlu bisa dikonfigurasi atau ditentukan sebagai default yang masuk akal.

### 4.6 Pencatatan servis/perawatan

Saat melakukan servis, user mencatat:
- Tanggal/waktu perawatan sebenarnya.
- Kilometer saat perawatan.
- Bengkel/tempat perawatan (opsional).
- Komponen atau pekerjaan.
- Harga barang/komponen.
- Biaya jasa.
- Jumlah barang.
- Catatan.

Satu transaksi servis dapat berisi beberapa item. Contoh: ganti oli mesin dan kampas rem dalam satu kunjungan bengkel.

Harga barang dan biaya jasa disimpan terpisah. Total biaya dihitung dari seluruh rincian item. Setiap item yang terkait jadwal perawatan akan memperbarui titik awal jadwalnya sendiri menggunakan tanggal dan kilometer transaksi servis.

### 4.7 Riwayat dan laporan pengeluaran

Aplikasi menyajikan:
- Total biaya BBM.
- Total biaya perawatan.
- Total biaya pengeluaran lain (jika modul pengeluaran umum diaktifkan).
- Rekap harian, bulanan, dan tahunan.
- Riwayat transaksi per kendaraan.
- Konsumsi BBM dan biaya per kilometer jika data mencukupi.

Jangan menghitung biaya BBM atau servis dua kali. Masing-masing sudah memiliki transaksi sumber tersendiri.

### 4.8 Notifikasi

Aplikasi memeriksa jadwal perawatan berdasarkan kilometer terbaru dan tanggal saat ini. Notifikasi ditampilkan ketika:
- Perawatan mendekati batas kilometer.
- Perawatan mendekati batas waktu.
- Perawatan telah melewati salah satu batas.

Notifikasi perlu dikendalikan agar tidak muncul berulang secara berlebihan. Untuk MVP, notifikasi di dashboard/web dapat diprioritaskan. Email/push notification dapat menyusul.

## 5. Prinsip data dan konsistensi

- Tanggal transaksi berbeda dari tanggal input.
- Transaksi lama boleh diinput belakangan.
- Riwayat diurutkan berdasarkan tanggal transaksi.
- Kilometer harus divalidasi secara kronologis.
- Harga pada transaksi adalah snapshot dan tidak mengikuti perubahan master harga.
- Reset jadwal perawatan selalu menggunakan tanggal dan KM perawatan aktual.
- Perubahan atau penghapusan transaksi historis harus memicu validasi/perhitungan ulang data terkait.
- Data tiap kendaraan terikat pada pemiliknya.
- Data web dan mobile di masa depan menggunakan sumber data backend yang sama.

## 6. Rancangan teknologi awal

Pilihan awal yang disarankan:
- Backend: Laravel.
- Database: MySQL.
- Frontend web: Bootstrap 5 dengan layout responsive.
- API: disiapkan agar dapat digunakan aplikasi mobile di tahap berikutnya.
- Mobile tahap lanjutan: Flutter atau teknologi lain yang mengonsumsi API backend yang sama.

Versi framework dan library final ditetapkan saat inisialisasi project, dengan memilih versi stabil dan kompatibel.

## 7. Batasan MVP

Fokus versi pertama:
1. Login dan akun dasar.
2. CRUD kendaraan.
3. Pencatatan kilometer manual.
4. Pencatatan BBM dengan backdate, harga/liter, liter otomatis, dan snapshot harga.
5. Master komponen perawatan.
6. Jadwal perawatan berbasis KM dan waktu.
7. Pencatatan servis dengan detail barang dan jasa.
8. Dashboard status perawatan.
9. Rekap pengeluaran BBM dan perawatan.

Belum menjadi prioritas MVP:
- Aplikasi mobile.
- Push notification.
- Integrasi otomatis harga BBM sebelum sumber data dipastikan.
- Berbagi kendaraan antar akun.
- Integrasi GPS/odometer otomatis.
- Integrasi bengkel atau marketplace.

## 8. Keputusan yang masih perlu ditetapkan

- Sumber harga BBM otomatis dan apakah harga dibedakan berdasarkan wilayah.
- Aturan tanggal jatuh tempo untuk interval bulanan pada tanggal 29, 30, atau 31.
- Nilai ambang pengingat sebelum jatuh tempo.
- Apakah notifikasi MVP hanya di dashboard atau juga email.
- Apakah biaya lain seperti pajak, parkir, cuci, dan aksesori masuk MVP.
- Apakah perlu upload foto nota/bukti servis pada versi awal.
- Apakah kendaraan dapat memiliki beberapa profil jadwal untuk komponen yang sama (misalnya oli dengan jenis berbeda).

## 9. Istilah penting

- **Odometer/KM:** angka kilometer kendaraan.
- **Backdate:** mencatat transaksi dengan tanggal kejadian di masa lalu.
- **Interval KM:** jarak tempuh maksimum sebelum perawatan berikutnya.
- **Interval waktu:** durasi maksimum sebelum perawatan berikutnya.
- **Snapshot harga:** harga yang disimpan pada saat transaksi dan tidak berubah mengikuti master harga.
- **Reset jadwal:** memperbarui titik awal interval berdasarkan perawatan terakhir.
