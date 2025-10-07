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
        <form action="/admin/pelatihan/save" method="post" enctype="multipart/form-data" id="uploadForm">
            <?= csrf_field(); ?>

            <!-- Judul -->
            <div class="mb-3">
                <label for="judul" class="form-label fw-bold">Judul</label>
                <input type="text" name="judul" id="judul" class="form-control" required placeholder="Masukkan judul pelatihan">
            </div>

            <div class="row mb-3">
                <!-- Upload Video -->
                <div class="col-md-6">
                    <label for="video" class="form-label fw-bold">Video</label>
                    <input type="file" name="video" id="video" class="form-control" accept=".mp4,.mov,.avi">
                    <small class="text-muted">Format: mp4, mov, avi | Maksimal 10MB</small>

                    <!-- Progress Bar Container -->
                    <div id="uploadProgressContainer" class="mt-2" style="display: none;">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Upload Progress:</span>
                            <span id="progressPercentage">0%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div id="uploadStatus" class="mt-1 small"></div>
                    </div>
                </div>

                <!-- Link Video -->
                <div class="col-md-6">
                    <label for="link_youtube" class="form-label fw-bold">Link YouTube</label>
                    <input type="text" name="link_youtube" id="link_youtube" class="form-control" required placeholder="Masukkan link video">
                </div>
            </div>

            <!-- Caption -->
            <div class="mb-3">
                <label for="caption" class="form-label fw-bold">Caption</label>
                <textarea name="caption" id="caption" class="form-control" rows="20" placeholder="Tulis caption di sini..."></textarea>
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
                <input type="checkbox" name="akses_publik" id="akses_publik" value="1" class="form-check-input">
                <label for="akses_publik" class="form-check-label">Akses Publik</label>
            </div>

            <!-- Tambahkan checkbox konfirmasi di form -->
            <div class="form-check mb-3">
                <input type="checkbox" name="confirm_upload" id="confirm_upload" class="form-check-input">
                <label for="confirm_upload" class="form-check-label">Saya ingin mengupload video ke sistem</label>
            </div>

            <!-- Tombol Aksi -->
            <button type="submit" class="btn btn-primary" id="submitButton">Simpan</button>
        </form>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('uploadForm');
        const videoInput = document.getElementById('video');
        const confirmUpload = document.getElementById('confirm_upload');
        const progressContainer = document.getElementById('uploadProgressContainer');
        const progressBar = document.getElementById('uploadProgressBar');
        const progressPercentage = document.getElementById('progressPercentage');
        const uploadStatus = document.getElementById('uploadStatus');
        const submitButton = document.getElementById('submitButton');

        // Nonaktifkan video input awal
        videoInput.disabled = true;
        videoInput.required = false;

        // Aktifkan/nonaktifkan video input berdasarkan checkbox
        confirmUpload.addEventListener('change', function() {
            videoInput.disabled = !this.checked;
            videoInput.required = this.checked;

            if (!this.checked) {
                videoInput.value = ''; // Reset jika dicentang ulang
            }
        });

        // Validasi ukuran file sebelum upload
        videoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const fileSizeMB = file.size / (1024 * 1024);
                if (fileSizeMB > 500) {
                    alert('Ukuran file terlalu besar. Maksimal 500MB.');
                    this.value = '';
                }
            }
        });

        // Handle form submission
        form.addEventListener('submit', function(e) {
            const isUploading = confirmUpload.checked && videoInput.files.length > 0;

            // Jika tidak upload video, biarkan form submit normal
            if (!isUploading) {
                // Non-AJAX submission biasa
                submitButton.disabled = true;
                submitButton.textContent = 'Menyimpan...';
                return true; // Biarkan form submit normal
            }

            // Jika upload video, gunakan AJAX
            e.preventDefault();
            handleAjaxUpload();
        });

        function handleAjaxUpload() {
            const formData = new FormData(form);
            const xhr = new XMLHttpRequest();

            // Tampilkan progress bar
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-primary';
            progressPercentage.textContent = '0%';
            uploadStatus.textContent = 'Mempersiapkan upload...';
            submitButton.disabled = true;
            submitButton.textContent = 'Mengupload...';

            // Track progress upload
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    const roundedPercent = Math.round(percentComplete);

                    progressBar.style.width = percentComplete + '%';
                    progressBar.setAttribute('aria-valuenow', roundedPercent);
                    progressPercentage.textContent = roundedPercent + '%';

                    if (percentComplete < 100) {
                        uploadStatus.textContent = 'Mengupload video... ' + roundedPercent + '%';
                    } else {
                        uploadStatus.textContent = 'Menyimpan data...';
                    }
                }
            });

            // Handle response setelah upload selesai
            xhr.addEventListener('load', function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);

                        if (response.status === 'success') {
                            progressBar.classList.remove('progress-bar-animated', 'bg-primary');
                            progressBar.classList.add('bg-success');
                            uploadStatus.innerHTML = '<span class="text-success">' + response.message + '</span>';

                            setTimeout(function() {
                                window.location.href = '/admin/pelatihan';
                            }, 2000);
                        } else {
                            progressBar.classList.remove('progress-bar-animated', 'bg-primary');
                            progressBar.classList.add('bg-danger');

                            let errorMessage = response.message;
                            if (response.errors) {
                                errorMessage += '<br>' + Object.values(response.errors).join('<br>');
                            }

                            uploadStatus.innerHTML = '<span class="text-danger">' + errorMessage + '</span>';
                            submitButton.disabled = false;
                            submitButton.textContent = 'Simpan';
                        }
                    } catch (e) {
                        progressBar.classList.remove('progress-bar-animated', 'bg-primary');
                        progressBar.classList.add('bg-success');
                        uploadStatus.innerHTML = '<span class="text-success">Upload berhasil! Mengarahkan...</span>';

                        setTimeout(function() {
                            window.location.href = '/admin/pelatihan';
                        }, 2000);
                    }
                } else {
                    progressBar.classList.remove('progress-bar-animated', 'bg-primary');
                    progressBar.classList.add('bg-danger');
                    uploadStatus.innerHTML = '<span class="text-danger">Error: ' + xhr.status + ' - ' + xhr.statusText + '</span>';
                    submitButton.disabled = false;
                    submitButton.textContent = 'Simpan';
                }
            });

            xhr.addEventListener('error', function() {
                progressBar.classList.remove('progress-bar-animated', 'bg-primary');
                progressBar.classList.add('bg-danger');
                uploadStatus.innerHTML = '<span class="text-danger">Terjadi kesalahan jaringan saat upload.</span>';
                submitButton.disabled = false;
                submitButton.textContent = 'Simpan';
            });

            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.send(formData);
        }
    });
</script>

<style>
    .progress-bar-animated {
        animation: progress-bar-stripes 1s linear infinite;
    }

    @keyframes progress-bar-stripes {
        0% {
            background-position: 1rem 0;
        }

        100% {
            background-position: 0 0;
        }
    }
</style>

<?= $this->endSection(); ?>