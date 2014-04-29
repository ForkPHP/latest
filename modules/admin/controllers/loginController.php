<?php 

	class admin_loginController extends Controller
	{
		public function __construct()
		{
			//echo "I am Controller Object<br/>";
			//echo $this->viewRequired=false;
		}
		public function indexAction()
		{
			$this->layoutRequired=false;
			$modal = new Model_Admin();
			$isLogeedIn = $modal->validateLogin();
			if($isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'home'));
				$this->redirect($mvc);
			}
			$status=$this->get('status');
			if($status==-1)
			{
				$this->view['has_error']=1;
			}
			$this->title="Login : Admin";
			
		}
		public function processloginAction()
		{
			
			$username=$this->post('username');
			$password=$this->post('password');
			$modal = new Model_Admin();
			$result =  $modal->processLogin($username,$password);
			if($result)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'home'));
				$this->redirect($mvc);
			}
			else
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'login'));
				$this->redirect($mvc,array('status'=>'-1'));
			}
			
		}
		

	}



?>
