<?php
namespace Libs;

/**
* 视图
*/
class View 
{
	private $tpl; 

	public function __construct()
	{
		include_once 'smarty/Smarty.class.php';

		$this->tpl = new \Smarty();
		$this->tpl -> template_dir = ROOT_PAHT.'View';
		$this->tpl -> compile_dir   = ROOT_PAHT.'Data/Compile' ;
	}	

	public function __call($method,$arg)
	{
		call_user_func_array(array($this->tpl , $method) , $arg);
	}
}
