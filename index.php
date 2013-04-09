<?php

// Database connection settings and creating db_conn-instance
require 'database.php';
// Oauth-store settings and instantiating Oauth:store
require 'oauth.php';

/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim(array(
            'debug' => true,
            'mode' => 'development',
            'log.level' => \Slim\Log::DEBUG,
            'log.enabled' => true
        ));

$app->setName('foo');

function authorized() {
    if (OAuthRequestVerifier::requestIsSigned()) {
        try {
            $req = new OAuthRequestVerifier();
            $user_id = $req->verify();
            // If we have an user_id, then login as that user (for this request)
            if ($user_id) {
                return true;
            }
            return false;
        } catch (OAuthException $e) {
            // The request was signed, but failed verification
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: OAuth realm=""');
            header('Content-Type: text/plain; charset=utf8');
            echo $e->getMessage();
            exit();
        }
    }
    return false;
}

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, and `Slim::delete`
 * is an anonymous function.
 */
// GET route
$app->get('/locations', function () {
            
        });

$app->get('/messages/add/:user_id/:latitude/:longitude/:message', function ($user_id, $latitude, $longitude, $message) {
            if (authorized()) {
                global $db;
                $db->addMessage($user_id, $latitude, $longitude, $message);
                echo "Authorized and saved!";
            } else {
                echo "Not authorized!";
            }
        });


$app->get('/messages/get/:id', function ($id) {
            global $db;
            $result = $db->getMessage($id);
        });



$app->post('/messages/getall/', function () {
            global $db;
            $result = $db->getAllMessages();
        });

$app->get('/messages/delete/:id', function ($id) {

            if (authenticated()) {
                global $db;
                $result = $db->deleteMessage($id);
            }
        });


// Add a missing path handler
$app->notFound(function () use ($app) {
            $req = $app->request();
            $rootUri = $req->getRootUri();
            $resourceUri = $req->getResourceUri();
            echo "This path does not exist: $resourceUri!<br />";
        });



// This is a GET path to example.com/request_token
$app->get('/request_token', function () {
            $server = new OAuthServer();
            $server->requestToken();
        });


// This is a GET path to example.com/access_token
$app->get('/access_token', function () {
            $server = new OAuthServer();
            $server->accessToken();
        });


// This is a GET path to example.com/authorize
$app->post('/authorize', function () {
            // Fetch the oauth store and the oauth server.
            if (isset($_POST['user_id']) && $_POST['user_id'] > 0) {
                $store = OAuthStore::instance();
                $server = new OAuthServer();

                try {
                    // Check if there is a valid request token in the current request
                    // Returns an array with the consumer key, consumer secret, token, token secret and token type.
                    $rs = $server->authorizeVerify();

                    if ($_SERVER['REQUEST_METHOD'] == 'POST') { // CHANGE THIS TO POST
                    // See if the user clicked the 'allow' submit button (or whatever you choose)
                    //$authorized = array_key_exists('allow', $_POST);
                    $authorized = true;

                    // Set the request token to be authorized or not authorized
                    // When there was a oauth_callback then this will redirect to the consumer
                    $oauth_verifier = $server->authorizeFinish($authorized, $_POST["user_id"]);
                    $json = array(
                    'pin'         => $oauth_verifier
                );
                    // No oauth_callback, show the user the result of the authorization
                    echo json_encode($json);
                    }
                } catch (OAuthException $e) {
                    // No token to be verified in the request, show a page where the user can enter the token to be verified
                    // **your code here**
                    echo "exception";
                }
            } else {
                echo "foobar";
            }
        });

// This is a GET path to example.com/request_token
$app->get('/register_user', function () {
            session_start();
            $action = $_SERVER['DOCUMENT_ROOT'] . "/toarjusa/itks545/index.php/save_user";
            require $_SERVER['DOCUMENT_ROOT'] . '/toarjusa/itks545/register_user/index.php';
        });

// This saves a new user to our database and returns userID and tokens.
$app->post('/save_user', function () {
            global $db;
            $user_id = $db->addUser($_POST["users_name"], $_POST["users_password"], $_POST["users_email"]);
            $consumer = array(
	    // These two are required
	    'requester_name' => $_POST["users_name"],
	    'requester_email' => $_POST["users_email"]
	);
        $store = OAuthStore::instance(); 
	$key   = $store->updateConsumer($consumer, $user_id);
	// Get the complete consumer from the store
	$consumer = $store->getConsumer($key, $user_id);

	// The tokens
	$tokens = array(
		'user_id'         => $user_id,
		'consumer_key'    => $consumer['consumer_key'],
		'consumer_secret' => $consumer['consumer_secret']
	);
	// Set content type to JSON
    header("Content-Type: application/json");
    // Output JSON
	echo json_encode($tokens);
        });

// This log in user and returns userID and old tokens.
$app->post('/login', function () {
            global $db;
            $user_id = $db->login($_POST["users_name"], $_POST["users_password"]);
            if ($user_id != 0) {
                $store = OAuthStore::instance(); 
                $creds = $store->listConsumers($user_id);

                // The tokens
                $json = array(
                    'user_id'         => $user_id,
                    'consumer_key'    => $creds[0]['consumer_key'],
                    'consumer_secret' => $creds[0]['consumer_secret']
                );
            // Set content type to JSON
            header("Content-Type: application/json");
            // Output JSON
            echo json_encode($json);
            }
});
        
// testing
$app->get('/list/:userID', function ($userID) {
            $store = OAuthStore::instance();
            var_dump($store->listConsumers($userID));
        });
        

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
?>