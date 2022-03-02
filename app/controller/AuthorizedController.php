<?php

abstract class AuthorizedController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['authorized'])){
            $this->view->render('login', [
                'email' => '',
                'message' => 'Prvo se prijavite.'
            ]);
            exit;
        }
    }
}