<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>

<div class="container mt-3">

    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">TAMBAH PENGGUNA BARU</h2>
            <a href="/admin/user" class="btn btn-danger rounded-pill">
                <i class="bi bi-arrow-left"></i> Kembali Ke Daftar
            </a>
        </div>

        <!-- Alert pesan -->
        <?php if (session()->getFlashdata('pesan')): ?>
            <div class="alert alert-success mt-3 mb-0">
                <?= session()->getFlashdata('pesan'); ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <!-- Form Filter dan Pencarian -->
    <div class="card p-3 border rounded bg-light">

        <form action="/admin/user/store" method="post" autocomplete="off">
            <?= csrf_field() ?>

            <div class="row g-3">

                <div class="col-md-6">
                    <label for="username" class="form-label fw-bold ">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>"
                        id="username" name="username"
                        value="<?= old('username') ?>"
                        required>
                    <small class="form-text text-muted">Minimal 3 karakter, tanpa spasi</small>
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label fw-bold ">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
                            id="password" name="password"
                            required>

                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="bi bi-eye"></i>
                        </button>

                    </div>
                    <small class="form-text text-muted">Minimal 6 karakter</small>
                </div>



                <div class="col-md-6">
                    <label for="nama" class="form-label fw-bold ">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= session('errors.nama') ? 'is-invalid' : '' ?>"
                        id="nama" name="nama"
                        value="<?= old('nama') ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label fw-bold ">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                        id="email" name="email"
                        value="<?= old('email') ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label for="nik" class="form-label fw-bold ">NIK <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= session('errors.nik') ? 'is-invalid' : '' ?>"
                        id="nik" name="nik"
                        value="<?= old('nik') ?>"
                        maxlength="16">
                    <small class="form-text text-muted">16 digit angka</small>
                </div>

                <div class="col-md-6">
                    <label for="no_hp" class="form-label fw-bold ">No HP</label>
                    <input type="text" class="form-control <?= session('errors.no_hp') ? 'is-invalid' : '' ?>"
                        id="no_hp" name="no_hp"
                        value="<?= old('no_hp') ?>">
                </div>
                <div class="col-md-6">
                    <label for="level" class="form-label fw-bold ">Level <span class="text-danger">*</span></label>
                    <select class="form-control" id="level" name="level" required>
                        <option value="admin" <?= old('level') == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="pegawai" <?= old('level') == 'pegawai' || !old('level') ? 'selected' : '' ?>>Pegawai</option>
                        <option value="user" <?= old('level') == 'user' || !old('level') ? 'selected' : '' ?>>User</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="unit_kerja" class="form-label fw-bold ">Unit Kerja</label>
                    <input type="text" class="form-control"
                        id="unit_kerja" name="unit_kerja"
                        value="<?= old('unit_kerja') ?>">
                </div>
                <div class="col-md-12">
                    <label for="alamat" class="form-label fw-bold ">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= old('alamat') ?></textarea>
                </div>
            </div>



            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-primary">SIMPAN</button>
            </div>

        </form>
    </div>
</div>

<script>
    // Toggle show/hide password
    document.querySelector('.toggle-password').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
</script>

<?= $this->endSection(); ?>