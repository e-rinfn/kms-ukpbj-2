<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>


<div class="container mt-3">
    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">DAFTAR TEMPLATE</h2>
            <a href="/pegawai/template/create" style="background-color: #EC1928;" class="btn btn-danger rounded-pill fw-bold">
                <i class="bi bi-plus-circle"></i> Tambah Template
            </a>
        </div>

        <!-- Alert pesan -->
        <?php if (session()->getFlashdata('pesan')): ?>
            <div class="alert alert-success mt-3 mb-0">
                <?= session()->getFlashdata('pesan'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Form Filter dan Pencarian -->
    <div class="card p-3 border rounded bg-light">
        <div class="card-body">
            <form action="" method="get" class="row g-3">
                <div class="col-md-8">
                    <label for="search" class="form-label">Pencarian</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search"
                            placeholder="Cari berdasarkan judul, deskripsi atau pembuat..."
                            value="<?= esc($search ?? '') ?>">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="akses" class="form-label">Filter Akses Publik</label>
                    <select class="form-select" id="akses" name="akses">
                        <option value="">Semua</option>
                        <option value="1" <?= ($filterAkses ?? '') === '1' ? 'selected' : '' ?>>Publik</option>
                        <option value="0" <?= ($filterAkses ?? '') === '0' ? 'selected' : '' ?>>Privat</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" style="background-color: #341EBB; border: none;" class="btn btn-primary w-100">Terapkan</button>
                </div>
            </form>

            <?php if (!empty($search) || isset($filterAkses)): ?>
                <div class="mt-3">
                    <a href="/pegawai/template" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light text-center">
                    <tr class="text-center">
                        <th>No</th>
                        <th>Judul</th>
                        <th>File</th>
                        <th>Akses Publik</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($template)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data ditemukan</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1 + (($pager->getCurrentPage() - 1) * $pager->getPerPage()); ?>
                        <?php foreach ($template as $p): ?>
                            <tr>
                                <td class="text-center"><?= $i++; ?></td>
                                <td><?= esc($p['judul']); ?></td>
                                <td class="text-center">
                                    <?php if (!empty($p['file_docx'])): ?>
                                        <a href="<?= base_url('/assets/uploads/template/' . $p['file_docx']); ?>" target="_blank" style="background-color: green; border: none;" class="badge">Download File</a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak ada file</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $p['akses_publik'] ? 'success' : 'secondary'; ?>">
                                        <?= $p['akses_publik'] ? 'Publik' : 'Tidak'; ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= esc($p['user_nama']); ?></td>
                                <td class="text-center">
                                    <a href="/pegawai/template/view/<?= $p['id']; ?>" class="btn btn-sm btn-info">Detail</a>
                                    <a href="/pegawai/template/edit/<?= $p['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="/pegawai/template/delete/<?= $p['id']; ?>" method="post" class="d-inline delete-form">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= $p['id']; ?>">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php
            $surroundCount = 2; // Jumlah halaman yang ditampilkan di sekitar halaman aktif
            $current = $pager->getCurrentPage();
            $last = $pager->getPageCount();
            ?>

            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php
                    $surroundCount = 2; // Jumlah halaman di sekitar halaman aktif
                    $current = $pager->getCurrentPage();
                    $last = $pager->getPageCount();

                    // Hitung range halaman yang akan ditampilkan
                    $start = max(1, $current - $surroundCount);
                    $end = min($last, $current + $surroundCount);

                    // Tampilkan angka halaman saja
                    for ($i = $start; $i <= $end; $i++) : ?>
                        <li class="page-item <?= $i == $current ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $pager->getPageURI($i) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor ?>
                </ul>
            </nav>

            <div class="text-center text-muted small mt-2">
                Menampilkan <?= ($current - 1) * $pager->getPerPage() + 1 ?>
                sampai <?= min($current * $pager->getPerPage(), $pager->getTotal()) ?>
                dari <?= $pager->getTotal() ?> data
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll(".btn-delete");

        deleteButtons.forEach(button => {
            button.addEventListener("click", function() {
                const form = this.closest("form");

                Swal.fire({
                    title: "Yakin ingin menghapus?",
                    text: "Data yang sudah dihapus tidak bisa dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>


<?= $this->endSection(); ?>