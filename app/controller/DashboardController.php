<?php

class DashboardController extends AuthorizedController
{
    private $viewDir = 'private' . DIRECTORY_SEPARATOR;

    public function index()
    {
        $this->view->render($this->viewDir . 'dashboard');
    }
}