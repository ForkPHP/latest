<?php 

	class admin_classController extends Controller
	{
		public function __construct()
		{

		}
		public function classAction()
		{
			//print_r($this->get());
			$classname = $this->get('classname');
			
			print_r($GLOBALS['DOC'][$classname]);die;
		}

	}


