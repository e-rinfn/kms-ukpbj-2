<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div class="container mt-3">

    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">UBAH PENGGUNA</h2>
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

    <div class="card p-3 border rounded bg-light">

        <form action="<?= base_url('admin/user/update/' . $user['id']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="row g-3">


                <div class="col-md-6">
                    <label for="username" class="form-label fw-bold">Username</label>
                    <span class="text-danger">*</span>
                    <input type="text" class="form-control <?= session()->getFlashdata('errors.username') ? 'is-invalid' : '' ?>"
                        id="username" name="username"
                        value="<?= old('username', $user['username'] ?? '') ?>" required>
                    <?php if (session()->getFlashdata('errors.username')): ?>
                        <div class="invalid-feedback">
                            <?= esc(session()->getFlashdata('errors.username')) ?>
                        </div>
                    <?php endif; ?>
                    <small class="form-text text-muted">Minimal 3 karakter, tanpa spasi</small>
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label fw-bold">Password (Kosongkan jika tidak ingin mengubah)</label>
                    <div class="input-group">
                        <input type="password" class="form-control <?= session()->getFlashdata('errors.password') ? 'is-invalid' : '' ?>"
                            id="password" name="password">
                        <?php if (session()->getFlashdata('errors.password')): ?>
                            <div class="invalid-feedback">
                                <?= esc(session()->getFlashdata('errors.password')) ?>
                            </div>
                        <?php endif; ?>
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <small class="form-text text-muted">Minimal 6 karakter</small>
                </div>

                <div class="col-md-6">
                    <label for="nama" class="form-label fw-bold">Nama Lengkap</label>
                    <span class="text-danger">*</span>
                    <input type="text" class="form-control <?= session()->getFlashdata('errors.nama') ? 'is-invalid' : '' ?>"
                        id="nama" name="nama"
                        value="<?= old('nama', $user['nama'] ?? '') ?>" required>
                    <?php if (session()->getFlashdata('errors.nama')): ?>
                        <div class="invalid-feedback">
                            <?= esc(session()->getFlashdata('errors.nama')) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <span class="text-danger">*</span>
                    <input type="email" class="form-control <?= session()->getFlashdata('errors.email') ? 'is-invalid' : '' ?>"
                        id="email" name="email"
                        value="<?= old('email', $user['email'] ?? '') ?>" required>
                    <?php if (session()->getFlashdata('errors.email')): ?>
                        <div class="invalid-feedback">
                            <?= esc(session()->getFlashdata('errors.email')) ?>
                        </div>
                    <?php endif; ?>
                </div>





                <div class="col-md-6">
                    <label for="nik" class="form-label fw-bold">NIK</label>
                    <input type="number" class="form-control <?= session()->getFlashdata('errors.nik') ? 'is-invalid' : '' ?>"
                        id="nik" name="nik"
                        value="<?= old('nik', $user['nik'] ?? '') ?>" maxlength="16">
                    <?php if (session()->getFlashdata('errors.nik')): ?>
                        <div class="invalid-feedback">
                            <?= esc(session()->getFlashdata('errors.nik')) ?>
                        </div>
                    <?php endif; ?>
                    <small class="form-text text-muted">16 digit angka</small>
                </div>

                <div class="col-md-6">
                    <label for="no_hp" class="form-label fw-bold">No HP</label>
                    <input type="number" class="form-control <?= session()->getFlashdata('errors.no_hp') ? 'is-invalid' : '' ?>"
                        id="no_hp" name="no_hp"
                        value="<?= old('no_hp', $user['no_hp'] ?? '') ?>">
                    <?php if (session()->getFlashdata('errors.no_hp')): ?>
                        <div class="invalid-feedback">
                            <?= esc(session()->getFlashdata('errors.no_hp')) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="level" class="form-label fw-bold">Level</label>
                    <span class="text-danger">*</span>
                    <select class="form-control <?= session()->getFlashdata('errors.level') ? 'is-invalid' : '' ?>"
                        id="level" name="level" required>
                        <option value="admin" <?= old('level', $user['level'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="pegawai" <?= old('level', $user['level'] ?? '') == 'pegawai' ? 'selected' : '' ?>>UKPBJ</option>
                        <option value="user" <?= old('level', $user['level'] ?? '') == 'user' ? 'selected' : '' ?>>OPD</option>
                    </select>
                    <?php if (session()->getFlashdata('errors.level')): ?>
                        <div class="invalid-feedback">
                            <?= esc(session()->getFlashdata('errors.level')) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="unit_kerja" class="form-label fw-bold">Nama Instansi</label>
                    <input type="text" class="form-control <?= session()->getFlashdata('errors.unit_kerja') ? 'is-invalid' : '' ?>"
                        id="unit_kerja" name="unit_kerja"
                        value="<?= old('unit_kerja', $user['unit_kerja'] ?? '') ?>">
                    <?php if (session()->getFlashdata('errors.unit_kerja')): ?>
                        <div class="invalid-feedback">
                            <?= esc(session()->getFlashdata('errors.unit_kerja')) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-12">
                    <label for="alamat" class="form-label fw-bold">Alamat</label>
                    <textarea class="form-control <?= session()->getFlashdata('errors.alamat') ? 'is-invalid' : '' ?>"
                        id="alamat" rows="3" name="alamat"><?= old('alamat', $user['alamat'] ?? '') ?></textarea>
                    <?php if (session()->getFlashdata('errors.alamat')): ?>
                        <div class="invalid-feedback">
                            <?= esc(session()->getFlashdata('errors.alamat')) ?>
                        </div>
                    <?php endif; ?>
                </div>


                <div class="text-end">
                    <button type="submit" class="btn btn-primary">SIMPAN PERUBAHAN</button>
                </div>
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