<?php
require_once '../utils/constants.inc.php';
$navbar_register = $logged_in_as = $log_out = $search_task = $create_task = '';
$is_logged_in = isset($_SESSION[EMAIL]) && isset($_SESSION[LOGIN]);
if ($is_logged_in !== false) {
    $logged_in_as = '<span class="navbar-text mr-5">Logged in as'.$_SESSION[EMAIL].'</span>';
    $log_out = '<a class="nav-item nav-link" href="logout.php">Log out</a>';
    $create_task = '<a class="nav-item nav-link" href="create_task.php">Create Task</a>';
    $search_task = '<a class="nav-item nav-link" href="search_task.php">Search Task</a>';
} else {
    $navbar_register = '<a class="nav-item nav-link" href="register.php">Register</a>';
}

$navbar_time = '<span class="navbar-text">'.date('D, j M Y').'</span>';

$navbar = <<<EOT
<nav class="navbar navbar-inverse bg-primary navbar-toggleable" style="background-color: dodgerblue">
    <div class="container">
        <h2 class="navbar-brand">TaskSource21</h2>
        <div class="navbar-nav nav-tabs mr-auto">
            $create_task
            $search_task
            $navbar_register
            $log_out
        </div><!--navbar-nav-->
        $logged_in_as
        $navbar_time
    </div><!-- container -->
</nav>
EOT;

echo $navbar;

