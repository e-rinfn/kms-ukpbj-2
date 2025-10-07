<?php
// Ganti pengecekan session sesuai dengan yang Anda set di Auth controller
$isLoggedIn = session()->get('logged_in') === true;
$user_id = session()->get('id'); // Sesuai dengan 'id' yang diset di session
?>

<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>

<style>
    .pdf-viewer-container {
        border: 1px solid #eee;
        border-radius: 5px;
        padding: 10px;
        background: #f9f9f9;
        margin-bottom: 20px;
    }

    .pdf-viewer-container embed {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Chatbot Styles */
    .pdf-chat-container {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        background: #fff;
        margin-top: 30px;
    }

    #chat-history {
        height: 300px;
        overflow-y: auto;
        margin-bottom: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
        border: 1px solid #eee;
    }

    .chat-message {
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 8px;
        max-width: 80%;
    }

    .user-message {
        background: #e3f2fd;
        margin-left: auto;
    }

    .bot-message {
        background: #f1f1f1;
        margin-right: auto;
    }

    .chat-input-group {
        display: flex;
        gap: 10px;
    }

    .source-reference {
        font-size: 0.8em;
        color: #666;
        margin-top: 5px;
        border-left: 3px solid #90caf9;
        padding-left: 8px;
    }

    .loading-spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(0, 0, 0, .1);
        border-radius: 50%;
        border-top-color: #007bff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<div class="container my-4">
    <!-- Baris judul + tombol -->

    <div class="card-body">
        <div class="container">

            <div class="row">
                <!-- Kolom PDF Viewer -->
                <div class="col-md-8">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="mb-0"><?= esc($pengetahuan['judul']); ?></h2>
                        <a href="/users/pengetahuan" style="background-color: #EC1928;" class="btn btn-danger rounded-pill fw-bold">
                            <i class="bi bi-arrow-left"></i> Kembali Ke Daftar
                        </a>
                    </div>
                    <?php
                    $pdfPath = WRITEPATH . '../public/assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan'];
                    $pdfUrl = base_url('assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan']);
                    ?>

                    <?php if (!empty($pengetahuan['file_pdf_pengetahuan']) && file_exists($pdfPath)): ?>
                        <div class="pdf-viewer-container">
                            <!-- PDF Viewer -->
                            <embed
                                src="<?= $pdfUrl ?>"
                                type="application/pdf"
                                width="100%"
                                height="400px"
                                style="border: 1px solid #ddd;">

                            <!-- PDF Actions -->
                            <div class="text-end mt-2">
                                <a href="<?= $pdfUrl ?>"
                                    class="btn btn-sm btn-outline-primary"
                                    target="_blank">
                                    <i class="bi bi-eye"></i> Buka PDF
                                </a>
                            </div>

                            <!-- Hidden iframe as alternative viewer -->
                            <iframe
                                src="<?= $pdfUrl ?>"
                                width="100%"
                                height="400px"
                                style="border: 1px solid #ddd; display: none;"
                                id="pdfIframe">
                            </iframe>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <?php if (empty($pengetahuan['file_pdf_pengetahuan'])): ?>
                                File PDF tidak tersedia di database
                            <?php else: ?>
                                File PDF tidak ditemukan di server
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Informasi Dokumen -->
                    <div class="text-end mt-2">
                        <strong>Di Posting Oleh:</strong> <?= esc($pengetahuan['user_nama']); ?>
                    </div>

                    <div class="mt-4">
                        <p><?= $pengetahuan['caption_pengetahuan']; ?></p>
                    </div>
                    <hr>
                    <ul class="list-unstyled">
                        <li>
                            <strong>Dibuat pada:</strong>
                            <?= tanggal_indo($pengetahuan['created_at']); ?>
                        </li>
                        <li>
                            <strong>Diupdate pada:</strong>
                            <?= tanggal_indo($pengetahuan['updated_at']); ?>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4 mb-3">
                    <h5 class="text-center mt-1 fw-bold">DAFTAR PENGETAHUAN</h5>
                    <hr>
                    <div class="border bg-light rounded p-3" style="height: 1000px; overflow-y: auto;">
                        <div class="row g-4">
                            <?php foreach ($pengetahuan_lain as $p): ?>
                                <div class="col-12">
                                    <div class="card h-100">
                                        <?php if (!empty($p['thumbnail_pengetahuan'])): ?>
                                            <img src="<?= base_url('/assets/uploads/pengetahuan/' . $p['thumbnail_pengetahuan']); ?>"
                                                class="card-img-top bg-light p-1 border"
                                                alt="<?= esc($p['judul']); ?>"
                                                style="height: 200px; object-fit: contain;">
                                        <?php else: ?>
                                            <img src="<?= base_url('/assets/img/default-thumbnail.png'); ?>"
                                                class="card-img-top bg-light p-1 border"
                                                alt="Default Thumbnail"
                                                style="height: 200px; object-fit: contain;">
                                        <?php endif; ?>

                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?= esc($p['judul']); ?></h5>
                                            <hr>
                                            <p class="card-text" style="text-align: justify;">
                                                <?= mb_strimwidth(strip_tags($p['caption_pengetahuan']), 0, 200, '...'); ?>
                                            </p>

                                            <div class="mt-auto">
                                                <hr>
                                                <p class="card-text">
                                                    <small class="text-muted"><?= date('d M Y', strtotime($p['created_at'])); ?></small>
                                                </p>
                                                <a href="<?= base_url('users/pengetahuan/view/' . $p['id']); ?>" style="background-color: #341EBB; border: none;" class="btn btn-primary rounded-pill w-100">Lihat Detail</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="mt-4">
            <h5 class="mb-3 text-center">KOMENTAR</h5>
            <?php if (!empty($komentar)): ?>
                <?php
                $limit = 3; // Jumlah komentar yang langsung ditampilkan
                $totalKomentar = count($komentar);
                ?>

                <?php foreach (array_slice($komentar, 0, $limit) as $k): ?>
                    <?php include 'komentar_card.php'; ?>
                <?php endforeach; ?>

                <?php if ($totalKomentar > $limit): ?>
                    <div class="collapse" id="moreComments">
                        <?php foreach (array_slice($komentar, $limit) as $k): ?>
                            <?php include 'komentar_card.php'; ?>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-center m-3">
                        <button class="btn btn-primary rounded-pill" style="background-color: #EC1928; border: none;" type="button" data-bs-toggle="collapse" data-bs-target="#moreComments" aria-expanded="false" aria-controls="moreComments" id="toggleComments">
                            Tampilkan Semua Komentar
                        </button>
                    </div>

                    <script>
                        document.getElementById('toggleComments').addEventListener('click', function() {
                            const btn = this;
                            if (btn.getAttribute('aria-expanded') === 'true') {
                                btn.textContent = 'Tutup Komentar';
                            } else {
                                btn.textContent = 'Tampilkan Semua Komentar';
                            }
                        });
                    </script>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-warning">
                    Belum ada komentar untuk pengetahuan ini.
                </div>
            <?php endif; ?>

            <?php if ($isLoggedIn && $user_id): ?>
                <form action="<?= base_url('pengetahuan/comment/' . $pengetahuan['id']); ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <textarea name="komentar" id="komentar" rows="10" class="form-control" placeholder="Tulis komentar di sini..."></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" style="background-color: #341EBB; border: none;" class="btn btn-primary rounded-pill">Kirim Komentar</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-info">
                    Silakan <a href="<?= base_url('login') ?>">login</a> untuk memberikan komentar.
                </div>
            <?php endif; ?>
        </div>

        <script>
            // Fungsi untuk tombol balas
            document.querySelectorAll('.reply-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const commentId = this.dataset.commentId;
                    const replyForm = document.getElementById('reply-form-' + commentId);

                    // Sembunyikan semua form balas lainnya
                    document.querySelectorAll('.reply-form').forEach(form => {
                        if (form.id !== 'reply-form-' + commentId) {
                            form.style.display = 'none';
                        }
                    });

                    // Toggle form balas
                    if (replyForm.style.display === 'none') {
                        replyForm.style.display = 'block';
                        // Scroll ke form
                        replyForm.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    } else {
                        replyForm.style.display = 'none';
                    }
                });
            });

            // Fungsi untuk tombol batal
            document.querySelectorAll('.cancel-reply').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.reply-form').style.display = 'none';
                });
            });

            // Auto close form balas ketika submit
            document.querySelectorAll('.reply-form form').forEach(form => {
                form.addEventListener('submit', function() {
                    this.closest('.reply-form').style.display = 'none';
                });
            });
        </script>

        <!-- Tambahkan SweetAlert2 (via CDN) -->
        <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            function confirmDelete(event, form) {
                event.preventDefault(); // hentikan submit default

                Swal.fire({
                    title: 'Yakin Hapus?',
                    text: "Komentar ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // submit form jika user konfirmasi
                    }
                });
            }
        </script> -->

    </div>
</div>


<?= $this->endSection(); ?>