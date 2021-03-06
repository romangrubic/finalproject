<?php

class OperatorController extends AuthorizedController
{
    private $viewDir = 'admin' . DIRECTORY_SEPARATOR . 'operator' . DIRECTORY_SEPARATOR;
    private $cssDir =  'admin' . DIRECTORY_SEPARATOR . 'operator' . DIRECTORY_SEPARATOR;

    private $operator;
    private $message;

    public function __construct()
    {
        parent::__construct();
        $this->operator = new stdClass();
        $this->operator->id = 0;
        $this->operator->email = '';
        $this->operator->user_password = '';
        $this->operator->user_password_repeat = '';
        $this->operator->user_password_new = '';
        $this->operator->user_password_new_repeat = '';
        $this->operator->firstname = '';
        $this->operator->lastname = '';
        $this->operator->user_role = '';

        $this->message = new stdClass();
        $this->message->email = '';
        $this->message->user_password = '';
        $this->message->user_password_repeat = '';
        $this->message->user_password_new = '';
        $this->message->user_password_new_repeat = '';
        $this->message->firstname = '';
        $this->message->lastname = '';
        $this->message->user_role = '';
    }

    public function index()
    {
        $operators = Operator::read();

        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'operators' => $operators
        ]);
    }

    public function details($id = 0)
    {
        if ($id == 0) {
            $this->view->render($this->viewDir . 'details', [
                'css' => $this->cssDir . 'index.css',
                'operator' => $this->operator,
                'message' => $this->message,
                'action' => 'Dodaj novog operatora.'
            ]);
        } else {
            $this->view->render($this->viewDir . 'details', [
                'css' => $this->cssDir . 'index.css',
                'operator' => Operator::readOne($id),
                'message' => $this->message,
                'action' => 'Spremi promijene.'
            ]);
        }
    }

    public function action()
    {
        $this->operator = (object)$_POST;

        if ($this->operator->id == 0) {
            if (
                $this->validationFirstname() &&
                $this->validationLastname() &&
                $this->validateEmail() &&
                $this->validateRole() &&
                $this->validatePassword()
            ) {
                $this->securePassword();
                Operator::insert((array)$this->operator);
            } else {
                $this->view->render($this->viewDir . 'details', [
                    'css' => $this->cssDir . 'index.css',
                    'operator' => $this->operator,
                    'message' => $this->message,
                    'action' => 'Spremi promijene.'
                ]);
                return;
            }
        } else {;
            if (
                $this->validationFirstname() &&
                $this->validationLastname() &&
                $this->validateRole() &&
                $this->validateEmail()
            ) {
                Operator::update((array)$this->operator);
            } else {
                $this->view->render($this->viewDir . 'details', [
                    'css' => $this->cssDir . 'index.css',
                    'operator' => (object)$_POST,
                    'message' => $this->message,
                    'action' => 'Spremi promijene.'
                ]);
                return;
            }
        }
        header('location:' . App::config('url') . 'operator/index');
    }

    public function passwordchange($id)
    {
        $this->view->render($this->viewDir . 'changepassword', [
            'css' => $this->cssDir . 'index.css',
            'message'=>$this->message,
            'operator'=>Operator::readOne($id)
        ]);
    }

    public function change()
    {
        $id=$_POST['id'];
        $oldpassword=trim($_POST['user_password']);
        $newpassword=trim($_POST['user_password_new']);
        $newpasswordrepeat=trim($_POST['user_password_new_repeat']);
        $password = Operator::getpassword($id);

        if(password_verify($oldpassword, $password)==1){
            $this->operator->user_password = $oldpassword;
            if(strlen($newpassword)<6){
                $this->message->user_password_new = 'Lozinka mora imati najmanje 6 znakova.';
                $this->passwordchange($id);
                return;
            }
            if(strlen($newpasswordrepeat)<6){
                $this->message->user_password_new_repeat = 'Lozinka mora imati najmanje 6 znakova.';
                $this->passwordchange($id);
                return;
            }
            if($newpassword === $newpasswordrepeat){
                $password=password_hash($newpassword,PASSWORD_BCRYPT);
                Operator::updatepassword($id,$password);
                $this->index(1);
                return;
            }else{
                $this->password->newpassword = $newpassword;
                $this->message->user_password_new_repeat = 'Nova lozinka ponovno nije jednaka novoj lozinci.';
                $this->message->user_password_new = '';
                $this->passwordchange($id);
                return;
            }
        }else{
            $this->message->user_password = 'Unesite Vasu trenutnu lozinku.';
            $this->passwordchange($id);
            return;
        }
    }

    public function delete($id)
    {
        Operator::delete($id);
        header('Location:' . App::config('url') . 'operator/index');
    }

    // Hashing password
    private function securePassword()
    {
        $this->operator->user_password = password_hash($this->operator->user_password, PASSWORD_BCRYPT);
        return true;
    }

    private function validatePassword()
    {
        if (strlen(trim($this->operator->user_password)) < 4) {
            $this->message->user_password = 'Lozinka mora imati najmanje 4 znaka';
            return false;
        }
        if ($this->operator->user_password !== $this->operator->user_password_repeat) {
            $this->operator->user_password_repeat = '';
            $this->message->user_password_repeat = 'Lozinke nisu iste. Unesite ponovno.';
            return false;
        }
        return true;
    }

    // Validation functions
    private function validationFirstname()
    {
        if (strlen(trim($this->operator->firstname)) === 0) {
            $this->message->firstname = 'Ime je obavezno.';
            return false;
        }
        if (strlen(trim($this->operator->firstname)) > 50) {
            $this->message->firstname = 'Ime moze imati do 50 znakova.';
            return false;
        }
        return true;
    }

    private function validationLastname()
    {
        if (strlen(trim($this->operator->lastname)) === 0) {
            $this->message->lastname = 'Prezime je obavezno.';
            return false;
        }
        if (strlen(trim($this->operator->lastname)) > 50) {
            $this->message->lastname = 'Prezime moze imati do 50 znakova.';
            return false;
        }
        return true;
    }

    private function validateEmail()
    {
        if(filter_var($this->operator->email, FILTER_VALIDATE_EMAIL)){
            return true;
        }else{
            $this->message->email = 'Email nije korektnog formata.';
            return false;
        };
    }

    private function validateRole()
    {
        if($this->operator->user_role==''){
            $this->message->user_role='Odaberite jednu od uloga.';
            return false;
        }
        return true;
    }
}
