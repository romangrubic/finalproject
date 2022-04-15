<?php

class OrderController extends AuthorizedController
{
    private $viewDir = 'admin' . DIRECTORY_SEPARATOR . 'order' . DIRECTORY_SEPARATOR;
    private $cssDir =  'admin' . DIRECTORY_SEPARATOR . 'order' . DIRECTORY_SEPARATOR;
    private $nf;

    public function __construct()
    {
        parent::__construct();
        $this->nf = new \NumberFormatter("hr-HR", \NumberFormatter::GROUPING_USED);
    }

    public function index()
    {
        $this->view->render($this->viewDir . 'index',[
            'css'=>$this->cssDir . 'index.css',
        ]);
        return;
    }

    public function active()
    {
        if(!isset($_GET['search'])){
            $search = '';
        }else{
            $search = $_GET['search'];
        }

        $totalOrders = Order::totalActiveOrder($search);
        $orders = Order::readActiveOrderCustomers($search);

        $this->view->render($this->viewDir . 'orders',[
            'css'=>$this->cssDir . 'index.css',
            'type'=>'active',
            'search'=>$search,
            'orders'=>$orders,
            'totalOrders'=>$totalOrders,
            'javascript'=>'<script src="'. App::config('url'). 'public/js/custom/AdminActiveOrders.js"></script>
            <script src="'. App::config('url'). 'public/js/custom/getOrderDetails.js"></script>'
        ]);
        return;
    }

    public function searchactive($search){
        header('Content-type: application/json');
        echo json_encode(Order::searchActive($search));
    }

    public function getDetails($id)
    {
        $data = (array)Order::readOrderDetails($id);
        echo json_encode($data);
    }
}