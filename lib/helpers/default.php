<?php

/**
* Default helper class
* This file helps you to define some methods which you can use in all the places including Layouts.
*/
class DefaultHelper 
{
	
	function __construct()
	{
		
	}
	function currentUser()
	{
		return $_SESSION['user'];
	}
}