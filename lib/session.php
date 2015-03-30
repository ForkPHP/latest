<?php if(!defined('APP_HOST')) die('Access restricted.');

class Session
{
	
	public function __construct($db="maindb")
	{
		$this->db=$db;
	}
	
	public function fetchAll($query)
	{
		$dbObj = new sys_config();
		$connectionObj = $dbObj->getConnection($this->db);
		$result = mysqli_query($connectionObj,$query);
		$data_array = array();
		while ($array = mysqli_fetch_assoc($result))
		{
			$data_array[] = $array;
		}
		return $data_array;
	}
}