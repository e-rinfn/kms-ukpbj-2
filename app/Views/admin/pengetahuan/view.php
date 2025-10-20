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
                <div class="col-md-8">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="mb-0"><?= esc($pengetahuan['judul']); ?></h2>
                        <a href="/admin/pengetahuan" style="background-color: #EC1928;" class="btn btn-danger rounded-pill fw-bold">
                            <i class="bi bi-arrow-left"></i> Kembali Ke Daftar
                        </a>
                    </div>
                    <?php
                    $pdfPath = WRITEPATH . '../public/assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan'];
                    $pdfUrl = base_url('assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan']);
                    ?>

                    <?php if (!empty($pengetahuan['file_pdf_pengetahuan']) && file_exists($pdfPath)): ?>
                        <div class="pdf-viewer-container">
                            <!-- PDF Viewer -->
                            <embed
                                src="<?= $pdfUrl ?>"
                                type="application/pdf"
                                width="100%"
                                height="400px"
                                style="border: 1px solid #ddd;">

                            <!-- PDF Actions -->
                            <div class="text-end mt-2">
                                <a href="<?= $pdfUrl ?>"
                                    class="btn btn-sm btn-outline-primary"
                                    target="_blank">
                                    <i class="bi bi-eye"></i> Buka PDF
                                </a>
                                <!-- <button onclick="showIframe()" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i> Tampilkan PDF Alternatif
                            </button> -->
                            </div>

                            <!-- Hidden iframe as alternative viewer -->
                            <iframe
                                src="<?= $pdfUrl ?>"
                                width="100%"
                                height="400px"
                                style="border: 1px solid #ddd; display: none;"
                                id="pdfIframe">
                            </iframe>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <?php if (empty($pengetahuan['file_pdf_pengetahuan'])): ?>
                                File PDF tidak tersedia di database
                            <?php else: ?>
                                File PDF tidak ditemukan di server
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>



                    <!-- Informasi Dokumen -->
                    <div class="text-end mt-2">
                        <strong>Di Posting Oleh:</strong> <?= esc($pengetahuan['user_nama']); ?>
                    </div>

                    <div class="my-3">
                        <p><?= $pengetahuan['caption_pengetahuan']; ?></p>
                    </div>
                    <hr>
                    <ul class="list-unstyled">
                        <li>
                            <strong>Dibuat pada:</strong>
                            <?= tanggal_indo($pengetahuan['created_at']); ?>
                        </li>
                        <li>
                            <strong>Diupdate pada:</strong>
                            <?= tanggal_indo($pengetahuan['updated_at']); ?>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4 mb-3">
                    <h5 class="text-center mt-1 fw-bold">DAFTAR PENGETAHUAN</h5>
                    <hr>
                    <div class="border bg-light rounded p-3" style="height: 800px; overflow-y: auto;">
                        <div class="row g-4">
                            <?php foreach ($pengetahuan_lain as $p): ?>
                                <div class="col-12">
                                    <div class="card h-100">
                                        <?php if (!empty($p['thumbnail_pengetahuan'])): ?>
                                            <img src="<?= base_url('/assets/uploads/pengetahuan/' . $p['thumbnail_pengetahuan']); ?>"
                                                class="card-img-top bg-light p-1 border"
                                                alt="<?= esc($p['judul']); ?>"
                                                style="height: 200px; object-fit: contain;">
                                        <?php else: ?>
                                            <img src="<?= base_url('/assets/img/default-thumbnail.png'); ?>"
                                                class="card-img-top bg-light p-1 border"
                                                alt="Default Thumbnail"
                                                style="height: 200px; object-fit: contain;">
                                        <?php endif; ?>

                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?= esc($p['judul']); ?></h5>
                                            <hr>
                                            <p class="card-text">
                                                <?= $p['caption_pengetahuan'] > 150 ? substr($p['caption_pengetahuan'], 0, 150) . '...' : $p['caption_pengetahuan']; ?>
                                            </p>

                                            <div class="mt-auto">
                                                <hr>
                                                <p class="card-text">
                                                    <small class="text-muted"><?= date('d M Y', strtotime($p['created_at'])); ?></small>
                                                </p>
                                                <a href="<?= base_url('admin/pengetahuan/view/' . $p['id']); ?>" style="background-color: #341EBB; border: none;" class="btn btn-primary rounded-pill w-100">Lihat Detail</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
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

        <!-- Chatbot Section -->
        <div class="pdf-chat-container mt-4" hidden>
            <h4>Tanya tentang Dokumen Ini</h4>
            <p>Ajukan pertanyaan tentang isi dokumen ini dan dapatkan jawaban dari DeepSeek.</p>

            <div id="chat-history">
                <!-- Pesan chat akan muncul di sini -->
            </div>

            <form id="chat-form" target="deepseek-iframe">
                <div class="chat-input-group">
                    <input type="text" name="q" class="form-control" placeholder="Tanyakan tentang dokumen ini..." required>
                    <button type="submit" class="btn btn-primary">
                        <span id="submit-text">Kirim</span>
                        <span class="loading-spinner" id="loading-spinner"></span>
                    </button>
                </div>
            </form>

            <!-- Iframe tersembunyi untuk mengarahkan form -->
            <iframe name="deepseek-iframe" style="display: none;"></iframe>
        </div>


        <script>
            document.getElementById('chat-form').addEventListener('submit', function(e) {
                e.preventDefault();

                const form = this;
                const input = form.querySelector('input[name="q"]');
                const question = input.value.trim();
                const chatHistory = document.getElementById('chat-history');
                const submitText = document.getElementById('submit-text');
                const spinner = document.getElementById('loading-spinner');

                if (!question) return;

                // Tampilkan pertanyaan pengguna
                const userMessage = document.createElement('div');
                userMessage.className = 'chat-message user-message';
                userMessage.textContent = question;
                chatHistory.appendChild(userMessage);

                // Tampilkan loading
                submitText.style.display = 'none';
                spinner.style.display = 'inline-block';
                input.disabled = true;

                // Scroll ke bawah
                chatHistory.scrollTop = chatHistory.scrollHeight;

                // Buka DeepSeek di tab baru dengan pertanyaan
                const deepseekUrl = `https://chat.deepseek.com/search?q=${encodeURIComponent(question + ' ' + '<?= esc($pengetahuan['judul']) ?>')}`;
                window.open(deepseekUrl, '_blank');

                // Tambahkan pesan bot (simulasi)
                setTimeout(() => {
                    const botMessage = document.createElement('div');
                    botMessage.className = 'chat-message bot-message';
                    botMessage.innerHTML = `
            <p>Saya telah membuka pencarian di DeepSeek untuk pertanyaan Anda. Silakan lihat tab baru yang terbuka untuk hasil lengkap.</p>
            <p class="source-reference">Sumber: DeepSeek Chat</p>
        `;
                    chatHistory.appendChild(botMessage);

                    // Reset form
                    input.value = '';
                    input.disabled = false;
                    submitText.style.display = 'inline';
                    spinner.style.display = 'none';

                    // Scroll ke bawah lagi
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                }, 1500);
            });
        </script>


        <!-- PDF Chatbot Section -->
        <!-- <div class="pdf-chat-container mt-5">
            <h4 class="text-center mb-4">Tanya Dokumen</h4>
            <div id="chat-history" class="mb-3"></div>

            <div class="chat-input-group">
                <input type="text" id="user-question" class="form-control" placeholder="Tanyakan sesuatu tentang dokumen ini..." autocomplete="off">
                <button id="ask-button" class="btn btn-primary">
                    <span id="button-text">Tanya</span>
                    <span id="loading-spinner" class="loading-spinner"></span>
                </button>
            </div>

            <div class="text-center mt-2">
                <small class="text-muted">Contoh pertanyaan: "Apa itu scammer?", "Bagaimana cara kerja scammer?"</small>
            </div>
        </div> -->

        <script>
            // document.addEventListener('DOMContentLoaded', function() {
            //     const chatHistory = document.getElementById('chat-history');
            //     const userQuestion = document.getElementById('user-question');
            //     const askButton = document.getElementById('ask-button');
            //     const buttonText = document.getElementById('button-text');
            //     const loadingSpinner = document.getElementById('loading-spinner');

            //     // Fungsi untuk menambahkan pesan ke chat history
            //     function addMessage(message, isUser) {
            //         const messageDiv = document.createElement('div');
            //         messageDiv.className = `chat-message ${isUser ? 'user-message' : 'bot-message'}`;
            //         messageDiv.innerHTML = message;
            //         chatHistory.appendChild(messageDiv);
            //         chatHistory.scrollTop = chatHistory.scrollHeight;
            //     }

            //     // Fungsi untuk menangani pertanyaan
            //     async function handleQuestion() {
            //         const question = userQuestion.value.trim();
            //         if (!question) return;

            //         // Tampilkan pesan user
            //         addMessage(question, true);
            //         userQuestion.value = '';

            //         // Tampilkan loading
            //         buttonText.style.display = 'none';
            //         loadingSpinner.style.display = 'inline-block';
            //         askButton.disabled = true;

            //         try {
            //             // Kirim pertanyaan ke server
            //             const response = await fetch('/ask-pdf', {
            //                 method: 'POST',
            //                 headers: {
            //                     'Content-Type': 'application/json',
            //                     'X-Requested-With': 'XMLHttpRequest'
            //                 },
            //                 body: JSON.stringify({
            //                     question: question,
            //                     pdf_id: <?= $pengetahuan['id'] ?>
            //                 })
            //             });

            //             const data = await response.json();

            //             if (data.answer) {
            //                 // Tampilkan jawaban
            //                 let answerHtml = data.answer;

            //                 // Tambahkan referensi jika ada
            //                 if (data.sources && data.sources.length > 0) {
            //                     answerHtml += `<div class="source-reference">Sumber: ${data.sources.join(', ')}</div>`;
            //                 }

            //                 addMessage(answerHtml, false);
            //             } else {
            //                 addMessage("Maaf, saya tidak bisa menjawab pertanyaan itu saat ini.", false);
            //             }
            //         } catch (error) {
            //             console.error('Error:', error);
            //             addMessage("Terjadi kesalahan saat memproses pertanyaan Anda.", false);
            //         } finally {
            //             // Sembunyikan loading
            //             buttonText.style.display = 'inline-block';
            //             loadingSpinner.style.display = 'none';
            //             askButton.disabled = false;
            //         }
            //     }

            //     // Event listeners
            //     askButton.addEventListener('click', handleQuestion);
            //     userQuestion.addEventListener('keypress', function(e) {
            //         if (e.key === 'Enter') {
            //             handleQuestion();
            //         }
            //     });

            //     // Pesan selamat datang
            //     addMessage("Halo! Saya adalah asisten virtual yang siap menjawab pertanyaan Anda tentang dokumen ini. Silakan tanyakan apa saja terkait konten dokumen.", false);
            // });
        </script>

        <hr>

        <div class="mt-4">
            <h5 class="mb-3 text-center">KOMENTAR</h5>
            <?php if (!empty($komentar)): ?>
                <?php
                $limit = 3; // Jumlah komentar yang langsung ditampilkan
                $totalKomentar = count($komentar);
                ?>

                <?php foreach (array_slice($komentar, 0, $limit) as $k): ?>
                    <?php include 'komentar_card.php'; ?>
                <?php endforeach; ?>

                <?php if ($totalKomentar > $limit): ?>
                    <div class="collapse" id="moreComments">
                        <?php foreach (array_slice($komentar, $limit) as $k): ?>
                            <?php include 'komentar_card.php'; ?>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-center m-3">
                        <button class="btn btn-primary rounded-pill" style="background-color: #EC1928; border: none;" type="button" data-bs-toggle="collapse" data-bs-target="#moreComments" aria-expanded="false" aria-controls="moreComments" id="toggleComments">
                            Tampilkan Semua Komentar
                        </button>
                    </div>

                    <script>
                        document.getElementById('toggleComments').addEventListener('click', function() {
                            const btn = this;
                            if (btn.getAttribute('aria-expanded') === 'true') {
                                btn.textContent = 'Tutup Komentar';
                            } else {
                                btn.textContent = 'Tampilkan Semua Komentar';
                            }
                        });
                    </script>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-warning">
                    Belum ada komentar untuk pengetahuan ini.
                </div>
            <?php endif; ?>

            <?php if ($isLoggedIn && $user_id): ?>
                <form action="<?= base_url('pengetahuan/comment/' . $pengetahuan['id']); ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <textarea name="komentar" class="form-control" rows="5" placeholder="Tulis komentar..." required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" style="background-color: #341EBB; border: none;" class="btn btn-primary rounded-pill">Kirim Komentar</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-info">
                    Silakan <a href="<?= base_url('login') ?>">login</a> untuk memberikan komentar.
                </div>
            <?php endif; ?>
        </div>

        <script>
            // Fungsi untuk tombol balas
            document.querySelectorAll('.reply-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const commentId = this.dataset.commentId;
                    const replyForm = document.getElementById('reply-form-' + commentId);

                    // Sembunyikan semua form balas lainnya
                    document.querySelectorAll('.reply-form').forEach(form => {
                        if (form.id !== 'reply-form-' + commentId) {
                            form.style.display = 'none';
                        }
                    });

                    // Toggle form balas
                    if (replyForm.style.display === 'none') {
                        replyForm.style.display = 'block';
                        // Scroll ke form
                        replyForm.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    } else {
                        replyForm.style.display = 'none';
                    }
                });
            });

            // Fungsi untuk tombol batal
            document.querySelectorAll('.cancel-reply').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.reply-form').style.display = 'none';
                });
            });

            // Auto close form balas ketika submit
            document.querySelectorAll('.reply-form form').forEach(form => {
                form.addEventListener('submit', function() {
                    this.closest('.reply-form').style.display = 'none';
                });
            });
        </script>

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Ambil semua form hapus komentar
                const deleteForms = document.querySelectorAll('.delete-comment-form');

                deleteForms.forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault(); // cegah submit langsung

                        Swal.fire({
                            title: 'Yakin ingin menghapus komentar ini?',
                            text: 'Komentar yang dihapus tidak bisa dikembalikan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, hapus',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit(); // lanjutkan submit jika dikonfirmasi
                            }
                        });
                    });
                });
            });
        </script>


    </div>
    <!-- <a href="/pengetahuan" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a> -->
</div>


<?= $this->endSection(); ?>