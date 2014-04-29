<?php 
class sys_config
{
	public function getConnection($db)
	{
		  $config =parse_ini_file($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/App/application.ini");
		  $host=$config["mysql.".$db.".host"];
		  $uname=$config["mysql.".$db.".username"];
		  $pass=$config["mysql.".$db.".password"];
		  $dbase=$config["mysql.".$db.".dbname"];
		  
		  try
		  {
			   $con = new PDO("mysql:host=$host;dbname=$dbase", $uname, $pass);
			   $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		  }
		  catch (PDOException $e) 
		  {
			    print "Error!: " . $e->getMessage() . "<br/>";
			    die();
		  }
		  return $con;
		  
	}
}
class MysqlConnection
{
	private $db;
	function __construct($db="maindb")
	{
		$this->db=$db;
	}
	
	public function fetchAll($query)
	{
		$dbObj = new sys_config();
		$connectionObj = $dbObj->getConnection($this->db);
		$result = mysqli_query($connectionObj,$query);
		$data_array = array();
		while ($array = mysqli_fetch_assoc($result))
		{
			$data_array[] = $array;
		}
		return $data_array;
	}
	public function insert($data)
	{
		
		$dbObj = new sys_config();
		$connectionObj = $dbObj->getConnection($this->db);
		$table=$data['table'];
		$prep = array();
		foreach($data['data'] as $k => $v )
		{
	  	  $prep[':'.$k] = $v;
		}
		$query = $connectionObj->prepare("INSERT INTO $table ( " . implode(', ',array_keys($data['data'])) . ") VALUES (" . implode(', ',array_keys($prep)) . ")");
		$query->execute($prep);	
	}
	public function select($data)
	{
		$dbObj = new sys_config();
		$connectionObj = $dbObj->getConnection($this->db);
		$table=$data['table'];
		$prep = array();
		$where=array();
		foreach($data['condition'] as $k => $v )
		{
	  	  $prep[':'.$k] = $v;
	  	  $where[] = $k."=".":".$k;
		}
		if(count($data['condition'])>0)
		$query = $connectionObj->prepare("SELECT * FROM $table WHERE " . implode(' AND ',array_values($where)) );
		else
		$query = $connectionObj->prepare("SELECT * FROM $table " );
		$query->execute($prep);
		$res=$query->fetchAll(PDO::FETCH_ASSOC);

		return $res;
	}
	public function delete($data)
	{
		$dbObj = new sys_config();
		$connectionObj = $dbObj->getConnection($this->db);
		$table=$data['table'];
		$where=array();
		foreach($data['condition'] as $k => $v )
		{
	  	  $prep[':'.$k] = $v;
	  	  $where[] = $k."=".":".$k;
		}
		$query = $connectionObj->prepare("SELECT * FROM $table WHERE " . implode(' AND ',array_values($where)) );
		$query->execute($prep);
		$res=$query->fetch(PDO::FETCH_ASSOC);
		$query = $connectionObj->prepare("DELETE FROM $table WHERE " . implode(' AND ',array_values($where)) );
		$query->execute($prep);	
		return $res;
	}
	public function update($udata)
	{
		$dbObj = new sys_config();
		$connectionObj = $dbObj->getConnection($this->db);
		$table=$udata['table'];
		$cond=array();
		$data=array();
		foreach($udata['data'] as $k => $v )
		{
	  	  $prep[':data'.$k] = $v;
	  	  $data[] = $k."=".":data".$k;
		}
		foreach($udata['condition'] as $k => $v )
		{
	  	  $prep[':cond'.$k] = $v;
	  	  $cond[] = $k."=".":cond".$k;
		}
		$query = $connectionObj->prepare("UPDATE $table SET " . implode(',',array_values($data))." WHERE ".implode(' AND ', array_values($cond)) );
		$query->execute($prep);		
	}
}
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
 public function __construct($cObj,$layoutName)
 {
 	$this->head=array();
 	$this->js=array();
 	$this->css=array();
 	$this->meta=array();
 	$this->headers=array();
 	$this->controllerObj=$cObj;
 	$this->layoutPath=$layoutName;
 	$host=$GLOBALS['host'];
 	$cpath=$_SERVER['DOCUMENT_ROOT']."/".$host."/layouts/controllers/".$cObj->layout."layoutController.php";
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

		if(file_exists($_SERVER['DOCUMENT_ROOT']."/".$host."/modules/".$GLOBALS['forkobj']['module']."/views/".$GLOBALS['forkobj']['controller']."/".$GLOBALS['forkobj']['action'].".phtml"))
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
			//$path=$_SERVER['DOCUMENT_ROOT']."/".$host."/modules/".$GLOBALS['forkobj']['module']."/views/".$GLOBALS['forkobj']['controller']."/".$GLOBALS['forkobj']['action'].".phtml";
			//create view 
			//$view = new View($this->controllerObj,$path);	
			
		}
		else
		{

    		$errorObj= new Exceptions("1004",array("module"=>$GLOBALS['forkobj']['module'],"controller"=>$GLOBALS['forkobj']['controller'],"action"=>$GLOBALS['forkobj']['action']));	
			return $errorObj->throwException();// missing view
		}
	}
	else
	{
		ob_start();
			$lay = include $this->layoutPath;
			
			$buffer =ob_get_clean();
			$html = "<!DOCTYPE html>\n";
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

	if(file_exists($_SERVER['DOCUMENT_ROOT']."/".$host."/modules/".$GLOBALS['forkobj']['module']."/views/".$GLOBALS['forkobj']['controller']."/".$file.".phtml"))
	{
		
		$path=$_SERVER['DOCUMENT_ROOT']."/".$host."/modules/".$GLOBALS['forkobj']['module']."/views/".$GLOBALS['forkobj']['controller']."/".$file.".phtml";
		//create view 
		//echo $path;
		$partialView = new View($this->controllerObj,$path,1);	    
		$this->css=array_merge($this->css,$partialView->getCssFiles());
		$this->js=array_merge($this->js,$partialView->getJsFiles());

	}
	else
	{
		$errorObj= new Exceptions("1004",array("module"=>$GLOBALS['forkobj']['module'],"controller"=>$GLOBALS['forkobj']['controller'],"action"=>$file));
		echo $errorObj->throwException();// missing view
	}
}
public function meta($nameorarr,$content=null)
{
	if($content==null)
	{
		$metaStr = "<meta ";
		foreach ($nameorarr as $key => $value) {
			$metaStr .= $key."='"."' ";
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
 		$string .="<script type='text/javascript' src='".$this->baseUrl()."/js/".$js."'></script>\n";
 	}
 	foreach (array_unique($cssArr) as $css) {
 		$string .="<link rel='stylesheet' type='text/css' href='".$this->baseUrl()."/css/".$css."'>\n";
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
 	$url= "/".$GLOBALS['host']."/site";
 	return $url;
 }
 public function appBase()
 {
 	$server=$_SERVER['HTTP_HOST'];
 	$url= "/".$GLOBALS['host'];
 	return $url;
 }
 public function appendJS($file)
 {
	$this->js[]=$file;
 }
 public function appendCSS($file)
 {
	$this->css[]=$file;
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
	 	$path = $_SERVER['DOCUMENT_ROOT']."/".$host."/modules/".$GLOBALS['forkobj']['module']."/views/".$GLOBALS['forkobj']['controller']."/".$GLOBALS['forkobj']['action'].".phtml";
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
    		$errorObj= new Exceptions("1004",array("module"=>$GLOBALS['forkobj']['module'],"controller"=>$GLOBALS['forkobj']['controller'],"action"=>$GLOBALS['forkobj']['action']));
			return $errorObj->throwException();// missing view
		}
 	}
 	else
 	{
 		trigger_error("Error : 1006 Access to view whereas viewRequired is set to false from Action call.");
		$errorObj= new Exceptions("1006","");
		return $errorObj->throwException();// Access to view whereas viewRequired is set to false from Action call.
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
	public function __construct()
	{
		$view = new StdClass;
	}
	public function setLayout()
	{
		$host=$GLOBALS['host'];
		$module= $GLOBALS['forkobj']['module'];	
		$layout =parse_ini_file($_SERVER['DOCUMENT_ROOT']."/".$host."/App/application.ini");
		$layout=isset($layout["layouts.".$module])?$layout["layouts.".$module]:"default";
		$this->layout=$layout;
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
	public function redirect($url,$params)
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
			$url.="/";
			foreach ($params as $key => $value) {
				$url .=$key."/".$value."/";
			}
			$url=substr($url, 0,strlen($url)-1);
			
		}
		//echo $url;
		header('Location: '.$url);
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
			$html = "<!DOCTYPE html>\n";
			$html .="<head>\n";
			$html .="<title>".$controllerObj->title."</title>\n";
			$html .=$this->headFiles($this->css,$this->js)."\n";
			$html .="</head>\n<body>\n";
			echo $html;
			echo $buffer;
			echo "</body>\n</html>";
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
	 		$string .="<script type='text/javascript' src='".$this->baseUrl()."/js/".$js."'></script>\n";
	 	}
	 	foreach (array_unique($cssArr) as $css) {
	 		$string .="<link rel='stylesheet' type='text/css' href='".$this->baseUrl()."/css/".$css."'>\n";
	 	}
	 	return $string;
	}
	public function partial($file)
	{
		$host=$GLOBALS['host'];

		if(file_exists($_SERVER['DOCUMENT_ROOT']."/".$host."/modules/".$GLOBALS['forkobj']['module']."/views/".$GLOBALS['forkobj']['controller']."/".$file.".phtml"))
		{
			
			$path=$_SERVER['DOCUMENT_ROOT']."/".$host."/modules/".$GLOBALS['forkobj']['module']."/views/".$GLOBALS['forkobj']['controller']."/".$file.".phtml";
			//create view 
			//echo $path;
			$partialView = new View($this->controllerObj,$path,1);	    
			$this->css=array_merge($this->css,$partialView->css);
			$this->js=array_merge($this->js,$partialView->js);

		}
		else
		{
    		$errorObj= new Exceptions("1004",array("module"=>$GLOBALS['forkobj']['module'],"controller"=>$GLOBALS['forkobj']['controller'],"action"=>$file));
			echo $errorObj->throwException();// missing view
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
	public function appendJS($file)
	{
		$this->js[]=$file;
	}
	public function appendCSS($file)
	{
		$this->css[]=$file;
	}
	public function baseUrl()
 	{
	 	$server=$_SERVER['HTTP_HOST'];
	 	$url= "/".$GLOBALS['host']."/site";
	 	return $url;
 	}
 	public function appBase()
	{
	 	$server=$_SERVER['HTTP_HOST'];
	 	$url= "/".$GLOBALS['host'];
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
class pathProvider
{
	private $forkObj;
	private $errorObj;
	public function createHeader($title)
	{
	 	$header ="<head>";
	 	$header .="<title>".$title."</title>";
	 	$header .="</head>";

	 	return $header;
	}
	public function getPath($url,$host)
	{
		session_start();
		
		// Consideration of fine requests only
		$index = strpos($url,"?");
		if($index>0)
		{
			$getArray = substr($url,$index+1);
			$getArray = str_replace("=", "/", $getArray);
			$getArray = str_replace("&", "/", $getArray);
			$_GET['params']="MOD/CON/ACT/".$getArray;
			//echo $getArray;
			$url = substr($url,0,$index);
		}
		/*else
		$_GET['params']= "";*/
		$path_arr= explode("/",$url);
		//echo $url;die;
		$pathObj= array();
		$pathObj['host']=$path_arr[1];
		if(!$pathObj['host']==$host)
		{
			return "Invalid host.";
		}
		$pathObj['module']=isset($path_arr[2])&&$path_arr[2]!=""?$path_arr[2]:"default";
		$pathObj['controller']=isset($path_arr[3])&&$path_arr[3]!=""?$path_arr[3]:"index";
		$pathObj['action']=isset($path_arr[4])&&$path_arr[4]!=""?$path_arr[4]:"index";
		$info = array("module"=>$pathObj['module'],"controller"=>$pathObj['controller'],"action"=>$pathObj['action']);
    	if(is_dir($_SERVER['DOCUMENT_ROOT']."/".$host."/modules/".$pathObj['module']))
		{
    		if(file_exists($_SERVER['DOCUMENT_ROOT']."/".$host."/modules/".$pathObj['module']."/controllers/".$pathObj['controller']."Controller.php"))
			{
	    		include $_SERVER['DOCUMENT_ROOT']."/".$host."/modules/".$pathObj['module']."/controllers/".$pathObj['controller']."Controller.php";
		    	//echo "Controller exists";
    	    	if (class_exists($pathObj['module'].'_'.$pathObj['controller'].'Controller'))
		    	{
		    		$controllerClass=$pathObj['module'].'_'.$pathObj['controller'].'Controller';
		    		$GLOBALS['forkobj']=array();
		    		$GLOBALS['forkobj']['module']=$pathObj['module'];
		    		$GLOBALS['forkobj']['controller']=$pathObj['controller'];
		    		$GLOBALS['forkobj']['action']=$pathObj['action'];
		    		$GLOBALS['host']=$host;
		    		if(!method_exists($controllerClass,$pathObj['action']."Action" ))
		    		{
	    				$errorObj= new Exceptions("1003",$info);
						return $errorObj->throwException();//Action Undefined
		    		}
		    		$layout =parse_ini_file($_SERVER['DOCUMENT_ROOT']."/".$host."/App/application.ini");
		    		$layout=isset($layout["layouts.".$pathObj['module']])?$layout["layouts.".$pathObj['module']]:"default";

		    		$controllerObj = new $controllerClass();
		    		$controllerObj->setLayout(); // Important to call to read application.ini for layout of that module
		    		$action=$pathObj['action']."Action";
		    		$controllerObj->$action();
		    		
		    		if($controllerObj->layoutRequired)
		    		{
		    			$layout=$controllerObj->layout;
		    			//echo $layout;die;
	    				if(file_exists($_SERVER['DOCUMENT_ROOT']."/".$host."/layouts/".$layout.".phtml"))
		    			{

		    				$path=$_SERVER['DOCUMENT_ROOT']."/".$host."/layouts/".$layout.".phtml";
		    				//create layout 
		    				$layoutObj = new Layout($controllerObj,$path);		    			
		    			}
		    			else
		    			{
				    		$errorObj= new Exceptions("1005",$layout);
		    				return $errorObj->throwException();// missing layout
		    			}
		    		}
		    		else
		    		{

			    		if($controllerObj->viewRequired)
			    		{

			    			if(file_exists($_SERVER['DOCUMENT_ROOT']."/".$host."/modules/".$pathObj['module']."/views/".$pathObj['controller']."/".$pathObj['action'].".phtml"))
			    			{
			    				
			    				$path=$_SERVER['DOCUMENT_ROOT']."/".$host."/modules/".$pathObj['module']."/views/".$pathObj['controller']."/".$pathObj['action'].".phtml";
			    				//create view 
			    				$viewObj = new View($controllerObj,$path);		    

			    				
			    			}
			    			else
			    			{
					    		$errorObj= new Exceptions("1004",$info);
			    				return $errorObj->throwException();// missing view
			    			}
			    		}
			    		else
			    		{
			    			// Its a ajax call so: 
			    			/*
							1) Title set property will not work as not required.
							2) Parameters to view are not allowed.
			    			*/
							$view = $controllerObj->view;
							if(count($view)>0)
							{
								$errorObj= new Exceptions("1007",$pathObj['action']);
		    					return $errorObj->throwException();
							}

			    		}
		    		}
		    		
		    	}
		    	else
		    	{
		    		$errorObj= new Exceptions("1001",$info);
    				return $errorObj->throwException();// Controller Class not found
		    	}
			}	
			else
	    	{
	    		$errorObj= new Exceptions("1002",$info);
    			return $errorObj->throwException();//undefined Controller
	    	}
		}	
		else
    	{
    		$errorObj= new Exceptions("1000",$info);
    		return $errorObj->throwException();//undefined module
    	}

		
	}
}
/*$obj = new dbProvider();
$data = $obj->query("SELECT * FROM nav_links");*/

?>
