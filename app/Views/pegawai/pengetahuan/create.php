<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div class="container mt-3">
    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">TAMBAH PENGETAHUAN</h2>
            <a href="/pegawai/pengetahuan" style="background-color: #EC1928;" class="btn btn-danger rounded-pill fw-bold">
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
        <form action="/pegawai/pengetahuan/save" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>

            <!-- Judul -->
            <div class="mb-3">
                <label for="judul" class="form-label fw-bold">Judul</label>
                <input type="text" name="judul" id="judul" class="form-control" required placeholder="Masukkan judul pengetahuan">
            </div>

            <div class="row">
                <!-- File PDF -->
                <div class="col-md-6 mb-3">
                    <label for="file_pdf" class="form-label fw-bold">File PDF</label>
                    <input type="file" name="file_pdf" id="file_pdf" class="form-control" required>
                    <div class="form-text">Maksimal 5MB</div>
                </div>

                <!-- Thumbnail -->
                <div class="col-md-6 mb-3">
                    <label for="thumbnail" class="form-label fw-bold">Thumbnail</label>
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control">
                    <div class="form-text">Biarkan kosong untuk menggunakan thumbnail default</div>
                </div>
            </div>
            <!-- Caption -->
            <div class="mb-3">
                <label for="caption" class="form-label fw-bold">Deskripsi</label>
                <textarea name="caption" id="caption" rows="20" class="form-control" placeholder="Tulis caption di sini..."></textarea>
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
                <input type="checkbox" name="akses_publik" value="1" id="akses_publik" class="form-check-input">
                <label for="akses_publik" class="form-check-label">Akses Publik</label>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>