<?php

class CustomerController extends AuthorizedController
{
    private $viewDir = 'admin' . DIRECTORY_SEPARATOR . 'customer' . DIRECTORY_SEPARATOR;
    private $cssDir =  'admin' . DIRECTORY_SEPARATOR . 'customer' . DIRECTORY_SEPARATOR;

    public function index()
    {
        if(!isset($_GET['search'])){
            $search = '';
        }else{
            $search = $_GET['search'];
        }

        $customers = AdminCustomer::read($search);
        
        foreach($customers as $customer){
            if($customer->lastOnline !=null){
                $customer->lastOnline= date("F jS, Y, H:i:s", strtotime($customer->lastOnline));
            }
        }

        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'customers' => $customers,
            'totalCustomers'=>AdminCustomer::countCustomer($search),
            'javascript'=>'<script src="'. App::config('url'). 'public/js/custom/AdminCustomer.js"></script>'
        ]);
    }

        // Autocomplete search
        public function searchcustomer($search){
            header('Content-type: application/json');
            echo json_encode(AdminCustomer::searchCustomer($search));
        }

    public function details($id)
    {
            $this->view->render($this->viewDir . 'details',[
                'css' => $this->cssDir . 'index.css',
                'customer'=>AdminCustomer::readOne($id)
            ]);
    }
}