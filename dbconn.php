<?php

/**
 * Description of dbconn
 *
 * @author tonsal
 */
class dbconn {

    private $db;

    function __construct($address, $user, $password, $database, $port) {
        $this->db = new mysqli($address, $user, $password, $database, $port);
        if ($this->db->errno > 0) {
            echo "Failed to connect to MySQL: " . $this->db->error;
            exit();
        }
    }

    function __destruct() {
        $this->db . close();
    }

    public function addMessage($longitude, $latitude, $userID, $text) {
        $query = <<<SQL
INSERT INTO `data` (
      `ID` ,
      `data_text` ,
      `data_userID` ,
      `data_longitude` ,
      `data_latitude` ,
      `data_date`
    ) VALUES (NULL, ?, ?, ?, ?, CURRENT_TIMESTAMP)
SQL;

        $statement = $this->db->prepare($query);
        $statement->bind_param('sidd', $text, $userID, $longitude, $latitude);
        $statement->execute();
        if ($statement->errno > 0) {
            echo "Failed to execute prepared statement: " . $statement->error;
            exit();
        }
        $statement->free_result();
    }

    public function getMessage($id) {
        $query = <<<SQL
SELECT `data_text`, `data_userID`, `data_longitude`, `data_latitude` FROM `data` WHERE ID=?
SQL;

        $statement = $this->db->prepare($query);
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
    }

    public function getAllMessages() {
        $query = <<<SQL
SELECT `data_text`, `data_userID`, `data_longitude`, `data_latitude` FROM `data`
SQL;

        $statement = $this->db->prepare($query);
        $statement->execute();
        if ($statement->errno > 0) {
            echo "Failed to execute prepared statement: " . $statement->error;
            exit();
        }
        $statement->bind_result($text, $user, $lon, $lat);
        $json = array();
        while ($statement->fetch()) {
            $rows = array(
                'userID' => $user,
                'longitude' => $lon,
                'latitude' => $lat,
                'text' => $text
            );
            array_push($json, $rows);
        }
        echo '{"messages" : ';
        echo json_encode($json);
        echo '}';
        $statement->free_result();
    }

    public function deleteMessage($id) {
        $query = <<<SQL
DELETE FROM `data` WHERE `ID`=?
SQL;

        $statement = $this->db->prepare($query);
        $statement->bind_param('i', $id);
        $statement->execute();
        if ($statement->errno > 0) {
            echo "Failed to execute prepared statement: " . $statement->error;
            exit();
        }
        $statement->free_result();
    }

}

?>
