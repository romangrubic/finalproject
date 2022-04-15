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
        $averageFinishedTotal=$this->nf2->format(Admin::sumFinishedTotal()/Admin::closedOrders());
        $averageActiveTotal=$this->nf2->format(Admin::sumActiveTotal()/Admin::activeOrders());
        $totalProducts=$this->nf1->format(Admin::totalProducts());
        $activeProducts=$this->nf1->format(Admin::activeProducts());
        $categories=Admin::byCategory();
        $manufacturers=Admin::byManufacturer();
        $city=Admin::byCity();
        $mostSold=Admin::mostSold();
        $lessSold=Admin::lessSold();

        $this->view->render('admin/index',[
            'css'=>$this->cssDir.'index.css',
            'name'=> $name,
            'totalCustomers'=> $totalCustomers,
            'last2weeks'=>$last2weeks,
            'activeCustomers'=>$activeCustomers,
            'activeOrders'=>$activeOrders,
            'closedOrders'=>$closedOrders,
            'averageFinishedTotal'=>$averageFinishedTotal,
            'averageActiveTotal'=>$averageActiveTotal,
            'totalProducts'=>$totalProducts,
            'activeProducts'=>$activeProducts,
            'categories'=>$categories,
            'manufacturers'=>$manufacturers,
            'city'=>$city,
            'mostSold'=>$mostSold,
            'lessSold'=>$lessSold,
        ]);
        return;
    }
}