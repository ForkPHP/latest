<?php 

	class default_accountsController extends Controller
	{
		public function __construct()
		{

		}
		public function indexAction()
		{
			$mvc = new MVC(array("module"=>"default","controller"=>"accounts","action"=>"login"));

			$this->redirect($mvc,$this->get());
		}
		public function loginAction()
		{
			$this->layout="defaultfront";
			$modal = new Model_Default();
			$isLogeedIn = $modal->validateLogin();
			//echo $isLogeedIn;die;
			if($isLogeedIn==1)
			{
				$mvc = new MVC(array('module'=>'default','controller'=>'home','action'=>'index'));
				$this->redirect($mvc);
			}
			$status=$this->get('status');

			if($status==-1)
			{
				$this->view['has_error']=1;
			}
			$this->title="Login : Front End";
		}
		public function processloginAction()
		{
			
			$username=$this->post('username');
			$password=$this->post('password');
			//echo $username.$password;die;
			$modal = new Model_Default();
			$result =  $modal->processLogin($username,$password);
			//echo $result;die;
			if($result)
			{
				$mvc = new MVC(array('module'=>'default','controller'=>'home'));
				$this->redirect($mvc);
			}
			else
			{
				$mvc = new MVC(array('module'=>'default','controller'=>'accounts'));
				$this->redirect($mvc,array('status'=>'-1'));
			}
			
		}
		public function createAction()
		{
			$this->layout="defaultfront";
			$this->title="Crete New Account : Front End";
			$msg = $this->get("msg");

			if(!empty($msg))
			$this->view['msg']=$msg;
		}
		public function processSignupAction()
		{
			$name=$this->post("name");
			$email=$this->post("email");
			$password=$this->post("password");
			$repassword=$this->post("repassword");
			$mvc = new MVC(array('action'=>'create'));

			$msg="";
			if(empty($password))
			{
				$msg .= "Password field can't be empty.<br/>";
				echo $msg;
			}
			if(empty($email))
			{
				$msg .= "Please enter your Email.<br/>";
			}
			if(empty($name))
			{
				$msg .= "Name can't be empty.<br/>";
			}
			
			if($password!=$repassword)
			{
				$msg .= "Password does't matched.<br/>";
			}
			
			if(!empty($msg))
			{

				$msg = base64_encode($msg);
				$this->redirect($mvc,array('msg'=>$msg));

			}
			else
			{
				$modal = new Model_Default();
				$res = $modal->signup($name,$email,$password);
				if($res=="success")
				{
					$mvc = new MVC(array('action'=>'login'));
					$this->redirect($mvc,array('msg'=>base64_encode("Account Created Successfully. Please login to continue.")));
				}
				else
				{
					$msg = "User already exists with this Email.";
					$msg = base64_encode($msg);
					$this->redirect($mvc,array('msg'=>$msg));
				}
			}

		}
		
	}


