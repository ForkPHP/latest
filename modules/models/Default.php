<?php 

class Model_Default
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
		// API access to user Validation support ,0 as front user
		$userObj = new userSystem($iniData['users.class'],0);

		$res =$userObj->validate();
		return $res;
	}
	public function processLogin($username,$password)
	{
		$iniObj = new appConfig("application");
		$iniData= $iniObj->get();
		// API access to user Validation support ,1 as backend user
		$userObj = new userSystem($iniData['users.class'],0);
		return $userObj->processLogin($username,$password);
	}
	function signup($name,$email,$password)
	{
		$iniObj = new appConfig("application");
		$iniData= $iniObj->get();
		$userObj = new userSystem($iniData['users.class'],0);
		return $userObj->add($name,$email,$password);
	}
	


}

?>