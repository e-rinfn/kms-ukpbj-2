<?= $this->extend('templates/template_pengguna'); ?>

<?= $this->section('content'); ?>

<style>
    .section-with-bg {
        background-image: url('/assets/img/bg.png');
        /* Ganti dengan URL gambar Anda */
        background-color: #341EBB;
        /* Warna latar belakang jika gambar tidak tersedia */
        background-size: cover;
        /* Mengatur agar gambar mengisi seluruh area */
        background-position: center;
        /* Menempatkan gambar di tengah */
        background-attachment: fixed;
        /* Agar latar belakang tetap saat scrolling (opsional) */
        background-repeat: no-repeat;
        /* Mencegah pengulangan gambar */
        padding: 50px 0;
        /* Memberikan ruang atas dan bawah pada section */
        color: #fff;
        /* Warna teks agar kontras dengan latar belakang */
    }
</style>


<div class="container-fliud p-0">


    <?php
    $q = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : '';

    $filtered_pelatihan = array_filter($pelatihan, function ($item) use ($q) {
        if (!$q) return true;

        $judul = strtolower($item['judul'] ?? '');
        $caption = strtolower($item['caption_pelatihan'] ?? '');

        // Cari di judul atau caption
        return strpos($judul, $q) !== false || strpos($caption, $q) !== false;
    });
    ?>




    <!-- Daftar Pelatihan -->
    <section class="py-5 section-with-bg min-vh-100">
        <div class="container card bg-transparent border-0 mb-4">
            <!-- Judul -->
            <div class="bg-light rounded p-3">
                <h2 class="mb-2 fw-bold text-center">Daftar Pelatihan</h2>
                <p class="text-muted text-center mb-4">
                    Temukan berbagai pelatihan yang telah dibagikan.
                </p>
            </div>
            <br>
            <!-- Form Search -->
            <form method="GET" action="<?= base_url('pelatihan'); ?>">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-10 col-sm-12">
                        <div class="input-group">
                            <a href="<?= base_url('pelatihan'); ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-repeat"></i> Reset
                            </a>
                            <input type="text" name="q" class="form-control text-center"
                                placeholder="Cari pelatihan..."
                                value="<?= esc($keyword ?? '') ?>"> <!-- Diubah dari $q ke $keyword -->
                            <button type="submit" class="btn btn-primary" style="background-color: #EC1928; border: none;">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>




        <div class="container">

            <!-- Pemberitahuan jika data kosong -->
            <?php if (empty($filtered_pelatihan)) : ?>
                <div class="alert alert-warning text-center">
                    <strong>Data tidak ditemukan.</strong><br>
                    Coba gunakan kata kunci lain atau periksa ejaan pencarian.
                </div>
            <?php endif; ?>


            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($filtered_pelatihan as $p): ?>
                    <?php if ($p['akses_publik']): ?>
                        <!-- Card pelatihan -->
                        <div class="col">
                            <div class="card h-100" style="min-width: 20rem; min-height: 650px;">
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
                                            <small class="text-muted"><?= tanggal_indo($p['created_at']); ?></small>
                                        </p>
                                        <a href="pelatihan/view/<?= $p['id']; ?>" style="background-color: #341EBB; border: none;" class="btn btn-primary rounded-pill w-100">Detail</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <style>
        .pagination .page-link {
            color: #341EBB;
            /* Warna teks */
            border-color: #341EBB;
            /* Border */
        }

        .pagination .page-link:hover {
            background-color: #341EBB;
            color: #fff;
        }

        .pagination .page-item.active .page-link {
            background-color: #341EBB;
            border-color: #341EBB;
            color: #fff;
        }
    </style>


    <?php
    $surroundCount = 2; // Jumlah halaman yang ditampilkan di sekitar halaman aktif
    $current = $pager->getCurrentPage();
    $last = $pager->getPageCount();
    ?>

    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mt-3">
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
                    <a class="page-link " href="<?= $pager->getPageURI($i) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor ?>
        </ul>
    </nav>

    <!-- <div class="text-center text-muted small mt-2">
        Menampilkan <?= ($current - 1) * $pager->getPerPage() + 1 ?>
        sampai <?= min($current * $pager->getPerPage(), $pager->getTotal()) ?>
        dari <?= $pager->getTotal() ?> data
    </div> -->

    <section class="my-3 py-4 px-0" style="background-color: #EC1928;">
        <div class="container">
            <div class="row">
                <!-- Bagian Peta -->
                <div class="col-lg-7 mb-4 px-4"> <!-- Menambahkan padding kiri dan kanan -->
                    <h3 class="mb-3 text-white">Alamat Kami</h3>
                    <!-- Google Maps Embed -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.3358534195327!2d108.1943916757442!3d-7.316117271940007!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f50d180e2f13d%3A0xa98230685291996f!2sLPSE%20Kota%20Tasikmalaya!5e0!3m2!1sid!2sid!4v1732288774068!5m2!1sid!2sid" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                <!-- Bagian Kontak -->
                <div class="col-lg-5 mb-4 px-4 mt-4"> <!-- Menambahkan margin atas untuk memberikan jarak -->
                    <h3 class="mb-3 text-white">Hubungi Kami</h3>
                    <p class="text-white"><strong><i class="bi bi-geo-alt"></i> Alamat:</strong> Balai Kota Tasikmalaya</p>
                    <p class="text-white"><strong><i class="bi bi-telephone"></i> Telepon:</strong> 082217754652</p>
                    <p class="text-white"><strong><i class="bi bi-envelope"></i> Email:</strong> helpdesk.lpsetasikmalayakota@gmail.com</p>

                    <h4 class="mt-4 text-white">Tautan Terkait</h4>
                    <ul class="list-unstyled">
                        <li><a href="index.php" target="_blank" class="text-white">Beranda</a></li>
                        <li><a href="hubungiKami.php" target="_blank" class="text-white">Hubungi Kami</a></li>
                        <li><a href="login.php" target="_blank" class="text-white">Masuk</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

</div>

<?= $this->endSection(); ?>