<?php

include_once "iObject.php";

/**
* Double Linked List
*/
class Node{
	var $_data;
	var $_prev = null;
	var $_next = null;
	
	function __construct($data){
		$this->_data = $data;
	}
}

class iList extends iObject{
	private $_length = 0;
	private $_head;
	private $_tail;
	
	// Return length
	function llen(){
		return $this->_length;
	}
	
	// Append value
	function rpush($values){
		for($i = 0; $i < count($values); $i++){
			$newNode = new Node(($values[$i]));

			if($this->_length > 0){
				$this->_tail->_next = $newNode;
				$newNode->_prev = $this->_tail;
				$this->_tail = $newNode;
			}
			else{
				// Create new list
				$this->_head = $newNode;
				$this->_tail = $newNode;
			}
			
			$this->_length++;
		}
		
		return $this->_length;
	}
	
	// Remove and return the first item
	function lpop(){
		if($this->_length == 0)
			return "ERROR: null";
		else if($this->_length == 1){
			$tempNode = $this->_head;
			$this->_head = null;
			$this->_tail = null;
			
			$this->_length--;
			
			return $tempNode->_data;
		}
		else{
			$tempNode = $this->_head;
			$this->_head = $tempNode->_next;
			$this->_head->_prev = null;
			$tempNode->_next = null;
			
			$this->_length--;
			
			return $tempNode->_data;
		}			
	}
	
	// Remove and return the last item
	function rpop(){
		if($this->_length == 0)
			return "ERROR: null";
		else if($this->_length == 1){
			$tempNode = $this->_tail;
			$this->_head = null;
			$this->_tail = null;
			
			$this->_length--;
			
			return $tempNode->_data;
		}
		else{
			$tempNode = $this->_tail;
			$this->_tail = $tempNode->_prev;
			$this->_tail->_next = null;
			$tempNode->_prev = null;
			
			$this->_length--;
			
			return $tempNode->_data;
		}
	}
	
	// Return a range from $start to $stop element
	function lrange($start, $stop){
		$values = array();
		$tempNode = $this->_head;
		
		if($start >= 0 && $start <= $stop && $stop < $this->_length){
			for($i = 0; $i < $start; $i++){
				$tempNode = $tempNode->_next;
			}

			for($i = $start; $i <= $stop; $i++){
				$values[] = $tempNode->_data;
				$tempNode = $tempNode->_next;
			}
		}
		
		return $values;
	}
}
?>