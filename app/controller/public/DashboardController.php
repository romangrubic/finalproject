<?php

class DashboardController extends AuthorizedController
{
    private $viewDir = 'public' . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR;
    private $cssDir =  'dashboard' . DIRECTORY_SEPARATOR;

    private $customer;
    private $message;
    private $password;

    public function __construct()
    {
        parent::__construct();
        
        $this->customer = new stdClass();
        $this->customer->id = null;
        $this->customer->firstname = '';
        $this->customer->lastname = '';
        $this->customer->email = '';
        $this->customer->phonenumber = '';
        $this->customer->street = '';
        $this->customer->city = '';
        $this->customer->postalnumber = '';

        $this->password = new stdClass();
        $this->password->oldpassword='';
        $this->password->newpassword='';
        $this->password->newpasswordrepeat='';

        $this->message = new stdClass();
        $this->message->firstname='';
        $this->message->lastname='';
        $this->message->email = '';
        $this->message->phonenumber = '';
        $this->message->street = '';
        $this->message->city = '';
        $this->message->postalnumber = '';
        $this->message->oldpassword='';
        $this->message->newpassword='Lozinka mora imati najmanje 6 znakova.';
        $this->message->newpasswordrepeat='';
    }

    // Dashboard profile
    public function index($change=0)
    {
        $this->customer = Customer::readOne($_SESSION['authorized']->id);

        $this->view->render($this->viewDir . 'index',[
            'css' => $this->cssDir . 'index.css',
            'customer'=>$this->customer,
            'message'=>$this->message,
            'change'=>$change,
            'orders'=>Dashboard::getOrders($_SESSION['authorized']->id),
            'cartitems'=>Dashboard::getOrderDetails($_SESSION['authorized']->id)
        ]);
    }

    // Changing details
    public function details(){
        $this->customer = Customer::readOne($_SESSION['authorized']->id);
        $this->view->render($this->viewDir . 'changedetails',[
            'css' => $this->cssDir . 'index.css',
            'customer'=>$this->customer,
            'message'=>$this->message,
        ]);
        
    }

    public function updateData()
    {
        $this->customer = (object) $_POST;
        if($this->validateFirstname() &&
            $this->validateLastname() &&
            $this->validateEmail() &&
            $this->validatePhonenumber() && 
            $this->validateStreet() &&
            $this->validateCity() &&
            $this->validatePostalnumber()
            ){
                Customer::update((array)$this->customer);
                $this->index();
            }else{
                $this->details();
            }
    }

    // Changing password
    public function passwordchange(){
        $id=$_SESSION['authorized']->id;
        $this->view->render($this->viewDir . 'passwordchange',[
            'css' => $this->cssDir . 'index.css',
            'customer'=>$this->customer,
            'password'=>$this->password,
            'message'=>$this->message,
        ]);
    }

    public function updatepassword()
    {
        $id=$_SESSION['authorized']->id;
        $oldpassword=trim($_POST['oldpassword']);
        $newpassword=trim($_POST['newpassword']);
        $newpasswordrepeat=trim($_POST['newpasswordrepeat']);
        $password = Customer::getpassword($id);

        if(password_verify($oldpassword, $password)==1){
            $this->password->oldpassword = $oldpassword;
            if(strlen($newpassword)<6){
                $this->message->newpassword = 'Lozinka mora imati najmanje 6 znakova.';
                $this->passwordchange();
                return;
            }
            if(strlen($newpasswordrepeat)<6){
                $this->message->newpasswordrepeat = 'Lozinka mora imati najmanje 6 znakova.';
                $this->passwordchange();
                return;
            }
            if($newpassword === $newpasswordrepeat){
                $password=password_hash($newpassword,PASSWORD_BCRYPT);
                Customer::updatepassword($id,$password);
                $this->index(1);
                return;
            }else{
                $this->password->newpassword = $newpassword;
                $this->message->newpasswordrepeat = 'Nova lozinka ponovno nije jednaka novoj lozinci.';
                $this->message->newpassword = '';
                $this->passwordchange();
                return;
            }
        }else{
            $this->message->oldpassword = 'Unesite Vasu trenutnu lozinku.';
            $this->passwordchange();
            return;
        } 
    }


    // Validator methods
    private function validateFirstname()
    {
        if(strlen(trim($this->customer->firstname)) === 0){
            $this->message->firstname = 'Ime je obavezno.';
            return false;
        }
        if(strlen(trim($this->customer->firstname)) > 50){
            $this->message->firstname = 'Ime ne smije imati vise od 50 znakova.';
            return false;
        }
        return true;
    }

    private function validateLastname()
    {
        if(strlen(trim($this->customer->lastname)) === 0){
            $this->message->lastname = 'Prezime je obavezno.';
            return false;
        }
        if(strlen(trim($this->customer->lastname)) > 50){
            $this->message->lastname = 'Prezime ne smije imati vise od 50 znakova.';
            return false;
        }
        return true;
    }

    private function validateEmail()
    {
        if(filter_var($this->customer->email, FILTER_VALIDATE_EMAIL)){
            return true;
        }else{
            $this->message->email = 'Email nije korektnog formata.';
            return false;
        };
    }

    private function validatePhonenumber()
    {
        if(strlen(trim($this->customer->phonenumber)) > 15){
            $this->message->phonenumber = 'Telefonski broj ne moze imati vise od 15 znakova.';
            return false;
        }
        return true;
    }

    private function validateStreet()
    {
        if(strlen(trim($this->customer->street)) > 255){
            $this->message->street = 'Ulica i kucni broj ne mogu imati vise od 255 znakova.';
            return false;
        }
        return true;
    }

    private function validateCity()
    {
        if(strlen(trim($this->customer->city)) > 50){
            $this->message->city = 'Grad  ne moze imati vise od 50 znakova.';
            return false;
        }
        return true;
    }

    private function validatePostalnumber()
    {
        if(strlen(trim($this->customer->postalnumber)) > 5){
            $this->message->postalnumber = 'Postanski broj ima 5 znakova.';
            return false;
        }
        return true;
    }
}
