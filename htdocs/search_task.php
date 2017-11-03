<?php
require_once '../utils/login.inc.php';
login_validate_or_redirect();
//    TODO: run_update_function($dbh);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Search task</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<?php
    require_once '../utils/html_parts/task_table.php';
    require_once '../utils/db_con.inc.php';
    require_once '../utils/db_func.inc.php';
    $open_tasks = get_all_open_tasks($dbh);

?>
<body>
<?php
    include_once '../utils/html_parts/navbar.php';
?>

<div class="container mt-3 mb-4">

    <div class="row align-items-center">
        <div class="col m-4">
            <h2 class="">Search for tasks</h2>
            <?php
                echo_tasks_table_all($open_tasks);
            ?>
        </div>

    </div> <!-- row -->

</div>
    <!--    make sure this order is correct, and placed near the end of body tag-->
    <script type="text/javascript" src="../js/jquery-3.1.1.slim.min.js"></script>
    <script type="text/javascript" src="../js/tether.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
</body>
</html>
