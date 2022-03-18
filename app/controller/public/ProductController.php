<?php

class ProductController extends LoginController
{
    private $viewDir = 'public' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
    private $cssDir =  'product' . DIRECTORY_SEPARATOR;

    public function index($category=null)
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

        $totalProducts = Product::totalProducts($search, $category);
        $totalPages = ceil($totalProducts / App::config('ppp'));
        $products = Product::read($category, $search, $page);
        
        if($page > $totalPages){
            $page = $totalPages;
        }

        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'products' => $products,
            'email'=>$this->email,
            'message'=>$this->message,
            'page'=>$page,
            'totalPages'=>$totalPages,
            'search'=>$search
        ]);
    }
}