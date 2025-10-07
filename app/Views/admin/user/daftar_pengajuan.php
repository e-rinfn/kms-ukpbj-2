<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>

<div class="container mt-3">

    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">DAFTAR PENGAJUAN AKUN</h2>
        </div>

        <!-- Alert pesan -->
        <?php if (session()->getFlashdata('pesan')): ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: '<?= session()->getFlashdata('pesan'); ?>',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            </script>
        <?php endif; ?>
    </div>

    <div class="card p-3 border rounded bg-light">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Email</th>
                        <th>Unit Kerja</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Dokumen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pengajuan)): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($pengajuan as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($p['nama']) ?></td>
                                <td><?= esc($p['nik']) ?></td>
                                <td><?= esc($p['email']) ?></td>
                                <td><?= esc($p['unit_kerja']) ?></td>
                                <td><?= tanggal_indo($p['created_at'], true) ?></td>
                                <td>
                                    <?php if ($p['file_pengajuan']): ?>
                                        <a href="<?= base_url('uploads/pengajuan/' . $p['file_pengajuan']) ?>" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button onclick="approvePengajuan(<?= $p['id'] ?>)" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button onclick="deletePengajuan(<?= $p['id'] ?>)" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                Belum ada data pengajuan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function approvePengajuan(id) {
        Swal.fire({
            title: 'Approve Pengajuan?',
            text: "Pengajuan ini akan disetujui!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Approve',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('admin/pengajuan/approve') ?>/" + id;
            }
        });
    }

    function deletePengajuan(id) {
        Swal.fire({
            title: 'Hapus Data?',
            text: "Data pengajuan ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('admin/pengajuan/delete') ?>/" + id;
            }
        });
    }
</script>

<?= $this->endSection(); ?>