<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/">
            <img src="/assets/img/logo.png" alt="Logo" width="250" class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
            aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() === '' ? 'nav-link active' : '' ?>" href="/">BERANDA</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() === 'pengetahuan' ? 'nav-link active' : '' ?>" href="/pengetahuan">PENGETAHUAN</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() === 'pelatihan' ? 'nav-link active' : '' ?>" href="/pelatihan">PELATIHAN</a>
                </li>

                <?php if (session()->get('level') === 'admin') : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'admin/pengetahuan' ? 'nav-link active' : '' ?>" href="/admin/pengetahuan">Admin Pengetahuan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'admin/pelatihan' ? 'nav-link active' : '' ?>" href="/admin/pelatihan">Admin Pelatihan</a>
                    </li>
                <?php endif; ?>

                <?php if (session()->get('level') === 'user') : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'pegawai/pengetahuan' ? 'nav-link active' : '' ?>" href="/pegawai/pengetahuan">Pegawai Pengetahuan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'pegawai/pelatihan' ? 'nav-link active' : '' ?>" href="/pegawai/pelatihan">Pegawai Pelatihan</a>
                    </li>
                <?php endif; ?>

                <?php if (session()->get('id')) : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'logout' ? 'nav-link active' : '' ?>" href="/logout">KELUAR</a>
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