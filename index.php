<?php 

$directorys = array(
            'modules/models/',
            'lib/',
            'lib/Entities/'
);
foreach ($directorys as $directory)
{
  foreach(glob($directory . "*.php") as $class)
 {
     include_once $class;
 }
}
$url=$_SERVER['REQUEST_URI'];
$configObj = new pathProvider();
$host="forkPHP";
$path=$configObj->getPath($url,$host);
print_r($path);			
/*$ini_array = parse_ini_file("App/application.ini");
print_r($ini_array);*/

?>