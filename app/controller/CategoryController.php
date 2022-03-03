<?php

class CategoryController extends AuthorizedController
{
    private $viewDir = 'private' . DIRECTORY_SEPARATOR . 'category' . DIRECTORY_SEPARATOR;
    private $cssDir =  'category' . DIRECTORY_SEPARATOR;

    public function index()
    {
        $this->view->render($this->viewDir . 'index', [
            'css' => $this->cssDir . 'index.css',
            'categories' => Category::read()
        ]);
    }
}