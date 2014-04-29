<?php 

class Model_Admin
{
	private $dbObj;
	function __construct()
	{
		
	}
	public function validateLogin()
	{
		// API access to get ini data
		$iniObj = new appConfig("application");
		$iniData= $iniObj->get();
		// API access to user Validation support ,1 as backend user
		$userObj = new userSystem($iniData['users.class'],1);
		return $userObj->validate();
	}
	public function processLogin($username,$password)
	{
		$iniObj = new appConfig("application");
		$iniData= $iniObj->get();
		// API access to user Validation support ,1 as backend user
		$userObj = new userSystem($iniData['users.class'],1);
		return $userObj->processLogin($username,$password);

	}
	public function mysqlLogin($username,$password)
	{
		$dbhost="localhost";

		//$db = $this->getMysqlDbList();
		
		$dbcnx = @mysql_connect($dbhost, $username, $password); 
		//print_r($dbcnx);die;
		if($dbcnx)
		{
			$_SESSION['mysql_status']=true;
			$_SESSION['mysql_username']=$username;
			$_SESSION['mysql_password']=$password;
			//print_r($_SESSION);die;
			return true;
		}
		else
		{
			$_SESSION['mysql_status']=false;
			return false;
		}
	}
	public function mysqlAuth()
	{
		//echo $_SESSION['mysql_status'];
		if(isset($_SESSION['mysql_status']))
		{
			if($_SESSION['mysql_status'])
			{
				//print_r($_SESSION);die;
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	public function getMysqlDbList()
	{
		$db=array();
		try
		{
			$dbhost="localhost";
			$dbcnx = mysql_connect($dbhost, $_SESSION['mysql_username'], $_SESSION['mysql_password']); 
			$result = @mysql_query('SHOW DATABASES'); 
			
			while ($row = mysql_fetch_assoc($result)) { 
			 $db[]=$row['Database'];
			}
			//$_SESSION['mysql_status']=true;
		}
		catch(Exception $e) 
		{
			//$_SESSION['mysql_status']=false;
			
		}
		return $db;
	}
	public function getEntityClass($db,$tables)
	{
		
		$tablist="";
		foreach ($tables as  $value)
		{
			$tablist .="'".$value."',";	
		}
		//$tablist = trim($tablist);
		$tablist = substr($tablist, 0,strlen($tablist)-1);
		$dbcnx = mysqli_connect("localhost", $_SESSION['mysql_username'], $_SESSION['mysql_password'],$db); 
		if (mysqli_connect_errno($dbcnx))
	   {
	  		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	   }
        $result = mysqli_query($dbcnx,"SELECT TABLE_NAME, GROUP_CONCAT( COLUMN_NAME ) AS COLUMNS FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$db."' AND TABLE_NAME IN (".$tablist.") GROUP BY TABLE_NAME");
        //echo "SELECT TABLE_NAME, GROUP_CONCAT( COLUMN_NAME ) AS COLUMNS FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$db."' AND TABLE_NAME IN (".$tablist.") GROUP BY TABLE_NAME";
        $rows=array();
        while($row = $result->fetch_assoc()) {
		  $rows[]=$row;
		}
       // $result = mysqli_fetch_assoc($rows);
        $tables = array();
        $file="";
        //print_r($rows);
        foreach ($rows as $row)
        {
        
        	$table=$row["TABLE_NAME"];
                  //  tables.Add(table);
            $tables[]=$table;
            $columns=explode(",", $row["COLUMNS"]);
            $cols = "";
            foreach($columns as $s)
            {
                $cols .= "private $".$s.";\n ";
            }
            //print_r($cols);
            $entity = "class ".$table." extends EntityFramework \n{\n ".$cols.'function __construct()'."\n{\n".' $class = __CLASS__;'."\n".'parent::__construct('."'".$db."'".',$class);'."\n}\n".' public function __get($property) {'."\n".'    if (property_exists($this, $property)) {'."\n".'      return $this->$property;'."\n    }\n".'  }'."\n".'  public function __set($property, $value) {'."\n".'    if (property_exists($this, $property)) {'."\n".'      $this->$property = $value;'."\n    }\n ".'   return $this;'."\n  }\n}";
          //  MessageBox.Show(entity);
            $file .= $entity."\n";
        }
        return sprintf($file);
	}
	public function getMysqlTableList($db)
	{
		$dbcnx = mysqli_connect("localhost", $_SESSION['mysql_username'], $_SESSION['mysql_password'],$db); 
		if (mysqli_connect_errno($dbcnx))
	   {
	  		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	   }
        $result = mysqli_query($dbcnx,"SELECT DISTINCT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$db."'");
        $rows=array();
        while($row = $result->fetch_assoc()) {
		  $rows[]=$row;
		}
        $tables = array();
        foreach ($rows as $row)
        {
        	$table=$row["TABLE_NAME"];
            $tables[]=$table;
        }
        return $tables;
	}	
	public function getInIFiles()
	{
		$config = new appConfig();
		$iniFiles = $config->getList();
		return $iniFiles;
	}
	public function getTableData($db,$table)
	{
		
	    $query= "SELECT * FROM $db.$table";
        $rows = $this->execQuery($db,$query);
        return $rows;
	}
	public function execQuery($db,$query)
	{
		$dbcnx = mysqli_connect("localhost", $_SESSION['mysql_username'], $_SESSION['mysql_password'],$db); 
		if (mysqli_connect_errno($dbcnx))
	    {
	  		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	    }
	    try
	    {
		    $result = mysqli_query($dbcnx,$query);
		    
		}
		catch(Exception $e)
    	{
    		return $e;
    	}
    	if(!$result)
    	{
    		return mysqli_error($dbcnx);
    	}
        $rows=array();
        while($row = $result->fetch_assoc()) {
		  $rows[]=$row;
		}
        return $rows;
    	
	}
	public function execNonQuery($db,$query,$prep)
	{
		try
		{
			//$dbcnx = mysqli_connect("localhost", $_SESSION['mysql_username'], $_SESSION['mysql_password'],$db); 
			$dbcnx = new PDO("mysql:host=localhost;dbname=$db", $_SESSION['mysql_username'], $_SESSION['mysql_password']);
			$dbcnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$query = $dbcnx->prepare($query);
			return $query->execute($prep);	
		}
		catch(Exception $e)
		{
			return $e;
		}

	}
	public function getTableSchema($db,$table)
	{
		$query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$db."' AND TABLE_NAME = '".$table."'";
		$rows = $this->execQuery($db,$query);
		$columns=array();
        foreach ($rows as $row) 
        {
        	$columns[] = $row['COLUMN_NAME'];
        }
        return $columns;
	}
	public function insertRow($db,$table,$data)
	{
		$prep=array();
		$data = (array)$data;
		foreach($data as $k => $v )
		{
	  	  $prep[':'.$k] = $v;
		}
		
		$query ="INSERT INTO $table ( " . implode(', ',array_keys($data)) . ") VALUES (" . implode(', ',array_keys($prep)) . ")";
		//return $query;
		return $this->execNonQuery($db,$query,$prep);
	}
	public function deleteRow($db,$table,$id)
	{
		$prep=array();
		$prep[':'."id"] = $id;
	  	$where[] = "id"."=".":"."id";
		$query = "DELETE FROM $table WHERE " . implode(' AND ',array_values($where)) ;
		return $this->execNonQuery($db,$query,$prep);
	}
	public function getRow($db,$table,$id)
	{
		$query= "SELECT * FROM $db.$table WHERE id=$id";
        $rows = $this->execQuery($db,$query);
        return $rows[0];
	}
	public function updateRow($db,$table,$rdata,$id)
	{
		$prep=array();
		$rdata = (array)$rdata;
		foreach($rdata as $k => $v )
		{
	  	  $prep[':data'.$k] = $v;
	  	  $data[] = $k."=".":data".$k;
		}
		$prep[':cond'."id"] = $id;
	  	$cond[] = "id=:condid";
		$query ="UPDATE $table SET " . implode(',',array_values($data))." WHERE ".implode(' AND ', array_values($cond)) ;
		return $this->execNonQuery($db,$query,$prep);
	}
	public function createTable($db,$query)
	{
		$prep=array();
		return $this->execNonQuery($db,$query,$prep);
	}
	public function dropTable($db,$table)
	{
		$prep=array();
		$query="DROP TABLE $db.$table";
		return $this->execNonQuery($db,$query,$prep);
	}
	/* User Profile Methods*/
	public function getCurrentUser()
	{
		$username = $_SESSION['user'];
		$userObj = new users();
		$userObj->username=$username;
		$dbCon = new DbContext();
		$userInfo = $dbCon->Get($userObj)->ToArray();
		$userprofileObj = new user_profile();
		$userprofileObj->user_id=$userInfo['id'];
		$profileInfo = $dbCon->Get($userprofileObj)->ToArray();
		$userInfo = array_merge($userInfo,$profileInfo);
		return $userInfo;	
	}
	public function updateFileContent($file,$content)
	{
		$libObj = new Fork_Lib();
		$file = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS['host']."/App/".$file;
		$res = $libObj->updateFileContent($file,$content);

	}
}

?>