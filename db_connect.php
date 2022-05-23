<?php
// NOTES:
// DO NOT DO THIS IN LIVE CODING
// THIS WAY OF DOING DATABASE IS INSECURE, AND VULNERABLE TO SQL INJECTION
// USE PREPARED STATEMENTS INSTEAD OF CONCATENATION

/**
 *  Opens the connection to the MySQL Database.
 *  Returns a MySQL instance. Used to make changes and read from database
 */
    function OpenCon() {
        $dbhost = "localhost";

        // Leave user and password as 'root' and ''
        // In MySQL, the 'root' user doesn't need a password
        $dbuser = "root";
        $dbpass = "";
        // Change to table name if needed
        $db = "hcc_champion_data";

        $conn = new mysqli($dbhost, $dbuser, $dbpass, $db) or die("Connect failed: %s\n". $conn -> error);

        return $conn;
    }

/**
 * @param $conn - Connection instance to close
 * @return void
 *
 *  Closes an active database instance.
 *  Always close an instance after using it!
 */
    function CloseCon($conn) {
        $conn -> close();
    }

/**
 * @param $conn - Connection instance
 * @param $table_name - Table to back up
 * @return void
 *
 *  Creates a backup of a table.
 *  If you mess up a data save, you can restore a backup in phpMyAdmin.
 */

    function SaveDatabase($conn, $table_name) {
        $today = date("Y_m_d_H_i_s");
        $backup_file  = "./backup/".$today."_".$table_name.".sql";
        $sql = "SELECT * INTO OUTFILE '$backup_file' FROM $table_name";

        mysqli_select_db($conn, 'hcc_champion_data');
        $retval = mysqli_query( $conn, $sql );

        if(! $retval ) {
            die('Could not take data backup: ' . mysqli_error($conn));
        }

        //echo "Backed up data successfully\n";

        $conn -> close();
    }

?>