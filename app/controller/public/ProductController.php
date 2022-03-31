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

        if(!isset($_GET['manufacturer'])){
            $manufacturer = null;
        }else{
            $manufacturer = $_GET['manufacturer'];
        }

        $totalProducts = Product::totalProducts($search, $category, $manufacturer);
        $totalPages = ceil($totalProducts / App::config('ppp'));
        $products = Product::read($category, $search, $page, $manufacturer);
        
        $manufacturers = Manufacturer::read($category);
        $category = Category::readOne($category);

        if($page > $totalPages){
            $page = $totalPages;
        }

        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'products' => $products,
            'totalProducts' => $totalProducts,
            'manufacturers'=>$manufacturers,
            'manufacturer'=>Manufacturer::readOne($manufacturer),
            'category'=>$category,
            'email'=>$this->email,
            'message'=>$this->message,
            'page'=>$page,
            'totalPages'=>$totalPages,
            'search'=>$search
        ]);
    }

    public function searchproduct($search){
        header('Content-type: application/json');
        echo json_encode(Product::searchProduct($search));
    }
}