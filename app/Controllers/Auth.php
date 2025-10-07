<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function register()
    {
        return view('register');
    }

    public function login()
    {
        // Jika sudah login, redirect ke home
        if (session()->get('id')) {
            return redirect()->to('/');
        }

        return view('login');
    }

    // public function attemptLogin()
    // {
    //     $username = $this->request->getVar('username');
    //     $password = $this->request->getVar('password');

    //     $user = $this->userModel->getUserByUsername($username);

    //     if (!$user) {
    //         return redirect()->back()->withInput()->with('error', 'Username atau password salah');
    //     }

    //     if (!password_verify($password, $user['password'])) {
    //         return redirect()->back()->withInput()->with('error', 'Username atau password salah');
    //     }

    //     // Set session
    //     $sessionData = [
    //         'id' => $user['id'],
    //         'username' => $user['username'],
    //         'level' => $user['level'],
    //         'nama' => $user['nama'],
    //         'logged_in' => true
    //     ];
    //     session()->set($sessionData);

    //     return redirect()->to('/beranda');
    // }

    public function attemptLogin()
    {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        // Validasi input
        if (empty($email) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Email dan password harus diisi');
        }

        // Cari user berdasarkan email
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email tidak terdaftar');
        }

        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Password salah');
        }

        // Set session
        $sessionData = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'level' => $user['level'],
            'nama' => $user['nama'],
            'logged_in' => true
        ];
        session()->set($sessionData);

        return redirect()->to('/beranda');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
