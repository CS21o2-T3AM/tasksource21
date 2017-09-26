<?php
    /*
     * Use:
     * file to be required for database connection.
     */

    /*
     * Note:
     * require and include are only different in the way they handle errors. required file missing
     * will throw a fatal exception while include will simply continue running without using that file
     */

    $pass = '';
    $dbname = 'postgres';
    $port = 5432;
    $host = 'localhost';
    $pass = 'postgres';


    // in php, variable names inside double quotes get interpreted, whereas in single quotes
    // the string is literal, just like bash
    $dsn = "host=$host port=$port dbname=$dbname";

    $dbh = pg_connect($dsn)
    or die('Could not connect to the database\n');

?>