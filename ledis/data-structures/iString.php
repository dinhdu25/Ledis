<?php

include_once "iObject.php";

/**
* String
*/
class iString extends iObject{
	private $_str = "";
	
	function set($str){
		$this->_str = $str;
		
		// Unset value if overwriting other type
	}
	
	function get(){
		return $this->_str;
	}
}
?>