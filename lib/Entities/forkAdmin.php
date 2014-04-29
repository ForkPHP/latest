<?php 

class users extends EntityFramework
{
	private $id;
	private $username;
	private $password;
  private $role;
  private $token;
	function __construct()
	{
		$class = __CLASS__;
		parent::__construct("forkAdmin",$class);
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

    return $this;
  }
	
}
class user_profile extends EntityFramework 
{
 private $id;
 private $name;
 private $user_id;
 private $img_url;
 function __construct()
{
 $class = __CLASS__;
parent::__construct('forkAdmin',$class);
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
    return $this;
  }
}

?>