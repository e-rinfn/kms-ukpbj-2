<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?= isset($user) ? 'Edit' : 'Tambah' ?> Pengguna</h6>
    </div>
    <div class="card-body">
        <?php if (isset($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= isset($user) ? '/admin/user/update/' . $user['id'] : '/admin/user/store' ?>" method="post">
            <?= csrf_field() ?>
            <?php if (isset($user)): ?>
                <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                    value="<?= old('username', $user['username'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="<?= old('email', $user['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                    <?= !isset($user) ? 'required' : '' ?>>
                <?php if (isset($user)): ?>
                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama"
                    value="<?= old('nama', $user['nama'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="level">Level</label>
                <select class="form-control" id="level" name="level" required>
                    <option value="admin" <?= old('level', $user['level'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="user" <?= old('level', $user['level'] ?? '') == 'user' ? 'selected' : '' ?>>User</option>
                </select>
            </div>

            <div class="form-group">
                <label for="nik">NIK</label>
                <input type="text" class="form-control" id="nik" name="nik"
                    value="<?= old('nik', $user['nik'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="no_hp">No. HP</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp"
                    value="<?= old('no_hp', $user['no_hp'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="unit_kerja">Unit Kerja</label>
                <input type="text" class="form-control" id="unit_kerja" name="unit_kerja"
                    value="<?= old('unit_kerja', $user['unit_kerja'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat"><?= old('alamat', $user['alamat'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="/admin/user" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>