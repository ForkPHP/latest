<?php 

class Model_fileManager
{
	private $dbObj;
	function __construct()
	{
		
	}
	public function createFile($folder,$file)
	{
		$libObj = new Fork_Lib();
		$isFile = $libObj->isFileExists($folder."/".$file);
		if($isFile)
		{
			return "File Already Exists";
		}
		else
		{
			$res = $libObj->createFile($folder,$file);
			if($res==true)
			{
				return "success";
			}
			else
			{
				return $res;
			}
		}
	}
	public function createFolder($path,$folder)
	{
		$libObj = new Fork_Lib();
		$isFile = $libObj->isFolderExists($path."/".$folder);
		if($isFile)
		{
			return "Folder Already Exists";
		}
		else
		{
			$res = $libObj->createFolder($path,$folder);
			if($res==true)
			{
				return "success";
			}
			else
			{
				return $res;
			}
		}
	}
	public function deleteFolder($path)
	{
		$libObj = new Fork_Lib();
		$isFile = $libObj->isFolderExists($path);
		if(!$isFile)
		{
			return "Folder Doesn't Exists";
		}
		else
		{
			$res = $libObj->deleteFolder($path);
			if($res==true)
			{
				return "success";
			}
			else
			{
				return $res;
			}
		}
	}
	


}
