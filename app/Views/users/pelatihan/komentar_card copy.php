<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <h6 class="card-subtitle mb-2 text-muted"><?= esc($k['user_nama']) ?></h6>
            <small class="text-muted"><?= date('d M Y H:i', strtotime($k['created_at'])) ?></small>
        </div>
        <p class="card-text"><?= nl2br(esc($k['komentar'])) ?></p>

        <?php
        $canDelete = false;
        if ($isLoggedIn) {
            if (session()->get('level') === 'admin' || $user_id == $k['user_id']) {
                $canDelete = true;
            }
        }
        ?>

        <?php if ($canDelete): ?>
            <form action="<?= base_url('pelatihan/delete-comment/' . $k['id']) ?>" method="POST" class="mt-2">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>