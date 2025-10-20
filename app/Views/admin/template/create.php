<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div class="container mt-3">
    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">TAMBAH TEMPLATE</h2>
            <a href="/admin/template" style="background-color: #EC1928;" class="btn btn-danger rounded-pill fw-bold">
                <i class="bi bi-arrow-left"></i> Kembali Ke Daftar
            </a>
        </div>

        <!-- Alert pesan -->
        <?php if (session()->getFlashdata('pesan')): ?>
            <div class="alert alert-success mt-3 mb-0">
                <?= session()->getFlashdata('pesan'); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="card p-3 border rounded bg-light">
        <form action="/admin/template/save" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>

            <!-- Judul -->
            <div class="mb-3">
                <label for="judul" class="form-label fw-bold">Judul</label>
                <input type="text" name="judul" id="judul" class="form-control" required placeholder="Masukkan judul template">
            </div>

            <div class="row">
                <!-- File DOCX -->
                <div class="col-md-6 mb-3">
                    <label for="file_docx" class="form-label fw-bold">File DOCX</label>
                    <input type="file" name="file_docx" id="file_docx" class="form-control" required accept=".docx">
                    <div class="form-text">Maksimal 50MB</div>
                </div>
            </div>

            <!-- Akses Publik -->
            <div class="form-check mb-3">
                <input type="checkbox" name="akses_publik" value="1" id="akses_publik" class="form-check-input">
                <label for="akses_publik" class="form-check-label">Akses Publik</label>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('file_docx').addEventListener('change', function() {
        const fileInput = this.files[0];
        if (fileInput) {
            let fileName = fileInput.name;
            // Hapus ekstensi .docx
            fileName = fileName.replace(/\.[^/.]+$/, "");
            // Ubah underscore/dash jadi spasi
            fileName = fileName.replace(/[_-]+/g, " ");
            document.getElementById('judul').value = fileName;
        }
    });
</script>

<?= $this->endSection(); ?>