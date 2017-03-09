<?php

include_once "data-structures/iString.php";
include_once "data-structures/iList.php";
include_once "data-structures/iSet.php";

/**
* Ledis
* in-memory data structure store 
*/
class ledis{
	private $_data;
	private $_lastestSnap;
	
	function __construct(){
		$this->_data = array();
	}

	function keys(){
		$lstKeyAvailable = array();
		$numKey = 0;
		foreach($this->_data as $key=>$value){
			if($value->ttl() >= 0){
				$lstKeyAvailable[] = $key;
				$numKey++;
			}
		}
		if($numKey == 0) 
			return "NULL";
		return implode(" ", $lstKeyAvailable);
	}
	
	function del($key){
		if(isset($this->_data[$key])){
			unset($this->_data[$key]);
			return "OK";
		}
		else return "ERROR: key not found";
	}
	
	function flushdb(){
		foreach($this->_data as $key=>$value){
			if(isset($this->_data[$key])){
				unset($this->_data[$key]);
			}
		}
		return "OK";
	}
	
	function save(){
		$ser = serialize($this->_data);
		$date = new DateTime();
		$fileName = "snapshot-file/".$date->getTimestamp().".json";
		file_put_contents($fileName, $ser);
		$this->_lastestSnap = $fileName;
		
		return "OK";
	}
	
	function restore(){
		$fileName = $this->_lastestSnap;
		if($fileName){
			$ser = file_get_contents($fileName);
			$unser = unserialize($ser);
			$this->_data = $unser;
			
			return "OK";
		}
		else{
			return "ERROR: snapshot not found";
		}
	}

	// Parse command, execute and return response
	function commandParse($command){
		$res = "";
		
		// Get arguments from command
		$commandTrim = trim(preg_replace('/\s\s+/', ' ', $command));
		$argvs = explode(" ", $commandTrim);
		
		if(count($argvs) > 0){
			$opcode = strtoupper($argvs[0]);
			switch ($opcode){
				
				// String
				case "SET":
					if(count($argvs) != 3) 
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						$value = new iString;
						$this->_data[$key] = $value;
						$value->set($argvs[2]);
						$res = "OK";
					}
					break;
					
				case "GET":
					if(count($argvs) != 2)
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						if(isset($this->_data[$key])){
							$value = $this->_data[$key];
							if($value instanceof iString){
								$res = $value->get();
							}
							else{
								$res = "ERROR: wrong type";
							}	
						}
						else{
							$res = "ERROR: key not found";
						}
					}
					break;
				
				// List
				case "LLEN":
					if(count($argvs) != 2)
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						if(isset($this->_data[$key])){
							$value = $this->_data[$key];
							if($value instanceof iList){
								$res = $value->llen();
							}
							else{
								$res = "ERROR: wrong type";
							}
						}
						else{
							$res = "ERROR: key not found";
						}
					}
					break;
					
				case "RPUSH":
					if(count($argvs) < 3)
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						$values = array();
						
						for($i = 2; $i < count($argvs); $i++){
							$values[] = $argvs[$i];
						}
						
