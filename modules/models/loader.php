<?php 

class Model_loader
{
	private $dbObj;
	function __construct()
	{
		
	}
	public function loadNavData()
	{
		$con = new MysqlConnection();
		$result = $con->select("SELECT * FROM nav_links");
		return $result;

	}


}

?>