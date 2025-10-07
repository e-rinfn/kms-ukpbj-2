<!-- app/Views/komentar_card.php -->
<?php
$komentarModel = new \App\Models\KomentarPengetahuanModel();
$balasan = $komentarModel->getBalasanByParent($k['id']);
$canDelete = false;
if ($isLoggedIn) {
    if (session()->get('level') === 'admin' || $user_id == $k['user_id']) {
        $canDelete = true;
    }
}
?>

<div class="card mb-3 <?= $k['level'] > 0 ? 'ms-4 border-start' : '' ?>" style="<?= $k['level'] > 0 ? 'border-left: 3px solid #ddd !important;' : '' ?>">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <h6 class="card-subtitle mb-2 text-muted fw-bold"><?= esc($k['user_nama']) ?></h6>
            <small class="text-muted"><?= date('d M Y H:i', strtotime($k['created_at'])) ?></small>
        </div>
        <p class="card-text"><?= nl2br(esc($k['komentar'])) ?></p>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <?php if ($isLoggedIn && $user_id && $k['level'] < 2): ?>
                <button class="btn btn-sm btn-outline-primary reply-btn"
                    data-comment-id="<?= $k['id'] ?>"
                    data-username="<?= esc($k['user_nama']) ?>">
                    <i class="bi bi-reply"></i> Balas
                </button>
            <?php else: ?>
                <span></span>
            <?php endif; ?>

            <?php if ($canDelete): ?>
                <form action="<?= base_url('pengetahuan/delete-comment/' . $k['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Form Balas Komentar (Hidden) -->
        <?php if ($isLoggedIn && $user_id && $k['level'] < 2): ?>
            <div class="reply-form mt-3" id="reply-form-<?= $k['id'] ?>" style="display: none;">
                <form action="<?= base_url('pengetahuan/comment/' . $pengetahuan['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="parent_id" value="<?= $k['id'] ?>">
                    <div class="mb-2">
                        <textarea name="komentar" class="form-control" rows="2"
                            placeholder="Balas untuk <?= esc($k['user_nama']) ?>..." required></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">Kirim Balasan</button>
                        <button type="button" class="btn btn-sm btn-secondary cancel-reply">Batal</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Tampilkan Balasan -->
        <?php if (!empty($balasan)): ?>
            <div class="mt-3">
                <?php foreach ($balasan as $balas): ?>
                    <?php
                    $k = $balas; // Override untuk include file yang sama
                    include 'komentar_card.php';
                    ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>