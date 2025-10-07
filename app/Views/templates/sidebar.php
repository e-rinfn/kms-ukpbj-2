<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistem Manajemen Pengetahuan'; ?></title>

    <!-- Google Font: Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>

<aside>
    <h3>Menu</h3>
    <ul>
        <li><a href="/">Home</a></li>
        <!-- <li><a href="/pengetahuan">Pengetahuan</a></li> -->
        <!-- <li><a href="/pelatihan">Pelatihan</a></li>
        <li><a href="/chatbot">Chatbot</a></li> -->
        <?php if (session()->get('level') === 'admin') : ?>
            <li><a href="/admin/pengetahuan">Admin Pengetahuan</a></li>
            <li><a href="/admin/pelatihan">Admin Pelatihan</a></li>
        <?php endif; ?>
        <?php if (session()->get('level') === 'user') : ?>
            <li><a href="/pegawai/pengetahuan">Pegawai Pengetahuan</a></li>
            <li><a href="/pegawai/pelatihan">Pegawai Pelatihan</a></li>
        <?php endif; ?>
    </ul>
</aside>