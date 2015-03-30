<?php if(!defined('APP_HOST')) die('Access restricted.');

/**
* This is Autoloader file 
* This file includes all important classes.
* Autoloader array
* Add path here to include your additional files
*
*/

$directorys = array(
            'modules/models/',
            'lib/',
            'lib/Entities/',
            'lib/plugins/',
            'lib/helpers/'
); 

foreach ($directorys as $directory)
{
   foreach(glob($directory . "*.php") as $class)
   {
       include_once $class;
   }
}
