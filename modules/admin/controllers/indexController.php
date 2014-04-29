<?php 

	class admin_indexController extends Controller
	{
		public function __construct()
		{
			//echo "I am Controller Object<br/>";
			//echo $this->viewRequired=false;
		}
		public function indexAction()
		{
			$mvc = new MVC(array('action'=>'index','controller'=>'login'));
			$this->redirect($mvc);

		}
		

	}



?>
