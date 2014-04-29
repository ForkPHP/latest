
<?php 
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
	public function updateFileContent($file,$content)
	{
		try
		{
			file_put_contents($file, $content);
			return 1;
		}
		catch(Exception $e)
		{
			return $e->getMessage();
		}
	}

}
class appConfig
{
	private $ini;
	function __construct($ini=null)
	{
		if($ini !=null)
		$this->ini = $ini;
		
	}
	function get()
	{
		$host=$GLOBALS['host'];
		return parse_ini_file($_SERVER['DOCUMENT_ROOT']."/".$host."/App/".$this->ini.".ini");
	}
	function getList()
	{
		$path = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/App";
		$dir_handle = @opendir($path) or die("Unable to open $path");
             
              //Leave only the lastest folder name
      $dirname = @end(explode("/", $path));
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
	private $info;
	private $exceptions;
	function __construct($type,$info)
	{
		$this->type=$type;
		$this->info=$info;
		$host=$GLOBALS['host'];
		$this->exceptions = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/".$host."/App/exceptions.ini");
	}
	function throwException()
	{

		switch($this->type)
		{
			case "1000":
						return sprintf($this->exceptions['exception_'.$this->type],$this->type,$this->info['module']);
						break;
			case "1002":
						return sprintf($this->exceptions['exception_'.$this->type],$this->type,$this->info['controller']);
						break;	
			case "1003":
						return sprintf($this->exceptions['exception_'.$this->type],$this->type,$this->info['action'],$this->info['controller']);
						break;	
			case "1004":
						return sprintf($this->exceptions['exception_'.$this->type],$this->type,$this->info['action']);
						break;	
			case "1005":
						return sprintf($this->exceptions['exception_'.$this->type],$this->type,$this->info);
						break;
			case "1007":
							return sprintf($this->exceptions['exception_'.$this->type],$this->type,$this->info);
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
		if(isset($_SESSION['user']) && isset($_SESSION['token']))
		{
			
			$context = new DbContext();
			$class=$this->class;
			$user = new $class();
			$user->username=$_SESSION['user'];
			$user->role = $this->role;
			
			$row = $context->Get($user)->toArray();
			//print_r($row);
			if( count($row) >0 && isset($row['token']) && $row['token']==$_SESSION['token'])
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
				$_SESSION['user']=$username;
				$token = uniqid() . '_' . md5(mt_rand());
				$_SESSION['token']=$token;
				$user->token=$token;
				// update token in DB
				
				$context->Update($user);
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