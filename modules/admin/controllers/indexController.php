<?php 

	class admin_indexController extends Controller
	{
		public function __construct()
		{
			
		}
		public function indexAction()
		{
			$mvc = new MVC(array('action'=>'index','controller'=>'login'));
			$this->redirect($mvc);
		}
		

	}
