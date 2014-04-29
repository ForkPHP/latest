<?php 

class EntityFramework
{
	private $table;
	protected $db;
	function __construct($db,$table)
	{
		$this->table=$table;
		$this->db=$db;
	}
}
class Entity
{
 private $class;
 private $data;
 function __construct($object)
 {
 	if(is_array($object))
 	{
 		if(count($object)>0)
 		{
	 		$class=get_class($object[0]);
	 		$this->class=$class;
	 		$this->data=$object;
 		}
 	}
 }
 public function __get($property) {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  public function __set($property, $value) {
    if (property_exists($this, $property)) {
      $this->$property = $value;
    }
  }
 function where($cond)
 {
 	
 	$data=array();
 	foreach ($this->data as $obj) {
 		$condition = 'if('.$cond.'){return 1;}';
 		$condition = eval($condition);
 		if($condition)
 		{
 			$data[]=$obj;
 		}
 	}
 	$entityObj= new Entity($data);
 	return $entityObj;
 }
 function toObject()
 {
 	$data = array();

 	foreach ($this->data as $row) {
 		$data[]=$row;
 	}
 	if(count($data)==1)
 	{
 		$data= $data[0];
 	}
 	return $data;
 }
 function toArray()
 {
 	$data = array();
 	if(count($this->data)>0)
 	{
	 	foreach ($this->data as $row) {
	 		$class = $this->class;
			$reflection = new ReflectionClass($class);
			$objRow=array();
			foreach( $reflection -> getProperties(ReflectionProperty::IS_PRIVATE) as $field )
			{
			    $field_name = $field->name;
			    $field_val=$row->$field_name;
			    if($row->$field_name!="")
			    {
			    	$objRow[$field_name]=$field_val;
			    }
			}
	 		$data[]=$objRow;
	 	}
 	}
 	if(count($data)==1)
 	{
 		$data=$data[0];
 	}
 	return $data;
 }
}
class DbContext 
{

	function Add($object)
	{
		$reflection = new ReflectionClass(get_class($object));
		$data=array();
		foreach( $reflection -> getProperties(ReflectionProperty::IS_PRIVATE) as $field )
		{
		    $field_name = $field->name;
		    $field_val=$object->$field_name;
		    $data[$field_name]=$field_val;
		}
		$rowData= array("table"=>get_class($object),"data"=>$data);
		$con = new MysqlConnection($object->db);
		$con->insert($rowData);	
	}
	function Get($object)
	{
		$class = get_class($object);
		$reflection = new ReflectionClass($class);
		$data=array();
		foreach( $reflection -> getProperties(ReflectionProperty::IS_PRIVATE) as $field )
		{
		    $field_name = $field->name;
		    $field_val=$object->$field_name;
		    //echo $field_name."_".$field_val."_".$object->$field_name."<br>";
		    if(isset($field_val))
		    {
		    	$data[$field_name]=$field_val;
		    }
		}
		$rowData= array("table"=>$class,"condition"=>$data);
		
		$con = new MysqlConnection($object->db);
		
		$data=$con->select($rowData);
			//print_r($data);
			$objArray=array();
			foreach ($data as $var) {
				$obj = new $class();
				foreach( $reflection -> getProperties(ReflectionProperty::IS_PRIVATE) as $field )
				{
				    $field_name = $field->name;
				    $obj->$field_name=$var[$field_name];
				}
				$objArray[]=$obj;
			}
			$entityObj=new Entity($objArray);
			return $entityObj;
		
		
	}
	
	function Delete($object)
	{
		$class = get_class($object);
		$reflection = new ReflectionClass($class);
		$data=array();
		$con = new MysqlConnection($object->db);
		foreach( $reflection -> getProperties(ReflectionProperty::IS_PRIVATE) as $field )
		{
		    $field_name = $field->name;
		    $field_val=$object->$field_name;
		    if($object->$field_name!="")
		    {
		    	$data[$field_name]=$field_val;
		    }
		}
		$rowData= array("table"=>$class,"condition"=>$data);
		$obj = new $class();
		$result = $con->delete($rowData);
		foreach( $reflection -> getProperties(ReflectionProperty::IS_PRIVATE) as $field )
		{
		    $field_name = $field->name;
		    $obj->$field_name=$result[$field_name];
		}
		return $obj;
	}
	function Update($object)
	{
		$class = get_class($object);
		$reflection = new ReflectionClass($class);
		$data=array();
		$con = new MysqlConnection($object->db);
		foreach( $reflection -> getProperties(ReflectionProperty::IS_PRIVATE) as $field )
		{
		    $field_name = $field->name;
		    $field_val=$object->$field_name;
		    if($object->$field_name!="")
		    {
		    	$data[$field_name]=$field_val;
		    }
		}
		$rowData= array("table"=>$class,"data"=>$data,"condition"=>array("id"=>$object->id));
		$con->update($rowData);
		
	}
}


?>