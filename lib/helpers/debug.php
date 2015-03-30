<?php

/**
* Debug helper class
* This file provides debug info, like execution time & memory used
* You can get debug info by enabling it in App/application.ini
*/

class debugHelper 
{
	private $initialTime;
	private $initialMemory;
	function __construct()
	{
		$this->initialMemory = $this->getMemoryUses();
		$this->initialTime = $this->getCurrentTime();
	}

	function getMemoryUses($decimals = 2)
	{
	    $result = 0;

	    if (function_exists('memory_get_usage'))
	    {
	        $result = memory_get_usage() / 1024;
	    }

	    else
	    {
	        if (function_exists('exec'))
	        {
	            $output = array();

	            if (substr(strtoupper(PHP_OS), 0, 3) == 'WIN')
	            {
	                exec('tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output);

	                $result = preg_replace('/[\D]/', '', $output[5]);
	            }

	            else
	            {
	                exec('ps -eo%mem,rss,pid | grep ' . getmypid(), $output);

	                $output = explode('  ', $output[0]);

	                $result = $output[1];
	            }
	        }
	    }
	    return $result;
	}
	function getCurrentTime()
	{
		$mtime = microtime();
	    $mtime = explode(" ",$mtime);
	    $mtime = $mtime[1] + $mtime[0];
	    return $mtime;
	}
}