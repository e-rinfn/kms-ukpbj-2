<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div class="container mt-3">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">TAMBAH PENGETAHUAN</h2>
            <a href="/admin/pengetahuan" style="background-color: #EC1928;" class="btn btn-danger rounded-pill fw-bold">
                <i class="bi bi-arrow-left"></i> Kembali Ke Daftar
            </a>
        </div>

        <!-- Alert pesan -->
        <div id="alertMessage"></div>
    </div>

    <div class="card p-3 border rounded bg-light">
        <form id="pengetahuanForm" enctype="multipart/form-data">
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
                    <div class="form-text">Maksimal 50MB</div>
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
                let captionEditor;
                ClassicEditor
                    .create(document.querySelector('#caption'))
                    .then(editor => {
                        captionEditor = editor;
                    })
                    .catch(error => console.error(error));
            </script>

            <!-- Akses Publik -->
            <div class="form-check mb-3">
                <input type="checkbox" name="akses_publik" value="1" id="akses_publik" class="form-check-input">
                <label for="akses_publik" class="form-check-label">Akses Publik</label>
            </div>

            <!-- Progress bar -->
            <div class="progress mb-3" style="height: 20px; display: none;" id="uploadProgressWrapper">
                <div id="uploadProgress" class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                    role="progressbar" style="width: 0%">0%</div>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

<script>
    // isi otomatis judul dari nama file pdf
    document.getElementById('file_pdf').addEventListener('change', function(e) {
        if (this.files.length > 0) {
            let fileName = this.files[0].name;
            let nameWithoutExt = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;
            let judulInput = document.getElementById('judul');
            if (!judulInput.value) {
                // hanya isi otomatis kalau judul masih kosong
                judulInput.value = nameWithoutExt;
            }
        }
    });

    document.getElementById('pengetahuanForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        if (captionEditor) {
            formData.set('caption', captionEditor.getData());
        }

        let xhr = new XMLHttpRequest();
        xhr.open('POST', '/admin/pengetahuan/save', true);

        document.getElementById('uploadProgressWrapper').style.display = 'block';

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                let percent = Math.round((e.loaded / e.total) * 100);
                let progressBar = document.getElementById('uploadProgress');
                progressBar.style.width = percent + '%';
                progressBar.innerText = percent + '%';
            }
        });

        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('alertMessage').innerHTML = `
                    <div class="alert alert-success mt-3">Data berhasil ditambahkan!</div>
                `;
                document.getElementById('pengetahuanForm').reset();
                if (captionEditor) captionEditor.setData('');
                document.getElementById('uploadProgress').style.width = '0%';
                document.getElementById('uploadProgress').innerText = '0%';
                document.getElementById('uploadProgressWrapper').style.display = 'none';
            } else {
                document.getElementById('alertMessage').innerHTML = `
                    <div class="alert alert-danger mt-3">Gagal upload. Coba lagi.</div>
                `;
            }
        };

        xhr.send(formData);
    });
</script>
<?= $this->endSection(); ?>