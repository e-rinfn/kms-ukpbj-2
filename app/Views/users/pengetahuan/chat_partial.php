<?php
// File: app/Views/pengetahuan/chat_partial.php
// Ini adalah partial view untuk komponen chat PDF

$isLoggedIn = session()->get('logged_in') === true;
?>

<!-- PDF Chatbot Section -->
<div class="pdf-chat-container mt-5">
    <h4 class="mb-3">Tanya Dokumen</h4>
    <p>Ajukan pertanyaan tentang dokumen ini dan dapatkan jawaban berdasarkan isinya.</p>

    <?php if ($isLoggedIn): ?>
        <div id="chat-history">
            <!-- Pesan chat akan muncul di sini -->
            <?php if (isset($chat_history) && !empty($chat_history)): ?>
                <?php foreach ($chat_history as $chat): ?>
                    <div class="chat-message <?= $chat['role'] === 'user' ? 'user-message' : 'bot-message' ?>">
                        <?= esc($chat['content']) ?>
                        <?php if ($chat['role'] === 'bot' && !empty($chat['sources'])): ?>
                            <div class="source-reference">
                                Sumber: <?= esc(implode(', ', $chat['sources'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="chat-input-group">
            <input type="text" id="chat-question" class="form-control" placeholder="Tanyakan sesuatu tentang dokumen ini..." autocomplete="off">
            <button id="send-question" class="btn btn-primary">
                <span id="send-text">Kirim</span>
                <span id="loading-spinner" class="loading-spinner"></span>
            </button>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            Silakan <a href="<?= base_url('login') ?>">login</a> untuk menggunakan fitur tanya dokumen.
        </div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        const chatHistory = $('#chat-history');
        const chatQuestion = $('#chat-question');
        const sendButton = $('#send-question');
        const sendText = $('#send-text');
        const loadingSpinner = $('#loading-spinner');

        // Fungsi untuk menambahkan pesan ke chat history
        function addMessage(role, content, sources = null) {
            const messageDiv = $('<div class="chat-message ' + (role === 'user' ? 'user-message' : 'bot-message') + '"></div>');
            messageDiv.text(content);

            if (sources && role === 'bot') {
                const sourcesDiv = $('<div class="source-reference"></div>');
                sourcesDiv.text('Sumber: ' + sources.join(', '));
                messageDiv.append(sourcesDiv);
            }

            chatHistory.append(messageDiv);
            chatHistory.scrollTop(chatHistory[0].scrollHeight);
        }

        // Handle pengiriman pertanyaan
        sendButton.click(async function() {
            const question = chatQuestion.val().trim();
            if (!question) return;

            // Tambahkan pesan user ke chat
            addMessage('user', question);
            chatQuestion.val('');

            // Tampilkan loading
            sendText.hide();
            loadingSpinner.show();
            sendButton.prop('disabled', true);

            try {
                // Kirim pertanyaan ke server
                const response = await fetch('<?= base_url('pengetahuan/processChat') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        question: question,
                        pdf_id: <?= $pengetahuan['id'] ?>,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    })
                });

                const data = await response.json();

                if (data.error) {
                    addMessage('bot', 'Maaf, terjadi kesalahan: ' + data.error);
                } else {
                    addMessage('bot', data.answer, data.sources);
                }
            } catch (error) {
                addMessage('bot', 'Maaf, terjadi kesalahan saat memproses pertanyaan.');
                console.error('Error:', error);
            } finally {
                // Sembunyikan loading
                sendText.show();
                loadingSpinner.hide();
                sendButton.prop('disabled', false);
            }
        });

        // Handle enter key
        chatQuestion.keypress(function(e) {
            if (e.which === 13) {
                sendButton.click();
            }
        });

        // Auto-focus input chat
        chatQuestion.focus();
    });
</script>