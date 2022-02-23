<?php

class View
{
    private $template;

    public function __construct($template='template')
    {
        $this->template=$template;
    }

    // We are taking two phtml pages and combining them into one that will be shown to user
    public function render($phtmlpage, $parameters=[])
    {
        ob_start();
        extract($parameters);
        include_once BP_APP . 'view' . DIRECTORY_SEPARATOR . $phtmlpage . '.phtml';
        $content=ob_get_clean();
        include_once BP_APP . 'view' . DIRECTORY_SEPARATOR . $this->template . '.phtml';
        return $content;
    }
}