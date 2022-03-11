<?php

class ProductController extends Controller
{
    private $viewDir = 'private' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
    private $cssDir =  'product' . DIRECTORY_SEPARATOR;

    public function index($category=null)
    {
        $products = Product::read();
        
        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'products' => $products
        ]);
    }
}