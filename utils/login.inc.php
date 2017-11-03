<?php
    require_once 'constants.inc.php';
    function login_validate_or_redirect() {
        session_start();
        session_regenerate_id(true); // prevent session hijacking
        if (!isset($_SESSION[LOGIN]) || $_SESSION[LOGIN] !== true || !isset($_SESSION[EMAIL])) {
            header('Location: index.php');
            exit;
        } else {
            return true;
        }
    }

    function admin_login_validate_or_redirect() {
    session_start();
    session_regenerate_id(true); // prevent session hijacking
    if (!isset($_SESSION[LOGIN]) || $_SESSION[LOGIN] !== true || !isset($_SESSION[EMAIL]) || $_SESSION[ADMIN] === false) {
        header('Location: index.php');
        exit;
    } else {
        return true;
    }
}

    function set_session_and_redirect($user_id, $is_admin) {
        session_start();
        $_SESSION[EMAIL] = $user_id;
        $_SESSION[LOGIN] = true;
        $_SESSION[ADMIN] = $is_admin;
        if ($is_admin === true) {
            header('Location: admin.php');
        } else {
            header('Location: home.php');
        }
        exit;
    }