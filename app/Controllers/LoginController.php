<?php


namespace App\Controllers;

use Config\Services;

class LoginController extends BaseController
{
    private $session;

    public function __construct()
    {
        $this->session = Services::session();
    }

    public function login()
    {
        if (!empty($_SESSION["user"])) {
            return redirect()->to(base_url() . '/public/painel');
        }
        return view('auth/login');
    }

    public function register()
    {
        return view('auth/register');
    }

    public function forget()
    {
        return view('auth/forget');
    }

    public function reset()
    {
        return view('auth/reset');
    }
}