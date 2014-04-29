<?php 

	class admin_homeController extends Controller
	{
		public function __construct()
		{
			//echo "I am Controller Object<br/>";
			//echo $this->viewRequired=false;
		}
		public function indexAction()
		{
			//$this->viewRequired=tr;
			$modal = new Model_Admin();
			$isLogeedIn = $modal->validateLogin();
			if(!$isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'login'));
				$this->redirect($mvc,array('status'=>'-1'));
			}
			$this->layoutRequired=true;
			$this->viewRequired=true;
			//$this->layout="news";
			$this->layout="admin";
			//echo "hi";
			$this->title="Fork PHP : Home";
			
		}
		public function editorAction()
		{
			$modal = new Model_Admin();
			$isLogeedIn = $modal->validateLogin();
			if(!$isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'login'));
				$this->redirect($mvc);
			}
			$this->layoutRequired=true;
			$this->viewRequired=true;
			//$this->layout="news";
			$this->layout="admin";
			//echo "hi";
			$this->title="Fork PHP : Home";
		}
		public function logoutAction()
		{
			session_destroy();
			$mvc = new MVC(array('module'=>'admin','controller'=>'login'));
			$this->redirect($mvc);
		}
		

	}



?>
