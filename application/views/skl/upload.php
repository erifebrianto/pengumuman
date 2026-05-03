<!-- Upload Template SKL -->
<div class="container">
  <div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
      <div>
        <h3 class="fw-bold mb-3">Upload Template SKL</h3>
        <h6 class="op-7 mb-2">Unggah template Surat Keterangan Lulus (.docx) untuk digunakan pada proses generate dokumen</h6>
      </div>
      <div class="ms-md-auto py-2 py-md-0">
        <?php if (file_exists(FCPATH . 'template/skl_template.docx')): ?>
          <a href="<?= base_url('template/skl_template.docx') ?>" class="btn btn-info btn-round" download="skl_template.docx">
            <i class="fas fa-download"></i> Download Template Aktif
          </a>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Form Upload Template</div>
          </div>
          <div class="card-body">
            <form method="post" action="<?= base_url('skl/upload') ?>" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="template" class="form-label">Pilih File Template (.docx) <span class="text-danger">*</span></label>
                <input type="file" name="template" class="form-control" required accept=".docx">
                <small class="form-text text-muted">Pastikan format file adalah <b>.docx</b>.</small>
              </div>
              <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Upload Template</button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-header">
            <div class="card-title"><i class="fas fa-tags text-primary"></i> Panduan Variabel Template</div>
          </div>
          <div class="card-body" style="max-height: 500px; overflow-y: auto;">
            <p>Gunakan format variabel berikut pada file Word (.docx) Anda. Sistem akan merubahnya secara otomatis:</p>
            
            <h5 class="fw-bold mt-2">Data Identitas Siswa:</h5>
            <ul class="list-group list-group-bordered mb-3">
              <li class="list-group-item"><code>${nama_lengkap}</code> <br><small class="text-muted">Nama Lengkap Siswa</small></li>
              <li class="list-group-item"><code>${nis}</code> <br><small class="text-muted">Nomor Induk Siswa</small></li>
              <li class="list-group-item"><code>${nisn}</code> <br><small class="text-muted">NISN</small></li>
              <li class="list-group-item"><code>${kelas}</code> <br><small class="text-muted">Kelas Siswa</small></li>
              <li class="list-group-item"><code>${no_ujian}</code> <br><small class="text-muted">Nomor Ujian</small></li>
              <li class="list-group-item"><code>${tempat_lahir}</code> <br><small class="text-muted">Tempat Lahir</small></li>
              <li class="list-group-item"><code>${tanggal_lahir}</code> <br><small class="text-muted">Tanggal Lahir</small></li>
              <li class="list-group-item"><code>${status_lulus_rich}</code> <br><small class="text-muted">Teks coret: LULUS / <s>TIDAK LULUS</s></small></li>
              <li class="list-group-item"><code>${kurikulum}</code> <br><small class="text-muted">Kurikulum</small></li>
              <li class="list-group-item"><code>${program_keahlian}</code> <br><small class="text-muted">Program Keahlian</small></li>
              <li class="list-group-item"><code>${konsentrasi_keahlian}</code> <br><small class="text-muted">Konsentrasi Keahlian</small></li>
              <li class="list-group-item"><code>${tanggal_kelulusan}</code> <br><small class="text-muted">Tanggal Kelulusan</small></li>
              <li class="list-group-item"><code>${no_ijazah}</code> <br><small class="text-muted">Nomor Ijazah</small></li>
              <li class="list-group-item"><code>${rata_rata}</code> <br><small class="text-muted">Rata-rata Nilai Siswa</small></li>
              <li class="list-group-item"><code>${nama_sekolah}</code> <br><small class="text-muted">Nama Sekolah</small></li>
              <li class="list-group-item"><code>${alamat_sekolah}</code> <br><small class="text-muted">Alamat Sekolah</small></li>
              <li class="list-group-item"><code>${nama_kepala_sekolah}</code> <br><small class="text-muted">Nama Kepala Sekolah</small></li>
            </ul>

            <h5 class="fw-bold mt-2">Nilai Mata Pelajaran:</h5>
            <ul class="list-group list-group-bordered">
              <li class="list-group-item"><code>${n_pendidikan_agama_dan_budi_pekerti}</code> <br><small class="text-muted">Pendidikan Agama & Budi Pekerti</small></li>
              <li class="list-group-item"><code>${n_pendidikan_pancasila}</code> <br><small class="text-muted">Pendidikan Pancasila</small></li>
              <li class="list-group-item"><code>${n_bahasa_indonesia}</code> <br><small class="text-muted">Bahasa Indonesia</small></li>
              <li class="list-group-item"><code>${n_pendidikan_jasmani_olahraga_dan_kesehatan}</code> <br><small class="text-muted">PJOK</small></li>
              <li class="list-group-item"><code>${n_sejarah}</code> <br><small class="text-muted">Sejarah</small></li>
              <li class="list-group-item"><code>${n_seni_budaya}</code> <br><small class="text-muted">Seni Budaya</small></li>
              <li class="list-group-item"><code>${n_matematika}</code> <br><small class="text-muted">Matematika</small></li>
              <li class="list-group-item"><code>${n_bahasa_inggris}</code> <br><small class="text-muted">Bahasa Inggris</small></li>
              <li class="list-group-item"><code>${n_informatika}</code> <br><small class="text-muted">Informatika</small></li>
              <li class="list-group-item"><code>${n_projek_ilmu_pengetahuan_alam_dan_sosial}</code> <br><small class="text-muted">Projek IPAS</small></li>
              <li class="list-group-item"><code>${n_dasar_dasar_program_keahlian}</code> <br><small class="text-muted">Dasar Program Keahlian</small></li>
              <li class="list-group-item"><code>${n_konsentrasi_keahlian}</code> <br><small class="text-muted">Konsentrasi Keahlian</small></li>
              <li class="list-group-item"><code>${n_kreativitas_inovasi_dan_kewirausahaan}</code> <br><small class="text-muted">Kreativitas & Kewirausahaan</small></li>
              <li class="list-group-item"><code>${n_praktik_kerja_lapangan}</code> <br><small class="text-muted">PKL</small></li>
              <li class="list-group-item"><code>${n_mata_pelajaran_pilihan}</code> <br><small class="text-muted">Mapel Pilihan</small></li>
              <li class="list-group-item"><code>${n_bahasa_jawa}</code> <br><small class="text-muted">Muatan Lokal / Bahasa Jawa</small></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
