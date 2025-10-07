<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Chatbot</h4>
                </div>
                <div class="card-body">
                    <div id="chat-container" style="height: 400px; overflow-y: scroll; margin-bottom: 20px; border: 1px solid #ddd; padding: 10px;">
                        <div class="chat-message bot-message">
                            <p>Halo! Saya adalah chatbot yang siap membantu Anda. Silakan ajukan pertanyaan tentang pengetahuan atau pelatihan yang tersedia.</p>
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="text" id="user-input" class="form-control" placeholder="Ketik pertanyaan Anda...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" id="send-button">Kirim</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Daftar Pengetahuan</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($pengetahuan as $p) : ?>
                            <li class="list-group-item">
                                <a href="/pengetahuan/view/<?= $p['id']; ?>"><?= $p['judul']; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Daftar Pelatihan</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($pelatihan as $p) : ?>
                            <li class="list-group-item">
                                <a href="/pelatihan/view/<?= $p['id']; ?>"><?= $p['judul']; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#send-button').click(function() {
            sendMessage();
        });

        $('#user-input').keypress(function(e) {
            if (e.which == 13) {
                sendMessage();
            }
        });

        function sendMessage() {
            const userInput = $('#user-input').val();
            if (userInput.trim() === '') return;

            // Tampilkan pesan pengguna
            appendMessage(userInput, 'user-message');

            // Kirim ke server
            $.ajax({
                url: '/chatbot/process',
                type: 'POST',
                data: {
                    message: userInput
                },
                success: function(response) {
                    // Tampilkan respon bot
                    appendMessage(response.response, 'bot-message');
                },
                error: function() {
                    appendMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot-message error');
                }
            });

            $('#user-input').val('');
        }

        function appendMessage(message, className) {
            const chatContainer = $('#chat-container');
            const messageElement = $('<div class="chat-message ' + className + '"><p>' + message + '</p></div>');
            chatContainer.append(messageElement);
            chatContainer.scrollTop(chatContainer[0].scrollHeight);
        }
    });
</script>
<?= $this->endSection(); ?>