<?php 

	class admin_settingsController extends Controller
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
			$this->title="Fork PHP : Settings";
			
			$iniFiles=$modal->getInIFiles();
			$this->view['inifiles']=$iniFiles;
		}
		public function updateInIAction()
		{
			$modal = new Model_Admin();
			$isLogeedIn = $modal->validateLogin();
			if(!$isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'login'));
				$this->redirect($mvc,array('status'=>'-1'));
			}
			$file = $this->post("file");
			$content = $this->post("content");
			
			$res = $modal->updateFileContent($file,$content);
			echo $res;

		}
	}


