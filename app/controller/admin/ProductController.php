<?php

class ProductController extends Controller
{
    private $viewDir = 'admin' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
    private $cssDir =  'admin' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;

    private $product;
    private $message;

    public function __construct()
    {
        parent::__construct();
        $this->product = new stdClass();
        $this->product->id = 0;
        $this->product->name = '';
        $this->product->description = '';
        $this->product->category = '';
        $this->product->categoryName = '';        
        $this->product->manufacturer = '';
        $this->product->manufacturerName = '';
        $this->product->price = '';
        $this->product->inventoryquantity = '';
        $this->product->dateadded = '';
        $this->product->lastUpdated = '';
        $this->product->imageurl = '';

        $this->message = new stdClass();
        $this->message->name='';
        $this->message->description='';
        $this->message->category='';
        $this->message->manufacturer='';
        $this->message->price='';
        $this->message->inventoryquantity='';
        $this->message->imageurl='';
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
                'css' => $this->cssDir . 'index.css',
                'product'=>$this->product,
                'message'=>$this->message,
                'categories'=>Category::read(),
                'manufacturers'=>Manufacturer::read(),
                'action'=>'Dodaj novi proizvod.'
            ]);
        }else{
            $this->product = Product::readOne($id);

            $this->view->render($this->viewDir . 'details', [
                'css' => $this->cssDir . 'index.css',
                'product'=>$this->product,
                'categories'=>Category::read(),
                'manufacturers'=>Manufacturer::read(),
                'message'=>$this->message,
                'action'=>'Spremi promijene.'
            ]);
        }
    }

    public function action()
    {
        $this->product = (object)$_POST;

        if($this->product->id == 0){
            if($this->validationName() &&
                $this->validationDescription() &&
                $this->validateCategory() &&
                $this->validateManufacturer()){
                Product::create((array)$this->product);
            }else{
                $this->view->render($this->viewDir . 'details',[
                    'css' => $this->cssDir . 'index.css',
                    'product'=>$this->product,
                    'message'=>$this->message,
                    'categories'=>Category::read(),
                    'manufacturers'=>Manufacturer::read(),
                    'action'=>'Dodaj novi proizvod.'
                ]);
                return;
            }
        }else{
            if($this->validationName() &&
                $this->validationDescription() &&
                $this->validateCategory() &&
                $this->validateManufacturer()){
                Product::update((array)$this->product);

            }else{
                $this->view->render($this->viewDir . 'details',[
                    'css' => $this->cssDir . 'index.css',
                    'product'=>(object)$_POST,
                    'message'=>$this->message,
                    'categories'=>Category::read(),
                    'manufacturers'=>Manufacturer::read(),
                    'action'=>'Dodaj novi proizvod.'
                ]);
                return;
            }
        }
        header('location:' . App::config('url').'product/index');

    }

    public function delete($id)
    {
        Product::delete($id);
        header('location:' . App::config('url').'product/index');
    }

    // Validation functions
    private function validationName()
    {
        if(strlen(trim($this->product->name)) === 0){
            $this->message->name = 'Naziv je obavezan.';
            return false;
        }
        if(strlen(trim($this->product->name)) > 50){
            $this->message->name = 'Naziv moze imati do 50 znakova.';
            return false;
        }
        return true;
    }

    private function validationDescription()
    {
        if(strlen(trim($this->product->description)) < 5){
            $this->message->description = 'Kratak opis je dovoljan.';
            return false;
        }
        if(strlen(trim($this->product->description)) > 255){
            $this->message->description = 'Opis moze imati najvise 255 znakova.';
            return false;
        }
        return true;
    }

    private function validateCategory()
    {
        if($this->product->category==0){
            $this->message->category='Odaberite jednu od kategorija.';
            return false;
        }
        return true;
    }

    private function validateManufacturer()
    {
        if($this->product->manufacturer==0){
            $this->message->manufacturer='Odaberite jednog od proizvođača.';
            return false;
        }
        return true;
    }
}