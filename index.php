<?php 

/**
 * 
 * @filename index.php
 * @copyright Fork PHP
 * @author Eshant Sahu
 * @see http://forkphp.com/document
 */

 // important to specify
$host = strtolower(rtrim(ltrim(str_replace("index.php",'',$_SERVER['PHP_SELF']),'/'),'/'));

	define('APP_HOST', $host);
if(!empty($host))
{
	$GLOBALS['host']="/".$host;

}

include_once "autoloader.php";


$url=$_SERVER['REQUEST_URI'];

/**
* 
* Generates route for current path 
* Path is resolved to appropriate module, controller and action.
*
*/
$configObj = new requestObject();

$path=$configObj->getPath($url,$GLOBALS['host']);
