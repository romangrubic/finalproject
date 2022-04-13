<?php

class IndexController extends AuthorizedController
{
    // private $cssDir =  'index' . DIRECTORY_SEPARATOR;
    private $nf;

    public function __construct()
    {
        parent::__construct();
        $this->nf = new \NumberFormatter("hr-HR", \NumberFormatter::GROUPING_USED);
    }

    public function index()
    {
        $name=$_SESSION['authorized']->firstname;
        $totalCustomers=$this->nf->format(Admin::totalCustomers());
        $last2weeks=$this->nf->format(Admin::last2weeks());
        $activeOrders=$this->nf->format(Admin::activeOrders());
        $closedOrders=$this->nf->format(Admin::closedOrders());

        $this->view->render('admin/index',[
            'css'=>'admin' . DIRECTORY_SEPARATOR .'index.css',
            'name'=> $name,
            'totalCustomers'=> $totalCustomers,
            'last2weeks'=>$last2weeks,
            'activeOrders'=>$activeOrders,
            'closedOrders'=>$closedOrders
        ]);
        return;
    }
}