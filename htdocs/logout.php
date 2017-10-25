<?php
    session_start();
    $_SESSION = array(); // resets the session array
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/'); //session_name() == PHPSESSID by default
    } // erases the client-slide data == cookie

    session_destroy(); // erases the server-side data
    header('Location: index.php');
    exit;
?>