<?php
require_once '../utils/login.inc.php';
login_validate_or_redirect();
require_once '../utils/db_con.inc.php';
require_once '../utils/db_func.inc.php';
$task_array = get_task_array_or_redirect($dbh);
if ($_SESSION[EMAIL] !== $task_array[DB_OWNER] || $task_array[DB_STATUS] !== STATUS_OPEN) {
    // id does not exist or the user is not the owner or the task has already been bidded/assigned/completed
    header('Location: home.php');
    exit;
}

delete_task($dbh, $_GET[TASK_ID]);
header('Location: home.php');
exit;