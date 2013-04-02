<?php
require 'dbconn.php';

// Toni:
//$db_address = 'localhost';
//$db_user = 'toarjusa';
//$db_password = 'mysql';
//$db_name = 'toarjusa';
//$db_port = '3306';

// ITKS545-server:
$db_address = 'localhost';
$db_user = 'toarjusa';
$db_password = 'mysql';
$db_name = 'toarjusa';
$db_port = '3306';

// Bela:
//$db_address = 'localhost';
//$db_user = 'itks545';
//$db_password = 'itks545';
//$db_name = 'itks545';
//$db_port = '3306';

$db = new dbconn($db_address, $db_user, $db_password, $db_name, $db_port);
?>