						if(isset($this->_data[$key])){
							$value = $this->_data[$key];
							if($value instanceof iList){
								$res = $value->rpush($values);
							}
							else{
								$res = "ERROR: wrong type";
							}
						}
						else{
							$value = new iList;
							$this->_data[$key] = $value;
							$res = $value->rpush($values);
						}
					}
					break;
					
				case "LPOP":
					if(count($argvs) != 2)
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						if(isset($this->_data[$key])){
							$value = $this->_data[$key];
							if($value instanceof iList){
								$res = $value->lpop();
							}
							else{
								$res = "ERROR: wrong type";
							}	
						}
						else{
							$res = "ERROR: key not found";
						}
					}
					break;
					
				case "RPOP":
					if(count($argvs) != 2)
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						if(isset($this->_data[$key])){
							$value = $this->_data[$key];
							if($value instanceof iList){
								$res = $value->rpop();
							}
							else{
								$res = "ERROR: wrong type";
							}	
						}
						else{
							$res = "ERROR: key not found";
						}
					}
					break;
					
				case "LRANGE":
					if(count($argvs) != 4)
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						$start = $argvs[2];
						$stop = $argvs[3];
						if(isset($this->_data[$key])){
							$value = $this->_data[$key];
							if($value instanceof iList && is_numeric($start) && is_numeric($stop)){
								$results = $value->lrange($start, $stop);
								if(count($results) == 0)
									$res = "NULL";
								else
									$res = implode(" ", $results);
							}
							else{
								$res = "ERROR: wrong type";
							}	
						}
						else{
							$res = "ERROR: key not found";
						}
					}
					break;
					
				// Set	
				case "SADD":
					if(count($argvs) < 3)
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						$values = array();
						
						for($i = 2; $i < count($argvs); $i++){
							$values[] = $argvs[$i];
						}
						
						if(isset($this->_data[$key])){
							$value = $this->_data[$key];
							if($value instanceof iSet){
								$res = $value->sadd($values);
							}
							else{
								$res = "ERROR: wrong type";
							}							
						}
						else{
							$value = new iSet;
							$res = $value->sadd($values);
							$this->_data[$key] = $value;
						}
					}
					break;
					
				case "SCARD":
					if(count($argvs) != 2)
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						if(isset($this->_data[$key])){
							$value = $this->_data[$key];
							if($value instanceof iSet){
								$res = $value->scard();
							}
							else{
								$res = "ERROR: wrong type";
							}
						}
						else{
							$res = "ERROR: key not found";
						}
					}
					break;
				
				case "SMEMBERS":
					if(count($argvs) != 2)
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						if(isset($this->_data[$key])){
							$value = $this->_data[$key];
							if($value instanceof iSet){
								$results = $value->smembers();
								if(count($results) == 0)
									$res = "NULL";
								else
									$res = implode(" ", $results);
							}
							else{
								$res = "ERROR: wrong type";
							}
							
						}
						else{
							$res = "ERROR: key not found";
						}
					}
					break;
				
				case "SREM":
					if(count($argvs) < 3)
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						$values = array();
						
						for($i = 2; $i < count($argvs); $i++){
							$values[] = $argvs[$i];
						}
						
						if(isset($this->_data[$key])){
							$value = $this->_data[$key];
							if($value instanceof iSet){
								$res = $value->srem($values);
							}
							else{
								$res = "ERROR: wrong type";
							}	
						}
						else{
							$res = "ERROR: key not found";
						}
					}
					break;
				
				case  "SINNER":
					if(count($argvs) < 3)
						$res = "ERROR: wrong number arguments";
					else{
						$values = array();
						
						// Check value of keys are set
						for($i = 1; $i < count($argvs); $i++){
							$key = $argvs[$i];
						
							if(isset($this->_data[$key])){
								$value = $this->_data[$key];
								if($value instanceof iSet){
									$values[] = $value->_values;
								}
								else{
									$res = "ERROR: wrong type";
									break;
								}						
							}
							else{
								$res = "ERROR: key not found";
								break;
							}
						}
						
						if(count($values) == count($argvs)-1){
							$results = iSet::sinner($values);
							if(count($results) == 0)
								$res = "NULL";
							else
								$res = implode(" ", $results);
						}
							
					}
					break;
				
				// Data Expiration
				case "KEYS":
					if(count($argvs) != 1)
						$res = "ERROR: wrong number arguments";
					else
						$res = $this->keys();
					break;
				
				case "DEL":
					if(count($argvs) != 2)
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						$res = $this->del($key);
					}
					break;
				
				case "FLUSHDB":
					if(count($argvs) != 1)
						$res = "ERROR: wrong number arguments";
					else
						$res = $this->flushdb();
					break;
				
				case "EXPIRE":
					if(count($argvs) != 3)
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						$seconds = $argvs[2];
						if(isset($this->_data[$key])){
							$value = $this->_data[$key];
							if($value instanceof iObject){
								if(is_numeric($seconds) && ($seconds > 0)){
									$res = $value->expire($seconds);
								}
								else {
									$res = "ERROR: must be positive integer";
								}
							}
							else{
								$res = "ERROR: wrong type";
							}
							
						}
						else{
							$res = "ERROR: key not found";
						}
					}
					break;
				
				case "TTL":
					if(count($argvs) != 2)
						$res = "ERROR: wrong number arguments";
					else{
						$key = $argvs[1];
						if(isset($this->_data[$key])){
							$value = $this->_data[$key];
							if($value instanceof iObject){
								$timeout = $value->ttl();
								if($timeout > 0){ 
									$res = $timeout;
								}
								else{
									$res = "-2";
								}
									
							}
							else{
								$res = "ERROR: wrong type";
							}						
						}
						else{
							$res = "ERROR: key not found";
						}
					}
					break;
				
				// Snapshot
				case "SAVE":
					if(count($argvs) != 1)
						$res = "ERROR: wrong number arguments";
					else
						$res = $this->save();
					break;
				
				case "RESTORE":
					if(count($argvs) != 1)
						$res = "ERROR: wrong number arguments";
					else
						$res = $this->restore();
					break;
				
				default:
					$res = "ERROR: command not found";
			}
			
			return $res;
		}
	}
}
?>