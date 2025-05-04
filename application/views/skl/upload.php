<!-- Upload Template SKL -->
<div class="container">
  <div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
      <div>
        <h3 class="fw-bold mb-3">Upload Template SKL</h3>
        <h6 class="op-7 mb-2">Unggah template Surat Keterangan Lulus (.docx) untuk digunakan pada proses generate dokumen</h6>
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

    <div class="card">
      <div class="card-body">
        <form method="post" action="<?= base_url('skl/upload') ?>" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="template" class="form-label">Pilih File Template (.docx)</label>
            <input type="file" name="template" class="form-control" required accept=".docx">
          </div>
          <button type="submit" class="btn btn-primary">Upload Template</button>
        </form>
      </div>
    </div>
  </div>
</div>
