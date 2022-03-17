<?php

class IndexController extends LoginController
{
    public function index()
    {
        if (isset($_SESSION['authorized']->user_role) && ($_SESSION['authorized']->user_role == 'admin' || $_SESSION['authorized']->user_role == 'oper')) {
            $this->view->render('admin/index');
            return;
        }
        $this->view->render('index',[
            'email'=>$this->email,
            'message'=>$this->message
        ]);
    }
}
