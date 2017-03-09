<?php

/**
* Http server
*/

$host = "127.0.0.1";
$port = 12345;

if(isset($_POST['command'])){
	$command = $_POST['command'];

	// Create socket
	$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
	$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");  

	// Execute command
	socket_write($socket, $command, strlen($command)) or die("Could not send data to server\n");
	$result = socket_read ($socket, 1024) or die("Could not read server response\n");
	
	socket_close($socket);
	
	echo $result;
}
?>