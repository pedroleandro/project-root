<?php


namespace App\Controllers;


use App\Models\User;

class AuthController extends BaseController
{
    public function login()
    {

    }

    public function register(): void
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
        }else{
            if(!filter_var($data["email"], FILTER_VALIDATE_EMAIL)){
                $json["status"] = 0;
                $json["error"]["#btnRegister"] = "Whooops!!! O e-mail informado não é válido!";
            }
        }

        //Verifica se o e-mail já está registrado
        if($user->findByEmail($data["email"])){
            $json["status"] = 0;
            $json["error"]["#btnRegister"] = "Whooops!!! O e-mail informado já foi cadastrado!";
        }

        //Verifica se as senhas informadas são iguais
        if($data["passwd"] === $data["password-confirm"]){
            $data["passwd"] = password_hash($data["passwd"], PASSWORD_DEFAULT);
            unset($data["password-confirm"]);
        }else{
            $json["status"] = 0;
            $json["error"]["#btnRegister"] = "Whooops!!! As senhas não coincidem!";
        }

        //Verifica se existe algum campo invalidado para cadastrar
        if($json["status"] == 1){
            $user->save($data);
        }

        echo json_encode($json);

    }
}