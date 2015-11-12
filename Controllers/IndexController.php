<?php

namespace Controllers;

use \Libs\Controller;
use \Libs\Spider;

/**
*
*/
class IndexController extends Controller
{

	public function index()
	{
		$this->view->display('index.tpl');
	}

	public function post()
	{
        $urls = $_POST['urls'];
        if(empty($urls)){
            $this->view->display('index.tpl');
            die();
        }
        $urls = explode("\n" , $urls);
        foreach ($urls as $key => $value) {
            $url = trim($value);
            $spider = new Spider($url);
            $spider->init();
        }
	}
}
