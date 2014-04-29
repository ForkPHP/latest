<?php 

	class default_homeController extends Controller
	{
		public function __construct()
		{

		}
		public function indexAction()
		{
			//$this->viewRequired=tr;
			$modal = new Model_Default();
			$isLogeedIn = $modal->validateLogin();
			$this->title="Fork PHP : Home";
			if(!$isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'default','controller'=>'accounts'));
				$this->redirect($mvc,array('status'=>'-1'));
			}
			$this->layoutRequired=true;
			$this->viewRequired=true;
			//$this->layout="news";
			$this->layout="default";
			//echo "hi";
			//$this->title="Login : Front End";
			
			
		}
		public function logoutAction()
		{
			session_destroy();
			$mvc = new MVC(array('module'=>'default','controller'=>'index'));
			$this->redirect($mvc);
		}
		
		
	}


