<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div class="container mt-3">
    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">EDIT PELATIHAN</h2>
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

    <div class="card p-3 border rounded bg-light">

        <form action="/admin/pelatihan/update/<?= $pelatihan['id']; ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <input type="hidden" name="_method" value="PUT">
            <div class="mb-3">
                <label for="judul" class="form-label fw-bold">Judul</label>
                <input type="text" name="judul" id="judul" class="form-control" value="<?= $pelatihan['judul']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">
                    Video Saat Ini:
                    <?php if (!empty($pelatihan['video_pelatihan'])): ?>
                        <a href="<?= base_url('assets/uploads/pelatihan/' . $pelatihan['video_pelatihan']); ?>" target="_blank">
                            <?= esc($pelatihan['video_pelatihan']); ?>
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Belum ada video</span>
                    <?php endif; ?>
                </label>
                <input type="file" name="video" id="video" class="form-control">
                <small>Biarkan kosong jika tidak ingin mengubah video</small>
            </div>

            <!-- Caption -->
            <div class="mb-3">
                <label for="caption" class="form-label fw-bold">Caption</label>
                <textarea name="caption" id="caption" class="form-control" rows="20"><?= old('caption', $pelatihan['caption_pelatihan']); ?></textarea>
            </div>
            <!-- Akses Publik -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="akses_publik" id="akses_publik" value="1"
                    <?= old('akses_publik', $pelatihan['akses_publik']) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="akses_publik">
                    Akses Publik
                </label>
            </div>
            <!-- Tombol -->
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>