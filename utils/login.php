<?php
    require_once 'constants.php';

    // check userID and token in cookie matches those in session
    function login_validate() {
        if (empty($_COOKIE[USER_ID]) || empty($_COOKIE[TOKEN]))
            return false;

        if ($_COOKIE[USER_ID] === $_SESSION[USER_ID] && $_COOKIE[TOKEN] === $_SESSION[TOKEN]) {
            return true;
        } else {
            return false;
        }
    }

    // generates a random token (md5) that is valid till the user logs out
    function generate_token($username, $password) {
        $date = date('r');
        return md5($username.$password.$date);
    }

    function set_session_cookie($username, $password) {
        unset($_SESSION[USER_ID]);
        unset($_SESSION[TOKEN]);
        $token = generate_token($username, $password);
        $_SESSION[USER_ID] = $username;
        $_SESSION[TOKEN] = $token;
        $_COOKIE[USER_ID] = $username;
        $_COOKIE[TOKEN] = $token;
    }