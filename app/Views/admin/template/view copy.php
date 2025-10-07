<?php
// Ganti pengecekan session sesuai dengan yang Anda set di Auth controller
$isLoggedIn = session()->get('logged_in') === true;
$user_id = session()->get('id'); // Sesuai dengan 'id' yang diset di session
?>

<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>

<style>
    .pdf-viewer-container {
        border: 1px solid #eee;
        border-radius: 5px;
        padding: 10px;
        background: #f9f9f9;
        margin-bottom: 20px;
    }

    .pdf-viewer-container embed {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Chatbot Styles */
    .pdf-chat-container {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        background: #fff;
        margin-top: 30px;
    }

    #chat-history {
        height: 300px;
        overflow-y: auto;
        margin-bottom: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
        border: 1px solid #eee;
    }

    .chat-message {
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 8px;
        max-width: 80%;
    }

    .user-message {
        background: #e3f2fd;
        margin-left: auto;
    }

    .bot-message {
        background: #f1f1f1;
        margin-right: auto;
    }

    .chat-input-group {
        display: flex;
        gap: 10px;
    }

    .source-reference {
        font-size: 0.8em;
        color: #666;
        margin-top: 5px;
        border-left: 3px solid #90caf9;
        padding-left: 8px;
    }

    .loading-spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(0, 0, 0, .1);
        border-radius: 50%;
        border-top-color: #007bff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<div class="container my-4">
    <!-- Baris judul + tombol -->

    <div class="card-body">
        <div class="container">

            <div class="row">
                <!-- Kolom PDF Viewer -->
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="mb-0"><?= esc($template['judul']); ?></h2>
                        <a href="/admin/template" style="background-color: #EC1928;" class="btn btn-danger rounded-pill fw-bold">
                            <i class="bi bi-arrow-left"></i> Kembali Ke Daftar
                        </a>
                    </div>


                    <?php
                    $docxPath = WRITEPATH . '../public/assets/uploads/template/' . $template['file_docx'];
                    $fileUrl = base_url('assets/uploads/template/' . $template['file_docx']);
                    $ext = strtolower(pathinfo($template['file_docx'], PATHINFO_EXTENSION));

                    // Path untuk file PDF hasil konversi
                    $convertedDir = WRITEPATH . '../public/assets/uploads/template/converted/';
                    $pdfPath = $convertedDir . pathinfo($template['file_docx'], PATHINFO_FILENAME) . '.pdf';
                    $pdfUrl = base_url('assets/uploads/template/converted/' . pathinfo($template['file_docx'], PATHINFO_FILENAME) . '.pdf');
                    ?>

                    <?php if (!empty($template['file_docx']) && file_exists($docxPath)): ?>
                        <div class="pdf-viewer-container">
                            <?php if ($ext === 'pdf'): ?>
                                <!-- PDF Viewer langsung -->
                                <embed
                                    src="<?= $fileUrl ?>"
                                    type="application/pdf"
                                    width="100%"
                                    height="500px"
                                    style="border: 1px solid #ddd;">

                            <?php elseif ($ext === 'docx'): ?>
                                <!-- Konversi DOCX ke PDF -->
                                <?php
                                // Buat direktori converted jika belum ada
                                if (!is_dir($convertedDir)) {
                                    mkdir($convertedDir, 0755, true);
                                    // Set permission yang benar
                                    chmod($convertedDir, 0755);
                                }

                                // Cek apakah file PDF sudah ada atau perlu dikonversi
                                $needConversion = true;
                                $conversionSuccess = false;

                                if (file_exists($pdfPath) && filesize($pdfPath) > 0) {
                                    // Cek apakah file DOCX lebih baru dari PDF
                                    if (filemtime($docxPath) <= filemtime($pdfPath)) {
                                        $needConversion = false;
                                        $conversionSuccess = true;
                                    }
                                }

                                // Konversi DOCX ke PDF jika diperlukan
                                if ($needConversion) {
                                    try {
                                        // Hapus file PDF lama jika ada dan kosong
                                        if (file_exists($pdfPath) && filesize($pdfPath) === 0) {
                                            unlink($pdfPath);
                                        }

                                        // Method 1: Gunakan LibreOffice dengan command yang lebih spesifik
                                        if ($this->isCommandAvailable('libreoffice')) {
                                            // Gunakan user profile yang dedicated untuk避免 permission issues
                                            $userProfile = WRITEPATH . 'libreoffice_profile/';
                                            if (!is_dir($userProfile)) {
                                                mkdir($userProfile, 0755, true);
                                            }

                                            $command = "export HOME=" . escapeshellarg(WRITEPATH) . " && ";
                                            $command .= "libreoffice --headless --norestore --nofirststartwizard --nologo ";
                                            $command .= "--convert-to pdf:writer_pdf_Export ";
                                            $command .= "--outdir " . escapeshellarg($convertedDir) . " ";
                                            $command .= escapeshellarg($docxPath) . " 2>&1";

                                            $output = shell_exec($command);
                                            $returnCode = $this->getCommandReturnCode($command);

                                            // Clean up LibreOffice profile
                                            if (is_dir($userProfile)) {
                                                $this->deleteDirectory($userProfile);
                                            }

                                            if ($returnCode === 0 && file_exists($pdfPath) && filesize($pdfPath) > 0) {
                                                $conversionSuccess = true;
                                                error_log("LibreOffice conversion successful. File size: " . filesize($pdfPath) . " bytes");
                                            } else {
                                                error_log("LibreOffice conversion failed. Return code: $returnCode, Output: $output");
                                                throw new Exception('Konversi LibreOffice gagal');
                                            }
                                        }
                                        // Method 2: Gunakan unoconv (alternative)
                                        elseif ($this->isCommandAvailable('unoconv')) {
                                            $command = "unoconv -f pdf -o " . escapeshellarg($pdfPath) . " " . escapeshellarg($docxPath) . " 2>&1";
                                            $output = shell_exec($command);

                                            if (file_exists($pdfPath) && filesize($pdfPath) > 0) {
                                                $conversionSuccess = true;
                                            } else {
                                                throw new Exception('Konversi unoconv gagal');
                                            }
                                        } else {
                                            throw new Exception('Tidak ada tool konversi yang tersedia (libreoffice/unoconv)');
                                        }
                                    } catch (Exception $e) {
                                        echo '<div class="alert alert-warning">Gagal mengonversi dokumen: ' . htmlspecialchars($e->getMessage()) .
                                            '<br><small>Menggunakan fallback viewer JavaScript</small></div>';
                                        error_log('DOCX Conversion Error: ' . $e->getMessage());
                                    }
                                }
                                ?>

                                <!-- Tampilkan PDF hasil konversi atau fallback -->
                                <?php if ($conversionSuccess && file_exists($pdfPath) && filesize($pdfPath) > 0): ?>
                                    <iframe
                                        src="<?= $pdfUrl ?>#toolbar=0&view=FitH"
                                        width="100%"
                                        height="500px"
                                        style="border: 1px solid #ddd;"
                                        frameborder="0">
                                        <p>Browser Anda tidak mendukung iframe. <a href="<?= $pdfUrl ?>" target="_blank">Download PDF</a></p>
                                    </iframe>
                                <?php else: ?>
                                    <!-- Fallback ke viewer DOCX JavaScript -->
                                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/docx-preview@0.3.4/dist/docx-preview.css" />
                                    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.3.4/dist/docx-preview.js"></script>

                                    <div id="docx-container" style="border:1px solid #ddd; height:500px; overflow:auto; background:#fff; padding:10px;">
                                        <p><em>Memuat dokumen... (Mode fallback)</em></p>
                                    </div>

                                    <script>
                                        document.addEventListener("DOMContentLoaded", () => {
                                            const container = document.getElementById("docx-container");

                                            // Tambahkan timeout untuk handle slow loading
                                            const loadingTimeout = setTimeout(() => {
                                                container.innerHTML = "<div class='alert alert-info'>Dokumen sedang dimuat...</div>";
                                            }, 2000);

                                            fetch("<?= $fileUrl ?>")
                                                .then(res => {
                                                    if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
                                                    return res.blob();
                                                })
                                                .then(blob => {
                                                    clearTimeout(loadingTimeout);
                                                    if (window.docx && typeof window.docx.renderAsync === 'function') {
                                                        window.docx.renderAsync(blob, container, null, {
                                                            inWrapper: true,
                                                            ignoreWidth: false,
                                                            ignoreHeight: false,
                                                            breakPages: true,
                                                            experimental: false
                                                        });
                                                    } else {
                                                        container.innerHTML = "<div class='alert alert-info'>Preview tidak tersedia. Silakan download dokumen untuk melihatnya.</div>";
                                                    }
                                                })
                                                .catch(err => {
                                                    clearTimeout(loadingTimeout);
                                                    console.error('Error loading DOCX:', err);
                                                    container.innerHTML = "<div class='alert alert-danger'>Gagal memuat dokumen (" + err.message + "). Silakan coba lagi atau download dokumennya.</div>";
                                                });
                                        });
                                    </script>
                                <?php endif; ?>

                            <?php else: ?>
                                <div class="alert alert-warning">
                                    Format file <strong>.<?= $ext ?></strong> tidak didukung untuk preview.
                                </div>
                            <?php endif; ?>

                            <!-- Tombol Aksi -->
                            <div class="text-end mt-2">
                                <a href="<?= $fileUrl ?>"
                                    class="btn btn-sm btn-outline-primary"
                                    target="_blank"
                                    download="<?= $template['file_docx'] ?>">
                                    <i class="bi bi-download"></i> Download Original
                                </a>

                                <?php if ($conversionSuccess && file_exists($pdfPath) && filesize($pdfPath) > 0): ?>
                                    <a href="<?= $pdfUrl ?>"
                                        class="btn btn-sm btn-outline-success"
                                        target="_blank"
                                        download="<?= pathinfo($template['file_docx'], PATHINFO_FILENAME) ?>.pdf">
                                        <i class="bi bi-file-earmark-pdf"></i> Download PDF
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <?php if (empty($template['file_docx'])): ?>
                                File tidak tersedia di database
                            <?php else: ?>
                                File tidak ditemukan di server
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Informasi Dokumen -->
                    <div class="text-end mt-2">
                        <strong>Di Posting Oleh:</strong> <?= esc($template['user_nama']); ?>
                    </div>
                    <hr>
                    <ul class="list-unstyled">
                        <li>
                            <strong>Dibuat pada:</strong>
                            <?= tanggal_indo($template['created_at']); ?>
                        </li>
                        <li>
                            <strong>Diupdate pada:</strong>
                            <?= tanggal_indo($template['updated_at']); ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="container my-4" hidden>
            <div class="card shadow-lg border-0 p-3">
                <div class="card-body p-0">
                    <!-- Responsive iframe -->
                    <div class="ratio ratio-16x9">
                        <iframe src="http://localhost:8501/" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>