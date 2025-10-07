<?php

function isAdmin()
{
    if (!session()->get('logged_in')) {
        // kalau belum login, paksa ke halaman login
        header('Location: ' . base_url('login'));
        exit;
    }

    return session()->get('level') === 'admin';
}

function isPegawai()
{
    if (!session()->get('logged_in')) {
        header('Location: ' . base_url('login'));
        exit;
    }

    return session()->get('level') === 'pegawai';
}

function isUser()
{
    if (!session()->get('logged_in')) {
        header('Location: ' . base_url('login'));
        exit;
    }

    return session()->get('level') === 'user';
}
