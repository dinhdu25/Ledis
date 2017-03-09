<?php

include_once "ledis.php";

/**
* Socket server
*/

$host = "127.0.0.1";
$port = 12345;

set_time_limit(0);

// Create ledis object
$ledisObj = new ledis;

// Create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
socket_listen($socket) or die("Could not set up socket listener\n");

while(true){
	$client =  socket_accept($socket);
	
	// Read command
	$input =  socket_read($client, 1024000);
	
	// Execute command and return result
	$output = $ledisObj->commandParse($input);
	socket_write($client, $output, strlen ($output)) or die("Could not write output\n");

	socket_close($client);
}

socket_close($socket);
?>