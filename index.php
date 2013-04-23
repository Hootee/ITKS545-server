<?php

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

$app->setName('ITKS545');

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, and `Slim::delete`
 * is an anonymous function.
 */
// GET route


// =============================================================================
// Routes:
// =============================================================================
// Messages
$app->get('/messages/add/:user_id/:latitude/:longitude/:message', $addMessage);
$app->get('/messages/get/:id', $getMessage);
$app->post('/messages/getall/', $getAllMessage);
$app->get('/messages/delete/:id', $deleteMessage);
// Users
$app->get('/register_user', $registerUser);
$app->post('/save_user', $saveUser);
$app->post('/login', $login);
// Oauth
$app->get('/request_token', $requestToken);
$app->get('/access_token', $accesToken);
$app->post('/authorize', $authorize);
// testing
$app->get('/list/:userID', $getUser);

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