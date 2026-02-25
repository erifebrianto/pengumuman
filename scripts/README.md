# Panduan Optimasi Kinerja Generate PDF SKL

Dokumen ini berisi informasi mengenai arsitektur, cronjob, listener, dan rekomendasi tuning server untuk menangani _high load_ saat melakukan konversi ratusan hingga ribuan dokumen Word (.docx) ke PDF.

## 1. Arsitektur Batch Processing Baru
Sistem lama mere-instantiate *LibreOffice* secara realtime per-request, yang artinya ada Cold Start & Spike CPU pada request masuk. Proses terbaru menggunakan arsitektur berikut:

1. Admin menekan tombol "Generate Pengumuman Batch" dari Panel `Data Siswa`.
2. Controller `Siswa::trigger_pdf_batch()` akan memicu perintah `CLI` secara asinkron lalu me-response AJAX.
3. Controller CLI `Generator::generate_pengumuman_batch` menangani iterasi dan koneksi ke Database. 
4. Di *Background*, ia memakai program `unoconv` (atau soffice secara langsung jika tak ada unoconv) yang mere-use UNO Listener yang standby di memori.
5. Tracking berjalan kelipatan 10 lalu ditampung dalam tabel `batch_generation`. Browser AJAX akan polling tiap 2 detik untuk animasi Progress Bar.

Gunakan skrip `scripts/run_libreoffice_listener.sh` untuk meng-standby-kan LibreOffice di daemon/memori.

## 2. Pemasangan Kebutuhan Server (Produksi Linux)
Sangat direkomendasikan menginstal package **unoconv** (Universal Office Converter). `unoconv` bertugas sebagai wrapper antara PHP dan LibreOffice secara socket sehingga mencegah `cold start`.

```bash
# Ubuntu / Debian
sudo apt-get update
sudo apt-get install libreoffice unoconv
```

## 3. Persistent LibreOffice Listener
Jalankan UNO Listener daemon minimal satu kali, lalu biarkan ia duduk diam di CPU. 
Buat *systemd* service agar ia selalu menyala atau tambahkan ke startup:

```bash
# Menjalankan manual (atau via script run_libreoffice_listener.sh)
unoconv --listener &
```
_(Jika Anda menggunakan `unoconv`, di saat memanggil command PDF dia akan mencari listener ini otomatis)._

## 4. Setup Cronjob (Opsional)
Jika instansi ingin generate PDF per jam khusus siswa baru/diubah tanpa klik di layar browser, buat Cronjob.

1. Sesuaikan path project pada skrip `scripts/cron_generate_pengumuman.sh`.
2. Berikan permisi eksekusi:
```bash
chmod +x /opt/lampp/htdocs/pengumuman/scripts/cron_generate_pengumuman.sh
```
3. Registrasikan cronjob (`crontab -e`), contoh di bawah menjalankan proses setiap hari pada pk. 01.00 pagi:
```cron
0 1 * * * /opt/lampp/htdocs/pengumuman/scripts/cron_generate_pengumuman.sh
```

## 5. Rekomendasi Tuning Server untuk High Load
Untuk mengamankan CPU agar stabil menampung > 5000 siswa tanpa nge-hang:

1. **Memory Handling PHP:** 
   Di dalam controller `Generator.php` kami sudah setup batas bypass:
   `ini_set('memory_limit', '1024M');` (Disarankan VPS punya RAM min. 2GB jika ini di set 1GB).
2. **CPU Sleeping:**
   Setiap generasi PDF di-jeda selama 50 ms (`usleep(50000)`) pada source code. Ini mengizinkan OS untuk memberikan nafas ke CPU thread dan menjinakkan "CPU Spike". Anda bisa meninggikan jeda jika server sering *stuttering*.
3. **Database Tracker** 
   Tabel `batch_generation` telah dibuat otomatis (*Zero-Config Database CI3 dbforge*) sehingga tak ada antrean process yang bertabrakan. Jika Admin menekan 2 kali, query menolaknya. Update ID pun hanya memanggil DB per 10 siswa, sehingga Query Per Second (QPS) terdiskon hingga 90%.
