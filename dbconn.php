<?php

///**
// * Description of dbconn
// *
// * @author tonsal
// */
class dbconn {

    private function getDB() {
        $address = "localhost";
        $user = "user";
        $password = "password";
        $database = "itks545";
        $port = "3306";
        $db = new mysqli($address, $user, $password, $database, $port);
        if ($db->errno > 0) {
            echo "Failed to connect to MySQL: " . $db->error;
            exit();
        }
        return $db;
    }

    private function query($query) {
        $db = dbconn::getDB();
        $result = $db->query($link, $query);
        if (!$result) {
            die('There was an error running the query [' . $db->error . ']');
        }
        $db->close();
        return $result;
    }

    public function insert($query) {
        $result = dbconn::query($query);
        $result->free();
    }

    public function select($query) {
        $result = dbconn::query($query);
        $result->free();
    }

    public function addMessage($longitude, $latitude, $userID, $text) {
        $db = dbconn::getDB();
        $query = <<<SQL
INSERT INTO `itks545`.`data` (
      `ID` ,
      `data_text` ,
      `data_userID` ,
      `data_longitude` ,
      `data_latitude` ,
      `data_date`
    ) VALUES (NULL, ?, ?, ?, ?, CURRENT_TIMESTAMP)
SQL;

        $statement = $db->prepare($query);
        $statement->bind_param('siii', $text, $userID, $longitude, $latitude);
        $statement->execute();
        if ($statement->errno > 0) {
            echo "Failed to execute prepared statement: " . $statement->error;
            exit();
        }
        $statement->free_result();
        $db->close();
    }

    public function getMessage($id) {
        $db = dbconn::getDB();
        $query = <<<SQL
SELECT `data_text`, `data_userID`, `data_longitude`, `data_latitude` FROM `itks545`.`data` WHERE ID=?
SQL;

        $statement = $db->prepare($query);
        $statement->bind_param('i', $id);
        $statement->execute();
        if ($statement->errno > 0) {
            echo "Failed to execute prepared statement: " . $statement->error;
            exit();
        }
        $statement->bind_result($text, $user, $lon, $lat);
        if ($statement->fetch()) {
            $rows = array(
                'userID' => $user,
                'longitude' => $lon,
                'latitude' => $lat,
                'text' => $text
            );
            $json = json_encode($rows);
            print_r($json);
        }

        $statement->free_result();
        $db->close();
    }

    public function getAllMessages() {
        $db = dbconn::getDB();
        $query = <<<SQL
SELECT `data_text`, `data_userID`, `data_longitude`, `data_latitude` FROM `itks545`.`data`
SQL;

        $statement = $db->prepare($query);
        $statement->execute();
        if ($statement->errno > 0) {
            echo "Failed to execute prepared statement: " . $statement->error;
            exit();
        }
        $statement->bind_result($text, $user, $lon, $lat);
//        for ($i = 0; $i < $statement->num_rows; $i++) {
        while ($statement->fetch()) {
            $rows = array(
                'userID' => $user,
                'longitude' => $lon,
                'latitude' => $lat,
                'text' => $text
            );
            $json = json_encode($rows);
            print_r($json);
        }

        $statement->free_result();
        $db->close();
    }
    
    public function deleteMessage($id) {
        $db = dbconn::getDB();
        $query = <<<SQL
DELETE FROM `itks545`.`data` WHERE `ID`=?
SQL;

        $statement = $db->prepare($query);
        $statement->bind_param('i', $id);
        $statement->execute();
        if ($statement->errno > 0) {
            echo "Failed to execute prepared statement: " . $statement->error;
            exit();
        }
        $statement->free_result();
        $db->close();
    }
}

?>
