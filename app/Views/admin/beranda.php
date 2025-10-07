<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div class="container p-3">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body text-center py-5 position-relative">
                    <!-- Background Shape -->
                    <div class="position-absolute top-0 start-0 w-100 h-100"
                        style="background: linear-gradient(135deg, rgba(220,53,69,0.05), rgba(0,123,255,0.05)); 
                            z-index:0; border-radius: .5rem;">
                    </div>

                    <!-- Konten -->
                    <div class="position-relative" style="z-index:1;">
                        <h1 class="fw-bold mb-3 text-dark">
                            Selamat Datang, <span class="text-muted"><?= session()->get('nama') ?? '' ?></span>
                        </h1>
                        <p class="lead text-muted mb-0">
                            Sistem Manajemen <span class="fw-semibold text-danger">Pengetahuan</span> &
                            <span class="fw-semibold text-primary">Pelatihan</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Statistik Utama -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 border-0 position-relative overflow-hidden">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <!-- Kiri -->
                    <div>
                        <div class="text-uppercase text-danger small fw-bold mb-1">Total Pengetahuan</div>
                        <div class="h4 fw-bold mb-0"><?= number_format($total_pengetahuan) ?></div>
                    </div>

                    <!-- Kanan (ikon) -->
                    <div class="position-relative">
                        <i class="fas fa-book fa-2x text-primary"></i>
                    </div>
                </div>

                <!-- Background Shape -->
                <div class="position-absolute top-0 end-0 opacity-25" style="width:100px; height:100px; transform: translate(25%, -25%);">
                    <div class="bg-danger rounded-circle w-100 h-100"></div>
                </div>
            </div>
        </div>


        <!-- Total Pelatihan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 border-0 position-relative overflow-hidden">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <!-- Kiri -->
                    <div>
                        <div class="text-uppercase text-primary small fw-bold mb-1">Total Pelatihan</div>
                        <div class="h4 fw-bold mb-0"><?= number_format($total_pelatihan) ?></div>
                    </div>
                    <!-- Kanan (ikon) -->
                    <div class="position-relative">
                        <i class="fas fa-video fa-2x text-success"></i>
                    </div>
                </div>
                <!-- Background Shape -->
                <div class="position-absolute top-0 end-0 opacity-25" style="width:100px; height:100px; transform: translate(25%, -25%);">
                    <div class="bg-primary rounded-circle w-100 h-100"></div>
                </div>
            </div>
        </div>

        <!-- Pengguna Terdaftar -->
        <?php if (session()->get('level') === 'admin') : ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 border-0 position-relative overflow-hidden">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <!-- Kiri -->
                        <div>
                            <div class="text-uppercase text-success small fw-bold mb-1">Pengguna Terdaftar</div>
                            <div class="h4 fw-bold mb-0"><?= number_format($total_pengguna) ?></div>
                        </div>
                        <!-- Kanan (ikon) -->
                        <div class="position-relative">
                            <i class="fas fa-users fa-2x text-info"></i>
                        </div>
                    </div>
                    <!-- Background Shape -->
                    <div class="position-absolute top-0 end-0 opacity-25" style="width:100px; height:100px; transform: translate(25%, -25%);">
                        <div class="bg-success rounded-circle w-100 h-100"></div>
                    </div>
                </div>
            </div>


            <!-- Pengajuan Pending -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 border-0 position-relative overflow-hidden">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <!-- Kiri -->
                        <div>
                            <div class="text-uppercase text-warning small fw-bold mb-1">Pengajuan Pending</div>
                            <div class="h4 fw-bold mb-0"><?= number_format($total_pengajuan) ?></div>
                        </div>
                        <!-- Kanan (ikon) -->
                        <div class="position-relative">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                    <!-- Background Shape -->
                    <div class="position-absolute top-0 end-0 opacity-25" style="width:100px; height:100px; transform: translate(25%, -25%);">
                        <div class="bg-warning rounded-circle w-100 h-100"></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- Grafik dan Tabel Ringkasan -->
    <div class="row">

        <!-- Tabel Pengetahuan Terbaru -->
        <div class="col-lg-6 mb-0">
            <div class="card shadow mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex flex-row align-items-center justify-content-between shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-book text-danger me-2"></i>
                        <h6 class="m-0 fw-bold text-danger">Pengetahuan Terbaru</h6>
                    </div>

                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-center">
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                    <th>Publik</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($pengetahuan_terbaru as $pengetahuan):
                                    if ($i++ >= 5) break; // stop setelah 5 data
                                ?>
                                    <tr>
                                        <td><?= esc($pengetahuan['judul']) ?></td>
                                        <td><?= tanggal_indo($pengetahuan['created_at']) ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-<?= $pengetahuan['akses_publik'] ? 'success' : 'secondary' ?>">
                                                <?= $pengetahuan['akses_publik'] ? 'Ya' : 'Tidak' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Pelatihan Terbaru -->
        <div class="col-lg-6 mb-0">
            <div class="card shadow mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex flex-row align-items-center justify-content-between shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-book text-primary me-2"></i>
                        <h6 class="m-0 fw-bold text-primary">Pelatihan Terbaru</h6>
                    </div>

                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-center">
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                    <th>Publik</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($pelatihan_terbaru as $pelatihan):
                                    if ($i++ >= 5) break; // stop setelah 5 data
                                ?> <tr>
                                        <td><?= esc($pelatihan['judul']) ?></td>
                                        <td><?= tanggal_indo($pelatihan['created_at']) ?></td>
                                        <td class="text-center"><span class="badge bg-<?= $pelatihan['akses_publik'] ? 'success' : 'secondary' ?>">
                                                <?= $pelatihan['akses_publik'] ? 'Ya' : 'Tidak' ?>
                                            </span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>