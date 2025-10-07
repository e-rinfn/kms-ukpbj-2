<?= $this->extend('templates/template_pengguna'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-0">


    <!-- Carousel Style -->
    <style>
        #customCarousel {
            border-radius: 20px;
            /* Efek melengkung pada seluruh carousel */
            overflow: hidden;
            /* Menghindari konten keluar dari area carousel */
            background-color: #f8f9fa;
            /* Warna background carousel */
        }

        .carousel-item {
            border-radius: 20px;
            /* Efek melengkung pada setiap item */
            padding: 10px;
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 5%;
            /* Mengurangi lebar kontrol */
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: rgba(0, 0, 0, 0.5);
            /* Membuat ikon kontrol lebih terlihat */
            border-radius: 50%;
            padding: 5px;
        }

        .carousel-image {
            width: 100%;
            /* Menyesuaikan lebar dengan elemen kontainer */
            height: 60vh;
            /* Tinggi gambar 50% dari tinggi viewport */
            object-fit: contain;
            /* Menjaga rasio aspek gambar dan menyesuaikan gambar dalam batasan kontainer */
        }

        .carousel-indicators [data-bs-target] {
            width: 12px;
            height: 12px;
            border: 2px solid #000000;
            /* Warna border putih */
            border-radius: 50%;
            background-color: transparent;
            /* Biar hanya border yang tampak */
            margin: 0 5px;
            transition: all 0.3s ease;
            opacity: 0.7;
        }

        .carousel-indicators .active {
            background-color: #EC1928;
            /* Warna isi saat aktif */
            opacity: 1;
        }
    </style>


    <!-- Section Carousel -->
    <section class="py-5">
        <div class="">
            <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">

                <!-- Carousel Items -->
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="/assets/img/Carousel1.png" class="d-block w-100 carousel-image" alt="Slide 1">
                        <div class="carousel-caption d-none d-md-block"></div>
                    </div>
                    <div class="carousel-item">
                        <img src="/assets/img/Carousel2.png" class="d-block w-100 carousel-image" alt="Slide 2">
                        <div class="carousel-caption d-none d-md-block"></div>
                    </div>
                    <div class="carousel-item">
                        <img src="/assets/img/Carousel3.png" class="d-block w-100 carousel-image" alt="Slide 3">
                        <div class="carousel-caption d-none d-md-block"></div>
                    </div>
                </div>

                <!-- Indikator di bawah gambar -->
                <div class="d-flex justify-content-center mt-3">
                    <div class="carousel-indicators position-static">
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1" style="border: 2px solid #000;"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2" style="border: 2px solid #000;"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3" style="border: 2px solid #000;"></button>
                    </div>
                </div>
            </div>

            <!-- Carousel Controls -->
            <div class="d-flex justify-content-center mt-1">
                <button class="btn text-white rounded-pill" style="background-color: #EC1928;" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <button class="btn text-white rounded-pill ms-3" style="background-color: #EC1928;" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Pengetahuan Terbaru -->
    <section style="background-color: #EC1928;" class="my-5 py-4 px-0">
        <div class="container-fluid">
            <h2 class="mb-4 text-white text-center">PENGETAHUAN TERBARU</h2>
            <?php if (!empty($pengetahuan)) : ?>
                <!-- Wrapper scroll -->
                <div class="custom-scroll d-flex flex-row overflow-auto py-3 px-2">
                    <?php foreach ($pengetahuan as $p) : ?>
                        <div class="card me-3 h-100 shadow-sm" style="min-width: 23rem; min-height: 620px; border-radius: 10px;">
                            <?php if (!empty($p['thumbnail_pengetahuan'])) : ?>
                                <img src="<?= ('assets/uploads/pengetahuan/' . $p['thumbnail_pengetahuan']); ?>"
                                    class="card-img-top bg-light p-1 border rounded"
                                    alt="Thumbnail <?= esc($p['judul']) ?>"
                                    style="height: 200px; object-fit: contain;">
                            <?php else : ?>
                                <img src="<?= ('assets/img/default-thumbnail.png'); ?>"
                                    class="card-img-top bg-light p-1 border rounded"
                                    alt="Default Thumbnail"
                                    style="height: 200px; object-fit: contain;">
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold"><?= esc($p['judul']); ?></h5>
                                <hr>
                                <p class="card-text" style="text-align: justify;">
                                    <?= mb_strimwidth(strip_tags($p['caption_pengetahuan']), 0, 200, '...'); ?>
                                </p>
                                <!-- Spacer untuk mendorong tombol ke bawah -->
                                <div class="mt-auto">
                                    <hr>
                                    <p class="card-text">
                                        <small class="text-muted"><?= tanggal_indo($p['created_at']); ?></small>
                                    </p>
                                    <a style="background-color: #341EBB; border: none;" href="/pengetahuan/view/<?= $p['id']; ?>" class=" text-center btn btn-primary w-100 rounded-pill">Lihat Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="alert alert-info">Belum ada pengetahuan tersedia.</div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Custom Scroll Style -->
    <style>
        .custom-scroll {
            scroll-behavior: smooth;
        }

        /* Hilangkan scrollbar default, ganti lebih tipis */
        .custom-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 10px;
            transition: background 0.3s ease;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #fff;
        }
    </style>


    <!-- Pelatihan Terbaru -->
    <section style="background-color: #341EBB;" class="my-5 py-4 px-0">
        <div class="container-fluid">
            <h2 class="mb-4 text-white text-center">PELATIHAN TERBARU</h2>
            <?php if (!empty($pelatihan)) : ?>
                <!-- Wrapper scroll -->
                <div class="custom-scroll d-flex flex-row overflow-auto py-3 px-2">
                    <?php foreach ($pelatihan as $p) : ?>
                        <div class="card me-3 h-100 bg-light p-1 border shadow-sm"
                            style="min-width: 23rem; min-height: 650px; border-radius: 10px;">

                            <?php if (!empty($p['video_pelatihan'])) : ?>
                                <video class="card-img-top rounded bg-dark"
                                    style="height: 200px; object-fit: contain;"
                                    autoplay loop muted playsinline>
                                    <source src="/assets/uploads/pelatihan/<?= $p['video_pelatihan']; ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php else : ?>
                                <img src="<?= base_url('assets/img/default-thumbnail.png'); ?>"
                                    class="card-img-top bg-light p-1 border rounded"
                                    alt="Default Thumbnail"
                                    style="height: 200px; object-fit: contain;">
                            <?php endif; ?>


                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold"><?= esc($p['judul']); ?></h5>
                                <hr>
                                <p class="card-text" style="text-align: justify;">
                                    <?= mb_strimwidth(strip_tags($p['caption_pelatihan']), 0, 200, '...'); ?>
                                </p>
                                <!-- Spacer untuk mendorong tombol ke bawah -->
                                <div class="mt-auto">
                                    <hr>
                                    <p class="card-text">
                                        <small class="text-muted"><?= tanggal_indo($p['created_at']); ?></small>
                                    </p>
                                    <a style="background-color: #EC1928; border: none;" href="/pelatihan/view/<?= $p['id']; ?>" class="btn btn-danger rounded-pill w-100">Lihat Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="alert alert-info">Belum ada pelatihan tersedia.</div>
            <?php endif; ?>
        </div>
    </section>

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