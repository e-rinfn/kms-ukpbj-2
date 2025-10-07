<?php
// Ganti pengecekan session sesuai dengan yang Anda set di Auth controller
$isLoggedIn = session()->get('logged_in') === true;
$user_id = session()->get('id'); // Sesuai dengan 'id' yang diset di session
?>

<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>

<div class="container my-4">
    <div class="card-body">
        <div class="container row mt-3">
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                    <h2 class="mb-0"><?= esc($pelatihan['judul']); ?></h2>
                    <a href="/users/pelatihan" class="btn btn-danger rounded-pill">
                        <i class="bi bi-arrow-left"></i> Kembali Ke Daftar
                    </a>
                </div>
                <!-- Video Responsive -->
                <div class="mb-4">
                    <?php if (!empty($pelatihan['video_pelatihan'])): ?>
                        <!-- Tampilkan video lokal jika ada -->
                        <div class="ratio ratio-16x9 border rounded">
                            <video controls preload="auto"
                                onloadstart="this.volume=0.5"
                                oncanplay="this.muted=false">
                                <source src="<?= base_url('assets/uploads/pelatihan/' . $pelatihan['video_pelatihan']); ?>" type="video/mp4">
                                Browser Anda tidak mendukung pemutaran video.
                            </video>
                            <div id="bufferingIndicator" style="display: none;">
                                <div class="text-center text-white bg-dark p-2">
                                    <i class="bi bi-hourglass-split"></i> Memuat video...
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Tampilkan iframe YouTube jika tidak ada video lokal -->
                        <div class="ratio ratio-16x9 border rounded">
                            <?php
                            // Extract YouTube video ID dari link
                            $youtube_url = $pelatihan['link_youtube'];
                            $video_id = '';

                            // Pattern untuk berbagai format YouTube URL
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtube_url, $matches)) {
                                $video_id = $matches[1];
                            }

                            // Jika berhasil extract video ID, gunakan embed URL yang benar
                            if (!empty($video_id)) {
                                $embed_url = "https://www.youtube.com/embed/{$video_id}?rel=0&modestbranding=1&autoplay=0";
                            } else {
                                // Fallback ke URL asli jika gagal extract
                                $embed_url = $youtube_url;
                            }
                            ?>

                            <iframe
                                src="<?= esc($embed_url); ?>"
                                title="YouTube video player"
                                allowfullscreen
                                class="w-100 h-100"
                                style="border:0;"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                loading="lazy">
                            </iframe>
                        </div>
                    <?php endif; ?>

                    <script>
                        class VideoOptimizer {
                            constructor(videoId) {
                                this.video = document.getElementById(videoId);
                                this.bufferingIndicator = document.getElementById('bufferingIndicator');
                                this.init();
                            }

                            init() {
                                this.setupEventListeners();
                                this.setOptimizationSettings();
                            }

                            setupEventListeners() {
                                this.video.addEventListener('loadstart', () => this.onLoadStart());
                                this.video.addEventListener('canplay', () => this.onCanPlay());
                                this.video.addEventListener('waiting', () => this.onWaiting());
                                this.video.addEventListener('playing', () => this.onPlaying());
                                this.video.addEventListener('progress', () => this.onProgress());
                            }

                            setOptimizationSettings() {
                                // Preload hanya metadata
                                this.video.preload = 'metadata';

                                // Set buffer size (jika didukung)
                                if (this.video.buffered) {
                                    this.video.addEventListener('progress', () => {
                                        if (this.video.buffered.length > 0) {
                                            const bufferedEnd = this.video.buffered.end(0);
                                            const currentTime = this.video.currentTime;

                                            // Jika buffer kurang dari 10 detik, pause sementara
                                            if (bufferedEnd - currentTime < 10) {
                                                this.video.playbackRate = 0.5; // Slow down jika buffer rendah
                                            }
                                        }
                                    });
                                }
                            }

                            onLoadStart() {
                                console.log('Video mulai dimuat');
                                this.showBufferingIndicator();
                            }

                            onCanPlay() {
                                console.log('Video siap diputar');
                                this.hideBufferingIndicator();
                            }

                            onWaiting() {
                                console.log('Video buffering...');
                                this.showBufferingIndicator();
                            }

                            onPlaying() {
                                console.log('Video mulai diputar');
                                this.hideBufferingIndicator();
                            }

                            onProgress() {
                                // Monitor loading progress
                                if (this.video.buffered.length > 0) {
                                    const buffered = this.video.buffered.end(0);
                                    const duration = this.video.duration;
                                    const percent = (buffered / duration) * 100;
                                    console.log(`Video loaded: ${percent.toFixed(1)}%`);
                                }
                            }

                            showBufferingIndicator() {
                                if (this.bufferingIndicator) {
                                    this.bufferingIndicator.style.display = 'block';
                                }
                            }

                            hideBufferingIndicator() {
                                if (this.bufferingIndicator) {
                                    this.bufferingIndicator.style.display = 'none';
                                }
                            }
                        }

                        // Initialize video optimizer
                        document.addEventListener('DOMContentLoaded', function() {
                            new VideoOptimizer('trainingVideo');
                        });
                    </script>

                </div>



                <div class="text-end mt-2">
                    <p><strong>Dibuat oleh:</strong> <?= esc($pelatihan['user_nama']); ?></p>
                    <p><strong>Akses Publik:</strong> <?= $pelatihan['akses_publik'] ? 'Ya' : 'Tidak'; ?></p>
                </div>


                <div class="my-3">
                    <p><?= $pelatihan['caption_pelatihan']; ?></p>
                </div>
                <hr>
                <p><strong>Dibuat pada:</strong> <?= tanggal_indo($pelatihan['created_at']); ?></p>
                <p><strong>Diupdate pada:</strong> <?= tanggal_indo($pelatihan['updated_at']); ?></p>

            </div>

            <div class="col-md-4 mb-3 border bg-light rounded p-2">
                <h5 class="text-center mt-1">DAFTAR PELATIHAN</h5>
                <hr>
                <div class="p-3" style="height: 1000px; overflow-y: auto;">
                    <div class="row g-4">
                        <?php foreach ($pelatihan_lain as $p): ?>
                            <div class="col-12">
                                <div class="card h-100">
                                    <?php if (!empty($p['video_pelatihan'])) : ?>
                                        <video class="card-img-top"
                                            style="height: 200px; object-fit: contain;"
                                            autoplay
                                            muted
                                            loop
                                            playsinline>
                                            <source src="/assets/uploads/pelatihan/<?= $p['video_pelatihan']; ?>" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    <?php else : ?>
                                        <img src="/assets/img/default-thumbnail.png"
                                            class="card-img-top bg-light p-1 border"
                                            alt="Default Thumbnail"
                                            style="height: 200px; object-fit: contain;">
                                    <?php endif; ?>

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?= esc($p['judul']); ?></h5>
                                        <hr>
                                        <p class="card-text">
                                            <?= $p['caption_pelatihan'] > 150 ? substr($p['caption_pelatihan'], 0, 150) . '...' : $p['caption_pelatihan']; ?>
                                        </p>

                                        <div class="mt-auto">
                                            <hr>
                                            <p class="card-text">
                                                <small class="text-muted"><?= date('d M Y', strtotime($p['created_at'])); ?></small>
                                            </p>
                                            <a href="<?= base_url('users/pelatihan/view/' . $p['id']); ?>" class="btn btn-sm btn-primary w-100">Lihat Detail</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
        <hr>
        <div class="mt-4">
            <?php if ($isLoggedIn && $user_id): ?>
                <form action="<?= base_url('pelatihan/comment/' . $pelatihan['id']); ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <textarea name="komentar" class="form-control" rows="3" placeholder="Tulis komentar..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </form>
            <?php else: ?>
                <div class="alert alert-info">
                    Silakan <a href="<?= base_url('login') ?>">login</a> untuk memberikan komentar.
                </div>
            <?php endif; ?>

            <hr>

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

                    <div class="text-center mt-3">
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#moreComments" aria-expanded="false" aria-controls="moreComments" id="toggleComments">
                            Lihat
                        </button>
                    </div>

                    <script>
                        document.getElementById('toggleComments').addEventListener('click', function() {
                            const btn = this;
                            if (btn.getAttribute('aria-expanded') === 'true') {
                                btn.textContent = 'Tutup';
                            } else {
                                btn.textContent = 'Tampilkan';
                            }
                        });
                    </script>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-warning">
                    Belum ada komentar untuk pelatihan ini.
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
    </div>
</div>
<?= $this->endSection(); ?>