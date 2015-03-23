<?php 
 
	class default_indexController extends Controller
	{ 
   		public function __construct()
		{
			
		}
		public function indexAction()
		{
			$this->title="Welcome to ".APP_NAME." : Home";
			
		}

		public function documentAction()
		{
			 $this->title = "Fork PHP : Documentation";

		}
		public function forkomAction()
		{
			$this->layoutRequired = false;
			 $this->title = "Fork PHP : Documentation";

		}
 		public function contactAction()
		{
			 $this->title = "Fork PHP : Contact US";
		}
		public function processContactAction()
		{
			@session_start();
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: Fork PHP Admin <admin@forkphp.com>' . "\r\n";
			$adminMessage .= "Hi Admin,<br/>New Query received at Fork PHP site.The details are :<br/> Email : ".$_POST['email']."<br/>";
				$adminMessage  .="Subject : ".$_POST["subject"]."<br/>";
				$adminMessage  .="Message : ".$_POST["message"]."<br/>";
			if(mail("admin@forkphp.com",$_POST["subject"],$adminMessage,$headers))
			{
				$_SESSION['success']=true;
				
				$message="Hi ".$_POST['name'].",<br/>";
				$message .="Thanks for Contacting Us. We will catch you back soon regarding your query.<br/><hr/><br/>";
				$message .= "Email : ".$_POST['email']."<br/>";
				$message .="Subject : ".$_POST["subject"]."<br/>";
				$message .="Message : ".$_POST["message"]."<br/>";
				mail($_POST['email'],"Fork PHP : Query Received",$message,$headers);
				$mvc = new MVC(array('module'=>'default','controller'=>'index','action'=>'contact'));
				$this->redirect($mvc);
			}
			else
			{
				$_SESSION['success']=false;
				$mvc = new MVC(array('module'=>'default','controller'=>'index','action'=>'contact'));
				$this->redirect($mvc);
			}
		}
		public function tutorialsAction()
		{
			$this->title = "Fork PHP : Tutorials";
		}
		public function downloadAction()
		{
			$this->title = "Fork PHP : Download Now";
		}
		public function classAction()
		{
			$classname = $this->get('classname');
			$this->title = "Fork PHP : ".$classname." Class";
			$context = new DbContext();
			if(!empty($classname)){


				/*$this->view['classname'] = $classname;
				$this->view['classData'] = $GLOBALS['DOC'][$classname];
				print_r($this->view['classData']);*/
				$classObj = new docs_class();
				$classObj->name = $classname;
				
				$classData = $context->Get($classObj)->toObject();
				//print_r($classData);die;
				$methodObj = new docs_method();
				$methodObj->class_id = $classData->id;
				$methodObj->status = 1;
				$methodData = $context->Get($methodObj)->toObject(false);
				
				$this->view['classData'] = $classData;
				$this->view['methodData'] = $methodData;
			}
			$classObj = new docs_class();
			$classObj->status = 1;
			$classList = $context->Get($classObj)->toObject();
			$this->view['classList'] = $classList;
			//print_r($classList);
			
		//	print_r($methodData);die;

		}
		public function getCountformSocialAction($url){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
			$data = curl_exec($curl);
			curl_close($curl);
			return $data;
		}
 
	}