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

        if(!isset($_GET['negation'])){
            $negation = '0';
        }else{
            $negation = $_GET['negation'];
        }

        if(!isset($_GET['page'])){
            $page = 1;
        }else{
            $page = (int)$_GET['page'];
        }
        if($page == 0){
            $page = 1;
        }


        $totalCustomers = AdminCustomer::countCustomer($search, $negation);
        $totalPages = ceil($totalCustomers / App::config('ppp'));

        if($page > $totalPages){
            $page = $totalPages;
        }

        $customers = AdminCustomer::read($search, $negation, $page);
        
        foreach($customers as $customer){
            if($customer->lastOnline !=null){
                $customer->lastOnline= date("F jS, Y, H:i:s", strtotime($customer->lastOnline));
            }
        }

        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'customers' => $customers,
            'totalCustomers'=>$totalCustomers,
            'page'=>$page,
            'totalPages'=>$totalPages,
            'search'=>$search,
            'negation'=>$negation,
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