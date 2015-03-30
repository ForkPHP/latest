<?php

class ForkRefresh{
	//private $paths = array();
	private $resolvers = array();

	/* Add path to the resolver, path value must match with jquery refresh plugin paths*/

	public function addPath($path,$method){
		
		//$this->paths[] = $path;
		$this->resolvers[$path] = $method;

	}
	public static function init(){
		return $GLOBALS['refreshObj'];
	}
	public function resolve($path,$data){

		if(isset($this->resolvers[$path])){   
			$response = $this->resolvers[$path]($data);
			return $response;
		}
		else{

			$forkObj = new Fork_Lib();

			$requestObj = new requestObject();
			unset($_POST['terminal']);
			$data = $requestObj->getPath("/".$GLOBALS['host'].$path,$GLOBALS['host']);
			print_r($_POST['terminal']);
			die;
		}

	}

}


$refreshObj = new ForkRefresh();

$refreshObj->addPath('/Noreload/index/index',function($data){
	
	return  (array("d"=> "hello eshant"));

});
$refreshObj->addPath('/Noreload/index/ajax',function($data){
	
	return  (array("d"=> "hello ajax eshant"));

});



$GLOBALS['refreshObj'] = $refreshObj;
?>