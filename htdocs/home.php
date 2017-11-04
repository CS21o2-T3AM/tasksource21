<?php
require_once '../utils/login.inc.php';
login_validate_or_redirect();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Home Page</title>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
          integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">

    <style rel="stylesheet">
        .rcorners {
            border-radius: 5px;
            border: 1px solid #c9c9c9;
            padding: 20px;
            background-color: white;
        }

    </style>

</head>

<body style="background-color: #f9f9f9">
<?php
require_once '../utils/html_parts/task_table.php';
require_once '../utils/db_con.inc.php';
require_once '../utils/db_func.inc.php';
run_update_function($dbh);

$user_email = $_SESSION[EMAIL];

$assigned_tasks = get_tasks_assigned($dbh, $user_email);
$bidding_tasks = get_tasks_in_bidding($dbh, $user_email);
$created_tasks = get_tasks_created($dbh, $user_email);
$completed_tasks = get_tasks_complete($dbh, $user_email);
?>

<?php
include_once '../utils/html_parts/navbar.php';
?>

<div class="container mt-3 mb-4">

    <div class="row align-items-center">
        <div class="col-8 m-4 rcorners">
            <h3 class="mb-3">Bidding in progress</h3>
            <?php
            echo_table_bidding_tasks($bidding_tasks);
            ?>
        </div>

        <div class="col-8 m-4 rcorners">
            <h3 class="mb-3">Assigned tasks</h3>

            <?php
            echo_table_assigned_tasks($assigned_tasks);
            ?>
        </div>

        <div class="col-8 m-4 rcorners">
            <h3 class="mb-3">Created tasks</h3>
            <?php
            echo_table_created_tasks($created_tasks);
            ?>
        </div>

        <div class="col-8 m-4 rcorners">
            <h3 class="mb-3">Your past activities</h3>
            <?php
            echo_table_completed_tasks($completed_tasks);
            ?>

        </div>

    </div> <!-- row -->

</div>

<!--<!--    make sure this order is correct, and placed near the end of body tag-->-->
<!--<script type="text/javascript" src="../js/jquery-3.1.1.slim.min.js"></script>-->
<!--<script type="text/javascript" src="../js/tether.min.js"></script>-->
<!--<script type="text/javascript" src="../js/bootstrap.min.js"></script>-->

<!-- jQuery first, then Tether, then Bootstrap JS. -->
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

</body>
</html>
