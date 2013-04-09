<?php

// Initialize OAuth server & store
require_once 'oauth-php/library/OAuthServer.php';
require_once 'oauth-php/library/OAuthStore.php';

//ITKS545-server:
//OAuthStore::instance('MySQL', array(
//	'server'   => 'localhost',
//	'username' => 'toarjusa',
//	'password' => 'mysql',
//	'database' => 'toarjusa'
//));

// Bela
OAuthStore::instance('MySQL', array(
	'server'   => 'localhost',
	'username' => 'itks545',
	'password' => 'itks545',
	'database' => 'itks545_oauth'
));

?>
