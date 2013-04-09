<?php

// UTILITY FUNCTIONS ===========================================================

function authorized() {
//    return true; // For testing...
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
 * Check first if there is user_id in session, if not, then check is there user_id
 * in the request array 
 * @param type $session
 * @param type $request
 * @return null
 */
function getUserId($session, $request) {
    if (isset($session["user_id"]))
        return $session["user_id"];
    if (isset($request["user_id"]))
        return $request["user_id"];
    return NULL;
}

// ACTIONS ======================================================================

$getAllMessagesAction = function() {
            global $db;
            $result = $db->getAllMessages();
        };

$addMessageAction = function () {
            if (authorized()) {
                global $db;
                $db->addMessage($_REQUEST["userID"], $_REQUEST["latitude"], $_REQUEST["longitude"], $_REQUEST["message"]);
                echo "Authorized and saved!";
            } else {
                echo "Not authorized!";
            }
        };

$getMessageAction = function ($id) {
            global $db;
            $result = $db->getMessage($id);
        };

$deleteMessagesAction = function ($id) {
            if (authenticated()) {
                global $db;
                $result = $db->deleteMessage($id);
            }
        };

$requestTokenAction = function () {
            $server = new OAuthServer();
            $server->requestToken();
        };

$accessTokenAction = function () {
            $server = new OAuthServer();
            $server->accessToken();
        };

$authorizeActionAction = function () {
            // The current user
            session_start();
            $user_id = getUserId($_SESSION, $_REQUEST);
            if ($user_id != NULL) {
                // Fetch the oauth store and the oauth server.
                $store = OAuthStore::instance();
                $server = new OAuthServer();

                try {
                    // Check if there is a valid request token in the current request
                    // Returns an array with the consumer key, consumer secret, token, token secret and token type.
                    $rs = $server->authorizeVerify();

                    // See if the user clicked the 'allow' submit button (or whatever you choose)
                    //$authorized = array_key_exists('allow', $_POST);
                    $authorized = true;
                    // Set the request token to be authorized or not authorized
                    // When there was a oauth_callback then this will redirect to the consumer
                    $oauth_verifier = $server->authorizeFinish($authorized, $user_id);
                    echo $oauth_verifier;
                    // No oauth_callback, show the user the result of the authorization
                } catch (Exception $e) {
                    // No token to be verified in the request, show a page where the user can enter the token to be verified
                    echo "exception";
                }
            } else {
                $redirect = AUTHORIZE_ACTION;
                $action = LOGIN_ACTION;
                require LOGIN_FORM;
            }
        };

$registerUserAction = function () {
            session_start();
            $action = REGISTER_USER_ACTION;
            require REGISTER_USER_FORM;
        };

$saveUserAction = function () {
            global $db;
            $user_id = $db->addUser($_REQUEST["users_name"], $_REQUEST["users_password"], $_REQUEST["users_email"]);
            $consumer = array(
                // These two are required
                'requester_name' => $_REQUEST["users_name"],
                'requester_email' => $_REQUEST["users_email"]
            );
            $store = OAuthStore::instance();
            $key = $store->updateConsumer($consumer, $user_id);
            // Get the complete consumer from the store
            $consumer = $store->getConsumer($key, $user_id);

            // The tokens
            $tokens = array(
                'user_id' => $user_id,
                'consumer_key' => $consumer['consumer_key'],
                'consumer_secret' => $consumer['consumer_secret']
            );
            // Set content type to JSON
            header("Content-Type: application/json");
            // Output JSON
            echo json_encode($tokens);
        };

$loginAction = function ($users_name, $users_password) {
            global $db;
            $user_id = $db->login($users_name, $users_password);
            $store = OAuthStore::instance();
            $creds = $store->listConsumers($user_id);

            // The tokens
            $tokens = array(
                'user_id' => $user_id['ID'],
                'consumer_key' => $creds[0]['consumer_key'],
                'consumer_secret' => $creds[0]['consumer_secret']
            );
            // Set content type to JSON
            header("Content-Type: application/json");
            // Output JSON
            echo json_encode($tokens);
        };
?>