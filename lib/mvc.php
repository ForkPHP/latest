<?php if(!defined('APP_HOST')) die('Access restricted.');

class Layout
{
 private $controllerObj;
 private $layoutPath;
 private $lcontroller;
 private $layoutName;
 private $layout;
 private $meta;
 private $headers;
 private $view;
 private $head;
 private $js;
 private $css;
 private $title;
 private $_helper;
 public function __construct($cObj,$layoutName)
 {
 	$this->head=array();
 	$this->js=array();
 	$this->css=array();
 	$this->meta=array();
 	$this->headers=array();
 	$this->controllerObj=$cObj;
 	$this->layoutPath=$layoutName;
 	$this->layoutName=$cObj->layout;
 	$this->_helper = new DefaultHelper();
 	$host=$GLOBALS['host'];
 	$cpath=$_SERVER['DOCUMENT_ROOT'].$host."/layouts/controllers/".$cObj->layout."layoutController.php";
 	$this->title=$this->controllerObj->title;
 	if(file_exists($cpath))
 	{
 		include $cpath;
 		$layoutControllerName=$cObj->layout."layoutController";
 		if(class_exists($layoutControllerName))
 		{
 			$this->lcontroller=new $layoutControllerName();
 		}
 		else
 		{
 			trigger_error($cObj->layout."layoutController Class does't exists.");
 		}
 	}
 	else
 	{
 		//trigger_error($cObj->layout."layoutController is not defined.",E_USER_WARNING);
 	}
 	if($this->controllerObj->viewRequired) // update : view is not included by default , call view() to include
	{

		if(file_exists($_SERVER['DOCUMENT_ROOT'].$host."/modules/".$GLOBALS['forkobj']['module']."/views/".$GLOBALS['forkobj']['controller']."/".$GLOBALS['forkobj']['action'].".phtml"))
		{
			ob_start();
			$lay = include $this->layoutPath;
			
			$buffer =ob_get_clean();
			$html = "<!DOCTYPE html>\n<html>\n";
			$html .="<head>\n";
			$html .="<title>".$this->title."</title>\n";
			$html .=$this->getHeaders()."\n";
			$html .=$this->headFiles($this->css,$this->js)."\n";
			$html .="</head>\n<body>\n";
			echo trim($html);
			echo $buffer;
			echo "</body>\n</html>";
			
			
		}
		else
		{

    		trigger_error("MISSING_VIEW_FILE",E_USER_ERROR); // missing view// missing view
		}
	}
	else
	{
		ob_start();
			$lay = include $this->layoutPath;
			
			$buffer =ob_get_clean();
			$html = "<!DOCTYPE html>\n<html>\n";
			$html .="<head>\n";
			$html .="<title>".$this->title."</title>\n";
			$html .=$this->getHeaders()."\n";
			$html .=$this->headFiles($this->css,$this->js)."\n";
			$html .="</head>\n<body>\n";
			echo $html;
			echo $buffer;
			echo "</body>\n</html>";
	}
 	//print_r($this->css);
 }
public function partial($file)
{
	$host=$GLOBALS['host'];

	if(file_exists($_SERVER['DOCUMENT_ROOT'].$host."/modules/".$GLOBALS['forkobj']['module']."/views/".$GLOBALS['forkobj']['controller']."/".$file.".phtml"))
	{
		
		$path=$_SERVER['DOCUMENT_ROOT'].$host."/modules/".$GLOBALS['forkobj']['module']."/views/".$GLOBALS['forkobj']['controller']."/".$file.".phtml";
		//create view 
		//echo $path;
		$partialView = new View($this->controllerObj,$path,1);	    
		$this->css=array_merge($this->css,$partialView->getCssFiles());
		$this->js=array_merge($this->js,$partialView->getJsFiles());

	}
	else
	{
		$GLOBALS['tracK_info']['partial']=$file;
		$GLOBALS['tracK_info']['source']=$this->layoutName.".phtml layout";
		trigger_error("MISSING_PARTIAL_VIEW_FILE",E_USER_ERROR);
		// missing view
	}
}
public function partialLayout($file)
{
	$host=$GLOBALS['host'];

	if(file_exists($_SERVER['DOCUMENT_ROOT'].$host."/layouts/".$file.".phtml"))
	{
		
		$path=$_SERVER['DOCUMENT_ROOT'].$host."/layouts/".$file.".phtml";
		//create view 
		//echo $path;
		$partialView = new View($this->controllerObj,$path,1);	    
		$this->css=array_merge($this->css,$partialView->getCssFiles());
		$this->js=array_merge($this->js,$partialView->getJsFiles());

	}
	else
	{
		$GLOBALS['tracK_info']['partial']=$file;
		$GLOBALS['tracK_info']['source']=$this->layoutName.".phtml layout";
		trigger_error("MISSING_PARTIAL_VIEW_FILE",E_USER_ERROR);
		// missing view
	}
}
public function meta($nameorarr,$content=null)
{
	if($content==null)
	{
		$metaStr = "<meta ";
		foreach ($nameorarr as $key => $value) {
			$metaStr .= $key."='".$value."' ";
		}
		$metaStr .=">\n";
		
	}
	else
	{
		$metaStr = "<meta name='".$nameorarr."' content='".$content."' >\n";
	}
	$this->meta[] = $metaStr;
}
public function headString($str)
{
	$this->headers[] = $str;

}
 public function title()
 {
 	echo "<title>".$this->title."</title>";
 }
 public function headFiles($cssArr,$jsArr)
 {
 	//print_r($this->css);
 	$string="";
 	foreach (array_unique($jsArr) as $js) {
 		$string .="<script type='text/javascript' src='".$js."'></script>\n";
 	}
 	foreach (array_unique($cssArr) as $css) {
 		$string .="<link rel='stylesheet' type='text/css' href='".$css."'>\n";
 	}
 	return $string;
 }
 public function getHeaders()
 {
 	$string="";
 	foreach (array_unique($this->meta) as $meta) {
 		$string .=$meta;
 	}
 	foreach (array_unique($this->headers) as $headstr) {
 		$string .=$headstr;
 	}
 	return $string;

 }
 public function baseUrl()
 {
 	$server=$_SERVER['HTTP_HOST'];
 	$url= $GLOBALS['host']."/site";
 	return $url;
 }
 public function appBase()
 {
 	$server=$_SERVER['HTTP_HOST'];
 	$url= $GLOBALS['host'];
 	return $url;
 }
 public function appendJS($file,$path='')
 {
 	if(empty($path)){
 		$this->js[]=$this->baseUrl()."/js/".$file;
 	}
 	else {
 		$this->js[]=$path."/".$file;
 	}
	
 }
 public function appendCSS($file,$path='')
 {
	if(empty($path)){
 		$this->css[]=$this->baseUrl()."/css/".$file;
 	}
 	else {
 		$this->css[]=$path."/".$file;
 	}
 }
 public function getCssFiles()
 {
	$css = $this->css;
	return $css;
 }

public function getJsFiles()
{
	$js = $this->js;
	return $js;
}
 public function view()
 {
 	if($this->controllerObj->viewRequired)
 	{
	 	$host=$GLOBALS['host'];
	 	$path = $_SERVER['DOCUMENT_ROOT'].$host."/modules/".$GLOBALS['forkobj']['module']."/views/".$GLOBALS['forkobj']['controller']."/".$GLOBALS['forkobj']['action'].".phtml";
	 	if(file_exists($path))
	 	{
	 		$viewObj = new View($this->controllerObj,$path);
	 		$this->css= array_merge($this->css,$viewObj->getCssFiles());
	 		$this->js= array_merge($this->js,$viewObj->getJsFiles());
	 	//	print_r($this->css);
	 	//	print_r($this->js);
	 	}
		else
		{
    		trigger_error("MISSING_VIEW_FILE",E_USER_ERROR); // missing view file
		}
 	}
 	else
 	{
 		trigger_error("INVALID_VIEW_ACCESS",E_USER_ERROR); // Access to view whereas viewRequired is set to false from Action call.
 	}
 }

}

