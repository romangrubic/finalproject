<?php

class DashboardController extends AuthorizedController
{
    private $viewDir = 'private' . DIRECTORY_SEPARATOR;

    public function index()
    {
        $this->view->render($this->viewDir . 'dashboard',[
            'customer'=>$_SESSION['authorized'],
            'orders'=>Dashboard::getOrders($_SESSION['authorized']->id)
        ]);
    }
}