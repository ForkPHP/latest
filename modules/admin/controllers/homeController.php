<?php 

	class admin_homeController extends Controller
	{
		public function __construct()
		{
			//echo "I am Controller Object<br/>";
			//echo $this->viewRequired=false;
			$modal = new Model_Admin();
			$isLogeedIn = $modal->validateLogin();
			if(!$isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'login'));
				$this->redirect($mvc,array('status'=>'-1'));
			}

		}
		public function indexAction()
		{

			$this->layutRequired=true;

			$modal = new Model_Admin();
			$isLogeedIn = $modal->validateLogin();
			if(!$isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'login'));
				$this->redirect($mvc,array('status'=>'-1'));
			}
			//$this->layout="admin";
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
			$this->title="Fork PHP : Code Editor";
			$file = $this->get('edit');
			if (!empty($file))
			{
				$mod = explode("/", base64_decode(($this->get('edit'))));
				$files =array();
				$files[]=array('path'=>"/modules/".$mod[0]."/controllers/".$mod[1]."Controller.php",'name'=>$mod[1]."Controller_php",'ext'=>'php');
				$files[]=array('path'=>"/modules/".$mod[0]."/views/".$mod[1]."/".$mod[2].".phtml",'name'=>$mod[2]."_phtml",'ext'=>'phtml');
				$this->view['files']=$files;			
			}
			
		}
		public function logoutAction()
		{
			session_destroy();
			$mvc = new MVC(array('module'=>'admin','controller'=>'login'));
			$this->redirect($mvc);
		}
		public function loadFileAction()
		{
			$file = str_replace("//", "/", $this->post('url'));
		//	echo $file;
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$modal = new Model_Admin();
			$content = $modal->getFileContent($file);
			
			echo $content;
		}
		public function saveFileAction()
		{
			$file = $this->post('file');
			$content = $this->post('content');
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$modal = new Model_Admin();
			$res = $modal->setFileContent($file,$content);
			echo $res;
		}
		public function filemanagerAction()
		{
			$this->layoutRequired=false;
			//return $this->view();
		}
		public function modulemanagerAction()
		{
			
			$modal = new Model_Admin();
			$isLogeedIn = $modal->validateLogin();
			if(!$isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'login'));
				$this->redirect($mvc);
			}
			$this->title="Module Manager : fork PHP";
		}
		public function getModulesDataAction()
		{
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$mm = new ModuleManager($this->post('module'));
			echo json_encode($mm->getModuleContollers());

		}
		public function getControllersDataAction()
		{
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$module = $this->post('module');
			$controller = $this->post('controller');
			$pos = strpos($controller, "Controller");
			if($pos===false)
			{
				$controller.="Controller";
			}
			$mm = new ModuleManager($module);
			
			echo json_encode($mm->getContollerActions($module,$controller));
		}
		public function createModuleAction()
		{
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$module = $this->post("module");
			$isLayout = $this->post("layout");
			$isView = $this->post("view");
			$mm = new ModuleManager();
			$res = $mm->createModule($module,$isLayout,$isView);
			if($res !=1)
			{
				echo $res;
			}
			else
			{
				echo "Module Created Successfully";
			}
		}
		public function deleteModuleAction()
		{
			$module = $this->post("module");
			$mm = new ModuleManager();
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$res = $mm->deleteModule($module);
			if($res !=1)
			{
				echo $res;
			}
			else
			{
				echo "Module Deleted Successfully";
			}
		}
		public function createControllerAction()
		{
			$module = $this->post("module");
			$controller = $this->post("controller");
			$isView = $this->post("view");
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$mm = new ModuleManager();
			$res = $mm->createController($module,$controller,$isView);
			if($res !=1)
			{
				echo $res;
			}
			else
			{
				echo "Controller Created Successfully.";
			}
		}
		public function deleteControllerAction()
		{
			$module = $this->post("module");
			$controller = $this->post("controller");
			$mm = new ModuleManager();
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$res = $mm->deleteController($module,$controller);
			if($res !=1)
			{
				echo $res;
			}
			else
			{
				echo "Controller Deleted Successfully";
			}
		}
		public function createActionAction()
		{
			$module = $this->post("module");
			$controller = $this->post("controller");
			$action = $this->post("action");
			$isView = $this->post("view");
			$pos = strpos($controller, "Controller");
			if($pos===false)
			{
				$controller.="Controller";
			}
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$mm = new ModuleManager();
			$res = $mm->createAction($module,$controller,$action,$isView);
			if($res !=1)
			{
				echo $res;
			}
			else
			{
				echo "$action Action Created Successfully";
			}
		}
		public function docsAction()
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
			$methodObj = new docs_method();
			$methodObj->class_id = $classData->id;
			$methodData = $context->Get($methodObj)->toObject(false);
			$this->view['classData'] = $classData;
			$this->view['methodData'] = $methodData;
			}
			$classList = $context->Get(new docs_class())->toObject();
			$this->view['classList'] = $classList;
		}
		public function updatedocsAction(){
			$this->viewRequired= false;
			$this->layoutRequired = false;
			$type = $this->post('type');
			$context = new DbContext();
			if($type == 'class_desc'){
				$classObj = new docs_class();
				$classObj->id = $this->post('class_id');
				$classObj = $context->Get($classObj)->toObject();
				$classObj->description = $this->post('class_desc');
				$context->Update($classObj);
			}
			else if($type == 'method_desc'){
				$methodObj = new docs_method();
				$methodObj->id = $this->post('method_id');
				$methodObj->status = $this->post('method_status');
				//$methodObj = $context->Get($methodObj)->toObject();
				$methodObj->description = nl2br(htmlspecialchars($this->post('method_desc')));
				$context->Update($methodObj);
			}
			else if($type == 'methods'){
				$methods = $this->post('methods');
				
				foreach ($methods as $method) {
					$methodObj = new docs_method();
					$methodObj->id = $method['method_id'];
					$methodObj->status = $method['method_status'];
					//$methodObj = $context->Get($methodObj)->toObject();
					$methodObj->description = nl2br(htmlspecialchars($method['method_desc']));
					$context->Update($methodObj);
				}

			}

		}

	}


    