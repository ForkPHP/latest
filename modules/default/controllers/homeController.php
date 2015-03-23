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
			
			
		}
		public function invalidAction()
		{
			
		}
		public function logoutAction()
		{
			session_destroy();
			$mvc = new MVC(array('module'=>'default','controller'=>'index'));
			$this->redirect($mvc);
		}
		public function getProfileAction()
		{
			$modal = new Model_Default();
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$isLogeedIn = $modal->validateLogin();
			if(!$isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'default','controller'=>'login'));
				$this->redirect($mvc,array('status'=>'-1'));
			}
			$userInfo = $modal->getCurrentUser();
			unset($userInfo['password']);
			echo json_encode($userInfo);

		}
	
		
	}
