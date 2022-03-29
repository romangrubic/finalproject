<?php

class ShoppingorderController extends AuthorizedController
{
    private $viewDir = 'public' . DIRECTORY_SEPARATOR . 'shoppingorder' . DIRECTORY_SEPARATOR;
    private $cssDir =  'shoppingorder' . DIRECTORY_SEPARATOR;

    public function index()
    {
        $this->view->render($this->viewDir . 'index',[
            'css' => $this->cssDir . 'index.css',
            'shoppingorder'=>Shoppingorder::getShoppingorder($_SESSION['authorized']->id)
        ]);
    }

    public function addtocart($product)
    {
        $customerId = $_SESSION['authorized']->id;

        if(Shoppingorder::getShoppingorder($customerId) == null){
            Shoppingorder::create($customerId);
        }
        
        $shoppingorderId = Shoppingorder::getShoppingorder($customerId)->id;

        Shoppingorder::addtocart($product, $shoppingorderId);
    }
}