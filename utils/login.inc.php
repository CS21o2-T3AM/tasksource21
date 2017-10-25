<?php
    require_once 'constants.inc.php';
    function login_validate_or_redirect() {
        session_start();
        session_regenerate_id(true); // prevent session hijacking
        if (!isset($_SESSION[LOGIN]) || $_SESSION[LOGIN] !== true || !isset($_SESSION[USER_ID])) {
            header('Location: index.php');
            exit;
        } else {
            return true;
        }
    }

    function set_session_and_redirect($user_id, $is_admin) {
        session_start();
        $_SESSION[USER_ID] = $user_id;
        $_SESSION[LOGIN] = true;
        $_SESSION[ADMIN] = $is_admin;
        if ($is_admin) {
            header('Location: admin.php');
        } else {
            header('Location: home.php');
        }
        exit;
    }