<?php if(!defined('APP_HOST')) die('Access restricted.');
class webservice
{
	function __construct()
	{
		$host=$GLOBALS['host'];
		$config = new appConfig("application",$host);
		$iniData = $config->get();

		$plugins = $iniData["plugins"];
		$module=$plugins['webservice.module'];
		$path = $_SERVER['DOCUMENT_ROOT']."/".$host."/modules/".$module."/controllers/";
		foreach(scandir($path) as $class)
		{
			if ('.' === $class) continue;
		    if ('..' === $class) continue;
		    include_once $path.$class;
		}
	}
	public function getClasses()
	{
		$host=$GLOBALS['host'];
		$config = new appConfig("application",$host);
		$iniData = $config->get();
		$iniData = $config->get();
		$plugins = $iniData["plugins"];
		$module=$plugins['webservice.module'];
		$classes= array();
		foreach(get_declared_classes() as $class)
		{
			if(preg_match("/".$module.".*Controller/", $class))
				$classes[] = $class;
		}
		return $classes;
	}
}