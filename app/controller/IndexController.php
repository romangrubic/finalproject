<?php

class IndexController extends LoginController
{
    private $cssDir =  'index' . DIRECTORY_SEPARATOR;

    public function __construct()
    {
        parent::__construct();
        $this->nf = new \NumberFormatter("hr-HR", \NumberFormatter::DECIMAL);
        $this->nf->setPattern('#,##0.00');
    }

    public function index()
    {

        $newestProductList=Index::newestProductList();
        foreach($newestProductList as $product){
            $product->price=$this->nf->format($product->price);
        }

        $mostSoldProductList=Index::mostSoldProductList();
        foreach($mostSoldProductList as $product){
            $product->price=$this->nf->format($product->price);
        }
        $this->view->render('index',[
            'email'=>$this->email,
            'message'=>$this->message,
            'css'=>$this->cssDir . 'index.css',
            'newestProductList'=> $newestProductList,
            'mostSoldProductList'=> $mostSoldProductList
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
