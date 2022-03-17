<?php

abstract class AuthorizedController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['authorized'])){
            header('location: ' . App::config('url') . 'login/index');
            exit;
        }
    }
}