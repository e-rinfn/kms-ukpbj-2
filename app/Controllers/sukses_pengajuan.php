<?= $this->extend('templates/template_pengguna'); ?>

<?= $this->section('content'); ?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Pengajuan Berhasil</h4>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                    <h3>Terima kasih!</h3>
                    <p>Pengajuan akun Anda berhasil dikirim.</p>
                    <p>Silakan tunggu konfirmasi dari admin melalui email atau nomor HP yang Anda daftarkan.</p>
                    <a href="<?= base_url() ?>" class="btn btn-primary">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>