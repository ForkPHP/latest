<?php if(!defined('APP_HOST')) die('Access restricted.');

class Fork_Lib
{
	private $module;
	private $controller;
	private $action;
	
	public function __construct()
	{
		$this->module=$GLOBALS['forkobj']['module'];
		$this->controller=$GLOBALS['forkobj']['controller'];
		$this->action=$GLOBALS['forkobj']['action'];
	}
	public function getModuleName()
	{
		return $this->module;
	}
	public function getControllerName()
	{
		return $this->controller;
	}
	public function getActionName()
	{
		return $this->action;
	}
	public function Session()
	{
		
	}
	public function getFileContent($file)
	{
		try
		{
			$cont = @file_get_contents(str_replace("//", "/", $file));
			if(!$cont){
				return "File not found or Permission denided.";
			}
			else{
				return $cont;
			}
			
		}
		catch(Exception $e)
		{
			return $e->getMessage();
		}
	}
	public function updateFileContent($file,$content)
	{
		try
		{
			$res = @file_put_contents($file, $content);
			if(!$res){
				return "Permission denided to save file.";
			}
		}
		catch(Exception $e)
		{
			return $e->getMessage();
		}
	}
	public function isFileExists($file)
	{
		return is_file($file);
	}
	public function isFolderExists($file)
	{
		return is_dir($file);
	}
	public function createFile($folder,$file)
	{
		try
		{
			  $file = fopen($folder."/".$file,"w");
			  return true;
		}
		catch(Exception $e)
		{
			return $e->getMessage();
		}
	}
	public function createFolder($folder,$file)
	{
		try
		{
			  $file = mkdir($folder."/".$file);
			  return true;
		}
		catch(Exception $e)
		{
			return $e->getMessage();
		}
	}
	public function deleteFolder($folder)
	{
		try
		{
			  $file = $this->deleteDir($folder);
			  return true;
		}
		catch(Exception $e)
		{
			return $e->getMessage();
		}
	}
	public function deleteDir($dir) 
	{
		
	   $iterator = new RecursiveDirectoryIterator($dir);
	   foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) 
	   {
	      if ($file->isDir()) {
	         @rmdir($file->getPathname());
	      } else {
	         @unlink($file->getPathname());
	      }
	   }
	   rmdir($dir);
	}
}
class ModuleManager
{
	private $module="";

