<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('beranda', 'Beranda::index');

// Auth routes
$routes->get('login', 'Auth::login');
$routes->get('register', 'Auth::register');
$routes->post('auth/login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

// Public routes
$routes->get('pengetahuan', 'Pengetahuan::index');
$routes->get('pengetahuan/view/(:num)', 'Pengetahuan::view/$1');
$routes->post('pengetahuan/ask-pdf', 'Pengetahuan::askPdf');

$routes->get('pelatihan', 'Pelatihan::index');
$routes->get('pelatihan/view/(:num)', 'Pelatihan::view/$1');

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->post('rag/upload', 'Rag::uploadPdf');
    $routes->post('rag/query', 'Rag::query');
});

$routes->post('pengetahuan/ask-pdf', 'Pengetahuan::askPdf');
$routes->post('pengetahuan/ask', 'Pengetahuan::ask');
$routes->get('pengetahuan/get_pdf_for_chat/(:num)', 'Pengetahuan::get_pdf_for_chat/$1');

$routes->post('pengetahuan/comment/(:num)', 'Pengetahuan::comment/$1');
$routes->post('pengetahuan/delete-comment/(:num)', 'Pengetahuan::deleteComment/$1');

$routes->post('pelatihan/comment/(:num)', 'Pelatihan::comment/$1');
$routes->post('pelatihan/delete-comment/(:num)', 'Pelatihan::deleteComment/$1');

$routes->get('video/(:any)', 'Video::stream/$1');

$routes->get('pengajuan', 'Pengajuan::index');
$routes->post('pengajuan/prosesPengajuan', 'Pengajuan::prosesPengajuan');
$routes->get('sukses', 'Pengajuan::sukses');

$routes->post('pdfchat/chat/(:num)', 'PdfChatController::chat/$1');

// Admin routes - hanya bisa diakses oleh admin
$routes->group('admin', ['filter' => 'level:admin'], function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::dashboard');

    $routes->get('pengajuan', 'Pengajuan::daftarPengajuan');
    $routes->get('pengajuan/approve/(:num)', 'Pengajuan::approve/$1');
    $routes->get('pengajuan/delete/(:num)', 'Pengajuan::delete/$1');

    $routes->post('pengetahuan/process/(:num)', 'Admin\Pengetahuan::processPdf/$1');

    $routes->group('user', function ($routes) {
        $routes->get('/', 'Admin\User::index');
        $routes->get('create', 'Admin\User::create');
        $routes->post('store', 'Admin\User::store');
        $routes->get('edit/(:num)', 'Admin\User::edit/$1');
        $routes->put('update/(:num)', 'Admin\User::update/$1');
        $routes->post('update/(:num)', 'Admin\User::update/$1');
        $routes->delete('delete/(:num)', 'Admin\User::delete/$1');
    });

    $routes->group('pelatihan', function ($routes) {
        $routes->get('/', 'Admin\Pelatihan::index');
        $routes->get('create', 'Admin\Pelatihan::create');
        $routes->post('save', 'Admin\Pelatihan::save');
        $routes->get('edit/(:num)', 'Admin\Pelatihan::edit/$1');
        $routes->put('update/(:num)', 'Admin\Pelatihan::update/$1');
        $routes->delete('delete/(:num)', 'Admin\Pelatihan::delete/$1');
        $routes->get('view/(:num)', 'Admin\Pelatihan::view/$1');
    });

    $routes->group('pengetahuan', function ($routes) {
        $routes->get('/', 'Admin\Pengetahuan::index');
        $routes->get('create', 'Admin\Pengetahuan::create');
        $routes->post('save', 'Admin\Pengetahuan::save');
        $routes->get('edit/(:num)', 'Admin\Pengetahuan::edit/$1');
        $routes->put('update/(:num)', 'Admin\Pengetahuan::update/$1');
        $routes->delete('delete/(:num)', 'Admin\Pengetahuan::delete/$1');
        $routes->get('view/(:num)', 'Admin\Pengetahuan::view/$1');
    });

    $routes->group('template', function ($routes) {
        $routes->get('/', 'Admin\Template::index');
        $routes->get('create', 'Admin\Template::create');
        $routes->post('save', 'Admin\Template::save');
        $routes->get('edit/(:num)', 'Admin\Template::edit/$1');
        $routes->put('update/(:num)', 'Admin\Template::update/$1');
        $routes->delete('delete/(:num)', 'Admin\Template::delete/$1');
        $routes->get('view/(:num)', 'Admin\Template::view/$1');
    });
});

