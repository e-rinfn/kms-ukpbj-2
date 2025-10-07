<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div class="container mt-3">
    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">EDIT PELATIHAN</h2>
            <a href="/pegawai/pelatihan" style="background-color: #EC1928;" class="btn btn-danger rounded-pill fw-bold">
                <i class="bi bi-arrow-left"></i> Kembali Ke Daftar
            </a>
        </div>

        <!-- Alert pesan -->
        <?php if (session()->getFlashdata('pesan')): ?>
            <div class="alert alert-success mt-3 mb-0">
                <?= session()->getFlashdata('pesan'); ?>
            </div>
        <?php endif; ?>

        <!-- Alert error -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger mt-3 mb-0">
                <?= session()->getFlashdata('error'); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="card p-3 border rounded bg-light shadow-sm">
        <form action="/pegawai/pelatihan/update/<?= $pelatihan['id']; ?>" method="post" enctype="multipart/form-data" id="uploadForm">
            <?= csrf_field(); ?>
            <input type="hidden" name="_method" value="PUT">

            <!-- Judul -->
            <div class="mb-3">
                <label for="judul" class="form-label fw-bold">Judul <span class="text-danger">*</span></label>
                <input type="text" name="judul" id="judul"
                    class="form-control <?= session('errors.judul') ? 'is-invalid' : '' ?>"
                    value="<?= old('judul', $pelatihan['judul']); ?>"
                    required placeholder="Masukkan judul pelatihan">
                <?php if (session('errors.judul')): ?>
                    <div class="invalid-feedback">
                        <?= session('errors.judul') ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pilihan Upload Video -->
            <div class="mb-3">
                <label class="form-label fw-bold">Pilihan Video</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="upload_choice" id="choice_keep" value="keep_existing" checked>
                    <label class="form-check-label" for="choice_keep">
                        Pertahankan video saat ini
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="upload_choice" id="choice_youtube" value="youtube_only">
                    <label class="form-check-label" for="choice_youtube">
                        Gunakan YouTube saja
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="upload_choice" id="choice_upload" value="upload_video">
                    <label class="form-check-label" for="choice_upload">
                        Upload video baru
                    </label>
                </div>
            </div>

            <!-- Video Saat Ini -->
            <div id="currentVideoSection" class="mb-3 p-3 border rounded bg-light">
                <label class="form-label fw-bold">Video Saat Ini:</label>
                <?php if (!empty($pelatihan['video_pelatihan'])): ?>
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-file-earmark-play-fill text-primary fs-4"></i>
                        <div>
                            <div><?= esc($pelatihan['video_pelatihan']); ?></div>
                            <small class="text-muted">Ukuran:
                                <?php
                                $filePath = 'assets/uploads/pelatihan/' . $pelatihan['video_pelatihan'];
                                if (file_exists($filePath)) {
                                    $size = filesize($filePath) / (1024 * 1024);
                                    echo number_format($size, 2) . ' MB';
                                } else {
                                    echo 'File tidak ditemukan';
                                }
                                ?>
                            </small>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-muted">
                        <i class="bi bi-exclamation-circle"></i> Tidak ada video yang diupload
                    </div>
                <?php endif; ?>
            </div>

            <!-- Section Upload Video Baru -->
            <div id="videoUploadSection" class="mb-3" style="display: none;">
                <label for="video" class="form-label fw-bold">Video Baru</label>
                <input type="file" name="video" id="video"
                    class="form-control <?= session('errors.video') ? 'is-invalid' : '' ?>"
                    accept=".mp4,.mov,.avi">
                <small class="text-muted">Format: mp4, mov, avi | Maksimal 500MB</small>
                <?php if (session('errors.video')): ?>
                    <div class="invalid-feedback">
                        <?= session('errors.video') ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Link YouTube -->
            <div class="mb-3">
                <label for="link_youtube" class="form-label fw-bold">Link YouTube</label>
                <input type="text" name="link_youtube" id="link_youtube" class="form-control"
                    value="<?= old('link_youtube', $pelatihan['link_youtube']); ?>"
                    placeholder="Masukkan link video YouTube">
                <small class="text-muted">Opsional - untuk backup video</small>
            </div>

            <!-- Caption -->
            <div class="mb-3">
                <label for="caption" class="form-label fw-bold">Caption <span class="text-danger">*</span></label>
                <textarea name="caption" id="caption"
                    class="form-control <?= session('errors.caption') ? 'is-invalid' : '' ?>"
                    rows="20" required placeholder="Tulis caption di sini..."><?= old('caption', $pelatihan['caption_pelatihan']); ?></textarea>
                <?php if (session('errors.caption')): ?>
                    <div class="invalid-feedback">
                        <?= session('errors.caption') ?>
                    </div>
                <?php endif; ?>
            </div>

            <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
            <script>
                ClassicEditor
                    .create(document.querySelector('#caption'), {
                        toolbar: [
                            'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'
                        ],
                        height: '500px'
                    })
                    .catch(error => {
                        console.error(error);
                    });
            </script>

            <!-- Akses Publik -->
            <div class="form-check mb-3">
                <input type="checkbox" name="akses_publik" id="akses_publik" value="1"
                    class="form-check-input" <?= old('akses_publik', $pelatihan['akses_publik']) ? 'checked' : '' ?>>
                <label for="akses_publik" class="form-check-label">Akses Publik</label>
            </div>

            <!-- Tombol Aksi -->
            <button type="submit" id="submitButton" class="btn btn-primary">Simpan Perubahan</button>
            <a href="/pegawai/pelatihan" class="btn btn-secondary">Batal</a>


            <!-- Progress Upload -->
            <div id="uploadProgressContainer" class="mt-3" style="display:none;">
                <div class="progress">
                    <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                        role="progressbar" style="width:0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="mt-2 d-flex justify-content-between">
                    <span id="progressPercentage">0%</span>
                    <span id="uploadStatus" class="text-muted">Menunggu upload...</span>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const choiceKeep = document.getElementById('choice_keep');
        const choiceYouTube = document.getElementById('choice_youtube');
        const choiceUpload = document.getElementById('choice_upload');
        const currentVideoSection = document.getElementById('currentVideoSection');
        const videoUploadSection = document.getElementById('videoUploadSection');
        const videoInput = document.getElementById('video');

        // Toggle sections berdasarkan pilihan
        function updateSections() {
            if (choiceKeep.checked) {
                currentVideoSection.style.display = 'block';
                videoUploadSection.style.display = 'none';
                videoInput.disabled = true;
                videoInput.required = false;
            } else if (choiceYouTube.checked) {
                currentVideoSection.style.display = 'none';
                videoUploadSection.style.display = 'none';
                videoInput.disabled = true;
                videoInput.required = false;
            } else if (choiceUpload.checked) {
                currentVideoSection.style.display = 'none';
                videoUploadSection.style.display = 'block';
                videoInput.disabled = false;
                videoInput.required = true;
            }
        }

        // Event listeners untuk radio buttons
        choiceKeep.addEventListener('change', updateSections);
        choiceYouTube.addEventListener('change', updateSections);
        choiceUpload.addEventListener('change', updateSections);

        // Inisialisasi awal
        updateSections();

        // Validasi ukuran file
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

        // Validasi form sebelum submit
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const isUploading = choiceUpload.checked;

            if (isUploading && !videoInput.files.length) {
                e.preventDefault();
                alert('Pilih video terlebih dahulu!');
                return false;
            }

            return true;
        });
    });
