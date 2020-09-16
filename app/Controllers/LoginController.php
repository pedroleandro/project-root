<?php


namespace App\Controllers;

use App\Models\User;
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
        if(empty($_SESSION["forget"])){
            return redirect()->to(base_url() . '/public/recuperar');
        }

        $user = new User();

        $data = $this->request->uri->getSegments();

        $email = $data[1];
        $forget = $data[2];

        $register = [];

        if(!filter_var($email, FILTER_VALIDATE_EMAIL) || !filter_var($forget, FILTER_DEFAULT)){
            return redirect()->to(base_url() . '/public/recuperar');
        }

        if(empty($email) || empty($forget)){
            return redirect()->to(base_url() . '/public/recuperar');
        }

        if($user->emailExists($email)){
            $register = $user->findByEmailAndForget($email, $forget);

            if(empty($register)){
                return redirect()->to(base_url() . '/public/recuperar');
            }
        }else{
            return redirect()->to(base_url() . '/public/recuperar');
        }

        return view('auth/reset');
    }

    public function recover($data)
    {
        return view('emails/recover', $data);
    }
}