<?php 

	class default_indexController extends Controller
	{
		public function __construct()
		{
			//echo "I am Controller Object<br/>";
			//echo $this->viewRequired=false;
		}
		public function indexAction()
		{
			//$this->viewRequired=true;
			//$this->layoutRequired=true;
			//$this->layout="news";
			$this->layout="defaultfront";
			//echo "hi";
			$this->title="Welcome to Fork PHP : Home";
			//$this->view->as="Eshant Sahdu";
			//$this->view['obj']="test";
			//$this->view['name']=array("eshant","sahu");	
			//$this->layoutRequired=false;
			
		}
		public function testAction()
		{
			$this->layoutRequired=false;
			$this->viewRequired=false;
			echo "action".$_POST['program'];
		//	print_r($_POST);
		}
		
	}



?>
