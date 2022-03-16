<?php

class DashboardController extends AuthorizedController
{
    private $viewDir = 'public' . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR;

    public function index()
    {
        $this->view->render($this->viewDir . 'dashboard',[
            'customer'=>Customer::readOne($_SESSION['authorized']->id),
            'orders'=>Dashboard::getOrders($_SESSION['authorized']->id)
        ]);
    }
}