<?php
require_once '../utils/login.inc.php';
login_validate_or_redirect();
require_once '../utils/db_con.inc.php';
require_once '../utils/db_func.inc.php';
$task_array = get_task_array_or_redirect($dbh);
if ($task_array[DB_OWNER] !== $_SESSION[EMAIL] || $task_array[DB_STATUS] !== STATUS_BIDDING_CLOSED) {
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

    if (isset($_POST['submit'])) {
        if (isset($_POST['winner'])) {
            $winner_email = urldecode($_POST['winner']);
            set_as_winner($dbh, $winner_email, $task_id);
            header('Location: view_task.php?task_id='.$task_id);
            exit;
        }

    } else if (isset($_POST['close'])) {
        close_task($dbh, $task_id);
        header('Location: home.php');
        exit;
    }

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
                echo_bidding_table_form($bids);
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