	public function __construct($module="")
	{
		if($module=="") return;
		$this->module=$module;
		$directory = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$module."/controllers/";
		 foreach(glob($directory . "*.php") as $class)
		 {
		     include_once $class;
		 }
	}
	public function getAllModules()
	{
		$path = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/";
		$results = scandir($path);
		$modules=array();
		foreach ($results as $result) {
		    if ($result === '.' or $result === '..' or $result=="models") continue;

		    if (is_dir($path . '/' . $result)) {
		        $modules[]=$result;
		    }
		}
		return $modules;
	}
	public function getModuleContollers()
	{
		if(is_dir($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$this->module."/controllers/"))
		{
			$path = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$this->module."/controllers/";
			$results = scandir($path);
			$controllers = array();
			foreach ($results as $result)
			{
				if(is_file($path . '/' . $result))
				{
					$controllers[str_replace(".php", "", $result)] = array_diff(get_class_methods($this->module."_".str_replace(".php", "", $result)), get_class_methods('Controller'));
				}
			}
			return $controllers;
		}
		else
		{
			return -1;
		}

	}
	public function getContollerActions($module,$controller)
	{
		if(class_exists($module."_".$controller))
		{
			return array_diff(@get_class_methods($module."_".$controller), get_class_methods('Controller'));
		}
		else
		{
			return -1;
		}
	}
	public function createModule($module,$isLayout,$isView)
	{
		
		try
		{
			$path = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$module."/";
			if(!is_dir($path))
			{
				$isDir  = @mkdir($path);
				if(!$isDir){
					return "Permission denided to create directory. Please update directory permissions on root directory.";
				}
			}
			else
			{
				return "Module already exists.";
			}
			if(!is_dir($path."/controllers/"))
			{
				mkdir($path."/controllers/");
				$content = file_get_contents($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/lib/templates/controller");
				if(!is_file($path."/controllers/indexController.php"))
				{
					 $file = fopen($path."/controllers/indexController.php","w");
					 $code="";
					 if($isView=="false")
					 {
					 	$code .='$this->viewRequired=false;'."\n\t\t";
					 }
					 if($isLayout=="false")
					 {
					 	$code .='$this->layoutRequired=false;'."\n\t\t";
					 }
					 file_put_contents($path."/controllers/indexController.php",str_replace("{{code}}", $code, str_replace("{{controllername}}", $module."_indexController", $content)) );
				}
			}
			if(!is_dir($path."/views/"))
			{
				mkdir($path."/views/");
				if(!is_dir($path."/views/index/"))
				{
					mkdir($path."/views/index/");
					if($isView=="true")
					{
						if(!is_file($path."/views/index/index.phtml"))
						{
							$file = fopen($path."/views/index/index.phtml","w");
							$content = file_get_contents($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/lib/templates/view");
							file_put_contents($path."/views/index/index.phtml", str_replace("{{actionname}}", "index", $content));
						}
					}
					
				}
			}
			if($isLayout=="true")
			{
				if(!is_file($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/layouts/".$module.".phtml"))
				{
					$content = file_get_contents($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/lib/templates/layout");
					$file = fopen($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/layouts/".$module.".phtml","w");
					file_put_contents($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/layouts/".$module.".phtml", str_replace("{{modulename}}", $module, $content));
				}
				$arr = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/App/application.ini",true);
				$arr['layouts']['layouts'][$module]=$module;
				$val = write_ini_file($arr,$_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/App/application.ini",true);

			}
			return 1;
		}
		catch(Exception $exp)
		{
			return $exp->getMessage();
		}
	}
	function deleteModule($module)
	{
		$path = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$module;
		$libObj = new Fork_Lib();
		if($module !="")
		{
			if(is_file($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/layouts/".$module.".phtml"))
			{
				unlink($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/layouts/".$module.".phtml");
			}
			$libObj->deleteDir($path);
		}
		
		return 1;
	}
	function createController($module,$controller,$isView)
	{
		$path = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$module;
		try
		{
			$content = file_get_contents($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/lib/templates/controller");
			if(!is_file($path."/controllers/".$controller."Controller.php"))
			{
				 $file = @fopen($path."/controllers/".$controller."Controller.php","w");
				 if(!$file){
				 	return "Permission denided to open file.";
				 }
				 $code="";
				 if($isView=="false")
				 {
				 	$code .='$this->viewRequired=false;'."\n\t\t";
				 }
				 file_put_contents($path."/controllers/".$controller."Controller.php",str_replace("{{code}}", $code, str_replace("{{controllername}}", $module."_".$controller."Controller", $content)) );
				 if($isView=="true")
				 {
				 	$createView = $this->createView($module,$controller,"index");
				 	if($createView == "-1")
				 	{
				 		return "view file $action.phtml already exists.";
				 	}
				 }
			}
			else
			{
				return "Controller $controller already Exists.";
			}
			return 1;
		}
		catch(Exception $exp)
		{
			return $exp->getMessage();
		}
	}
	public function deleteController($module,$controller)
	{	
		$path = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$module."/controllers/";
		$libObj = new Fork_Lib();
		try
		{
			if($module !="" && $controller !="")
			{
				if(is_file($path.$controller.".php"))
				{
					unlink($path.$controller.".php");
				}
				if(is_dir($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$module."/views/".str_replace("Controller", "", $controller)."/"))
				$files = scandir($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$module."/views/".str_replace("Controller", "", $controller)."/"); // get all file names
				foreach($files as $file){ // iterate files
				  if(is_file($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$module."/views/".str_replace("Controller", "", $controller)."/".$file))
				    @unlink($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$module."/views/".str_replace("Controller", "", $controller)."/".$file); // delete file
				}
				//print_r($files);
				@rmdir($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$module."/views/".str_replace("Controller", "", $controller)."/");
				return 1;
			}
			else
			{
				return "Something went wrong.Please try again in a moment.";
			}
		}
		catch(Exception $e)
		{
			return $e->getMessage();
		}
		
	}
	function createView($module,$controller,$action)
	{
		$path = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$module;
		if(!is_dir($path."/views/"))
		{
			mkdir($path."/views/");	
		}
		if(!is_dir($path."/views/$controller/"))
		{
			mkdir($path."/views/$controller/");	
		}
		if(!is_file($path."/views/$controller/$action.phtml"))
		{
			$file = fopen($path."/views/$controller/$action.phtml","w");
			$content = file_get_contents($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/lib/templates/view");
			$content = str_replace("{appname}", APP_NAME, $content);
			file_put_contents($path."/views/$controller/$action.phtml", str_replace("{{actionname}}", $action, $content));
		}
		else
		{
			return "-1";// view already exists
		}
	}
	function createAction($module,$controller,$action,$isView)
	{
		//return $module."_".$controller;
		include_once $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$module."/controllers/".$controller.".php";
		$path = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/modules/".$module;
		if($isView=="false")
		{
			$code='$this->viewRequired=false;';
		}
		else
		{
			$code="";
			$res = $this->createView($module,str_replace("Controller", "", $controller),$action);
			if($res==-1)
			{
				return "View file ".$action.".phtml Already exists. Action $action Created.";
			}
		}
		//code to create 
		$codmod = new CodeMod();
		$res = $codmod->addmethod($module."_".$controller,$action."Action",array(),$code);
		if($res==1)
		{
			return 1;
		}
		else
		{
			return $res;
		}
	}
}
class CodeMod 
{
	
	
	function addmethod($class,$methodname,$params=array(),$code="")
	{
		$str ='<?php'." \n \n";
		$parentclass = get_parent_class($class);
		$parent="";
		if($parentclass!=null)
		{
			$parent="extends $parentclass";
		}
		$str .="\tclass $class $parent\n\t{ \n ";
		//echo $parentclass;
		$methods = array();
		$methods[]="__construct";
		$methods = array_merge($methods,array_diff(get_class_methods($class),get_class_methods("Controller")));
		
		
		if(in_array($methodname, $methods))
		{
			return "Method already exists.";
		}
		
		foreach ($methods as $method) 
		{
			$func = new ReflectionMethod($class,$method);
			
			$filename = $func->getFileName();
			//print_r($func);
			$start_line = $func->getStartLine() - 1; // it's actually - 1, otherwise you wont get the function() block
			$end_line = $func->getEndLine();
			$length = $end_line - $start_line;
			/*echo $filename;
			echo $start_line;
			echo $end_line;*/
			$source = file($filename);
			$body = implode("", array_slice($source, $start_line, $length));
			$str .= $body;
			//echo $body."<br/>";
		}
		
		if(count($params)>0)
		$str .="\n\t\tpublic function $methodname($".implode(",$", $params).")\n\t\t{\n\t\t\t".$code." \n\t\t}\n";
		else
		$str .="\n\t\tpublic function $methodname()\n\t\t{\n\t\t\t".$code." \n\t\t}\n";
		$str .= " \n \n\t} \n ".'?>';
		//echo "<pre>".htmlspecialchars($str)."</pre>";
		file_put_contents($filename, $str);
		return 1;

	}
}
class appConfig
{
	private $ini;
	private $host;
	function __construct($ini='application',$host=null)
	{
		if($ini !=null)
		$this->ini = $ini;
		if($host !=null)
		$this->host = $host;
	}
	function get($prop='')
	{
		$host=isset($this->host)?$this->host:$GLOBALS['host'];
		$iniData = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/".$host."/App/".$this->ini.".ini");
		if(empty($prop))
		{
			return $iniData;
		}
		else
		{
			if(isset($iniData[$prop]))
			{
				return $iniData[$prop];
			}
			else
			{
				return '';
			}
		}
	}
	public static function appName()
	{
		return $GLOBALS['config']['application'];
	}
	function getList()
	{
		$path = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/App";
		$dir_handle = @opendir($path) or die("Unable to open $path");
             
              //Leave only the lastest folder name
      $dirname = @end(@explode("/", $path));
      $arr=array();
      while (false !== ($file = readdir($dir_handle)))
      {
          if($file!="." && $file!="..")
          {
              $content = file_get_contents($path."/".$file);
              $arr[$file]=$content;
          }
      }
      return $arr;
	}
	
}
class Exceptions
{
	private $type;
	private $exceptions;
	function __construct($type)
	{
		$this->type=$type;
		$host=$GLOBALS['host'];
		$this->exceptions = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/".$host."/App/exceptions.ini");
	}
	function throwException()
	{

		switch($this->type)
		{
			case "UNDEFINED_MODULE_EXCEPTION":
						return sprintf($this->exceptions['UNDEFINED_MODULE_EXCEPTION'],$GLOBALS['tracK_info']['module']);
						break;
			case "UNDEFINED_CONTROLLER_EXCEPTION":
						return sprintf($this->exceptions['UNDEFINED_CONTROLLER_EXCEPTION'],$GLOBALS['tracK_info']['controller'],$GLOBALS['tracK_info']['module']);
						break;	
			case "CONTROLLER_CLASS_EXCEPTION":
						return sprintf($this->exceptions['CONTROLLER_CLASS_EXCEPTION'],$GLOBALS['tracK_info']['module']."_".$GLOBALS['tracK_info']['controller']."Controller",$GLOBALS['tracK_info']['module']);
						break;
			case "UNDEFINED_ACTION_EXCEPTION":
						return sprintf($this->exceptions['UNDEFINED_ACTION_EXCEPTION'],$GLOBALS['tracK_info']['action'],$GLOBALS['tracK_info']['controller'],$GLOBALS['tracK_info']['module']);
						break;	
			case "MISSING_VIEW_FILE":
						return sprintf($this->exceptions['MISSING_VIEW_FILE'],$GLOBALS['tracK_info']['action'],$GLOBALS['tracK_info']['action'],$GLOBALS['tracK_info']['controller'],$GLOBALS['tracK_info']['module']);
						break;	
			case "MISSING_LAYOUT_FILE":
						return sprintf($this->exceptions['MISSING_LAYOUT_FILE'],$GLOBALS['errordata']['layout']);
						break;
			case "INVALID_VIEW_PARAMS":
							return sprintf($this->exceptions['INVALID_VIEW_PARAMS'],$GLOBALS['tracK_info']['action'],$GLOBALS['tracK_info']['controller'],$GLOBALS['tracK_info']['module']);
						break;
			case "INVALID_VIEW_ACCESS":
							return sprintf($this->exceptions['INVALID_VIEW_ACCESS'],$GLOBALS['tracK_info']['action'],$GLOBALS['tracK_info']['controller']);
						break;
			case "MISSING_PARTIAL_VIEW_FILE":
							return sprintf($this->exceptions['MISSING_PARTIAL_VIEW_FILE'],$GLOBALS['tracK_info']['partial'],$GLOBALS['tracK_info']['source'],$GLOBALS['tracK_info']['action'],$GLOBALS['tracK_info']['controller'],$GLOBALS['tracK_info']['module']);
						break;	
			case "DATABASE_EXCEPTION":
							return sprintf($this->exceptions['DATABASE_EXCEPTION'],$GLOBALS['errordata']['db']['name'],$GLOBALS['errordata']['db']['message']);
						break;															
			default:
						return sprintf($this->exceptions['exception_'.$this->type],$this->type);
						break;
		}
	}
}
/**
* User login and profile support classes
*/
class userSystem
{
	private $class;
	private $role;
	function __construct($class,$role)
	{
		$this->class=$class;
		$this->role=$role;
	}
	function validate()
	{
		if(isset($_SESSION['user']) && isset($_SESSION['user']['token']))
		{
			
			$context = new DbContext();
			$class=$this->class;
			$user = new $class();
			$user->username=$_SESSION['user']['username'];
			$user->role = $this->role;
			$row = $context->Get($user)->toArray();
			//print_r($row);
			if( count($row) >0 && isset($row['token']) && $row['token']==$_SESSION['user']['token'])
			{
				$token = uniqid() . '_' . md5(mt_rand());
				return true;
			}
			return false;
		}
		else
		{
			//echo "not set";
			return false;
		}
	}
	function processLogin($username,$password)
	{
		$context = new DbContext();
		// Create object of users table in forkAdmin DB.
		$user = new $this->class();
		$user->username=$username;
		$user->password=$password;
		$user->role=$this->role;
		//print_r($user);die;

		$user = $context->Get($user)->toObject();

		if($username !="" && $password !="")
		{	
			
			if(count($user)>0)
			{
				// login succesful

				$_SESSION['user']=array();
				$_SESSION['user']['username']=$username;
				$_SESSION['user']['id']=$user->id;
				$_SESSION['user']['cdate']=$user->cdate;
				$token = uniqid() . '_' . md5(mt_rand());
				$_SESSION['user']['token']=$token;
				$user->token=$token;
				// update token in DB
				$context->Update($user);
				$user_profile = new user_profile();
				$user_profile->user_id = $user->id;
				$user_profile = $context->Get($user_profile)->toArray();
				//print_r($user_profile);

				$_SESSION['user'] = array_merge($_SESSION['user'],$user_profile);

				//echo $user->id."HHEHHE";
				return true;
			}
		}
		return false;
	}
	function add($name,$email,$password)
	{
		$user = new $this->class();
		$user->username=$email;
		$user->password=$password;
		$user->role=$this->role;
		$user->token="";
		$context = new DbContext();
		try
		{
			$res = $context->Add($user);
		}
		catch(Exception $e)
		{
			$res = $e->getMessage();
			//$res = mysql_errno();
		}
		if(empty($res))
		{
			return "success";
		}
		else
		{
			return "error";
		}

	}
}



?>

<?php
# Core Methods
	function parse_error($error, $error_string, $filename, $line, $symbols)
	{
		/*/*echo $error;
		print_r($symbols);*/
		//echo "<pre>";
		//debug_print_backtrace();*/
		$e = new Exception();
		
		$stack = preg_replace("/\n/", '<br>', $e->getTraceAsString());
		$iniObj = new appConfig();
		$env = $iniObj->get('application.environment');
		if($env =="development")
		{
			$customException = new Exceptions($error_string);
			$text = $customException->throwException();
			//echo "</pre>";
			$errorFormat = str_replace("{{stacktrace}}",$stack,file_get_contents($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/lib/templates/error"));
			$errorFormat = str_replace("{{text}}", $text, $errorFormat);
			$errorFormat = str_replace("{{exceptionname}}", $error_string, $errorFormat);
			$errorFormat = str_replace("{{desc}}", "The exception '<b>$error_string</b>' occurred in the file '<i>$filename</i>'	on line $line.", $errorFormat);
			$errorFormat = str_replace("{{requesturi}}", $_SERVER['REQUEST_URI'], $errorFormat);
			$errorFormat = str_replace("{{appname}}", $GLOBALS['host'], $errorFormat);
			ob_end_clean();
			echo $errorFormat;
			//echo file_get_contents($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/lib/templates/error");
			die;
		}
		else
		{
			/*$content = file_get_contents($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/lib/templates/custom_error");
			echo $content;*/
			if($error_string=='UNDEFINED_ACTION_EXCEPTION' || $error_string=='UNDEFINED_CONTROLLER_EXCEPTION' || $error_string=='UNDEFINED_MODULE_EXCEPTION')
			{
				$GLOBALS['ERROR_TYPE']=1;
			}
			$pathObj = new requestObject();
			$pathObj->getPath("/".$GLOBALS['host']."/default/index/error",$GLOBALS['host']);
			// custom handling : like 404 if ontroler , module or action not find 
		}
	  	//echo "<p>The error '<b>$error_string</b>' occurred in the file '<i>$filename</i>'	on line $line.</p>";
	}
    if (!function_exists('write_ini_file')) {
        function write_ini_file($assoc_arr, $path, $has_sections=FALSE) {
            $content = "";
     
            if ($has_sections) {
                foreach ($assoc_arr as $key=>$elem) {
                    $content .= "[".$key."]\n";
                    foreach ($elem as $key2=>$elem2)
                    {
                        if(is_array($elem2))
                        {
                            /*for($i=0;$i<count($elem2);$i++)
                            {
                                $content .= $key2."[] = \"".$elem2[$i]."\"\n";
                            }*/
                            foreach ($elem2 as $key => $value) {
                            	$content .= $key2."[$key] = \"".$value."\"\n";
                            }
                        }
                        else if($elem2=="") $content .= $key2." = \n";
                        else $content .= $key2." = \"".$elem2."\"\n";
                    }
                    $content.="\n";
                }
            }
            else {
                foreach ($assoc_arr as $key=>$elem) {
                    if(is_array($elem))
                    {
                        for($i=0;$i<count($elem);$i++)
                        {
                            $content .= $key2."[] = \"".$elem[$i]."\"\n";
                        }
                    }
                    else if($elem=="") $content .= $key2." = \n";
                    else $content .= $key2." = \"".$elem."\"\n";
                }
            }
     
            if (!$handle = fopen($path, 'w')) {
                return false;
            }
            if (!fwrite($handle, $content)) {
                return false;
            }
            fclose($handle);
            return true;
            
        }
    }
    if(!function_exists('array_to_xml')){
    	function array_to_xml($array, &$xml) {
		    foreach($array as $key => $value) {
		        if(is_array($value)) {
		            if(!is_numeric($key)){
		                $subnode = $xml->addChild("$key");
		                array_to_xml($value, $subnode);
		            } else {
		                array_to_xml($value, $xml);
		            }
		        } else {
		            $xml->addChild("$key","$value");
		        }
		    }
		}
    }
    class ResponseType 
    {

    	const HTML= 0; // default
    	const TEXT = 1;
    	const JSON = 2;
    	const XML = 3;
    	const TERMINAL = 4;
    }
    class responseObject{

    	public $responseType;
    	public $responseData;
    	function __construct(){
    		$this->responseType = ResponseType::HTML;
    	}
    	function setResponseType($type){
    		$this->responseType = ResponseType::$type;
    	}
    	function getResponseType($type){
    		return $this->responseType;
    	}
    	function appendResponseData($data){
    		$this->responseData .=$data;
    	}
    	function setResponseData($data){
    		$this->responseData =$data;
    	}
    	function resetResponseData(){
    		$this->responseData = '';
    	}

    }

    // class htmlResponseObject extends responseObject{

    // 	function __construct(){
    // 		$this->responseType = ResponseType::HTML;
    // 	}

    // }
    // class jsonResponseObject extends responseObject{

    // 	function __construct(){
    // 		$this->responseType = ResponseType::JSON;
    // 	}
    	

    // }
    class RequestData{
    	/**
		* @var string The Requested URL
		*/
		public $url;
		/**
		* @var string Parent subdirectory of the URL
		*/
		public $basePath;
		/**
		* @var string Request method (GET, POST, PUT, DELETE)
		*/
		public $method;
		/**
		* @var string IP address of the client
		*/
		public $ip;
		/**
		* @var string Host Name of the Server
		*/
		public $host;
		/**
		* @var integer Port number of the Server
		*/
		public $port;
		
		function __Construct(){

			$this->url = self::getValue('REQUEST_URI');
			$this->basePath = self::getValue('REQUEST_URI');
			$this->method = self::getValue('REQUEST_METHOD');
			$this->ip = self::getValue('SERVER_ADDR');
			$this->host = self::getValue('SERVER_NAME');
			$this->port = self::getValue('SERVER_PORT');
		}

		function getValue($name,$default=null){
			return isset($_SERVER[$name])?$_SERVER[$name]:$default;
		}

		function isPost(){
			return (strtoupper($this->method) == 'POST' )?true:false;
		}

		function isGet(){
			return (strtoupper($this->method) == 'GET' )?true:false;
		}

		function isValid(){
			$userSys = new userSystem();
			return $userSys->validate();
		}
		
		function getAll()
		{
			return $_SERVER;
		}

    }