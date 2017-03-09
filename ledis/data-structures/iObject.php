<?php

/**
* Data Expiration 
*/
class iObject{
	private $_seconds = 0;
	private $_timestamp;
	
	//set time out of key
	function expire($seconds){
		$this->_seconds = $seconds;
		$date = new DateTime();
		$this->_timestamp = $date->getTimestamp();
		return $this->_seconds;
	}
	
	//query timeout
	function ttl(){
		$date = new DateTime();
		$timeout = $this->_timestamp + $this->_seconds - $date->getTimestamp() ;
		return $timeout;
	}
}
