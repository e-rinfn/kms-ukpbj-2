<?php
// Ganti pengecekan session sesuai dengan yang Anda set di Auth controller
$isLoggedIn = session()->get('logged_in') === true;
$user_id = session()->get('id'); // Sesuai dengan 'id' yang diset di session
?>

<?= $this->extend('templates/template_pengguna'); ?>

<?= $this->section('content'); ?>

<div class="container my-4">
    <div class="container row mt-3">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                <h2 class="mb-0"><?= esc($pelatihan['judul']); ?></h2>
                <a href="/pelatihan" class="btn btn-danger rounded-pill">
                    <i class="bi bi-arrow-left"></i> Kembali Ke Daftar
                </a>
            </div>
            <!-- Video Responsive -->
            <div class="mb-4">

                <div class="ratio ratio-16x9 border rounded">
                    <!-- <video controls>
                            <source src="<?= base_url('assets/uploads/pelatihan/' . $pelatihan['video_pelatihan']); ?>" type="video/mp4">
                            Browser Anda tidak mendukung pemutaran video.
                        </video> -->
                    <video controls>
                        <source src="<?= base_url('video/' . $pelatihan['video_pelatihan']); ?>" type="video/mp4">
                        Browser Anda tidak mendukung pemutaran video.
                    </video>
                </div>
            </div>

            <div class="text-end mt-2">
                <p><strong>Dibuat oleh:</strong> <?= esc($pelatihan['user_nama']); ?></p>
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
                                    <p class="card-text" style="text-align: justify;">
                                        <?= mb_strimwidth(strip_tags($p['caption_pelatihan']), 0, 200, '...'); ?>
                                    </p>

                                    <div class="mt-auto">
                                        <hr>
                                        <p class="card-text">
                                            <small class="text-muted"><?= date('d M Y', strtotime($p['created_at'])); ?></small>
                                        </p>
                                        <a href="<?= base_url('pelatihan/view/' . $p['id']); ?>" class="btn btn-sm btn-primary w-100">Lihat Detail</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
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
        function confirmDelete(event) {
            if (!confirm('Apakah Anda yakin ingin menghapus komentar ini?')) {
                event.preventDefault();
            }
        }
    </script>

</div>
<?= $this->endSection(); ?>