<?= $this->extend('templates/template_pengguna'); ?>

<?= $this->section('content'); ?>
<div class="container mt-3">
    <div class="mb-4">

        <!-- Alert pesan -->
        <?php if (session()->getFlashdata('pesan')): ?>
            <div class="alert alert-success mt-3 mb-0">
                <?= session()->getFlashdata('pesan'); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="container d-flex justify-content-center align-items-start" style="min-height:100vh; padding-top:50px;">
        <div class="col-md-8">
            <div class="card p-3 border rounded bg-light">
                <div class="card">
                    <div style="background-color:#EC1928;" class="card-header text-white">
                        <h4 class="mb-0">Pengajuan Berhasil</h4>
                    </div>
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                        <h3>Terima kasih!</h3>
                        <p>Pengajuan akun Anda berhasil dikirim.</p>
                        <p>Silakan tunggu konfirmasi dari admin melalui email atau nomor HP yang Anda daftarkan.</p>
                        <a style="background-color: #341EBB; border: none;" href="<?= base_url() ?>" class="btn btn-primary">Kembali ke Beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection(); ?>