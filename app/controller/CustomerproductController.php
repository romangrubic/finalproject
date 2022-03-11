<?php

class CustomerproductController extends Controller
{
    private $viewDir = 'customer' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
    private $cssDir =  'product' . DIRECTORY_SEPARATOR;

    public function index($category=null)
    {
        $products = CustomerProduct::read($category);
        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'products' => $products
        ]);
    }
}