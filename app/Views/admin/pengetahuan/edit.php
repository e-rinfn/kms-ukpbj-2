<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div class="container mt-3">
    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">EDIT PENGETAHUAN</h2>
            <a href="/admin/pengetahuan" style="background-color: #EC1928;" class="btn btn-danger rounded-pill fw-bold">
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

        <form action="/admin/pengetahuan/update/<?= $pengetahuan['id']; ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <input type="hidden" name="_method" value="PUT">

            <!-- Judul -->
            <div class="mb-3">
                <label for="judul" class="form-label fw-bold">Judul</label>
                <input type="text" name="judul" id="judul"
                    class="form-control"
                    value="<?= old('judul', $pengetahuan['judul']); ?>" required>
            </div>

            <div class="row">
                <!-- File PDF -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">File PDF Saat Ini</label>
                    <input type="file" name="file_pdf" id="file_pdf" class="form-control">
                    <div class="form-text">Biarkan kosong jika tidak ingin mengubah file PDF (maks 5MB)</div>
                    <div class="mb-2">
                        <a href="<?= base_url('assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan']); ?>"
                            target="_blank" class="text-decoration-none">
                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                            <?= esc($pengetahuan['file_pdf_pengetahuan']); ?>
                        </a>
                    </div>
                </div>

                <!-- Thumbnail -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Thumbnail Saat Ini</label>
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control">
                    <div class="form-text">Biarkan kosong jika tidak ingin mengubah thumbnail</div>
                    <div class="text-center mb-2">
                        <img src="<?= base_url('assets/uploads/pengetahuan/' . $pengetahuan['thumbnail_pengetahuan']); ?>"
                            alt="Thumbnail" class="img-thumbnail shadow-sm" style="max-width: 180px;">
                    </div>
                </div>
            </div>

            <!-- Caption -->
            <div class="mb-3">
                <label for="caption" class="form-label fw-bold">Caption</label>
                <textarea name="caption" id="caption" class="form-control" rows="20"><?= old('caption', $pengetahuan['caption_pengetahuan']); ?></textarea>
            </div>

            <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>

            <script>
                ClassicEditor
                    .create(document.querySelector('#caption'), {
                        toolbar: [
                            'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'
                        ],
                        height: '500px' // Atur tinggi editor
                    })
                    .catch(error => {
                        console.error(error);
                    });
            </script>

            <!-- Akses Publik -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="akses_publik" id="akses_publik" value="1"
                    <?= old('akses_publik', $pengetahuan['akses_publik']) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="akses_publik">
                    Akses Publik
                </label>
            </div>

            <!-- Tombol -->
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>

        <!-- Tampilkan error validation -->
        <?php if (isset($validation)): ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection(); ?>