<?php 


class docs_class extends EntityFramework 
{
 private $id;
 private $name;
 private $description;
 private $properties;
 private $parent_class;
 private $modifiers;
 private $file_name;
 private $status;
 function __construct()
{
 $class = __CLASS__;
parent::__construct('maindb',$class);
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
class book extends EntityFramework 
{
 private $id;
 private $name;
 private $category;
 function __construct()
{
 $class = __CLASS__;
parent::__construct('maindb',$class);
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
class docs_method extends EntityFramework 
{
 private $example;
 private $code;
 private $parameters;
 private $description;
 private $class_id;
 private $id;
 private $name;
 private $class_name;
 private $status;
 function __construct()
{
 $class = __CLASS__;
parent::__construct('maindb',$class);
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
class users extends EntityFramework 
{
 private $token;
 private $role;
 private $password;
 private $username;
 private $id;
 private $cdate;
 function __construct()
{
 $class = __CLASS__;
parent::__construct('maindb',$class);
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
 private $img_url;
 private $user_id;
 private $name;
 private $last_name;
 private $id;
 private $first_name;
 function __construct()
{
 $class = __CLASS__;
parent::__construct('maindb',$class);
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