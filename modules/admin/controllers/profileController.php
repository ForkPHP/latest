<?php 

	class admin_profileController extends Controller
	{
		public function __construct()
		{
			
		}
		public function indexAction()
		{
			
			$modal = new Model_Admin();
			$isLogeedIn = $modal->validateLogin();
			if(!$isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'login'));
				$this->redirect($mvc,array('status'=>'-1'));
			}
			$this->layoutRequired=true;
			$this->viewRequired=true;
			$this->title="Fork PHP : User Profile";
			$userInfo = $modal->getCurrentUser();
			$this->view['profile']=$userInfo;
		}
		public function getProfileAction()
		{
			$modal = new Model_Admin();
			$isLogeedIn = $modal->validateLogin();
			if(!$isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'login'));
				$this->redirect($mvc,array('status'=>'-1'));
			}
			$userInfo = $modal->getCurrentUser();
			unset($userInfo['password']);
			echo json_encode($userInfo);
		}

	}



?>