// Pegawai routes - hanya bisa diakses oleh pegawai
$routes->group('pegawai', ['filter' => 'level:pegawai'], function ($routes) {
    $routes->get('dashboard', 'Pegawai\Dashboard::index'); // Tambahkan dashboard pegawai

    $routes->group('pelatihan', function ($routes) {
        $routes->get('/', 'Pegawai\Pelatihan::index');
        $routes->get('create', 'Pegawai\Pelatihan::create');
        $routes->post('save', 'Pegawai\Pelatihan::save');
        $routes->get('edit/(:num)', 'Pegawai\Pelatihan::edit/$1');
        $routes->put('update/(:num)', 'Pegawai\Pelatihan::update/$1');
        $routes->delete('delete/(:num)', 'Pegawai\Pelatihan::delete/$1');
        $routes->get('view/(:num)', 'Pegawai\Pelatihan::view/$1');
    });

    $routes->group('pengetahuan', function ($routes) {
        $routes->get('/', 'Pegawai\Pengetahuan::index');
        $routes->get('create', 'Pegawai\Pengetahuan::create');
        $routes->post('save', 'Pegawai\Pengetahuan::save');
        $routes->get('edit/(:num)', 'Pegawai\Pengetahuan::edit/$1');
        $routes->put('update/(:num)', 'Pegawai\Pengetahuan::update/$1');
        $routes->delete('delete/(:num)', 'Pegawai\Pengetahuan::delete/$1');
        $routes->get('view/(:num)', 'Pegawai\Pengetahuan::view/$1');
    });

    $routes->group('template', function ($routes) {
        $routes->get('/', 'Pegawai\Template::index');
        $routes->get('create', 'Pegawai\Template::create');
        $routes->post('save', 'Pegawai\Template::save');
        $routes->get('edit/(:num)', 'Pegawai\Template::edit/$1');
        $routes->put('update/(:num)', 'Pegawai\Template::update/$1');
        $routes->delete('delete/(:num)', 'Pegawai\Template::delete/$1');
        $routes->get('view/(:num)', 'Pegawai\Template::view/$1');
    });
});

// User routes - hanya bisa diakses oleh user biasa
$routes->group('users', ['filter' => 'level:user'], function ($routes) {
    $routes->get('dashboard', 'Users\Dashboard::index'); // Tambahkan dashboard user

    $routes->group('pelatihan', function ($routes) {
        $routes->get('/', 'Users\Pelatihan::index');
        $routes->get('create', 'Users\Pelatihan::create');
        $routes->post('save', 'Users\Pelatihan::save');
        $routes->get('edit/(:num)', 'Users\Pelatihan::edit/$1');
        $routes->put('update/(:num)', 'Users\Pelatihan::update/$1');
        $routes->delete('delete/(:num)', 'Users\Pelatihan::delete/$1');
        $routes->get('view/(:num)', 'Users\Pelatihan::view/$1');
    });

    $routes->group('pengetahuan', function ($routes) {
        $routes->get('/', 'Users\Pengetahuan::index');
        $routes->get('create', 'Users\Pengetahuan::create');
        $routes->post('save', 'Users\Pengetahuan::save');
        $routes->get('edit/(:num)', 'Users\Pengetahuan::edit/$1');
        $routes->put('update/(:num)', 'Users\Pengetahuan::update/$1');
        $routes->delete('delete/(:num)', 'Users\Pengetahuan::delete/$1');
        $routes->get('view/(:num)', 'Users\Pengetahuan::view/$1');
    });

    $routes->group('template', function ($routes) {
        $routes->get('/', 'Users\Template::index');
        $routes->get('create', 'Users\Template::create');
        $routes->post('save', 'Users\Template::save');
        $routes->get('edit/(:num)', 'Users\Template::edit/$1');
        $routes->put('update/(:num)', 'Users\Template::update/$1');
        $routes->delete('delete/(:num)', 'Users\Template::delete/$1');
        $routes->get('view/(:num)', 'Users\Template::view/$1');
    });
});

// Chatbot - bisa diakses oleh semua level yang login
$routes->get('chatbot', 'Chatbot::index', ['filter' => 'auth']);
$routes->post('chatbot/process', 'Chatbot::process', ['filter' => 'auth']);
$routes->post('komentar/pengetahuan', 'Chatbot::addKomentarPengetahuan', ['filter' => 'auth']);
$routes->post('komentar/pelatihan', 'Chatbot::addKomentarPelatihan', ['filter' => 'auth']);
