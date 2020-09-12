<?php


namespace App\Controllers;

class LoginController extends BaseController
{
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