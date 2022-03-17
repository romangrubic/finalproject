<?php

class ProductController extends LoginController
{
    private $viewDir = 'public' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
    private $cssDir =  'product' . DIRECTORY_SEPARATOR;

    public function index($category=null)
    {
        $products = Product::read($category);
        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'products' => $products,
            'email'=>$this->email,
            'message'=>$this->message
        ]);
    }
}