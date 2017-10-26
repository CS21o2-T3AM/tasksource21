<?php
require_once 'constants.inc.php';

$navbar_register = $logged_in_as = $log_out = '';
$is_logged_in = isset($_SESSION[USER_ID]);
if ($is_logged_in !== false) {
    $logged_in_as = '<span class=\"navbar-text\">Logged in as'.$_POST[USER_ID].'</span>';
    $log_out = '<a class="nav-item nav-link" href="logout.php">Log out</a>';
} else {
    $navbar_register = '<a class="nav-item nav-link" href="../htdocs/register.php">Register</a>';
}

$navbar_time = '<span class="navbar-text">'.date('D, j M Y').'</span>';

$navbar = <<<EOT
<nav class="navbar navbar-inverse bg-primary navbar-toggleable" style="background-color: dodgerblue">
    <div class="container">
        <h2 class="navbar-brand">TaskSource21</h2>
        <div class="navbar-nav nav-tabs mr-auto">
            $navbar_register
            $logged_in_as
            $log_out
        </div><!--navbar-nav-->
        
        $navbar_time
    </div><!-- container -->
</nav>
EOT;

echo $navbar;

