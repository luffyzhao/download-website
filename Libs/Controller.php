<?php

namespace Libs;
/**
* 
*/
class Controller 
{
	/**
	 * 视图
	 * @var null
	 */
	protected $view = null;

	function __construct()
	{
		$this->view = new View();
	}


}