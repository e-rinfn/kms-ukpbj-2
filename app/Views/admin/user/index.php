<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>

<div class="container mt-3">
    <div class="mb-4">
        <!-- Baris judul + tombol -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="mb-0">DAFTAR PENGGUNA</h2>
            <a href="/admin/user/create" class="btn btn-danger rounded-pill">
                <i class="bi bi-plus-circle"></i> Tambah Pengguna
            </a>
        </div>

        <!-- Alert pesan -->

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success mt-3 mb-0">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger mt-3 mb-0">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
    </div>

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
                    <label for="level" class="form-label">Filter Level</label>
                    <select class="form-select" id="level">
                        <option value="">Semua</option>
                        <option value="admin">Admin</option>
                        <option value="pegawai">UKPBJ</option>
                        <option value="user">OPD</option>
                    </select>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const levelSelect = document.getElementById("level");
                        const tableRows = document.querySelectorAll("table tbody tr");

                        levelSelect.addEventListener("change", function() {
                            const selectedLevel = this.value.toLowerCase();

                            tableRows.forEach(row => {
                                const levelCell = row.querySelector("td:nth-child(5)"); // kolom Level
                                if (!levelCell) return;

                                const userLevel = levelCell.textContent.trim().toLowerCase();

                                if (selectedLevel === "" || userLevel === selectedLevel) {
                                    row.style.display = ""; // tampilkan
                                } else {
                                    row.style.display = "none"; // sembunyikan
                                }
                            });
                        });
                    });
                </script>


                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Terapkan</button>
                </div>
            </form>

            <?php if (!empty($search) || isset($filterLevel)): ?>
                <div class="mt-3">
                    <a href="/admin/user" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                    </a>
                </div>
            <?php endif; ?>

        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Level</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1 + (($pager->getCurrentPage() - 1) * $pager->getPerPage()); ?>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= esc($user['username']) ?></td>
                            <td><?= esc($user['nama']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td class="text-center">
                                <?php
                                $mappingLevel = [
                                    'admin'   => 'Admin',
                                    'pegawai' => 'UKPBJ',
                                    'user'    => 'OPD'
                                ];
                                echo $mappingLevel[$user['level']] ?? ucfirst($user['level']);
                                ?>
                            </td>


                            <td><?= tanggal_indo($user['created_at'], true) ?></td>
                            <td class="text-center">
                                <a href="/admin/user/edit/<?= $user['id'] ?>" class="btn btn-sm btn-warning">
                                    Ubah
                                </a>
                                <form action="/admin/user/delete/<?= $user['id'] ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= $user['id']; ?>">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>


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