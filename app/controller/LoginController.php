<?php

class LoginController extends Controller
{

    private $cssDir =  'login' . DIRECTORY_SEPARATOR;
    protected $email;
    protected $message;

    public function __construct()
    {
        parent::__construct();
        $this->email = new stdClass();
        $this->email = '';

        $this->message = new stdClass();
        $this->message->email='';
        $this->message->password='';
    }
    
    public function index()
    {
        $this->view->render('login',[
            'css' => $this->cssDir . 'index.css',
            'message' => $this->message,
            'email' => $this->email
        ]);
    }

    public function authorize()
    {
        if(!isset($_POST['email']) || !isset($_POST['user_password'])){
            $this->index();
            return;
        }

        if(strlen(trim($_POST['email'])) === 0){
            $this->message->email = 'Email obavezan';
            $this->index();
            return;
        }

        if(strlen(trim($_POST['user_password']))===0){
            $this->message->password = 'Lozinka obavezna';
            $this->email = $_POST['email'];
            $this->index();
        }

        $operator = Login::authorize($_POST['email'], $_POST['user_password']);

        if($operator == null){
            $this->message->password = 'Neispravna kombinacija emaila i lozinke';
            $this->email = $_POST['email'];
            $this->index();
            return;
        }

        $_SESSION['authorized'] = $operator;

        if(!isset($_SESSION['authorized']->user_role)){
            header('location: ' . App::config('url') . $_POST['currentPage']);
        }else{
            header('location: ' . App::config('url'));
        }
    }

    public function logout()
    {
        unset($_SESSION['authorized']);
        session_destroy();
        
        if(!isset($_SESSION['authorized']->user_role)){
            header('location: ' . App::config('url') . $_POST['currentPage']);
        }else{
            header('location: ' . App::config('url'));
        }
        // $this->message->logout = 'Uspješno ste odjavljeni.';
        // $this->index();
    }


}