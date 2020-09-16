<?php


namespace App\Controllers;

use App\Models\User;
use App\Support\Email;
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

        if (!$email || !$passwd) {
            $json["status"] = 0;
            $json["error"]["#btnLogin"] = "Whooops!!! Informe seu e-mail e sua senha corretamente!";
        }

        $user = (new User())->findByEmail($data["email"]);

        if (empty($user) || !password_verify($passwd, $user[0]["passwd"])) {
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

        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);

        //Verifica se existe algum campo vazio e se o e-mail é válido
        if (in_array("", $data)) {
            $json["status"] = 0;
            $json["error"]["#btnRegister"] = "Whooops!!! Parece que alguns campos ainda estão vazios!";
        } elseif (!$email) {
            $json["status"] = 0;
            $json["error"]["#btnRegister"] = "Whooops!!! O e-mail informado não é válido!";

            //Verifica se as senhas informadas são iguais
        } elseif ($data["passwd"] === $data["password-confirm"]) {
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
        $data = $this->request->getPost();
        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);

        $json = [];
        $json["status"] = 1;
        $json["error"] = [];

        $user = new User();

        if (!$email) {
            $json["status"] = 0;
            $json["error"]["#btnForget"] = "Whooops!!! O e-mail informado não é válido!";
        } else {
            $register = $user->findByEmail($data["email"]);

            if (empty($register)) {
                $json["status"] = 0;
                $json["error"]["#btnForget"] = "Whooops!!! O e-mail informado não é cadastrado!";
            }
        }

        if ($json["status"] == 1) {
            $register[0]["forget"] = (md5(uniqid(rand(), true)));
            $user->replace($register[0]);

            $_SESSION["forget"] = $register[0]["id"];

            $register[0]["link"] = base_url() . "/public/senha/{$register[0]['email']}/{$register[0]['forget']}";

            $email = new Email();

            $email->add(
                "Recupere sua senha",
                (new LoginController())->recover($register[0]),
                "{$register[0]['first_name']} {$register[0]['last_name']}",
                $register[0]["email"]
            )->send();
        }
        echo json_encode($json);
    }

    public function reset()
    {
        if (empty($_SESSION["forget"])) {
            return redirect()->to(base_url() . '/public/recuperar');
        }

        $user = new User();

        $json = [];
        $json["status"] = 1;
        $json["error"] = [];

        $data = $this->request->getPost();

        //Verifica se existe algum campo vazio e se o e-mail é válido
        if (in_array("", $data)) {
            $json["status"] = 0;
            $json["error"]["#btnReset"] = "Whooops!!! Parece que alguns campos ainda estão vazios!";

            //Verifica se as senhas informadas são iguais
        } elseif ($data["passwd"] === $data["password-confirm"]) {
            if (strlen($data["passwd"]) < 4) {
                $json["status"] = 0;
                $json["error"]["#btnReset"] = "Whooops!!! Informe uma senha com pelo menos 4 caracteres!";
            }
        } else {
            $json["status"] = 0;
            $json["error"]["#btnReset"] = "Whooops!!! As senhas não coincidem!";
        }

        if ($json["status"] == 1) {
            $register = $user->findById($_SESSION["forget"]);

            $register[0]["forget"] = null;
            $register[0]["passwd"] = password_hash($data["passwd"], PASSWORD_DEFAULT);
            $user->replace($register[0]);

            unset($_SESSION["forget"]);
        }

        echo json_encode($json);

    }
}