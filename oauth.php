<?php

// Initialize OAuth server & store
require_once 'oauth-php/library/OAuthServer.php';
require_once 'oauth-php/library/OAuthStore.php';

// Toni:
//OAuthStore::instance('MySQL', array(
//	'server'   => 'localhost',
//	'username' => 'itks545',
//	'password' => 'itks545',
//	'database' => 'itks545_oauth'
//));

//ITKS545-server:
//OAuthStore::instance('MySQL', array(
//	'server'   => 'localhost',
//	'username' => 'itks545',
//	'password' => 'itks545',
//	'database' => 'itks545_oauth'
//));

// Bela
OAuthStore::instance('MySQL', array(
	'server'   => 'localhost',
	'username' => 'itks545',
	'password' => 'itks545',
	'database' => 'itks545_oauth'
));

?>
