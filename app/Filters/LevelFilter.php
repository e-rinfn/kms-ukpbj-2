<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LevelFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Jika belum login, redirect ke login
        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Jika arguments tidak diberikan, biarkan lewat
        if (empty($arguments)) {
            return;
        }

        $userLevel = $session->get('level');

        // Cek apakah level user sesuai dengan yang diizinkan
        if (!in_array($userLevel, $arguments)) {
            // Jika tidak sesuai, redirect ke halaman yang sesuai dengan level
            switch ($userLevel) {
                case 'admin':
                    return redirect()->to('/beranda');
                case 'pegawai':
                    return redirect()->to('/beranda');
                case 'user':
                    return redirect()->to('/beranda');
                default:
                    return redirect()->to('/beranda');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
