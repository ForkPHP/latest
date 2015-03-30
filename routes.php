<?php if(!defined('APP_HOST')) die('Access restricted.');

/**
 * This file is used to define magic paths in your Application
 * Define a key => value pair to map a path to different module, controller & action
 *
 * @filename routes.php
 * @copyright Fork PHP
 * @license
 * @access public
 * @author Eshant Sahu
 * @see http://forkphp.com/document
 */

$router = array(); 

 /* Add your paths here  */

//$router['login.html'] = "admin/login/index";
//$router['/module'] = "/admin/home/modulemanager";
//$route["admin/{:action}/{id:num}"] = "default/admin/{:action}/id/{id:num}";

$router["/home"] = "/default/index/index";
$router["/document"] = "/default/index/class";
$router["/contact"] = "/default/index/contact";
$router["/tutorials"] = "/default/index/tutorials";
$router["/download"] = "/default/index/download";
$router["/admin/db/{dbname:str}"] = "/admin/entity/dbmanager?db={dbname:str}";
//$router["admin/entity/dbmanager?db={db:str}&table={table:str}"] = "admin/db/{db:str}/table/{table:str}";
$router["/admin/db/{db:str}/table/{table:str}"] = "/admin/entity/dbmanager?db={db:str}&table={table:str}";
$router["/document/class/{classname:str}"] = "/default/index/class/classname/{classname:str}";
$router["/appi/{route:str}"] = "/api/index";
/**
 * This array will be returned to the router Object, to map magic paths.
 * you will be needed to enable it in ypur application.config before using them.
 *
 */
return $router;