class Controller
{
	public $layoutRequired=true;
	public $layout="default";
	public $viewRequired=true;
	public $view;
	public $title;
	public $responseType = ResponseType::HTML;
	public $responseObject;
	public function __construct()
	{
		$this->view = new StdClass;
		$this->responseObject = new responseObject();
	}
	public function setLayout()
	{
		$host=$GLOBALS['host'];
		$module= $GLOBALS['forkobj']['module'];	
		$layout =parse_ini_file($_SERVER['DOCUMENT_ROOT'].$host."/App/application.ini");
		$layout=isset($layout["layouts"][$module])?$layout["layouts"][$module]:"default";
		$this->layout=$layout;

	}

	public function request()
	{
		return (new requestData());
	}
	public function post($key="")
	{
		if(!$key)
		{
			return $_POST;
		}
		else
		{
			return isset($_POST[$key])?$_POST[$key]:"";
		}
	}
	public function get($key="")
	{
		$url = $_GET['params'];

		$data= explode('/', $url);
		//print_r($data);
		$params=array();
		for($i=3;$i < count($data);$i++)
		{
			if(isset($data[$i+1]))
			$params[$data[$i]] =$data[++$i]; 
		}
		if(!$key)
		{
			return $params;
		}
		else
		{
			if(isset($params[$key]))
			{
				return $params[$key];
			}
			else
			{
				return "";
			}
		}
	}
	public function redirect($url,$params=array())
	{
		if(!is_string($url))
		{
			if(get_class($url)=="MVC")
			{
				$url=$GLOBALS['host']."/".$url->module."/".$url->controller."/".$url->action;

			}
		}
		if(count($params)>0)
		{
			$url.="/";
			foreach ($params as $key => $value) {
				$url .=$key."/".$value."/";
			}
			$url=substr($url, 0,strlen($url)-1);
			
		}
		//echo $url;
		@header('Location: '.$url);
	}
	 public function appBase()
	 {
	 	$server=$_SERVER['HTTP_HOST'];
	 	$url= $GLOBALS['host'];
	 	return $url;
	 }

}
class ApiController extends Controller{
	
