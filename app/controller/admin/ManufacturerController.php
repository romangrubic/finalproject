<?php

class ManufacturerController extends AuthorizedController
{
    private $viewDir = 'admin' . DIRECTORY_SEPARATOR . 'manufacturer' . DIRECTORY_SEPARATOR;
    private $cssDir =  'admin' . DIRECTORY_SEPARATOR . 'manufacturer' . DIRECTORY_SEPARATOR;

    private $manufacturer;
    private $message;

    public function __construct()
    {
        parent::__construct();
        $this->manufacturer = new stdClass();
        $this->manufacturer->id = 0;
        $this->manufacturer->name = '';
        $this->manufacturer->description = '';
        $this->manufacturer->lastUpdated = '';
        $this->manufacturer->hasProducts = 0;

        $this->message = new stdClass();
        $this->message->name='';
        $this->message->description='';
    }

    public function index()
    {
        $manufacturers = Manufacturer::read();
        
        foreach($manufacturers as $manufacturer){
            if($manufacturer->lastUpdated !=null){
                // $manufacturer->lastUpdated= date("F jS, Y, H:i:s", strtotime($manufacturer->lastUpdated));
                $manufacturer->lastUpdated = strftime("%e. %B %Y u %H:%M", strtotime($manufacturer->lastUpdated));

            }
        }

        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'manufacturers' => $manufacturers,
        ]);
    }

    public function details($id=0)
    {
        if($id == 0){
            $this->view->render($this->viewDir . 'details',[
                'css' => $this->cssDir . 'index.css',
                'manufacturer'=>$this->manufacturer,
                'message'=>$this->message,
                'action'=>'Dodaj novog proizvođača'
            ]);
        }else{
            $this->view->render($this->viewDir . 'details',[
                'css' => $this->cssDir . 'index.css',
                'manufacturer'=>Manufacturer::readOne($id),
                'message'=>$this->message,
                'action'=>'Spremi promijene.'
            ]);
        }
    }

    public function action()
    {
        $this->manufacturer = (object)$_POST;

        if($this->manufacturer->id ==0){
            if($this->validationName() &&
                $this->validationDescription()){
                    Manufacturer::insert((array)$this->manufacturer);
            }else{
                $this->view->render($this->viewDir . 'details',[
                    'css' => $this->cssDir . 'index.css',
                    'manufacturer'=>$this->manufacturer,
                    'message'=>$this->message,
                    'action'=>'Spremi promijene.'
                ]);
                return;
            }
        }else{;
            if($this->validationName() &&
                $this->validationDescription()){
                    Manufacturer::update((array)$this->manufacturer);
            }else{
                $this->view->render($this->viewDir . 'details',[
                    'css' => $this->cssDir . 'index.css',
                    'manufacturer'=>(object)$_POST,
                    'message'=>$this->message,
                    'action'=>'Spremi promijene.'
                ]);
                return;
            }
        }
        header('location:' . App::config('url').'manufacturer/index');
    }

    public function delete($id)
    {
        Manufacturer::delete($id);
        header('Location:'.App::config('url').'manufacturer/index');
    }

    // Validation functions
    private function validationName()
    {
        if(strlen(trim($this->manufacturer->name)) === 0){
            $this->message->name = 'Naziv je obavezan.';
            return false;
        }
        if(strlen(trim($this->manufacturer->name)) > 50){
            $this->message->name = 'Naziv moze imati do 50 znakova.';
            return false;
        }
        return true;
    }

    private function validationDescription()
    {
        if(strlen(trim($this->manufacturer->description)) < 5){
            $this->message->description = 'Kratak opis je dovoljan.';
            return false;
        }
        if(strlen(trim($this->manufacturer->description)) > 255){
            $this->message->description = 'Opis moze imati najvise 255 znakova.';
            return false;
        }
        return true;
    }
}