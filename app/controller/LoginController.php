<?php

class LoginController extends Controller
{
    public function loginView($message, $email)
    {
        $this->view->render('login',[
            'message' => $message,
            'email' => $email
        ]);
    }

    public function index()
    {
        $this->loginView('Popunite email i lozinku','');
    }

    public function authorize()
    {
        if(!isset($_POST['email']) || !isset($_POST['user_password'])){
            $this->index();
            return;
        }

        if(strlen(trim($_POST['email'])) === 0){
            $this->loginView('Email obavezan.','');
            return;
        }

        if(strlen(trim($_POST['user_password']))===0){
            $this->loginView('Lozinka obavezna.', $_POST['email']);
        }

        $operator = Operator::authorize($_POST['email'], $_POST['user_password']);

        if($operator == null){
            $this->loginView('Neispravna kombinacija emaila i lozinke', $_POST['email']);
            return;
        }

        $_SESSION['authorized'] = $operator;
    }

    public function logout()
    {
        unset($_SESSION['authorized']);
        session_destroy();
        $this->loginView('Uspješno ste odjavljeni.','');
    }


}