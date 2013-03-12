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

$app->get('/messages/add/:longitude/:latitude/:userID/:text', function ($longitude, $latitude, $userID, $text) {
            if (authorized()) {
                global $db;
                $db->addMessage($longitude, $latitude, $userID, $text);
                echo "Authorized and saved!";
            } else {
                echo "Not authorized!";
            }
        });


$app->get('/messages/get/:id', function ($id) {
            global $db;
            $result = $db->getMessage($id);
        });



$app->get('/messages/getall/', function () {
            global $db;
            $result = $db->getAllMessages();
        });

$app->get('/messages/delete/:id', function ($id) {

            if (authenticated()) {
                global $db;
                $result = $db->deleteMessage($id);
            }
        });

// POST route
$app->post('/post', function () {
            echo 'This is a POST route';
        });

// PUT route
$app->put('/put', function () {
            echo 'This is a PUT route';
        });

// DELETE route
$app->delete('/delete', function () {
            echo 'This is a DELETE route';
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
$app->get('/authorize', function () {
            // The current user
            session_start();
            if (isset($_SESSION["user_id"]) && is_int($_SESSION["user_id"]) && $_SESSION["user_id"] > 0) {

                // Fetch the oauth store and the oauth server.
                $store = OAuthStore::instance();
                $server = new OAuthServer();

                try {
                    // Check if there is a valid request token in the current request
                    // Returns an array with the consumer key, consumer secret, token, token secret and token type.
                    $rs = $server->authorizeVerify();

//                    if ($_SERVER['REQUEST_METHOD'] == 'GET') { // CHANGE THIS TO POST
                    // See if the user clicked the 'allow' submit button (or whatever you choose)
                    //$authorized = array_key_exists('allow', $_POST);
                    $authorized = true;

                    // Set the request token to be authorized or not authorized
                    // When there was a oauth_callback then this will redirect to the consumer
                    $oauth_verifier = $server->authorizeFinish($authorized, $_SESSION["user_id"]);
//                        var_dump($_SESSION["user_id"]);
                    echo "Your PIN is: " . $oauth_verifier;
                    // No oauth_callback, show the user the result of the authorization
                    // ** your code here **
//                    }
                } catch (OAuthException $e) {
                    // No token to be verified in the request, show a page where the user can enter the token to be verified
                    // **your code here**
                }
            } else {
                $redirect = "/index.php/authorize";
                $action = "/login/index.php";
                require '/login/index.php';
            }
        });

// This is a GET path to example.com/request_token
$app->get('/register_user', function () {
            session_start();
            $action = "/register_user/index.php";
            require '/register_user/index.php';
        });

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
?>