<?php


namespace App\Controllers;

use App\Models\User;
use Config\Services;

class AuthController extends BaseController
{
    private $session;

    public function __construct()
    {
        $this->session = Services::session();
    }

    public function login()
    {
        $data = $this->request->getPost();

        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
        $passwd = filter_var($data["passwd"], FILTER_DEFAULT);

        $user = new User();

        $json = [];
        $json["status"] = 1;
        $json["error"] = [];

        if(!$email || !$passwd){
            $json["status"] = 0;
            $json["error"]["#btnLogin"] = "Whooops!!! Informe seu e-mail e sua senha corretamente!";
        }

        $user = (new User())->findByEmail($data["email"])[0];

        if(empty($user) || !password_verify($passwd, $user["passwd"])){
            $json["status"] = 0;
            $json["error"]["#btnLogin"] = "Whooops!!! E-mail ou senha inválidos!";
        }

        if ($json["status"] == 1) {
            $register = (new User())->findByEmail($data["email"]);
            $this->session->set("user", $register[0]['id']);
        }

        echo json_encode($json);

    }

    public function register()
    {
        $data = $this->request->getPost();

        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $json = [];
        $json["status"] = 1;
        $json["error"] = [];

        $user = new User();

        //Verifica se existe algum campo vazio e se o e-mail é válido
        if (in_array("", $data)) {
            $json["status"] = 0;
            $json["error"]["#btnRegister"] = "Whooops!!! Parece que alguns campos ainda estão vazios!";
        } elseif (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            $json["status"] = 0;
            $json["error"]["#btnRegister"] = "Whooops!!! O e-mail informado não é válido!";

            //Verifica se as senhas informadas são iguais
        } elseif ($data["passwd"] == $data["password-confirm"]) {
            if (strlen($data["passwd"]) < 4) {
                $json["status"] = 0;
                $json["error"]["#btnRegister"] = "Whooops!!! Informe uma senha com pelo menos 4 caracteres!";
            }
        } else {
            $json["status"] = 0;
            $json["error"]["#btnRegister"] = "Whooops!!! As senhas não coincidem!";
        }

        $data["passwd"] = password_hash($data["passwd"], PASSWORD_DEFAULT);
        unset($data["password-confirm"]);

        //Verifica se o e-mail já está registrado
        if ($user->emailExists($data["email"])) {
            $json["status"] = 0;
            $json["error"]["#btnRegister"] = "Whooops!!! O e-mail informado já foi cadastrado!";
        }

        //Verifica se existe algum campo invalidado para cadastrar
        if ($json["status"] == 1) {
            $user->save($data);

            $register = $user->findByEmail($data["email"]);
            $this->session->set("user", $register[0]['id']);
        }
        echo json_encode($json);
    }

    public function forget()
    {

    }

    public function reset()
    {

    }
}