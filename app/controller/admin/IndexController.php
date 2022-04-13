<?php

class IndexController extends AuthorizedController
{
    private $cssDir =  'admin' . DIRECTORY_SEPARATOR;
    private $nf1;
    private $nf2;

    public function __construct()
    {
        parent::__construct();
        $this->nf1 = new \NumberFormatter("hr-HR", \NumberFormatter::GROUPING_USED);
        $this->nf2 = new \NumberFormatter("hr-HR", \NumberFormatter::DECIMAL);
        $this->nf2->setPattern('#,##0.00');
    }

    public function index()
    {
        $name=$_SESSION['authorized']->firstname;
        $totalCustomers=$this->nf1->format(Admin::totalCustomers());
        $last2weeks=$this->nf1->format(Admin::last2weeks());
        $activeCustomers=$this->nf1->format(Admin::activeCustomers());
        $activeOrders=$this->nf1->format(Admin::activeOrders());
        $closedOrders=$this->nf1->format(Admin::closedOrders());
        $averageTotal=$this->nf2->format(Admin::sumTotal()/Admin::closedOrders());
        $totalProducts=$this->nf1->format(Admin::totalProducts());
        $activeProducts=$this->nf1->format(Admin::activeProducts());

        $this->view->render('admin/index',[
            'css'=>$this->cssDir.'index.css',
            'name'=> $name,
            'totalCustomers'=> $totalCustomers,
            'last2weeks'=>$last2weeks,
            'activeCustomers'=>$activeCustomers,
            'activeOrders'=>$activeOrders,
            'closedOrders'=>$closedOrders,
            'averageTotal'=>$averageTotal,
            'totalProducts'=>$totalProducts,
            'activeProducts'=>$activeProducts
        ]);
        return;
    }
}