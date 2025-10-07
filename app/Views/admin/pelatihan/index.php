<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div class="container mt-3">
    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">DAFTAR PELATIHAN</h2>
            <a href="/admin/pelatihan/create" style="background-color:#EC1928;" class="btn btn-danger rounded-pill fw-bold">
                <i class="bi bi-plus-circle"></i> Tambah Pelatihan
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
        <div class="card-body">
            <!-- Form Pencarian dan Filter -->
            <form action="" method="get" class="row g-3">
                <div class="col-md-8">
                    <label for="search" class="form-label">Pencarian</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search"
                            placeholder="Cari berdasarkan judul, deskripsi atau pembuat..."
                            value="<?= esc($search ?? '') ?>">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="akses" class="form-label">Filter Akses Publik</label>
                    <select class="form-select" id="akses" name="akses">
                        <option value="">Semua</option>
                        <option value="1" <?= ($filterAkses ?? '') === '1' ? 'selected' : '' ?>>Publik</option>
                        <option value="0" <?= ($filterAkses ?? '') === '0' ? 'selected' : '' ?>>Privat</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" style="background-color: #341EBB; border: none;" class="btn btn-primary w-100">Terapkan</button>
                </div>
            </form>

            <?php if (!empty($search) || isset($filterAkses)): ?>
                <div class="mb-3">
                    <a href="/admin/pelatihan" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <!-- Tabel Pelatihan -->
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light text-center">
                    <tr class="text-center">
                        <th>No</th>
                        <th>Judul</th>
                        <th>Video</th>
                        <th>Akses Publik</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pelatihan)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">Tidak ada data pelatihan</td>
                        </tr>
                    <?php else: ?>
                        <?php
                        // Ganti perhitungan nomor urut dengan:
                        $i = 1 + (($pager->getCurrentPage() - 1) * $pager->getPerPage());
                        ?> <?php foreach ($pelatihan as $p): ?>
                            <tr>
                                <td class="text-center"><?= $i++; ?></td>
                                <td>
                                    <?= esc($p['judul']); ?>
                                    <?php if (strlen($p['caption_pelatihan']) > 40): ?>
                                        <small class="text-muted d-block"><?= substr(esc($p['caption_pelatihan']), 0, 40) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($p['video_pelatihan'])): ?>
                                        <!-- Tombol Lihat Video Lokal -->
                                        <a href="#"
                                            class="text-decoration-none btn-play-local"
                                            data-bs-toggle="modal"
                                            data-bs-target="#videoModal"
                                            data-src="<?= base_url('/assets/uploads/pelatihan/' . $p['video_pelatihan']); ?>"
                                            data-title="<?= esc($p['judul']) ?>">
                                            <i class="bi bi-play-circle-fill text-danger"></i> Lihat Video
                                        </a>
                                    <?php elseif (!empty($p['link_youtube'])): ?>
                                        <!-- Tombol Lihat Video YouTube -->
                                        <a href="#"
                                            class="text-decoration-none btn-play-youtube"
                                            data-bs-toggle="modal"
                                            data-bs-target="#videoModal"
                                            data-youtube-url="<?= esc($p['link_youtube']) ?>"
                                            data-title="<?= esc($p['judul']) ?>">
                                            <i class="bi bi-youtube text-danger"></i> Lihat YouTube
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada video</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-<?= $p['akses_publik'] ? 'success' : 'warning' ?>">
                                        <?= $p['akses_publik'] ? 'Publik' : 'Tidak' ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= esc($p['user_nama']); ?></td>

                                <td class="text-center">
                                    <a href="/admin/pelatihan/view/<?= $p['id']; ?>" class="btn btn-sm btn-info">Detail</a>
                                    <a href="/admin/pelatihan/edit/<?= $p['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="/admin/pelatihan/delete/<?= $p['id']; ?>" method="post" class="d-inline delete-form">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= $p['id']; ?>">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal Video (Global untuk kedua jenis video) -->
        <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="videoModalLabel">Video Pelatihan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Container untuk Video Lokal -->
                        <div id="localVideoContainer" class="ratio ratio-16x9" style="display: none;">
                            <video id="localVideoPlayer" class="w-100" controls>
                                <source src="" type="video/mp4">
                                Browser Anda tidak mendukung pemutaran video.
                            </video>
                        </div>

                        <!-- Container untuk YouTube -->
                        <div id="youtubeVideoContainer" class="ratio ratio-16x9" style="display: none;">
                            <iframe
                                id="youtubePlayer"
                                src=""
                                title="YouTube video player"
                                allowfullscreen
                                class="w-100 h-100"
                                style="border:0;"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin">
                            </iframe>
                        </div>

                        <!-- Fallback message -->
                        <div id="videoError" class="text-center" style="display: none;">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                <p class="mb-0">Tidak dapat memuat video</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <?php
        $surroundCount = 2; // Jumlah halaman yang ditampilkan di sekitar halaman aktif
        $current = $pager->getCurrentPage();
        $last = $pager->getPageCount();
        ?>

        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                $surroundCount = 2; // Jumlah halaman di sekitar halaman aktif
                $current = $pager->getCurrentPage();
                $last = $pager->getPageCount();

                // Hitung range halaman yang akan ditampilkan
                $start = max(1, $current - $surroundCount);
                $end = min($last, $current + $surroundCount);

                // Tampilkan angka halaman saja
                for ($i = $start; $i <= $end; $i++) : ?>
                    <li class="page-item <?= $i == $current ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $pager->getPageURI($i) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor ?>
            </ul>
        </nav>

        <div class="text-center text-muted small mt-2">
            Menampilkan <?= ($current - 1) * $pager->getPerPage() + 1 ?>
            sampai <?= min($current * $pager->getPerPage(), $pager->getTotal()) ?>
            dari <?= $pager->getTotal() ?> data
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const videoModal = document.getElementById('videoModal');
        const localVideoContainer = document.getElementById('localVideoContainer');
        const youtubeVideoContainer = document.getElementById('youtubeVideoContainer');
        const videoError = document.getElementById('videoError');
        const modalTitle = document.getElementById('videoModalLabel');

        // Fungsi untuk extract YouTube ID dari berbagai format URL
        function extractYouTubeId(url) {
            const patterns = [
                /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/,
                /youtube\.com\/embed\/([^\/]+)/,
                /youtu\.be\/([^\/]+)/
            ];

            for (const pattern of patterns) {
                const match = url.match(pattern);
                if (match) {
                    return match[1];
                }
            }
            return null;
        }

        // Fungsi untuk membuat embed URL YouTube
        function createYouTubeEmbedUrl(url) {
            const videoId = extractYouTubeId(url);
            if (videoId) {
                return `https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1&autoplay=1`;
            }
            return null;
        }

        // Handle tombol play video lokal
        document.querySelectorAll(".btn-play-local").forEach(btn => {
            btn.addEventListener("click", function() {
                const videoSrc = this.getAttribute("data-src");
                const title = this.getAttribute("data-title");

                // Reset semua container
                localVideoContainer.style.display = 'block';
                youtubeVideoContainer.style.display = 'none';
                videoError.style.display = 'none';

                // Set title modal
                modalTitle.textContent = title || 'Video Pelatihan';

                // Setup video lokal
                const videoPlayer = document.getElementById('localVideoPlayer');
                const source = videoPlayer.querySelector("source");
                source.src = videoSrc;
                videoPlayer.load();
            });
        });

        // Handle tombol play YouTube
        document.querySelectorAll(".btn-play-youtube").forEach(btn => {
            btn.addEventListener("click", function() {
                const youtubeUrl = this.getAttribute("data-youtube-url");
                const title = this.getAttribute("data-title");
                const embedUrl = createYouTubeEmbedUrl(youtubeUrl);

                // Reset semua container
                localVideoContainer.style.display = 'none';
                youtubeVideoContainer.style.display = 'block';
                videoError.style.display = 'none';

                // Set title modal
                modalTitle.textContent = title || 'Video YouTube';

                if (embedUrl) {
                    // Setup YouTube iframe
                    const iframe = document.getElementById('youtubePlayer');
                    iframe.src = embedUrl;
                } else {
                    // Jika URL YouTube tidak valid
                    youtubeVideoContainer.style.display = 'none';
                    videoError.style.display = 'block';
                    videoError.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <p class="mb-2">Link YouTube tidak valid</p>
                            <a href="${youtubeUrl}" target="_blank" class="btn btn-sm btn-primary">
                                Buka di YouTube
                            </a>
                        </div>
                    `;
                }
            });
        });

        // Saat modal ditutup, reset semua player
        videoModal.addEventListener('hidden.bs.modal', function() {
            // Reset video lokal
            const videoPlayer = document.getElementById('localVideoPlayer');
            videoPlayer.pause();
            videoPlayer.currentTime = 0;

            // Reset YouTube
            const iframe = document.getElementById('youtubePlayer');
            iframe.src = '';

            // Sembunyikan semua container
            localVideoContainer.style.display = 'none';
            youtubeVideoContainer.style.display = 'none';
            videoError.style.display = 'none';
        });

        // SweetAlert untuk konfirmasi hapus
        const deleteButtons = document.querySelectorAll(".btn-delete");
        deleteButtons.forEach(button => {
            button.addEventListener("click", function() {
                const form = this.closest("form");

                Swal.fire({
                    title: "Yakin ingin menghapus?",
                    text: "Data yang sudah dihapus tidak bisa dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>

<?= $this->endSection(); ?>