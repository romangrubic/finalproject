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
        $this->category->name = '';
        $this->category->description = '';

        $this->message = new stdClass();
        $this->message->name='';
        $this->message->description='';
    }

    public function index()
    {
        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'categories' => Category::read()
        ]);
    }

    public function new()
    {
        $this->view->render($this->viewDir . 'new', [
            'css' => $this->cssDir . 'new.css',
            'message' => $this->message,
            'category' => $this->category
        ]);
    }

    public function addNew()
    {
        $this->category = (object)$_POST;

        if($this->validationName() && $this->validationDescription()){
            Category::insert($_POST);
            header('Location:'.App::config('url').'category/index');
        }else{
            $this->view->render($this->viewDir . 'new', [
                'css' => $this->cssDir . 'new.css',
                'message' => $this->message,
                'category' => $this->category
            ]);
        }
    }

    public function update($id)
    {
        $this->category = Category::readOne($id);

        $this->view->render($this->viewDir . 'update', [
            'css' => $this->cssDir . 'update.css',
            'message' => $this->message,
            'category' => $this->category
        ]);
    }

    public function updateNew()
    {
        $this->category = (object)$_POST;

        if($this->validationName() && $this->validationDescription()){
            Category::update($_POST);
            header('Location:'.App::config('url').'category/index');
        }else{
            $this->view->render($this->viewDir . 'update', [
                'css' => $this->cssDir . 'update.css',
                'message' => $this->message,
                'category' => $this->category
            ]);
        }
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
        if(strlen(trim($this->category->description)) > 255){
            $this->message->description = 'Opis moze imati najvise 255 znakova.';
            return false;
        }
        return true;
    }
}