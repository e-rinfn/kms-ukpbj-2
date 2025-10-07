<?= $this->extend('templates/template_pengguna'); ?>

<?= $this->section('content'); ?>
<div class="container">
    <div class="row justify-content-center align-items-center">
        <!-- Gambar -->
        <div class="col-md-7 mb-3 mb-md-0">
            <img src="/assets/img/login.jpg" alt="Login Image" class="img-fluid">
        </div>

        <!-- Form Login -->
        <div class="col-md-5">
            <h3 class="mb-4">MASUK</h3>
            <p>Masukan username dan password yang valid!</p>
            <hr>
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= session()->getFlashdata('error'); ?>
                </div>
            <?php endif; ?>

            <form action="/auth/login" method="post">
                <?= csrf_field(); ?>

                <!-- <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div> -->

                <div class="mb-3">
                    <label for="email" class="form-label">E-Mail</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="mb-3 text-center">
                    <button type="submit" class="btn rounded-pill w-50" style="background-color: #EC1928; color: white;">MASUK</button>
                </div>
            </form>

            <p class="text-center mb-0">Belum punya akun? <a href="/pengajuan">Daftar disini</a></p>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>