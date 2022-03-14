<?php

class RegistrationController extends Controller
{
    private $customer;
    private $message;

    private $cssDir =  'registration' . DIRECTORY_SEPARATOR;


    public function __construct()
    {
        parent::__construct();
        
        $this->customer = new stdClass();
        $this->customer->id = null;
        $this->customer->firstname = '';
        $this->customer->lastname = '';
        $this->customer->email = '';
        $this->customer->user_password = '';
        $this->customer->user_password_repeat = '';
        $this->customer->phonenumber = '';
        $this->customer->street = '';
        $this->customer->city = '';
        $this->customer->postalnumber = '';

        $this->message = new stdClass();
        $this->message->firstname='';
        $this->message->lastname='';
        $this->message->email = '';
        $this->message->user_password = '';
        $this->message->user_password_repeat = '';
        $this->message->phonenumber = '';
        $this->message->street = '';
        $this->message->city = '';
        $this->message->postalnumber = '';
    }

    public function index()
    {
        $this->view->render('registration', [
            'css' => $this->cssDir . 'index.css',
            'customer'=>$this->customer,
            'message'=>$this->message
        ]);
    }

    public function newCustomer(){
        $this->customer = (object) $_POST;

        // Validation part
        if($this->validateFirstname() &&
            $this->validateLastname() &&
            $this->validateEmail() &&
            $this->validatePassword() &&
            $this->validatePhonenumber() && 
            $this->validateStreet() &&
            $this->validateCity() &&
            $this->validatePostalnumber()
            ){
            // Hash password
            $this->securePassword();

            // All is good, send to database
            Customer::insert((array)$this->customer);

            // Getting the customer for SESSION (otherwise no id in session)
            $customer = Registration::readOne($this->customer->email);
            $_SESSION['authorized'] = $customer;

            // Redirecting to homepage
            header('location: ' . App::config('url'));
                
        }else{
            $this->index();
            return;
        }
    }

    public function details($id)
    {
        $this->customer = Customer::readOne($id);
        $this->view->render('private/dashboard/details', [
            'css' => $this->cssDir . 'index.css',
            'customer'=>$this->customer,
            'message'=>$this->message
        ]);
    }

    public function update()
    {
        $this->customer = (object) $_POST;
        // print_r($this->customer);
        // Validators
        Customer::update((array)$this->customer);
        header('location:' . App::config('url').'dashboard/index');

    }

    // Validating input methods
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

    private function validatePassword()
    {
        if(strlen(trim($this->customer->user_password)) < 4){
            $this->message->user_password = 'Lozinka mora imati najmanje 4 znaka';
            return false;
        }
        if($this->customer->user_password !== $this->customer->user_password_repeat){
            $this->customer->user_password_repeat = '';
            $this->message->user_password_repeat = 'Lozinke nisu iste. Unesite ponovno.';
            return false;
        }
        return true;
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

    // Hashing password
    private function securePassword()
    {
        $this->customer->user_password = password_hash($this->customer->user_password, PASSWORD_BCRYPT);    
        return true;

    }
}