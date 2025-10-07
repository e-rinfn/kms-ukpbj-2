<!-- <footer class="bg-white text-dark text-center mb-3 py-3">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y'); ?> All Rights Reserved. Rin Design by UKPBJ Kota Tasikmalaya.</p>
    </div>
</footer> -->


<footer class="bg-white text-dark text-center mb-3 py-3">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y'); ?> All Rights Reserved. Rin Design by UKPBJ Kota Tasikmalaya.</p>
    </div>
</footer>

<!-- Tombol Chatbot -->
<div class="chatbot-btn" id="chatbotButton">
    <i class="fas fa-comment-dots"></i>
</div>

<!-- Container Chatbox -->
<div class="chatbot-container" id="chatbotContainer">
    <div class="chatbox-header">
        <h6>Chatbot Assistant</h6>
        <span class="close-btn" id="closeChat">&times;</span>
    </div>
    <div class="chatbox-messages" id="chatMessages">
        <!-- Pesan akan dimuat di sini -->
    </div>
    <div class="chatbox-input">
        <input type="text" id="userInput" placeholder="Ketik pesan Anda...">
        <button id="sendMessage"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<!-- Styles untuk Chatbot -->
<style>
    .chatbot-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background-color: #4e73df;
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .chatbot-btn:hover {
        background-color: #3a56c4;
        transform: scale(1.05);
    }

    .chatbot-container {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 350px;
        height: 450px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        display: none;
        flex-direction: column;
        z-index: 1000;
        overflow: hidden;
    }

    .chatbox-header {
        background-color: #3a56c4;
        color: white;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .close-btn {
        cursor: pointer;
        font-size: 20px;
    }

    .chatbox-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .message {
        padding: 10px 15px;
        border-radius: 18px;
        max-width: 80%;
        word-wrap: break-word;
    }

    .user-message {
        background-color: #e6f7ff;
        align-self: flex-end;
    }

    .bot-message {
        background-color: #f1f0f0;
        align-self: flex-start;
    }

    .chatbox-input {
        display: flex;
        padding: 15px;
        border-top: 1px solid #eee;
    }

    .chatbox-input input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 20px;
        outline: none;
    }

    .chatbox-input button {
        background-color: #4e73df;
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        margin-left: 10px;
        cursor: pointer;
    }
</style>

<!-- JavaScript untuk Chatbot -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatbotButton = document.getElementById('chatbotButton');
        const chatbotContainer = document.getElementById('chatbotContainer');
        const closeChat = document.getElementById('closeChat');
        const userInput = document.getElementById('userInput');
        const sendMessage = document.getElementById('sendMessage');
        const chatMessages = document.getElementById('chatMessages');

        // Toggle chatbox
        chatbotButton.addEventListener('click', function() {
            chatbotContainer.style.display = chatbotContainer.style.display === 'flex' ? 'none' : 'flex';
        });

        closeChat.addEventListener('click', function() {
            chatbotContainer.style.display = 'none';
        });

        // Fungsi untuk mengirim pesan
        function sendMessageToBot() {
            const message = userInput.value.trim();
            if (message === '') return;

            // Tambahkan pesan pengguna ke chat
            addMessage(message, 'user');
            userInput.value = '';

            // Kirim pesan ke backend Flask
            fetch('http://localhost:5000/predict', {
                    // fetch('https://chat-kms.erinfn.my.id/predict', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        message: message
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Tambahkan respon bot ke chat
                    addMessage(data.answer, 'bot');
                })
                .catch(error => {
                    console.error('Error:', error);
                    addMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot');
                });
        }

        // Event listener untuk tombol kirim dan enter
        sendMessage.addEventListener('click', sendMessageToBot);
        userInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessageToBot();
            }
        });

        // Fungsi untuk menambahkan pesan ke chat
        function addMessage(text, sender) {
            const messageElement = document.createElement('div');
            messageElement.classList.add('message');
            messageElement.classList.add(sender + '-message');
            messageElement.textContent = text;
            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Pesan sambutan otomatis
        addMessage('Halo! Ada yang bisa saya bantu?', 'bot');
    });
</script>

<!-- Pastikan Font Awesome tersedia untuk ikon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">