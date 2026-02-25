<!-- Log Pengumuman View -->
<div class="container">
  <div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
      <div>
        <h3 class="fw-bold mb-3">Log Generator SKL</h3>
        <h6 class="op-7 mb-2">Riwayat pemrosesan dokumen batch di background (Menampilkan hari ini: <?= $log_date ?>)</h6>
      </div>
      <div class="ms-md-auto py-2 py-md-0 d-flex gap-2">
        <button onclick="location.reload()" class="btn btn-primary btn-round">
            <i class="fas fa-sync me-1"></i> Refresh Log
        </button>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <div class="card-title">Console Output: application/logs/batch/generate_<?= date('Y_m_d') ?>.log</div>
      </div>
      <div class="card-body">
        <div class="bg-dark text-white p-3 rounded" style="font-family: monospace; max-height: 500px; overflow-y: scroll; white-space: pre-wrap; font-size: 13px;" id="logConsole">
<?= htmlspecialchars($log_content) ?>
        </div>
      </div>
      <div class="card-action">
        <small class="text-muted">Setiap klik "Generate Pengumuman Batch" pada daftar siswa akan dicatat di panel ini. File log ter-generate otomatis per hari.</small>
      </div>
    </div>
  </div>
</div>

<script>
    // Auto-scroll ke bawah saat halaman dimuat
    window.onload = function() {
        var logElem = document.getElementById("logConsole");
        logElem.scrollTop = logElem.scrollHeight;
    };
</script>
