#!/bin/bash

# ==========================================================
# Script Cronjob untuk Generate Pengumuman Batch CodeIgniter
# ==========================================================

# Path ke root framework CodeIgniter
DIR="/opt/lampp/htdocs/pengumuman"

# Pindah ke direktori project agar path log/file relative CI tidak error
cd $DIR

# Jalankan CLI Controller dan simpan outputnya di log
php index.php generator generate_pengumuman_batch >> temp/cron_generate.log 2>&1

echo "Cronjob dieksekusi pada $(date)" >> temp/cron_generate.log
