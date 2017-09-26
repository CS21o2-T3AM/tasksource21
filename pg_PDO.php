<?php
    // not the most recommended way to go about this (Probably should make a class)

    // using PDO = PHP Data Object to access pgsql database

    // to use PDO, make sure you allow pg_pdo by uncommenting the line in php.ini
    // extension=php_pdo_pgsql.ddl
    // and enable the PDO driver for postgresql

    $pass = "";
    $dbname = "postgres";
    $port = 5432;
    $host = "localhost";
    $pass = "postgres";

    // $dsn stands for "Data Source Name"
    $dsn = "pgsql:host=$host; port=$port; dbname=$dbname;";

    try {
        $dbh = new PDO($dsn);
//        $pdo = new PDO($dsn, $user, $password);

        // instance method
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo $e->getMessage();
        die('Failed to connect to database'); // die is EXACTLY THE SAME as exit
    }

    $stmt = $dbh->prepare($query);
    $param = array();
    $stmt->execute($param);

    $result = $stmt->fetch(PDO::FETCH_ASSOC); // using the static constants

?>