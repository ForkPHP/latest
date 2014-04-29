<?php
 class defaultlayoutController
 {
 	function __construct()
 	{

 	}
 	public function test()
 	{
 		return array("test success here ","hu");
 	}
 	public function getHeaderLinks()
 	{/*
 		$loaderObj= new Model_loader();
 		$data=$loaderObj->loadNavData();	
 		$libObj = new Fork_Lib();
		$controller = $libObj->getControllerName();
		switch ($controller) 
		{
			case 'index':
				$data[0]['current']=1;
				break;
			case 'about':
				$data[1]['current']=1;
				break;
			case 'experiments':
				$data[2]['current']=1;
				break;
			
			case 'blog':
				$data[3]['current']=1;
				break;
			case 'contact':
				$data[4]['current']=1;
				break;
			
		}
		return $data;*/
 	}
 }
?>