<?php

class CategoryController extends AuthorizedController
{
    private $viewDir = 'admin' . DIRECTORY_SEPARATOR . 'category' . DIRECTORY_SEPARATOR;
    private $cssDir =  'admin' . DIRECTORY_SEPARATOR . 'category' . DIRECTORY_SEPARATOR;

    private $category;
    private $message;

    public function __construct()
    {
        parent::__construct();
        $this->category = new stdClass();
        $this->category->id = 0;
        $this->category->name = '';
        $this->category->description = '';
        $this->category->lastUpdated = '';

        $this->message = new stdClass();
        $this->message->name='';
        $this->message->description='';
    }

    public function index()
    {

        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'categories' => Category::read(),
            'totalCategories'=>Category::countCategories()
        ]);
    }

    public function details($id=0)
    {
        if($id == 0){
            $this->view->render($this->viewDir . 'details',[
                'css' => $this->cssDir . 'index.css',
                'category'=>$this->category,
                'message'=>$this->message,
                'action'=>'Dodaj novu kategoriju'
            ]);
        }else{
            $this->view->render($this->viewDir . 'details',[
                'css' => $this->cssDir . 'index.css',
                'category'=>Category::readOne($id),
                'message'=>$this->message,
                'action'=>'Update'
            ]);
        }
    }

    public function action()
    {
        $this->category = (object)$_POST;

        if($this->category->id ==0){
            if($this->validationName() &&
                $this->validationDescription()){
                Category::insert((array)$this->category);
            }else{
                $this->view->render($this->viewDir . 'details',[
                    'css' => $this->cssDir . 'index.css',
                    'category'=>$this->category,
                    'message'=>$this->message,
                    'action'=>'Spremi promijene'
                ]);
                return;
            }
        }else{;
            if($this->validationName() &&
                $this->validationDescription()){
                Category::update((array)$this->category);
            }else{
                $this->view->render($this->viewDir . 'details',[
                    'css' => $this->cssDir . 'index.css',
                    'category'=>(object)$_POST,
                    'message'=>$this->message,
                    'action'=>'Update'
                ]);
                return;
            }
        }
        header('location:' . App::config('url').'category/index');
    }

    public function delete($id)
    {
        Category::delete($id);
        header('Location:'.App::config('url').'category/index');
    }

    // Validation functions
    private function validationName()
    {
        if(strlen(trim($this->category->name)) === 0){
            $this->message->name = 'Naziv je obavezan.';
            return false;
        }
        if(strlen(trim($this->category->name)) > 50){
            $this->message->name = 'Naziv moze imati do 50 znakova.';
            return false;
        }
        return true;
    }

    private function validationDescription()
    {
        if(strlen(trim($this->category->description)) < 5){
            $this->message->description = 'Kratak opis je dovoljan.';
            return false;
        }
        if(strlen(trim($this->category->description)) > 255){
            $this->message->description = 'Opis moze imati najvise 255 znakova.';
            return false;
        }
        return true;
    }
}