<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div class="container mt-3">
    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">EDIT TEMPLATE</h2>
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

        <form action="/admin/template/update/<?= $template['id']; ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <input type="hidden" name="_method" value="PUT">

            <!-- Judul -->
            <div class="mb-3">
                <label for="judul" class="form-label fw-bold">Judul</label>
                <input type="text" name="judul" id="judul"
                    class="form-control"
                    value="<?= old('judul', $template['judul']); ?>" required>
            </div>

            <div class="row">
                <!-- File DOCX -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">File DOCX Saat Ini</label>
                    <input type="file" name="file_docx" id="file_docx" class="form-control">
                    <div class="form-text">Biarkan kosong jika tidak ingin mengubah file DOCX (maks 50MB)</div>
                    <div class="mb-2">
                        <a href="<?= base_url('assets/uploads/template/' . $template['file_docx']); ?>"
                            target="_blank" class="text-decoration-none">
                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                            <?= esc($template['file_docx']); ?>
                        </a>
                    </div>
                </div>
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
                    <?= old('akses_publik', $template['akses_publik']) ? 'checked' : ''; ?>>
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