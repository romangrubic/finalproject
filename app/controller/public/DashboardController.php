<?php

class DashboardController extends AuthorizedController
{
    private $viewDir = 'public' . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR;
    private $cssDir =  'dashboard' . DIRECTORY_SEPARATOR;

    private $customer;
    private $message;

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
        $this->customer = Customer::readOne($_SESSION['authorized']->id);


        $this->view->render($this->viewDir . 'index',[
            'css' => $this->cssDir . 'index.css',
            'customer'=>$this->customer,
            'message'=>$this->message,
            'orders'=>Dashboard::getOrders($_SESSION['authorized']->id),
            'cartitems'=>Dashboard::getOrderDetails($_SESSION['authorized']->id)
        ]);
    }
}
