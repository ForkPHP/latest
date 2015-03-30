<?php 

	class admin_fileManagerController extends Controller
	{
		public function __construct()
		{

		}
		public function addfileAction()
		{
			$folder = $this->post('folder');
			$file = $this->post('name');

			$modal = new Model_fileManager();
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$folder =$_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host'].$folder;
			$res = $modal->createFile($folder,$file);
			if($res=="success")
			{
				echo "File Created Successfully";
			}
			else
			{
				echo $res;
			}
		}
		public function addfolderAction()
		{
			$path = $this->post('folder');
			$name = $this->post('name');
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$modal = new Model_fileManager();
			$path =$_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host'].$path;
			$res = $modal->createFolder($path,$name);
			if($res=="success")
			{
				echo "Folder Created Successfully";
			}
			else
			{
				echo $res;
			}
		}
		public function delFolderAction()
		{
			$path = $this->post('folder');
			$this->viewRequired=false;
			$this->layoutRequired=false;
			$path =$_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host'].$path;
			$modal = new Model_fileManager();
			$res = $modal->deleteFolder($path);
			if($res=="success")
			{
				echo "Folder Deleted Successfully";
			}
			else
			{
				echo $res;
			}
		}

	}


