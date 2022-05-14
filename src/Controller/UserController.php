<?php

namespace Mvc\Controller;

use Config\Controller;
use JetBrains\PhpStorm\NoReturn;
use Mvc\Model\UserModel;

class UserController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        parent::__construct();
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['phone_number']) && isset($_POST['date_of_birth']) && !empty($_POST['email']) && !empty($_POST['password']) && $_POST['password'] === $_POST['password_verif'] && $_POST['email'] === $_POST['email_verif'])
        {
            $doesExit = $this->userModel->doesExist($_POST['email']);

            if ($doesExit === false) {
                $this->userModel->createUser($_POST['firstname'], $_POST['lastname'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT), intval($_POST['phone_number']), intval($_POST['date_of_birth']));

                header('Location: /login');
                exit;
            }
        }

        echo $this->twig->render('register_login/register.html.twig');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password']))
        {
            $user = $this->userModel->findOneByEmail($_POST['email']);

            if ($user && password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'nickname' => $user['nickname'],
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                    'email' => $user['email'],
                    'phone_number' => $user['phone_number'],
                    'date_of_birth' => $user['date_of_birth']
                ];

                header('Location: /');
                exit;
            }
        }
        echo $this->twig->render('register_login/login.html.twig');
    }

    #[NoReturn] public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }

}

?>