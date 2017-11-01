<?php
//require_once '../utils/login.inc.php';
//login_validate_or_redirect();
require_once '../utils/db_con.inc.php';
require_once '../utils/db_func.inc.php';
$task_array = get_task_array_or_redirect($dbh);
if ($task_array[DB_OWNER] !== $_SESSION[EMAIL] ||
    $task_array[DB_STATUS] !== STATUS_BIDDING_CLOSED ||
    empty($_GET[TASK_ID])) {
    header('Location: home.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assign task</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>
<?php
    $task_id = $_GET[TASK_ID];

    $close_task_button = "<div class=\"col-1 mr-5 \">
                            <a class=\"btn btn-primary\" href=\"edit_task.php?task_id=$task_id\">Edit</a>
                            </div>";
    $assign_button = "";

?>
<?php
include_once '../utils/html_parts/navbar.php';
?>

<div class="container mt-3 mb-3">
    <div class="row m-2 justify-content-center">
        <div class="col-8">
            <?php
                require_once '../utils/html_parts/bid_table.php';
                $bids = get_bids_and_ratings($dbh, $task_id, false);
                echo_bidding_board($bids);
                // provide option to either 1. choose a winner 2. close the task
            ?>
        </div>

    </div> <!-- wrapper row -->

</div> <!-- container -->

<!--    make sure this order is correct, and placed near the end of body tag-->
<script type="text/javascript" src="../js/jquery-3.1.1.slim.min.js"></script>
<script type="text/javascript" src="../js/tether.min.js"></script>
<script type="text/javascript" src="../js/bootstrap.min.js"></script>

</body>
</html>
