<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <div>
            <?php
            if (isset($_POST["Register"])) {
                require '../database.php';
                $db->addUser($_POST["users_name"], $_POST["users_password"], $_POST["users_email"]);
                ?>
                <p><strong>User added!</strong></p>
                <?php
            } else {
                ?>

                <form method="post" action="<?php echo $action?>">
                    <fieldset>
                        <legend>Register</legend>
                        <div>
                            <label for="users_name">Username</label>
                            <input type="text" id="users_name" name="users_name">
                        </div>
                        <div>
                            <label for="users_password">Password</label>
                            <input type="password" id="users_password" name="users_password">
                        </div>
                        <div>
                            <label for="users_email">Email</label>
                            <input type="text" id="users_email" name="users_email">
                        </div>
                    </fieldset>
                    <input type="submit" id="Register" name="Register" value="Register">
                </form>
            <?php } ?>
        </div>
    </body>
</html>
