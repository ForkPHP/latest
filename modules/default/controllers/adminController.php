<?php 

	class default_adminController extends Controller
	{
		public function __construct()
		{
			//echo "I am Controller Object<br/>";
			//echo $this->viewRequired=false;
		}
		public function indexAction()
		{
			$this->viewRequired=false;

			$this->view['obj']="test";
			$this->view['name']=array("eshant","sahu");	
		}
		public function learnAction()
		{
			$this->viewRequired=true;

			$this->view['obj']="test";
			$this->view['name']=array("eshant","sahu");	
		}

	}



?>