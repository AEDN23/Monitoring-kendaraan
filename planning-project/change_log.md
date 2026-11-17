# Change Log — Vehicle Monitoring

Semua perubahan dan progres pengembangan dicatat di dokumen ini.

---

## [MVP v1.0.0] - 2026-11-17

### Added
- **Database & Model:**
  - 10 migration schema lengkap: `users`, `vehicles`, `fuel_types`, `fuel_price_settings`, `fuel_transactions`, `maintenance_items`, `maintenance_records`, `maintenance_record_items`, `vehicle_maintenance_schedules`, `expenses`, `notification_logs`.
  - 9 Eloquent Model lengkap dengan cast, relasi antar-entitas, kalkulasi status jadwal (`status()`), snapshot harga, dan reset interval otomatis (`resetFromRecord()`).
  - Seeder master data: 7 varian bahan bakar Pertamina (`FuelTypeSeeder`) dan 15 komponen perawatan berkala (`MaintenanceItemSeeder`).

- **Autentikasi & Layout:**
  - `LoginController` & `RegisterController` dengan layout guest responsif.
  - `layouts.app` dengan navigasi sidebar responsif (mobile toggle), topbar user, flash alert, dan styling Tailwind CSS v4.

- **Modul Kendaraan (`VehicleController`):**
  - CRUD kendaraan (Motor, Mobil, Lainnya) dengan kilometer awal & tanggal berlaku.
  - Halaman detail kendaraan (`vehicles.show`) menampilkan odometer terkini, rangkuman biaya bulanan, status jadwal perawatan, serta 5 transaksi BBM & servis terakhir.

- **Modul Transaksi BBM (`FuelTransactionController`):**
  - Pencatatan BBM dengan pemisahan waktu transaksi riil (`transaction_at`) dan waktu input (`recorded_at`) untuk mendukung *backdate*.
  - Kalkulasi volume liter otomatis (`total_amount / price_per_liter`) secara *live preview* di frontend dan validasi di backend.
  - Snapshot harga BBM per transaksi.
  - Filter riwayat BBM berdasarkan kendaraan.

- **Modul Jadwal Perawatan (`VehicleMaintenanceScheduleController`):**
  - Konfigurasi interval KM dan/atau hari per komponen pada setiap kendaraan.
  - Ambang batas pengingat (*reminder before km/days*).
  - Status pemantauan dinamis: **Aman**, **Segera**, **Terlambat**, dan **Perlu Pemeriksaan** (kondisi fisik).

- **Modul Riwayat Servis (`MaintenanceRecordController`):**
  - Pencatatan servis dengan tabel baris multi-item dinamis (tambah/hapus baris secara interaktif tanpa reload).
  - Pemisahan rincian biaya suku cadang (*parts cost*) dan biaya jasa bengkel (*labor cost*).
  - Otomatis mereset titik awal KM dan tanggal pada jadwal perawatan yang ditautkan ke item servis.

- **Modul Pengaturan Master (`FuelTypeController` & `MaintenanceItemController`):**
  - Manajemen master jenis BBM (tambah, edit, toggle aktif/nonaktif).
  - Manajemen master komponen perawatan dengan kategori dan `tracking_mode` (`km`, `time`, `km_or_time`, `condition`).

- **Dashboard & Laporan (`DashboardController` & `ReportController`):**
  - Dashboard statistik total kendaraan aktif, pengeluaran BBM & servis bulan berjalan, dan kartu kondisi jadwal tiap kendaraan.
  - Halaman Laporan Pengeluaran dengan filter kendaraan, bulan, dan tahun, menampilkan total biaya gabungan, rincian BBM, dan rincian servis.
