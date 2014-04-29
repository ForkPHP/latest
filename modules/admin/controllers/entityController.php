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
			if(!$isLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'login'));
				$this->redirect($mvc);
			}
			$mysqlLogeedIn = $modal->mysqlAuth();
			if($mysqlLogeedIn)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'entity','action'=>'index'));
				$this->redirect($mvc);
			}
			$status=$this->get('status');

			if($status==-1)
			{
				$this->view['has_error']=1;
			}
			$this->layoutRequired=true;
			$this->viewRequired=true;
			//$this->layout="news";
			//$this->layout="admin";
			//echo "hi";
			$this->title="Fork PHP : Entity Generator";
			
		}
		public function indexAction()
		{
			$modal = new Model_Admin();
			$isLogeedIn = $modal->mysqlAuth();
			if(!$isLogeedIn)
			{
				//print_r($isLogeedIn);die;
				$mvc = new MVC(array('module'=>'admin','controller'=>'entity','action'=>'login'));
				$this->redirect($mvc);
			}
			$db = $modal->getMysqlDbList();
			$this->view['dblist']=$db;
			$this->title="Generate Entity Classes";
		}
		public function mysqlloginAction()
		{
			$dbusername=$this->post('username');
			$dbpassword=$this->post('password');
			$modal = new Model_Admin();
			$result =  $modal->mysqlLogin($dbusername,$dbpassword);
			if($result)
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'entity','action'=>'index'));
				$this->redirect($mvc);
			}
			else
			{
				$mvc = new MVC(array('module'=>'admin','controller'=>'entity','action'=>'login'));
				$this->redirect($mvc,array('status'=>'-1'));
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
			echo $classes;		
		}
		public function dbmanagerAction()
		{
			$modal = new Model_Admin();
			$isLogeedIn = $modal->mysqlAuth();
			if(!$isLogeedIn)
			{
				//print_r($isLogeedIn);die;
				$mvc = new MVC(array('module'=>'admin','controller'=>'entity','action'=>'login'));
				$this->redirect($mvc);
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
			$db = $this->get("db");
			$table = $this->get("table");
			$id = $this->get("id");
			$modal = new Model_Admin();
			$res = $modal->deleteRow($db,$table,$id);
			if($res ==1)
			{
				$msg = base64_encode("Row Deleted Successfully.");
			}
			else
			{
				$msg=$res;
			}
			$mvc = new MVC(array('action'=>'dbmanager'));
			$this->redirect($mvc,array('db'=>$db,'table'=>$table,'msg'=>$msg));
		}
		public function getRowAction()
		{
			$db = $this->post("db");
			$table = $this->post("table");
			$id = $this->post("id");
			
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
			$cols = $this->post('cols');
			$cols = json_decode($cols);
			
			$db = $this->post('db');
			$id = $this->post('id');
			$table = $this->post('table');
			$modal = new Model_Admin();
			$res = $modal->updateRow($db,$table,$cols,$id);
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

	}


