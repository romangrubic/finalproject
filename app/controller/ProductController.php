<?php

class ProductController extends Controller
{
    private $viewDir = 'private' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
    private $cssDir =  'product' . DIRECTORY_SEPARATOR;

    private $product;
    private $message;

    public function __construct()
    {
        parent::__construct();
        $this->product = new stdClass();
        $this->product->id = 0;
        $this->product->name = '';
        $this->product->description = '';
        $this->product->categoryId = '';
        $this->product->categoryName = '';
        $this->product->price = '';
        $this->product->inventoryquantity = '';
        $this->product->productimage = '';

        $this->message = new stdClass();
        $this->message->name='';
        $this->message->description='';
        $this->message->price='';
        $this->message->inventoryquantity='';
        $this->message->productimage='';
    }

    public function index()
    {
        $products = Product::read();
        
        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'products' => $products
        ]);
    }

    public function details($id=0)
    {
        if($id==0){
            $this->view->render($this->viewDir . 'details', [
                'css' => $this->cssDir . 'details.css',
                'product'=>$this->product,
                'message'=>$this->message,
                'action'=>'Add new.'
            ]);
        }else{
            $this->product = Product::readOne($id);

            $this->view->render($this->viewDir . 'details', [
                'css' => $this->cssDir . 'details.css',
                'product'=>$this->product,
                'categories'=>Category::read(),
                'message'=>$this->message,
                'action'=>'Update.'
            ]);
        }
    }
}