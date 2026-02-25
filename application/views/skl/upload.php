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
        <div class="card">
          <div class="card-header">
            <div class="card-title">Panduan Variabel Template</div>
          </div>
          <div class="card-body">
            <p>Gunakan format variabel berikut pada file Word (.docx) Anda. Sistem akan merubahnya secara otomatis:</p>
            <ul class="list-group list-group-bordered">
              <li class="list-group-item"><code>${nama_lengkap}</code> <br><small class="text-muted">Nama Lengkap Siswa</small></li>
              <li class="list-group-item"><code>${nis}</code> <br><small class="text-muted">Nomor Induk Siswa</small></li>
              <li class="list-group-item"><code>${kelas}</code> <br><small class="text-muted">Kelas Siswa</small></li>
              <li class="list-group-item"><code>${no_ujian}</code> <br><small class="text-muted">Nomor Ujian</small></li>
              <li class="list-group-item"><code>${tempat_lahir}</code> <br><small class="text-muted">Tempat Lahir</small></li>
              <li class="list-group-item"><code>${status_lulus_rich}</code> <br><small class="text-muted">Teks coret: LULUS / <s>TIDAK LULUS</s></small></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
