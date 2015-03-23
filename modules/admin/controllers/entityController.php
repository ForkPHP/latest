<?php 

	class admin_entityController extends Controller
	{
		public function __construct()
		{

		}
		public function loginAction()
		{
			
			$modal = new Model_Admin();
			$isLogeedIn = $modal->validateLogin();  // Admin login verification
			$return = base64_decode($this->get('return'));
			if(!$isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'login'));
				$this->redirect($mvc);
			}
			
			$mysqlLogeedIn = $modal->mysqlAuth();
			if($mysqlLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'entity','action'=>'index'));
				$this->redirect($return);
			}
			else
			{
				$app = new appConfig("application");
				 $config = $app->get();
				$this->view['db'] = $config['db'];

			}
			$status=$this->get('status');
			if($status==-1)
			{
				$this->view['has_error']=1;
			}
			$this->layoutRequired=true;
			$this->viewRequired=true;
			$this->title="Fork PHP : Entity Generator";
			
		}
		public function indexAction()
		{
			$modal = new Model_Admin();
			$isLogeedIn = $modal->mysqlAuth();
			if(!$isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'entity','action'=>'login'));
				$this->redirect($mvc,array('return'=>base64_encode($this->appBase().'/admin/entity/index')));
			}
			$db = $modal->getMysqlDbList();
			$this->view['dblist']=$db;
			$this->title="Generate Entity Classes";
		}
		public function mysqlloginAction()
		{
			$dbusername=$this->post('username');
			$dbpassword=$this->post('password');
			$isDbname = $this->post('login_db');
			//echo $isDbname;die;
			$return = base64_decode($this->post('return'));
			if($isDbname !="0")
			{
				$app = new appConfig("application");
				 $config = $app->get();
				$dbusername = $config["mysql.".$isDbname.".username"];
				$dbpassword = $config["mysql.".$isDbname.".password"];
			}
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$modal = new Model_Admin();
			$result =  $modal->mysqlLogin($dbusername,$dbpassword);
			if($result==1)
			{	if(!empty($return))
				{
					
					$this->redirect($return);
				}
				else
				{
				
					$mvc = new MVC(array('module'=>'admin','controller'=>'entity','action'=>'index'));
					$this->redirect($mvc);
				}
			}
			else
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'entity','action'=>'login'));
				$this->redirect($mvc,array('status'=>'-1','message'=>$result));
			}
			
		}
		public function logoutAction()
		{
			unset($_SESSION['mysql_status']);
			unset($_SESSION['mysql_usename']);
			unset($_SESSION['mysql_password']);
			$mvc = new MVC(array('module'=>'admin','controller'=>'entity','action'=>'login'));
			$this->redirect($mvc);
		}
		public function getTablesAction()
		{
			//print_r($_GET);
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$db = $this->get("dbName");
			$modal = new Model_Admin();
			$dblist = $modal->getMysqlTableList($db);
			echo json_encode($dblist);

		}
		public function generateEntityAction()
		{
			$this->viewRequired=false;
			$this->layoutRequired=false;
			//print_r($this->get());
			$db = $this->post("dbName");
			$tables = $this->post("tables");
			
			$modal = new Model_Admin();
			$classes = $modal->getEntityClass($db,$tables);		
			echo trim($classes);		
		}
		public function dbmanagerAction()
		{
			$modal = new Model_Admin();
			$isLogeedIn = $modal->mysqlAuth();
			if(!$isLogeedIn)
			{
				//print_r($isLogeedIn);die;
				$mvc = new MVC(array('module'=>'admin','controller'=>'entity','action'=>'login'));
				$this->redirect($mvc,array('return'=>base64_encode($this->appBase().'/admin/entity/dbmanager')));
			}
			$db = $modal->getMysqlDbList();
			$this->title="DB Manager : Fork PHP";
			$this->view['dblist']=$db;
			$db = $this->get("db");
			$table=$this->get("table");
			if(!empty($db))
			{
				$this->view['tables'] = $modal->getMysqlTableList($db);
				$this->view['current']=$db;
			}
			if(!empty($table))
			{
				$tabData = $modal->getTableData($db,$table);
				$tabCols = $modal->getTableSchema($db,$table);
				$this->view['tabledata']=array($table,$tabData,$tabCols);
				
			}

		}
		public function insertRowAction()
		{
			$cols = $this->post('cols');
			$cols = json_decode($cols);
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$db = $this->post('db');
			$table = $this->post('table');
			$modal = new Model_Admin();
			$res = $modal->insertRow($db,$table,$cols);
			if($res==1)
			{
				echo "New Row Inserted.";
			}
			else
			{
				echo $res;
			}
		}
		public function deleteRowAction()
		{
			$data = $this->post("data");
			$table = $this->post("table");
			$db = $this->post("db");
			$data=json_decode($data);
			$modal = new Model_Admin();
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$res = $modal->deleteRow($db,$table,$data);
			if($res ==1)
			{
				$msg = "Row Deleted Successfully.";
			}
			else
			{
				$msg=$res;
			}
			echo $msg;
		}
		public function getRowAction()
		{
			$db = $this->post("db");
			$table = $this->post("table");
			$id = $this->post("id");
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$modal = new Model_Admin();
			$res = $modal->getRow($db,$table,$id);
			$values=array();
			foreach ($res as $key => $value) {
				$values[]=$value;
			}
			echo json_encode($values);
		}
		public function updateRowAction()
		{
			$data = $this->post('data');
			$data = (array)json_decode($data);
			$actual=$data['actual'];
			$updated=$data['updated'];
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$db = $this->post('db');
			$table = $this->post('table');
			$modal = new Model_Admin();
			$res = $modal->updateRow($db,$table,$actual,$updated);
			if($res==1)
			{
				echo "Row Updated.";
			}
			else
			{
				echo $res;
			}
		}
		public function createTableAction()
		{
			$db = $this->post('db');
			$query = $this->post('table');
			$modal = new Model_Admin();
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$res = $modal->createTable($db,$query);
			if(!is_object($res) && $res==1)
			{
				echo "Table Created.";
			}
			else
			{
				echo $res->getMessage();
			}
		}
		public function dropTableAction()
		{
			$db = $this->post('db');
			$table = $this->post('table');
			$modal = new Model_Admin();
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$res = $modal->dropTable($db,$table);
			if(!is_object($res) && $res==1)
			{
				echo "Table $table has been Droped.";
			}
			else
			{
				echo $res->getMessage();
			}
		}
		public function execQueryAction()
		{
			$db = $this->post('db');
			$query = $this->post('query');
			$modal = new Model_Admin();
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$res = $modal->execQuery($db,$query);
			if(is_array($res))
			{
				$arr=array();
				$arr['error']=0;
				$arr['data']=$res;
				echo json_encode($arr);
			}
			else
			{
				$arr = array();
				$arr['error']=1;
				$arr['msg']=$res;
				echo json_encode($arr);

			}
		}
		public function saveEntityAction()
		{
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$db = $this->post('dbName');
			$code = '<?php '."\n".trim($this->post('code'));
		//	file_put_contents($path."/controllers/".$controller."Controller.php",str_replace("{{code}}", $code, ) );
			$res = file_put_contents($_SERVER['DOCUMENT_ROOT'].'/'.$GLOBALS['host'].'/lib/Entities/'.$db.'.php', $code);
			echo $res;
		}

	}


