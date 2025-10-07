<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistem Manajemen Pengetahuan'; ?></title>

    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/docx-preview@0.5.6/dist/docx-preview.css">
    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.5.6/dist/docx-preview.min.js"></script>


    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding-top: 100px;
        }
    </style>
</head>

<style>
    /* Gaya untuk item aktif di navbar */
    .navbar-nav .nav-link.active {
        background-color: #EC1928;
        /* Warna background untuk item aktif */
        color: white !important;
        /* Warna teks */
        border-radius: 25px;
        /* Membuat bentuk oval */
        padding: 8px 16px;
        /* Menambah ruang dalam */
    }

    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        /* Supaya navbar memanjang sesuai layar */
        z-index: 1030;
        /* Pastikan navbar di atas elemen lainnya */
        background-color: white;
        /* Berikan warna latar untuk kontras */
    }

    .navbar-nav .nav-link {
        font-size: 1rem;
        /* Ukuran teks lebih besar */
        font-weight: bold;
        /* Membuat teks lebih tebal (opsional) */
        margin-right: 15px;
        /* Menambahkan jarak antar elemen menu */
    }

    .nav-link:hover {
        color: #EC1928;
        /* Warna teks saat dihover */
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-white border">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/beranda">
            <img src="/assets/img/logo.png" alt="Logo" width="200" class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
            aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <?php if (session()->get('level') === 'admin' || session()->get('level') === 'pegawai' || session()->get('level') === 'user') : ?>
                        <!-- Jika sudah login -->
                        <a class="nav-link <?= uri_string() === 'beranda' ? 'nav-link active' : '' ?>" href="/beranda">BERANDA</a>
                    <?php else : ?>
                        <!-- Jika belum login -->
                        <a class="nav-link <?= uri_string() === '/' ? 'nav-link active' : '' ?>" href="/">BERANDA</a>
                    <?php endif; ?>
                </li>


                <?php if (session()->get('level') === 'admin') : ?>
                    <!-- Menu untuk Admin -->
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'admin/pengetahuan' ? 'nav-link active' : '' ?>" href="/admin/pengetahuan">PENGETAHUAN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'admin/pelatihan' ? 'nav-link active' : '' ?>" href="/admin/pelatihan">PELATIHAN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'admin/template' ? 'nav-link active' : '' ?>" href="/admin/template">TEMPLATE</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'admin/pengajuan' ? 'nav-link active' : '' ?>" href="/admin/pengajuan">PENGAJUAN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'admin/user' ? 'nav-link active' : '' ?>" href="/admin/user">PENGGUNA</a>
                    </li>

                <?php elseif (session()->get('level') === 'pegawai') : ?>
                    <!-- Menu untuk Pegawai -->
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'pegawai/pengetahuan' ? 'nav-link active' : '' ?>" href="/pegawai/pengetahuan">PENGETAHUAN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'pegawai/pelatihan' ? 'nav-link active' : '' ?>" href="/pegawai/pelatihan">PELATIHAN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'pegawai/template' ? 'nav-link active' : '' ?>" href="/pegawai/template">TEMPLATE</a>
                    </li>

                <?php elseif (session()->get('level') === 'user') : ?>
                    <!-- Menu untuk User -->
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'users/pengetahuan' ? 'nav-link active' : '' ?>" href="/users/pengetahuan">PENGETAHUAN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'users/pelatihan' ? 'nav-link active' : '' ?>" href="/users/pelatihan">PELATIHAN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'users/template' ? 'nav-link active' : '' ?>" href="/users/template">TEMPLATE</a>
                    </li>

                <?php else : ?>
                    <!-- Menu Default (Guest / belum login) -->
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'pengetahuan' ? 'nav-link active' : '' ?>" href="/pengetahuan">PENGETAHUAN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'pelatihan' ? 'nav-link active' : '' ?>" href="/pelatihan">PELATIHAN</a>
                    </li>
                <?php endif; ?>


                <?php if (session()->get('id')) : ?>
                    <li class="nav-item">
                        <a style="background-color:#341EBB; color: white;" class="nav-link active <?= uri_string() === 'logout' ? 'nav-link active' : '' ?>" href="/logout">KELUAR <i class="bi bi-box-arrow-in-right" style="color:white; padding-right: 4px;"></i></a>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a style="background-color:#341EBB; color: white;" class="nav-link active <?= uri_string() === 'login' ? 'nav-link active' : '' ?>" href="/login">MASUK <i class="bi bi-box-arrow-in-right" style="color:white; padding-right: 4px;"></i></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>