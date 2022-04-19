<?php

class ShoppingorderController extends AuthorizedController
{
    private $viewDir = 'public' . DIRECTORY_SEPARATOR . 'shoppingorder' . DIRECTORY_SEPARATOR;
    private $cssDir =  'shoppingorder' . DIRECTORY_SEPARATOR;

    public function __construct()
    {
        parent::__construct();
        $this->nf = new \NumberFormatter("hr-HR", \NumberFormatter::DECIMAL);
        $this->nf->setPattern('#,##0.00');

        $this->customer = new stdClass();
        $this->customer->id = null;
        $this->customer->firstname = '';
        $this->customer->lastname = '';
        $this->customer->email = '';
        $this->customer->phonenumber = '';
        $this->customer->street = '';
        $this->customer->city = '';
        $this->customer->postalnumber = '';
        $this->customer->cardnumber = '';
        $this->customer->cvv = '';

        $this->message = new stdClass();
        $this->message->firstname = '';
        $this->message->lastname = '';
        $this->message->email = '';
        $this->message->phonenumber = '';
        $this->message->street = '';
        $this->message->city = '';
        $this->message->postalnumber = '';
        $this->message->email = '';
        $this->message->cardnumber = '';
        $this->message->cvv = '';
    }

    public function index()
    {
        $shoppingorder = Shoppingorder::getShoppingorderCart($_SESSION['authorized']->id);
        foreach ($shoppingorder as $product) {
            $product->priceFormatted = $this->nf->format($product->price);
        }

        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'shoppingorder' => $shoppingorder,
            'javascript' => '<script src="' . App::config('url') . 'public/js/custom/removeFromCart.js"></script> '
        ]);
    }

    public function finalized()
    {
        $shoppingorder = Shoppingorder::getShoppingorderCart($_SESSION['authorized']->id);
        foreach ($shoppingorder as $product) {
            $product->priceFormatted = $this->nf->format($product->price);
        }
        $this->customer = Customer::readOne($_SESSION['authorized']->id);

        $this->view->render($this->viewDir . 'finalized', [
            'css' => $this->cssDir . 'index.css',
            'shoppingorder' => $shoppingorder,
            'customer' => $this->customer,
            'message' => $this->message,
            'javascript' => '<script src="' . App::config('url') . 'public/js/custom/removeFromCart.js"></script> '
        ]);
    }

    public function action()
    {
        $this->customer = (object)$_POST;

        if (
            $this->validateFirstname() &&
            $this->validateLastname() &&
            $this->validateEmail() &&
            $this->validatePhonenumber() &&
            $this->validateStreet() &&
            $this->validateCity() &&
            $this->validatePostalnumber() &&
            $this->validateCardnumber() &&
            $this->validateCvv()
        ) {
            Shoppingorder::finishShoppingorder($this->customer->id);
            header('location: ' . App::config('url') . 'dashboard/index');
        } else {
            $shoppingorder = Shoppingorder::getShoppingorderCart($_SESSION['authorized']->id);
            foreach ($shoppingorder as $product) {
                $product->priceFormatted = $this->nf->format($product->price);
            }

            $this->view->render($this->viewDir . 'finalized', [
                'css' => $this->cssDir . 'index.css',
                'shoppingorder' => $shoppingorder,
                'customer' => (object)$_POST,
                'message' => $this->message,
                'javascript' => '<script src="' . App::config('url') . 'public/js/custom/removeFromCart.js"></script> '
            ]);
            return;
        }
    }


    public function addtocart($productId, $quantity = 1)
    {
        $customerId = $_SESSION['authorized']->id;
        if (Shoppingorder::getShoppingorder($customerId) == null) {
            Shoppingorder::create($customerId);
        }
        $shoppingorderId = Shoppingorder::getShoppingorder($customerId)->id;



        echo Shoppingorder::addtocart($productId, $shoppingorderId, $quantity) ? 'OK' : 'Error';
    }

    public function removefromcart($productId)
    {
        $customerId = $_SESSION['authorized']->id;
        $shoppingorderId = Shoppingorder::getShoppingorder($customerId)->id;

        echo Shoppingorder::removefromcart($productId, $shoppingorderId) ? 'OK' : 'Error';
    }

    public function numberofuniqueproducts()
    {
        echo Shoppingorder::numberOfUniqueProducts($_SESSION['authorized']->id);
    }

    // Validating input methods
    private function validateFirstname()
    {
        if (strlen(trim($this->customer->firstname)) === 0) {
            $this->message->firstname = 'Ime je obavezno.';
            return false;
        }
        if (strlen(trim($this->customer->firstname)) > 50) {
            $this->message->firstname = 'Ime ne smije imati vise od 50 znakova.';
            return false;
        }
        return true;
    }

    private function validateLastname()
    {
        if (strlen(trim($this->customer->lastname)) === 0) {
            $this->message->lastname = 'Prezime je obavezno.';
            return false;
        }
        if (strlen(trim($this->customer->lastname)) > 50) {
            $this->message->lastname = 'Prezime ne smije imati vise od 50 znakova.';
            return false;
        }
        return true;
    }

    private function validateEmail()
    {
        if (filter_var($this->customer->email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            $this->message->email = 'Email nije korektnog formata.';
            return false;
        };
    }

    private function validatePhonenumber()
    {
        if (strlen(trim($this->customer->phonenumber)) < 7) {
            $this->message->phonenumber = 'Telefonski broj ne moze imati manje od 7 znakova.';
            return false;
        }
        if (strlen(trim($this->customer->phonenumber)) > 15) {
            $this->message->phonenumber = 'Telefonski broj ne moze imati vise od 15 znakova.';
            return false;
        }
        return true;
    }

    private function validateStreet()
    {
        if (strlen(trim($this->customer->street)) === 0) {
            $this->message->street = 'Ulica i kućni broj su obavezni.';
            return false;
        }
        if (strlen(trim($this->customer->street)) > 255) {
            $this->message->street = 'Ulica i kucni broj ne mogu imati vise od 255 znakova.';
            return false;
        }
        return true;
    }

    private function validateCity()
    {
        if (strlen(trim($this->customer->city)) > 50) {
            $this->message->city = 'Grad  ne moze imati vise od 50 znakova.';
            return false;
        }
        return true;
    }

    private function validatePostalnumber()
    {
        if (strlen(trim($this->customer->postalnumber)) > 5) {
            $this->message->postalnumber = 'Postanski broj ima 5 znakova.';
            return false;
        }
        return true;
    }

    private function validateCardnumber()
    {
        if (strlen(trim($this->customer->cardnumber)) < 16) {
            $this->message->cardnumber = 'Broj kartice ima najmanje 16 znakova.';
            return false;
        }
        return true;
    }

    private function validateCvv()
    {
        if (strlen(trim($this->customer->cvv)) != 3) {
            $this->message->cvv = 'CVV broj ima 5 znakova.';
            return false;
        }
        return true;
    }
}
