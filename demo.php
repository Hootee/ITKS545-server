<?php

// Initialize OAuth server & store
require_once 'oauth-php/library/OAuthServer.php';
require_once 'oauth-php/library/OAuthStore.php';
OAuthStore::instance('MySQL', array(
	'server'   => 'localhost',
	'username' => 'itks545',
	'password' => 'itks545',
	'database' => 'itks545_oauth'
));


// Import the Slim Framework
require "Slim/Slim.php";
\Slim\Slim::registerAutoloader();


// Initiate API object
// $API will be referencing to the Slim Framework object
$API = new \Slim\Slim();


// Define paths
// This is a GET path to example.com/register
// Registers a new consumer app
$API->get('/register', function () {
	$user_id = 1;
	// This should come from a form filled in by the requesting user
	$consumer = array(
	    // These two are required
	    'requester_name' => 'John Doe',
	    'requester_email' => 'john@example.com'
	
	    // These are all optional
	    /*'callback_uri' => 'http://example.com/oauth_callback',
	    'application_uri' => 'http://example.com/',
	    'application_title' => 'Example consumer app',
	    'application_descr' => 'To test out the server',
	    'application_notes' => '',
	    'application_type' => 'website',
	    'application_commercial' => 0*/
	);
	// Register the consumer
	$store = OAuthStore::instance(); 
	$key   = $store->updateConsumer($consumer, $user_id);
	// Get the complete consumer from the store
	$consumer = $store->getConsumer($key, 3);

	// The tokens
	$tokens = array(
		'consumer_key'    => $consumer['consumer_key'],
		'consumer_secret' => $consumer['consumer_secret']
	);
	// Set content type to JSON
    header("Content-Type: application/json");
    // Output JSON
	echo json_encode($tokens);
});


// This is a GET path to example.com/request_token
$API->get('/request_token', function () {
	$server = new OAuthServer();
	$server->requestToken();
});


// This is a GET path to example.com/access_token
$API->get('/access_token', function () {
	$server = new OAuthServer();
	$server->accessToken();
});


// This is a GET path to example.com/authorize
$API->get('/authorize', function () {
	// The current user
	$user_id = 1;
	
	// Fetch the oauth store and the oauth server.
	$store  = OAuthStore::instance();
	$server = new OAuthServer();
	
	try {
	    // Check if there is a valid request token in the current request
	    // Returns an array with the consumer key, consumer secret, token, token secret and token type.
	    $rs = $server->authorizeVerify();
	
	    if ($_SERVER['REQUEST_METHOD'] == 'GET') // CHANGE THIS TO POST
	    {
	        // See if the user clicked the 'allow' submit button (or whatever you choose)
	        //$authorized = array_key_exists('allow', $_POST);
			$authorized = true;

	        // Set the request token to be authorized or not authorized
	        // When there was a oauth_callback then this will redirect to the consumer
	        $verifier = $server->authorizeFinish($authorized, $user_id);
                echo $verifier;
	        // No oauth_callback, show the user the result of the authorization
	        // ** your code here **
	   }
	} catch (OAuthException $e) {
	    // No token to be verified in the request, show a page where the user can enter the token to be verified
	    // **your code here**
	}
});


// This is a GET path to example.com/hello-to/:name
// PATH PARAMETER: :name - a name to echo out
$API->get('/hello-to/:name', function ($name) {
	// Make an assosiative array for the JSON object
	$greeting = array(
		'message' => 'Hey there '.$name.'!'
	);
	// Set content type to JSON
    header("Content-Type: application/json");
    // Output JSON
	echo json_encode($greeting);
});


// This is a GET path to example.com/hello-to/:name
// PATH PARAMETER: :name - a name to echo out
$API->get('/require-auth/:name', function ($name) {
		if (OAuthRequestVerifier::requestIsSigned()) {
	    try {
	        $req = new OAuthRequestVerifier();
	        $user_id = $req->verify();
	
	        // If we have an user_id, then login as that user (for this request)
	        if ($user_id) {
	            // Make an assosiative array for the JSON object
				$greeting = array(
					'message' => 'Hey there '.$name.'!'
				);
				// Set content type to JSON
    			header("Content-Type: application/json");
    			// Output JSON
				echo json_encode($greeting);
				return;
	        }
	    } catch (OAuthException $e) {
	        // The request was signed, but failed verification
	        header('HTTP/1.1 401 Unauthorized');
	        header('WWW-Authenticate: OAuth realm=""');
	        header('Content-Type: text/plain; charset=utf8');
	                                
	        echo $e->getMessage();
	        exit();
	    }
	}
});


// Add a missing path handler
$API->notFound(function () use ($API) {
	echo "This path does not exist!<br />";
});


// Run the API
$API->run();

?>