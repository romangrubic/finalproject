<?php

class ShoppingorderController extends AuthorizedController
{
    private $viewDir = 'public' . DIRECTORY_SEPARATOR . 'shoppingorder' . DIRECTORY_SEPARATOR;
    private $cssDir =  'shoppingorder' . DIRECTORY_SEPARATOR;

    public function __construct()
    {
        parent::__construct();
        $this->nf = new \NumberFormatter("hr-HR", \NumberFormatter::DECIMAL);
        $this->nf->setPattern('#,##0.00');
    }

    public function index()
    {
        $shoppingorder=Shoppingorder::getShoppingorderCart($_SESSION['authorized']->id);
        foreach($shoppingorder as $product){
            $product->priceFormatted=$this->nf->format($product->price);
        }

        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'shoppingorder' =>$shoppingorder,
            'javascript'=>'<script src="'. App::config('url'). 'public/js/custom/removeFromCart.js"></script> '
        ]);
    }

    public function addtocart($productId, $quantity=1)
    {
        $customerId = $_SESSION['authorized']->id;
        if (Shoppingorder::getShoppingorder($customerId) == null) {
            Shoppingorder::create($customerId);
        }
        $shoppingorderId = Shoppingorder::getShoppingorder($customerId)->id;



        echo Shoppingorder::addtocart($productId, $shoppingorderId, $quantity) ? 'OK' : 'Error';
    }

    public function removefromcart($productId)
    {
        $customerId = $_SESSION['authorized']->id;
        $shoppingorderId = Shoppingorder::getShoppingorder($customerId)->id;

        echo Shoppingorder::removefromcart($productId, $shoppingorderId) ? 'OK' : 'Error';

    }

    public function numberofuniqueproducts()
    {
        echo Shoppingorder::numberOfUniqueProducts($_SESSION['authorized']->id);
    }
}
