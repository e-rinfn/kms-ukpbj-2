<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div class="container mt-3">
    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">TAMBAH PELATIHAN</h2>
            <a href="/admin/pelatihan" style="background-color: #EC1928;" class="btn btn-danger rounded-pill fw-bold">
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


    <div class="card p-3 border rounded bg-light shadow-sm">
        <form action="/admin/pelatihan/save" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>

            <!-- Judul -->
            <div class="mb-3">
                <label for="judul" class="form-label fw-bold">Judul</label>
                <input type="text" name="judul" id="judul" class="form-control" required placeholder="Masukkan judul pelatihan">
            </div>

            <!-- Upload Video -->
            <div class="mb-3">
                <label for="video" class="form-label fw-bold">Video</label>
                <input type="file" name="video" id="video" class="form-control" accept=".mp4,.mov,.avi" required>
                <small class="text-muted">Format: mp4, mov, avi | Maksimal 10MB</small>
            </div>

            <!-- Caption -->
            <div class="mb-3">
                <label for="caption" class="form-label fw-bold">Caption</label>
                <textarea name="caption" id="caption" class="form-control" rows="20" placeholder="Tulis caption di sini..."></textarea>
            </div>

            <!-- Akses Publik -->
            <div class="form-check mb-3">
                <input type="checkbox" name="akses_publik" id="akses_publik" value="1" class="form-check-input">
                <label for="akses_publik" class="form-check-label">Akses Publik</label>
            </div>

            <!-- Tombol Aksi -->
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>

</div>
<?= $this->endSection(); ?>