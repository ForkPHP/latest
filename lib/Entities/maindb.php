<?php 

class nav_links extends EntityFramework
{
	private $id;
	private $text;
	private $url;
	private $bold_text;
	function __construct()
	{
		$class = __CLASS__;
		parent::__construct("maindb",$class);
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