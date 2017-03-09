<?php

include_once "iObject.php";

/**
* Set
* add, remove, contain: O(1)
*/
class iSet extends iObject{
	public $_values;
	private $_length = 0;
	
	function __construct(){
		$_values = array();
	}
	
	function sadd($values){
		$numAdd = 0;
		for($i = 0; $i < count($values); $i++){
			$key = json_encode($values[$i]);
			if(!isset($this->_values[$key])){
				$this->_values[$key] = $values[$i];
				$numAdd++;
				$this->_length++;
			}
		}
		return $numAdd;
	}
	
	function scard(){
		return $this->_length;
	}
	
	function smembers(){
		$values = array();
		foreach($this->_values as $key=>$value){
			$values[] = $value;
		}
		return $values;
	}
	
	function srem($values){
		$numRem = 0;
		for($i = 0; $i < count($values); $i++){
			$key = json_encode($values[$i]);
			if(isset($this->_values[$key])){
				unset($this->_values[$key]);
				$this->_length--;
				$numRem++;
			}
		}
		return $numRem;
	}
	
	// Intersect value of sets
	public static function sinner($lvalues){
		return call_user_func_array('array_intersect',$lvalues);
	}
}
?>