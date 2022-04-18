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
            'javascript'=>'<script src="'. App::config('url'). 'public/js/custom/AdminActiveOrders.js"></script>'
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

    public function finalized()
    {
        if(!isset($_GET['search'])){
            $search = '';
            $totalOrders = Order::totalFinishedOrder();
        }else{
            $search = $_GET['search'];
            $totalOrders = Order::totalFinalizedOrder($search);
        }

        // $totalOrders = Order::totalFinalizedOrder($search);
        $orders = Order::readFinalizedOrderCustomers($search);

        $this->view->render($this->viewDir . 'orders',[
            'css'=>$this->cssDir . 'index.css',
            'type'=>'finalized',
            'search'=>$search,
            'orders'=>$orders,
            'totalOrders'=>$totalOrders,
            'javascript'=>'<script src="'. App::config('url'). 'public/js/custom/AdminFinalizedOrders.js"></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>'
        ]);
        return;
    }

    public function searchfinalized($search){
        header('Content-type: application/json');
        echo json_encode(Order::searchFinalized($search));
    }

    public function getFinalizedDetails($id)
    {
        $data = (array)Order::readOrderDetails($id);
        echo json_encode($data);
    }
}