	public $apiRoutes;
	public $callbacks;
	function __construct(){
		$this->apiRoutes = array();
		$this->callbacks = array();
	}
	public function addRoute($path,$function){
		$this->apiRoutes[] = $path;
		$this->callbacks[] = $function;
		$function(1);
	}

}
class View
{
	private $view;
	private $js;
	private $css;
	private $controllerObj;

	
	function __construct($controllerObj,$path,$isPartial=0)
	{
		if($controllerObj->layoutRequired==false && $isPartial==0)
		{
			$this->css=array();
			$this->js=array();
			$this->controllerObj=$controllerObj;
			$this->view=$controllerObj->view;
			ob_start();

			$cont = include $path;
			$buffer =ob_get_clean();
			if($controllerObj->responseType == ResponseType::TERMINAL){
				$response = array('title'=>$controllerObj->title,'headfiles'=>$this->headFiles($this->css,$this->js),'html'=>$buffer);
				//header('Content-Type: application/json; charset=utf8');
				echo json_encode($response);
			}else{
				$html = "<!DOCTYPE html>\n";
				$html .="<head>\n";
				$html .="<title>".$controllerObj->title."</title>\n";
				$html .=$this->headFiles($this->css,$this->js)."\n";
				$html .="</head>\n<body>\n";
				echo $html;
				echo $buffer;
				echo "</body>\n</html>";
			}
		}
		else
		{
			$this->css=array();
			$this->js=array();
			$this->controllerObj=$controllerObj;
			$this->view=$controllerObj->view;
			ob_start();
			$cont = include $path;
			$buffer =ob_get_clean();
			//print_r($this->css);
			echo $buffer;
		}
		
	}
 public function headFiles($cssArr,$jsArr)
 {
 	//print_r($this->css);
 	$string="";
 	foreach (array_unique($jsArr) as $js) {
 		$string .="<script type='text/javascript' src='".$js."'></script>\n";
 	}
 	foreach (array_unique($cssArr) as $css) {
 		$string .="<link rel='stylesheet' type='text/css' href='".$css."'>\n";
 	}
 	return $string;
 }
	public function partial($file)
	{
		$host=$GLOBALS['host'];

		if(file_exists($_SERVER['DOCUMENT_ROOT'].$host."/modules/".$GLOBALS['forkobj']['module']."/views/".$GLOBALS['forkobj']['controller']."/".$file.".phtml"))
		{
			
			$path=$_SERVER['DOCUMENT_ROOT'].$host."/modules/".$GLOBALS['forkobj']['module']."/views/".$GLOBALS['forkobj']['controller']."/".$file.".phtml";
			//create view 
			//echo $path;
			$partialView = new View($this->controllerObj,$path,1);	    
			$this->css=array_merge($this->css,$partialView->css);
			$this->js=array_merge($this->js,$partialView->js);

		}
		else
		{
    		$GLOBALS['tracK_info']['partial']=$file;
			$GLOBALS['tracK_info']['source']=$GLOBALS['forkobj']['action'].".phtml View";
			trigger_error("MISSING_PARTIAL_VIEW_FILE",E_USER_ERROR); // missing view
		}
		

	}
	public function getCssFiles()
	{
		$css = $this->css;
		return $css;
	}
	
	public function getJsFiles()
	{
		$js = $this->js;
		return $js;
	}
	 public function appendJS($file,$path='')
	 {
	 	if(empty($path)){
	 		$this->js[]=$this->baseUrl()."/js/".$file;
	 	}
	 	else {
	 		$this->js[]=$path."/".$file;
	 	}
		
	 }
	 public function appendCSS($file,$path='')
	 {
		if(empty($path)){
	 		$this->css[]=$this->baseUrl()."/css/".$file;
	 	}
	 	else {
	 		$this->css[]=$path."/".$file;
	 	}
	 }
	public function baseUrl()
 	{
	 	$server=$_SERVER['HTTP_HOST'];
	 	$url= $GLOBALS['host']."/site";
	 	return $url;
 	}
 	public function appBase()
	{
	 	$server=$_SERVER['HTTP_HOST'];
	 	$url= $GLOBALS['host'];
	 	return $url;
	}
	public function post($key="")
	{
		if(!$key)
		{
			return $_POST;
		}
		else
		{
			return isset($_POST[$key])?$_POST[$key]:"";
		}
	}
	public function get($key="")
	{
		$url = $_GET['params'];

		$data= explode('/', $url);
		//print_r($data);
		$params=array();
		for($i=3;$i < count($data);$i++)
		{
			if(isset($data[$i+1]))
			$params[$data[$i]] =$data[++$i]; 
		}
		if(!$key)
		{
			return $params;
		}
		else
		{
			if(isset($params[$key]))
			{
				return $params[$key];
			}
			else
			{
				return "";
			}
		}
	}
}