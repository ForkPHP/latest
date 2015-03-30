<?php if(!defined('APP_HOST')) die('Access restricted.');

class requestObject // acting as requestObject
{
	private $forkObj;
	private $errorObj;
	public  $responseType;
	public function __construct()
	{
		$appObj = new appConfig();
		$GLOBALS['config']=$appObj->get();
		
		$this->responseType = ResponseType::HTML;
		if(!defined('APP_NAME'))
		define('APP_NAME', $GLOBALS['config']['application']);
	}
	public function createHeader($title)
	{
	 	$header ="<head>";
	 	$header .="<title>".$title."</title>";
	 	$header .="</head>";

	 	return $header;
	}
	private function generateRegex($key)
	{  
		$regArr = array();
		$key = str_replace("?", "/", $key);
		$key = str_replace("&", "/", $key);
		$key = str_replace("=", "/", $key);
		$segments = explode("/", $key);
		//print_r($segments);
		$segmentsReg = array();
		foreach ($segments as $index) {
			if($index == "{:action}"){
				$segmentsReg[]="(?<:action>\S{1,})";
				continue;
			}
			else if(preg_match("/{(?<param>\S{1,}):num}$/", $index,$arr)){
				
				$segmentsReg[] = "(?<".substr($index, 1,strlen($index)-2).">\d{1,})";
				continue;
			}
			else if(preg_match("/{(?<param>\S{1,}):str}$/", $index,$arr)){
				
				$segmentsReg[] = "(?<".substr($index, 1,strlen($index)-2).">\S{1,})";
				continue;
			}
			else
			{
				$segmentsReg[] = $index;
			}
		}
		$key="";
		//print_r($segmentsReg);
		foreach ($segmentsReg as $seg) {
			if(!empty($seg)){
				//echo "seg:".$seg;
				$key .= "\/".$seg;
			}
		}
		//$key = implode("\/", $segmentsReg);
	   $key = "/^".$key."$/";
	   $key = str_replace(":", "_", $key);
	    return $key;
			
	}
	public function getPath($url,$host)
	{
		
		@session_start();
		set_error_handler('parse_error',E_USER_ERROR);
		// Consideration of fine requests only
		$absUrl = $url;
		$index = strpos($url,"?");
		if($index>0)
		{
			$getArray = substr($url,$index+1);
			$getArray = str_replace("=", "/", $getArray);
			$getArray = str_replace("&", "/", $getArray);
			$_GET['params']="MOD/CON/ACT/".$getArray;
			//echo $getArray;
			$absUrl = $url;
			$url = substr($url,0,$index);
		}
		else
		{
			//echo $host."<br/>".$url;
			$getArr = explode("/", ltrim(str_replace(strtolower($host), "", strtolower($url)),'/'));
			
			$_GET['params'] = implode("/", $getArr);
			/*if(count($getArr)>2){
				for ($ii=3; $ii < count($getArr); $ii+2) { 
					# code...
					//$_GET[$getArr[$ii]] = isset($getArr[$ii+1])?$getArr[$ii+1]:"";
				}
			}*/

		}
		

		/*else 
		$_GET['params']= "";*/
		//echo $GLOBALS['host'];
		$count = count(explode('/',$GLOBALS['host']));
		//echo $count;
		$url = ltrim($url,'/');

		/**
		 *	Check for magic paths & rerouting
		 */

		$router = include_once "routes.php"; /* get magic path array */
		$mapIndex = str_replace(ltrim($GLOBALS['host'],'/'),'',$absUrl);
		//echo $absUrl;
		$mapIndex = str_replace("?", "/", $mapIndex);
		$mapIndex = str_replace("&", "/", $mapIndex);
		$mapIndex = str_replace("=", "/", $mapIndex);
		$mapIndex = str_replace("//", "/", $mapIndex);
		//echo $mapIndex;die;
		/**
		* Checking for a existing mapping in magic routes
		*/
		//print_r($router);die;

		$router = (array)$router;
		foreach ($router as $key => $value) {	
			$reg =  $this->generateRegex($key);
			// echo "regex : ".$reg;  // reinitialize path mapping. 
			
			// echo "map: ".$mapIndex;
			// die();
			if(preg_match($reg,$mapIndex,$regOut))
			{
				foreach ($regOut as $segmentKey => $segmentsVal) {
					$value = str_replace("{".str_replace("_", ":", $segmentKey)."}", $segmentsVal, $value);
				}
			//	echo "valup : ".$value;
			//	die;
				$pathObj = new requestObject();
				$pathObj->getPath("/".$GLOBALS['host'].$value,$GLOBALS['host']); 
				//echo $value;
				die;
			}
			else
			{
				$reg =  $this->generateRegex($value);
				/*echo "oo:".$reg;
				echo "mi: ".$mapIndex;*/
				if(preg_match($reg,$mapIndex,$regOut))
				{
					//echo ":kk";die;
					foreach ($regOut as $segmentKey => $segmentsVal) {
						$key = str_replace("{".str_replace("_", ":", $segmentKey)."}", $segmentsVal, $key);
					}
					header("Location: ".$GLOBALS['host'].$key);
				}


			}

		}
		
			$path_arr= explode("/",$url);
		
				$count--;
			
			
			$pathObj= array();
			$pathObj['host']=$GLOBALS['host'];
			$pathObj['module']=isset($path_arr[$count])&&$path_arr[$count]!=""?$path_arr[$count]:"default";
			$pathObj['controller']=isset($path_arr[$count+1])&&$path_arr[$count+1]!=""?$path_arr[$count+1]:"index";
			$pathObj['action']=isset($path_arr[$count+2])&&$path_arr[$count+2]!=""?$path_arr[$count+2]:"index";
			//print_r($pathObj);
			$info = array("module"=>$pathObj['module'],"controller"=>$pathObj['controller'],"action"=>$pathObj['action']);
			$GLOBALS['tracK_info']=$info;
			//echo $_SERVER['DOCUMENT_ROOT'].$host."/modules/".$pathObj['module'];die();
	    	if(is_dir($_SERVER['DOCUMENT_ROOT'].$host."/modules/".$pathObj['module']))
			{
	    		if(file_exists($_SERVER['DOCUMENT_ROOT'].$host."/modules/".$pathObj['module']."/controllers/".$pathObj['controller']."Controller.php"))
				{
					
		    		include $_SERVER['DOCUMENT_ROOT'].$host."/modules/".$pathObj['module']."/controllers/".$pathObj['controller']."Controller.php";
			    	//echo "Controller exists";
	    	    	if (class_exists($pathObj['module'].'_'.$pathObj['controller'].'Controller'))
			    	{
			    		/*--- Validate mainDB existance ---*/
			    		$dbObj = sys_config::getDBConnection("maindb");
			    		if(!is_object($dbObj)){
			    			$GLOBALS['errordata']['db']=array('name'=>"maindb",'message'=>$dbObj);
			    			trigger_error("DATABASE_EXCEPTION",E_USER_ERROR);
			    		}
						$controllerClass=$pathObj['module'].'_'.$pathObj['controller'].'Controller';
			    		$GLOBALS['forkobj']=array();
			    		$GLOBALS['forkobj']['module']=$pathObj['module'];
			    		$GLOBALS['forkobj']['controller']=$pathObj['controller'];
			    		$GLOBALS['forkobj']['action']=$pathObj['action'];
			    		$GLOBALS['host']=$host;
			    		//print_r($GLOBALS['forkobj']);die();
			    		if(!method_exists($controllerClass,$pathObj['action']."Action" ))
			    		{
		    				/*$errorObj= new Exceptions("UNDEFINED_ACTION_EXCEPTION",$info);
							return $errorObj->throwException();*/
							trigger_error("UNDEFINED_ACTION_EXCEPTION",E_USER_ERROR); //Action Undefined
			    		}
			    		$layout =parse_ini_file($_SERVER['DOCUMENT_ROOT'].$host."/App/application.ini");
			    		
			    		$layout=isset($layout["layouts"][$pathObj['module']])?$layout["layouts"][$pathObj['module']]:"default";
			    		
			    		$controllerObj = new $controllerClass();
			    		

			    		$controllerObj->setLayout(); // Important to call to read application.ini for layout of that module

			    		$action=$pathObj['action']."Action";
			    		
			    		//if($pathObj['module']=="webservice")
			    		//{
			    			//print_r($_POST);die;
			    			//$webservice = new webservice();
			    			$r = new ReflectionMethod($pathObj['module']."_".$pathObj['controller']."Controller", $pathObj['action']."Action");
	                		$params = $r->getParameters();
	                		$strArr =array();
	                		foreach ($params as $param) {
	                			$postVar = isset($_POST[$param->getName()])?$_POST[$param->getName()]:"";
	                			$strArr[]="'".$postVar."'";
	                		}
	                		$str=implode(",", $strArr);
	                		$str = '$controllerObj->$action('.$str.");";
	                		ob_start();
	                		eval($str);
	                		$actionResponse =  ob_get_clean();

			    			//$controllerObj->$action();
			    		//}
			    		/*else
			    		{
			    			$controllerObj->$action();
			    		}*/
			    		if(isset($_POST['terminal']) && $_POST['terminal'] == 1){
			    			$refreshObj = ForkRefresh::init();
			    			$libObj = new Fork_Lib();
			    				$resolvePath = '/'.$libObj->getModuleName().'/'.$libObj->getControllerName().'/'.$libObj->getActionName();
			    			if(isset($this->resolvers[$resolvePath])){
			    				echo "here";die;
			    				$controllerObj->responseType = ResponseType::TERMINAL;	
			    				

			    			}else{
			    				$controllerObj->layoutRequired = false;
			    			}
			    		}
			    			
			    		if($controllerObj->responseType == ResponseType::HTML /*|| $controllerObj->responseType == ResponseType::TERMINAL*/){
			    		//	print_r($controllerObj);die;
			    			echo $actionResponse;
				    		if($controllerObj->layoutRequired)
				    		{
				    			$layout=$controllerObj->layout;
				    			//echo $layout;die;
				    			
			    				if(file_exists($_SERVER['DOCUMENT_ROOT'].$host."/layouts/".$layout.".phtml"))
				    			{
				    				
				    				$path=$_SERVER['DOCUMENT_ROOT'].$host."/layouts/".$layout.".phtml";
				    				//create layout 
				    				$layoutObj = new Layout($controllerObj,$path);		    			
				    			}
				    			else
				    			{
						    		/*$errorObj= new Exceptions("1005",$layout);
				    				return $errorObj->throwException();// missing layout*/
				    				$GLOBALS['errordata']['layout']=$layout;
				    				trigger_error("MISSING_LAYOUT_FILE",E_USER_ERROR);

				    			}
				    		}
				    		else
				    		{

					    		if($controllerObj->viewRequired)
					    		{

					    			if(file_exists($_SERVER['DOCUMENT_ROOT'].$host."/modules/".$pathObj['module']."/views/".$pathObj['controller']."/".$pathObj['action'].".phtml"))
					    			{
					    				
					    				$path=$_SERVER['DOCUMENT_ROOT'].$host."/modules/".$pathObj['module']."/views/".$pathObj['controller']."/".$pathObj['action'].".phtml";
					    				//create view 
					    				$viewObj = new View($controllerObj,$path);		    
					    				

					    				
					    			}
					    			else
					    			{
							    		/*$errorObj= new Exceptions("1004",$info);
					    				return $errorObj->throwException();// missing view*/
					    				trigger_error("MISSING_VIEW_FILE",E_USER_ERROR); // missing view
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
										trigger_error("INVALID_VIEW_PARAMS",E_USER_ERROR); 
									}

					    		}
				    		}
			    		}
			    		elseif($controllerObj->responseType == ResponseType::TERMINAL){
			    			$libObj = new Fork_Lib();
			    			$resolvePath = '/'.$libObj->getModuleName().'/'.$libObj->getControllerName().'/'.$libObj->getActionName();
			    			$refreshObj = ForkRefresh::init();
			    			header('Content-Type: application/json; charset=utf8');
			    			echo json_encode(array('title'=>$controllerObj->title,'response'=>$refreshObj->resolve($resolvePath,$_POST)));
			    		}
			    		elseif($controllerObj->responseType == ResponseType::JSON){
			    			header('Content-Type: application/json; charset=utf8');
			    			echo json_encode($controllerObj->responseObject->responseData);
			    			
			    		}
			    		elseif($controllerObj->responseType == ResponseType::XML){
			    			print_r($actionResponse);
			    			
			    		}


			    	}
			    	else
			    	{
			    		trigger_error("CONTROLLER_CLASS_EXCEPTION",E_USER_ERROR); // Controller Class not found
			    	}
				}	
				else
		    	{
		    		trigger_error("UNDEFINED_CONTROLLER_EXCEPTION",E_USER_ERROR);//undefined Controller
		    	}
			}	
			else
	    	{
	    		trigger_error("UNDEFINED_MODULE_EXCEPTION",E_USER_ERROR);
	    	}
	    
		
	}
}