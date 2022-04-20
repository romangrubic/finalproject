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

        if(!isset($_GET['page'])){
            $page = 1;
        }else{
            $page = (int)$_GET['page'];
        }
        if($page == 0){
            $page = 1;
        }

        $totalOrders = Order::totalActiveOrder($search);
        $totalPages = ceil($totalOrders / App::config('ppp'));

        if($page > $totalPages){
            $page = $totalPages;
        }

        $orders = Order::readActiveOrderCustomers($search, $page);
        foreach ($orders as $order) {
            $order->dateadded = strftime("%e. %B %Y u %H:%M", strtotime($order->dateadded));
        }
        $this->view->render($this->viewDir . 'orders',[
            'css'=>$this->cssDir . 'index.css',
            'type'=>'active',
            'search'=>$search,
            'orders'=>$orders,
            'totalOrders'=>$totalOrders,
            'page'=>$page,
            'totalPages'=>$totalPages,
            'javascript'=>'<script src="'. App::config('url'). 'public/js/custom/AdminActiveOrders.js"></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>'
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

        if(!isset($_GET['page'])){
            $page = 1;
        }else{
            $page = (int)$_GET['page'];
        }
        if($page == 0){
            $page = 1;
        }

        $totalFinalizedOrders = Order::totalFinalizedOrder($search);
        $totalPages = ceil($totalFinalizedOrders / App::config('ppp'));

        if($page > $totalPages){
            $page = $totalPages;
        }
        // $totalOrders = Order::totalFinalizedOrder($search);
        $orders = Order::readFinalizedOrderCustomers($search, $page);
        foreach ($orders as $order) {
            if ($order->dateFinished != null) {
                $order->dateFinished = strftime("%e. %B %Y u %H:%M", strtotime($order->dateFinished));
            }
        }
        $this->view->render($this->viewDir . 'orders',[
            'css'=>$this->cssDir . 'index.css',
            'type'=>'finalized',
            'search'=>$search,
            'orders'=>$orders,
            'totalOrders'=>$totalOrders,
            'page'=>$page,
            'totalPages'=>$totalPages,
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