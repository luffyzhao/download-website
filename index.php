<?php
header("Content-type:text/html;charset=utf-8");

define('ROOT_PAHT' , __DIR__."/");

$requestUri = $_SERVER['REQUEST_URI'];

if(($offset = strrpos($requestUri , '?')) !== false){
	$requestUri = substr($requestUri , 0 , $offset);
}

$requestUriArray = explode('/' , $requestUri);



$controller = (isset($requestUriArray[1]) && ($requestUriArray[1] != '')) ? $requestUriArray[1] : 'Index';
$method = (isset($requestUriArray[2]) && ($requestUriArray[2] != '')) ? $requestUriArray[2] : 'index';

spl_autoload_register('luffyAutoload');

function luffyAutoload($class)
{
	$class = str_replace('\\' , '/' , $class) . '.php';
	//prs-4 协议 这个地方不报错
	if(file_exists($class)){
		include_once $class;
	}
}


$controllerClassName = "\\Controllers\\".ucfirst($controller).'Controller';

try {
	$controllerClass = new $controllerClassName();
	if(method_exists($controllerClass , $method)){
		$controllerClass->$method();
	}else{
		throw new Exception("Error Processing Request", 1);		
	}
} catch (Exception $e) {
	echo $e->getMessage();
}
