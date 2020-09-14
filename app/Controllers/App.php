<?php


namespace App\Controllers;


use App\Core\Session;
use App\Models\User;
use Config\Services;

class App extends BaseController
{
    private $session;
    protected $user;

    public function __construct()
    {
        $this->session = Services::session();
        if (empty($_SESSION["user"])) {
            unset($_SESSION["user"]);
        }
    }

    public function home()
    {
        if (empty($_SESSION["user"])) {
            return redirect()->to(base_url() . "/public/entrar");
        } else {
            return view('dashboard/home');
        }
    }

    public function logoff()
    {
        if (!empty($_SESSION["user"])) {
            unset($_SESSION["user"]);
        }
        return redirect()->to(base_url() . '/public/entrar');
    }
}