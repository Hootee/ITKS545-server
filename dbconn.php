<?php

/**
 * Description of dbconn
 *
 * @author tonsal
 */
class dbconn {

    var $mysqli;

    function __construct($address, $user, $password, $database, $port) {
        $this->mysqli = new mysqli($address, $user, $password, $database, $port);
        if ($this->mysqli->connect_errno) {
            echo "Failed to connect to MySQL: " . $this->mysqli->connect_error;
        }
    }

    function __destruct() {
        $this->mysqli->close();
    }

    public function getName() {
        $res = mysqli_query($this->mysqli, "SELECT first AS _msg FROM testtable");
        $row = mysqli_fetch_assoc($res);
        echo $row['_msg'];
        $row = mysqli_fetch_assoc($res);
        echo $row['_msg'];
    }

    public function getResult($query) {
        $res = $this->mysqli->query($query);
//        $row = mysqli_fetch_assoc($res);
//        echo $row[$return];
        
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }

//        foreach ($rows as $row) {
//            echo $row['CountryCode'];
//        }
//        
            echo json_encode($rows, JSON_PRETTY_PRINT);

        $res->free();
    }

}

?>
