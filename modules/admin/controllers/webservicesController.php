<?php 

	class admin_webservicesController extends Controller
	{
		public function __construct()
		{
			//echo "I am Controller Object<br/>";
			//echo $this->viewRequired=false;
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
			$webservice = new webservice();
			$this->view['classes']=$webservice->getClasses();
			$this->title="Webservices : Fork PHP";
		}
		public function generateformAction()
		{
			$ins = new webservice();
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$data = explode("#", $this->post("data"));
			//print_r($_POST);
			$class =  $data[0];
			$method = $data[1];
			$r = new ReflectionMethod($class, $method);
            $params = $r->getParameters();
            $controller = str_replace("Controller","",str_replace("webservice_", "", $class));
            $action = str_replace("Action", "", $method);
            $thisParams = "This Method accepts ".count($params)." Paraneters: <br/><br/> ".implode("<br/> ", $params);
            $html  = "<div class='formContainer col-md-12 box box-primary'><div class='box-header' style='cursor: move;'><h3 class='box-title'>webservice/".$controller."/".$action."</h3><div class='box-tools pull-right'><button data-original-title='Collapse' class='btn btn-default btn-sm btn-box-collapse' data-widget='collapse' data-toggle='tooltip' ><i class='fa fa-minus'></i></button><button data-original-title='Remove' class='btn btn-default btn-sm btn-box-remove' data-widget='remove' data-toggle='tooltip' ><i class='fa fa-times'></i></button></div></div><div class='box-body'><form method='POST' target='_blank' action='".$this->appBase()."/webservice/".$controller."/".$action."'><hr/><p>".$thisParams."</p>";
            foreach ($params as $param) 
            {
            	$paramName = $param->getName();
            	$html.="<div class='form-group'><input type='text' class='form-control' name=$paramName placeholder=$paramName /></div>";
            }
            $html.="<input type='submit' class='btn btn-primary' value='Submit'></form></div></div>";
            echo $html;
		}
		function createAction()
		{
			$data = $this->post('data');
			$name = $data['name'];
			$params = $data['params'];
			$params_default = $data['params_default'];
		}
		

	}
