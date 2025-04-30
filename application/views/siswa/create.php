<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Tambah Siswa</h3>
                <h6 class="op-7 mb-2">Formulir untuk menambah siswa beserta nilai mata pelajaran</h6>
            </div>
        </div>

        <form method="POST">
            <!-- Data Siswa -->
            <div class="card">
                <div class="card-header">
                    <h4>Data Siswa</h4>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nis">NIS</label>
                        <input type="text" name="nis" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nisn">NISN</label>
                        <input type="text" name="nisn" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="kelas">Kelas</label>
                        <select name="kelas" id="kelas" class="form-control" required>
                            <option value="">Pilih Kelas</option>
                            <option value="10">Kelas 10</option>
                            <option value="11">Kelas 11</option>
                            <option value="12">Kelas 12</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama_ortu">Nama Orang Tua</label>
                        <input type="text" name="nama_ortu" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="rata_rata">Rata-Rata Nilai</label>
                        <input type="number" name="rata_rata" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Lulus">Lulus</option>
                            <option value="Tidak Lulus">Tidak Lulus</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Mata Pelajaran dan Nilai -->
            <div class="card">
                <div class="card-header">
                    <h4>Nilai Mata Pelajaran</h4>
                </div>
                <div class="card-body">
                    <div id="mata_pelajaran_container"></div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- JS untuk menambahkan mata pelajaran dinamis berdasarkan kelas -->
<script>
    document.getElementById('kelas').addEventListener('change', function () {
        var kelas = this.value;
        var mataPelajaranContainer = document.getElementById('mata_pelajaran_container');
        mataPelajaranContainer.innerHTML = ''; // Reset sebelum ditambahkan

        if (kelas) {
            var kelas_mapel = <?php echo json_encode($kelas_mapel); ?>;
            var mapel = kelas_mapel[kelas] || [];

            mapel.forEach(function (mapel, index) {
                var div = document.createElement('div');
                div.classList.add('form-group');
                div.innerHTML = `
                    <label for="mata_pelajaran_${index}">${mapel}</label>
                    <input type="hidden" name="mata_pelajaran[]" value="${mapel}">
                    <input type="number" name="nilai[]" class="form-control" placeholder="Nilai ${mapel}" required>
                `;
                mataPelajaranContainer.appendChild(div);
            });
        }
    });
</script>
