<?php

/**
 * Database API.
 *
 * @author Bela Borbely, Toni Salminen, Olga Tymofeva
 * @version 2013-04-23
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
        $this->db->close();
    }

    /**
     * Add a new message to the database
     * @param int $userID
     * @param double $latitude
     * @param double $longitude
     * @param String $text
     */
    public function addMessage($userID, $latitude, $longitude, $text) {
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

    /**
     * Add a new user and returns userId.
     * @param String $users_name
     * @param String $users_password
     * @param String $users_email
     * @return int userId
     */
    public function addUser($users_name, $users_password, $users_email) {
        $query = <<<SQL
INSERT INTO `users` (
      `users_name` ,
      `users_password` ,
      `users_email`,
      `users_created`
    ) VALUES (?, ?, ?, CURRENT_TIMESTAMP)
SQL;

        $statement = $this->db->prepare($query);
        $statement->bind_param('sss',$users_name, $users_password, $users_email);
        $statement->execute();
        $user_id = $statement->insert_id;
        if ($statement->errno > 0) {
            echo "Failed to execute prepared statement: " . $statement->error;
            exit();
        }
        $statement->free_result();
        return $user_id;
    }

    /**
     * Check username and password and return userId, if user found
     * @param String $users_name
     * @param String $users_password
     * @return int userId
     */
    public function login($users_name,$users_password) {
        $query = <<<SQL
SELECT ID FROM `users` WHERE users_name=? AND users_password=?
SQL;

        $statement = $this->db->prepare($query);
        $statement->bind_param('ss', $users_name, $users_password);
        $statement->execute();
        if ($statement->errno > 0) {
            echo "Failed to execute prepared statement: " . $statement->error;
            exit();
        }
        
        $statement->bind_result($ID);
        $user_id = 0;
        if ($statement->fetch()) {
            $user_id = $ID;
        }
        $statement->free_result();
        return $user_id;
    }
    
    public function getEmail($id) {
        $query = <<<SQL
SELECT `users_email` FROM `users` WHERE ID=?
SQL;

        $statement = $this->db->prepare($query);
        $statement->bind_param('i', $id);
        $statement->execute();
        if ($statement->errno > 0) {
            echo "Failed to execute prepared statement: " . $statement->error;
            exit();
        }
        $statement->bind_result($email);
        $email = array();
        if ($statement->fetch()) {
            $email = array(
                'users_email' => $email
            );
        }
        $statement->free_result();
        return $email;
    }

    /**
     * Search message by id
     * @param int $id
     * @return array columns
     */
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
            $columns = array(
                'userID' => $user,
                'longitude' => $lon,
                'latitude' => $lat,
                'text' => $text
            );
//            $json = json_encode($rows);
//            print_r($json);
        }
        $statement->free_result();
        return $columns;
    }

    /**
     * Get all message
     * @return array messages
     */
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
        $rows = array();
        while ($statement->fetch()) {
            $columns = array(
                'userID' => $user,
                'longitude' => $lon,
                'latitude' => $lat,
                'text' => $text
            );
            array_push($rows, $columns);
        }
        $statement->free_result();
        return $rows;
    }

    /**
     * Delete message by id
     * @param type $id
     */
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
