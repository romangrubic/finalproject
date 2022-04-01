<?php

class IndexController extends LoginController
{
    private $cssDir =  'index' . DIRECTORY_SEPARATOR;

    public function index()
    {
        if (isset($_SESSION['authorized']->user_role) && ($_SESSION['authorized']->user_role == 'admin' || $_SESSION['authorized']->user_role == 'oper')) {
            $this->view->render('admin/index',[
                'css'=>'admin' . DIRECTORY_SEPARATOR .'index.css',
            ]);
            return;
        }
        $this->view->render('index',[
            'email'=>$this->email,
            'message'=>$this->message,
            'css'=>$this->cssDir . 'index.css',
            'newestProductList'=> Index::newestProductList(),
            'mostSoldProductList'=> Index::mostSoldProductList()
        ]);
    }

    public function emailNewsletter()
    {
        $email = $_POST['email'];

        if($this->validateEmail($email)){
            if(Email::readOne($email) == $email){
                echo 'Email vec postoji';
            }else{
                Email::insert($email);
                echo 'Unesen novi e-mail';
            }
        }

        header('location: ' . App::config('url') . $_POST['currentPage']);
    }

    private function validateEmail($email)
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            return true;
        }else{
            return false;
        };
    }
}
