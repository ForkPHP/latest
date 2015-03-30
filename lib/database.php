<?php if(!defined('APP_HOST')) die('Access restricted.');

class sys_config
{
	public function getConnection($db)
	{
		  $config =parse_ini_file($_SERVER['DOCUMENT_ROOT'].$GLOBALS['host']."/App/application.ini");
		  $host=$config["mysql.".$db.".host"];
		  $uname=$config["mysql.".$db.".username"];
		  $pass=$config["mysql.".$db.".password"];
		  $dbase=$config["mysql.".$db.".dbname"];
		  
		  try
		  {
			   $con = new PDO("mysql:host=$host;dbname=$dbase", $uname, $pass);
			   $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		  }
		  catch (PDOException $e) 
		  {
		  		return $e->getMessage();
		  }
		  return $con;
		  
	}
	public static function getDBConnection($db){
		$obj = new sys_config();
		return $obj->getConnection($db);
	}
}

class MysqlConnection
{
	private $db;
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
	public function insert($data)
	{
		
		$dbObj = new sys_config();
		$connectionObj = $dbObj->getConnection($this->db);
		$table=$data['table'];
		$prep = array();
		foreach($data['data'] as $k => $v )
		{
	  	  $prep[':'.$k] = $v;
		}
		$query = $connectionObj->prepare("INSERT INTO $table ( " . implode(', ',array_keys($data['data'])) . ") VALUES (" . implode(', ',array_keys($prep)) . ")");
		$query->execute($prep);	
	}
	public function select($data)
	{
		$dbObj = new sys_config();
		$connectionObj = $dbObj->getConnection($this->db);
		$table=$data['table'];
		$prep = array();
		$where=array();
		foreach($data['condition'] as $k => $v )
		{
	  	  $prep[':'.$k] = $v;
	  	  $where[] = $k."=".":".$k;
		}
		if(count($data['condition'])>0)
		$query = $connectionObj->prepare("SELECT * FROM $table WHERE " . implode(' AND ',array_values($where)) );
		else
		$query = $connectionObj->prepare("SELECT * FROM $table " );
		$query->execute($prep);
		$res=$query->fetchAll(PDO::FETCH_ASSOC);

		return $res;
	}
	public function delete($data)
	{
		$dbObj = new sys_config();
		$connectionObj = $dbObj->getConnection($this->db);
		$table=$data['table'];
		$where=array();
		foreach($data['condition'] as $k => $v )
		{
	  	  $prep[':'.$k] = $v;
	  	  $where[] = $k."=".":".$k;
		}
		$query = $connectionObj->prepare("SELECT * FROM $table WHERE " . implode(' AND ',array_values($where)) );
		$query->execute($prep);
		$res=$query->fetch(PDO::FETCH_ASSOC);
		$query = $connectionObj->prepare("DELETE FROM $table WHERE " . implode(' AND ',array_values($where)) );
		$query->execute($prep);	
		return $res;
	}
	public function update($udata)
	{
		$dbObj = new sys_config();
		$connectionObj = $dbObj->getConnection($this->db);
		$table=$udata['table'];
		$cond=array();
		$data=array();
		foreach($udata['data'] as $k => $v )
		{
	  	  $prep[':data'.$k] = $v;
	  	  $data[] = $k."=".":data".$k;
		}
		foreach($udata['condition'] as $k => $v )
		{
	  	  $prep[':cond'.$k] = $v;
	  	  $cond[] = $k."=".":cond".$k;
		}
		$query = $connectionObj->prepare("UPDATE $table SET " . implode(',',array_values($data))." WHERE ".implode(' AND ', array_values($cond)) );
		return $query->execute($prep);		
	}
}