</script>

<style>
    .form-check {
        margin-bottom: 0.5rem;
    }

    #currentVideoSection {
        background-color: #f8f9fa;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('uploadForm');
        const videoInput = document.getElementById('video');
        const choiceUpload = document.getElementById('choice_upload');
        const progressContainer = document.getElementById('uploadProgressContainer');
        const progressBar = document.getElementById('uploadProgressBar');
        const progressPercentage = document.getElementById('progressPercentage');
        const uploadStatus = document.getElementById('uploadStatus');
        const submitButton = document.getElementById('submitButton');

        // Validasi ukuran file
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

        // Submit handler
        form.addEventListener('submit', function(e) {
            const isUploading = choiceUpload.checked && videoInput.files.length > 0;

            if (!isUploading) {
                // Normal submit tanpa upload video baru
                submitButton.disabled = true;
                submitButton.textContent = 'Menyimpan...';
                return true;
            }

            // AJAX upload untuk video baru
            e.preventDefault();
            handleAjaxUpload();
        });

        function handleAjaxUpload() {
            const formData = new FormData(form);
            const xhr = new XMLHttpRequest();

            // Show progress UI
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-primary';
            progressPercentage.textContent = '0%';
            uploadStatus.textContent = 'Mempersiapkan upload...';
            submitButton.disabled = true;
            submitButton.textContent = 'Mengupload...';

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

            xhr.addEventListener('load', function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);

                        if (response.status === 'success') {
                            progressBar.classList.remove('progress-bar-animated', 'bg-primary');
                            progressBar.classList.add('bg-success');
                            uploadStatus.innerHTML = '<span class="text-success">' + response.message + '</span>';

                            setTimeout(() => window.location.href = '/pegawai/pelatihan', 2000);
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
                        // fallback success
                        progressBar.classList.remove('progress-bar-animated', 'bg-primary');
                        progressBar.classList.add('bg-success');
                        uploadStatus.innerHTML = '<span class="text-success">Upload berhasil! Mengarahkan...</span>';

                        setTimeout(() => window.location.href = '/pegawai/pelatihan', 2000);
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