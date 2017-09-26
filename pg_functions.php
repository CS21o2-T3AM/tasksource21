<?php
    require_once 'db_con.php';

    $query = "SELECT * FROM table WHERE name = $1";
    // $1, $2, $3 etc. will be substituted with the element of array

    // $statement is just a name you give to this query so that you can later execute it
    $statement = 'selecting from table';
    $result = pg_prepare($dbh, $statement, $query) or die('prepare failed:\n'.pg_last_error());

    $params = array("John's cat"); // no need for string escapes etc as prepared statement
    $result = pg_execute($dbh, $statement, $params) or die('execute failed:\n'.pg_last_error());

    // $result stores the query result with which you can deal with using fetch,
    // analyse the $result using field etc.

    foreach(pg_fetch_array($result) as $row) {

        // do something
    }

    foreach(pg_fetch_assoc($result) as $row) {
        // now use field names to get the values
    }


    pg_close($dbh);

?>