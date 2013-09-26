<?php

function opbeat_autoload($classname) {
	$fullpath = str_replace('_', DIRECTORY_SEPARATOR, $classname);
	$fullpath = strtolower($fullpath) . '.php';
	require $fullpath;
}

spl_autoload_register('opbeat_autoload');

$opbeat_config = array(
	'organization_id' 	=> '',
	'application_id'	=> '',
	'secret_token' 		=> ''
);

$client = new Opbeat_Client($opbeat_config['organization_id'], $opbeat_config['application_id'], $opbeat_config['secret_token']);
$handler = new Opbeat_Handler();
$handler->addClient($client);
$handler->registerErrorHandler();
$handler->registerExceptionHandler();

// example of a uncatched exception.
function giveMeAbitOfStack() {
	throw new Exception('hello opbeat catch me!');
}

giveMeAbitOfStack();