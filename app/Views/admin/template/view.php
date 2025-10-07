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

<?php
$docxPath = WRITEPATH . '../public/assets/uploads/template/' . $template['file_docx'];
$docxUrl  = base_url('assets/uploads/template/' . $template['file_docx']);
?>

<?php if (!empty($template['file_docx']) && file_exists($docxPath)): ?>
    <div class="pdf-viewer-container">
        <!-- Google Docs Viewer -->
        <iframe
            src="https://docs.google.com/gview?url=<?= urlencode($docxUrl) ?>&embedded=true"
            style="width:100%; height:500px; border:1px solid #ddd;"
            frameborder="0">
        </iframe>

        <!-- Aksi tambahan -->
        <div class="text-end mt-2">
            <a href="<?= $docxUrl ?>"
                class="btn btn-sm btn-outline-primary"
                target="_blank">
                <i class="bi bi-download"></i> Unduh DOCX
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        <?php if (empty($template['file_docx'])): ?>
            File DOCX tidak tersedia di database
        <?php else: ?>
            File DOCX tidak ditemukan di server
        <?php endif; ?>
    </div>
<?php endif; ?>

<?= $this->endSection(); ?>