<?php
// Some constant
require 'constants.php';
// Database connection settings and creating db_conn-instance
require 'database.php';
// Oauth-store settings and instantiating Oauth:store
require 'oauth.php';
// Utility and API-functions
require 'api.php';

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

$app->setName('app');

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, and `Slim::delete`
 * is an anonymous function.
 */
$app->get('/messages/add/', $addMessageAction);
$app->post('/messages/add/', $addMessageAction);

$app->get('/messages/get/:id', $getMessageAction);
$app->post('/messages/get/:id', $getMessageAction);

$app->get('/messages/getall', $getAllMessagesAction);
$app->post('/messages/getall', $getAllMessagesAction);

$app->get('/messages/delete/:id', $deleteMessagesAction);
$app->post('/messages/delete/:id', $deleteMessagesAction);

$app->get('/request_token', $requestTokenAction);
$app->post('/request_token', $requestTokenAction);
$app->get('/access_token', $accessTokenAction);
$app->post('/access_token', $accessTokenAction);
$app->get('/authorize', $authorizeActionAction);
$app->post('/authorize', $authorizeActionAction);

$app->get('/save_user', $saveUserAction);
$app->post('/save_user', $saveUserAction);
$app->get('/register_user', $registerUserAction);
$app->post('/register_user', $registerUserAction);

$app->get('/login/:users_name/:users_password', $loginAction);

$app->get('/list/:userID', function ($userID) {
            $store = OAuthStore::instance();
            var_dump($store->listConsumers($userID));
        });

// Add a missing path handler
$app->notFound(function () use ($app) {
            $req = $app->request();
            $rootUri = $req->getRootUri();
            $resourceUri = $req->getResourceUri();
            echo "This path does not exist: $resourceUri!<br />";
        });



/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
?>