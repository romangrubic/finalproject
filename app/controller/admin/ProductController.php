<?php

class ProductController extends Controller
{
    private $viewDir = 'admin' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
    private $cssDir =  'admin' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;

    private $product;
    private $nf;
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
        $this->product->price = 1.00;
        $this->product->inventoryquantity = '';
        $this->product->dateadded = '';
        $this->product->lastUpdated = '';
        $this->product->imageurl = '';

        $this->nf = new \NumberFormatter("hr-HR", \NumberFormatter::DECIMAL);
        $this->nf->setPattern('#,##0.00');

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

        $totalProducts = Product::totalProducts($search);
        $totalPages = ceil($totalProducts / App::config('ppp'));

        if($page > $totalPages){
            $page = $totalPages;
        }
        $products = Product::read($search, $page);
        
        foreach($products as $product){
            $product->price=$this->nf->format($product->price);
        }

        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'products' => $products,
            'totalProducts' => $totalProducts,
            'page'=>$page,
            'totalPages'=>$totalPages,
            'search'=>$search
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

            if($this->product->price==0){
                $this->product->price = '';
            }else{
                $this->product->price = $this->nf->format($this->product->price);
            }

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
                $this->validateManufacturer()  &&
                $this->validatePrice() &&
                $this->validateInventoryQuantity()){
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
                $this->validateManufacturer() &&
                $this->validatePrice() &&
                $this->validateInventoryQuantity()){
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

    private function validatePrice()
    {
        if($this->product->price == ''){
            $this->message->price = 'Cijena ne smije biti 0.';
            return false;
        }
        if(strlen(trim($this->product->price)) > 0){
            $this->product->price = str_replace('.','',$this->product->price);
            $this->product->price = (float)str_replace(',','.',$this->product->price);
            
            if($this->product->price <= 0){
                $this->message->price = 'Ako unosite cijenu mora biti decimalni broj veci od 0';
                $this->product->price = 1.00;
            return false;
            }
        }
        return true;
    }

    private function validateInventoryQuantity()
    {
        if($this->product->inventoryquantity<0){
            $this->message->inventoryquantity='Stanje ne smije biti manje od 0.';
            return false;
        }
        return true;
    }
}