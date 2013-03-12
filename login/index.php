<?php
//var_dump($_SESSION);    
if (isset($_POST["Login"])) {
    session_start();
    require '../database.php';
    $user_id = $db->login($_POST["users_name"], $_POST["users_password"]);
    if (is_array($user_id) && $user_id["ID"] > 0) {
        $_SESSION["user_id"] = $user_id["ID"];
    }
    if (isset($_POST["redirect"])) {
        header("Location:" . $_POST["redirect"]);
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <div>
            <form method="post" action="<?php echo isset($action) ? $action : ""; ?>">
                <fieldset>
                    <legend>Login</legend>
                    <div>
                        <label for="users_name">Username</label>
                        <input type="text" id="users_name" name="users_name">
                    </div>
                    <div>
                        <label for="users_password">Password</label>
                        <input type="password" id="users_password" name="users_password">
                    </div>
                </fieldset>
                <input type="hidden" id="redirect" name="redirect" value="<?php echo isset($redirect)?$redirect:"" ?>">
                <input type="submit" id="Login" name="Login" value="Login">
            </form>
        </div>
    </body>
</html>
