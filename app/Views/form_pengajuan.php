<?= $this->extend('templates/template_pengguna'); ?>

<?= $this->section('content'); ?>
<div class="container mt-3">
    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">FORMULIR PENGAJUAN AKUN</h2>
            <a href="/login" style="background-color: #EC1928;" class="btn btn-danger rounded-pill">
                <i class="bi bi-arrow-left"></i> Kembali Ke Login
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

        <!-- Tampilkan Error -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('pengajuan/prosesPengajuan') ?>" method="post" enctype="multipart/form-data">
            <div class="row g-3">

                <div class="col-md-6">
                    <label for="nama" class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama" name="nama"
                        value="<?= old('nama') ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="nik" class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="nik" name="nik"
                        value="<?= old('nik') ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?= old('email') ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
                            id="password" name="password"
                            required>

                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="bi bi-eye"></i>
                        </button>

                    </div>
                </div>

                <div class="col-md-6">
                    <label for="no_hp" class="form-label fw-bold">Nomor HP <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="no_hp" name="no_hp"
                        value="<?= old('no_hp') ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="unit_kerja" class="form-label fw-bold">Nama Instansi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="unit_kerja" name="unit_kerja"
                        value="<?= old('unit_kerja') ?>" required>
                </div>

                <div class="col-12">
                    <label for="alamat" class="form-label fw-bold">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="2"><?= old('alamat') ?></textarea>
                </div>

                <div class="col-12">
                    <label for="file_pengajuan" class="form-label fw-bold">Upload Dokumen Pendukung dengan format PDF <span class="text-danger">*</span></label>
                    <input class="form-control" type="file" id="file_pengajuan" name="file_pengajuan" required>
                    <small class="text-muted">Maksimal ukuran file 20MB</small>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" style="background-color: #341EBB; border: none;" class="btn btn-primary px-4">AJUKAN</button>
                </div>

            </div>
        </form>
    </div>
</div>


<script>
    // Untuk menampilkan nama file yang dipilih
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = document.getElementById("file_pengajuan").files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
</script>

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