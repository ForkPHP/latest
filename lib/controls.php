<?php

class DOMObject
{
	private $class;
	function __construct()
	{

	}
	/*
	1) pass two or three parameters : text , url and parameters 
	2) url can be : text or MVC object 

	*/
	function link($text,$url,$params=array())
	{

		if(!is_string($url))
		{
			if(get_class($url)=="MVC")
			{
				$url="/".$GLOBALS['host']."/".$url->module."/".$url->controller."/".$url->action;

			}
		}
		if(count($params)>0)
		{
			$url.="?";
			foreach ($params as $key => $value) {
				$url .=$key."=".$value."&";
			}
			$url=substr($url, 0,strlen($url)-1);
			
		}
		//echo $url;
		echo '<a href='.$url.'>'.$text.'</a>';
	}
	function table($data,$class="")
	{
		if(count($data)>0)
		{
			$string="<table class='".$class."'>\n";
			$keys = array_keys($data[0]);
			$string .="<tr>\n";
			foreach ($keys as $key) {
				$string .="<th> $key </th>\n";
			}
			$string .="</tr>\n";
			foreach ($data as $row) {
				$string .="<tr>\n";
				foreach ($row as $key => $value) {
					$string .= "<td> $value </td>\n";;
				}
				$string .="</tr>\n";
			}
		}
		echo $string;
	}
	

}
/*
1) Initializing a new object of MVC class returns the current MVC object if no array is passed to it , 
2) array may or may not carry module , controller or action , 
3) by default : module is current module, controller is current controller , : action is index
*/
class MVC
{
	public $module;
	public $controller;
	public $action;
	function __construct($mvc=array())
	{
		$this->module = isset($mvc['module'])?$mvc['module']:$GLOBALS['forkobj']['module'];
		$this->controller = isset($mvc['controller'])?$mvc['controller']:$GLOBALS['forkobj']['controller'];
		$this->action = isset($mvc['action'])?$mvc['action']:"index";
	}
}



?>