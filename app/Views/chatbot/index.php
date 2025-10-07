<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div>
    <h1>Chatbot</h1>
    <div>
        <div id="chat-container" style="height: 300px; overflow-y: scroll; border: 1px solid #000; padding: 10px;">
            <div>
                <p>Halo! Saya adalah chatbot yang siap membantu Anda. Silakan ajukan pertanyaan tentang pengetahuan atau pelatihan yang tersedia.</p>
            </div>
        </div>
        <div>
            <input type="text" id="user-input" placeholder="Ketik pertanyaan Anda...">
            <button id="send-button">Kirim</button>
        </div>
    </div>
    <div>
        <h2>Daftar Pengetahuan</h2>
        <ul>
            <?php foreach ($pengetahuan as $p) : ?>
                <li><a href="/pengetahuan/view/<?= $p['id']; ?>"><?= $p['judul']; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div>
        <h2>Daftar Pelatihan</h2>
        <ul>
            <?php foreach ($pelatihan as $p) : ?>
                <li><a href="/pelatihan/view/<?= $p['id']; ?>"><?= $p['judul']; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
    document.getElementById('send-button').addEventListener('click', function() {
        const userInput = document.getElementById('user-input').value;
        if (userInput.trim() === '') return;

        // Tampilkan pesan pengguna
        const chatContainer = document.getElementById('chat-container');
        const userMessage = document.createElement('div');
        userMessage.innerHTML = '<p><strong>Anda:</strong> ' + userInput + '</p>';
        chatContainer.appendChild(userMessage);

        // Kirim ke server
        fetch('/chatbot/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    message: userInput
                })
            })
            .then(response => response.json())
            .then(data => {
                const botMessage = document.createElement('div');
                botMessage.innerHTML = '<p><strong>Bot:</strong> ' + data.response + '</p>';
                chatContainer.appendChild(botMessage);
                chatContainer.scrollTop = chatContainer.scrollHeight;
            })
            .catch(error => {
                const errorMessage = document.createElement('div');
                errorMessage.innerHTML = '<p><strong>Bot:</strong> Maaf, terjadi kesalahan. Silakan coba lagi.</p>';
                chatContainer.appendChild(errorMessage);
            });

        document.getElementById('user-input').value = '';
        chatContainer.scrollTop = chatContainer.scrollHeight;
    });
</script>
<?= $this->endSection(); ?>