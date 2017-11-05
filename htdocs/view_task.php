<?php
require_once '../utils/login.inc.php';
login_validate_or_redirect();
require_once '../utils/db_con.inc.php';
require_once '../utils/db_func.inc.php';
run_update_function($dbh);
$task_array = get_task_array_or_redirect($dbh);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View task</title>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style rel="stylesheet">
        .rcorners {
            border-radius: 5px;
            border: 1px solid #c9c9c9;
            padding: 20px;
            background-color: white;
        }

        .table_borderless {
            border-bottom:0 !important;
        }
        .table_borderless th, .table_borderless td {
            border: 1px !important;
        }

    </style>

</head>

<?php
    // the task attributes stored in the database
    $task_desc = $task_array[DB_DESC];
    $task_name = $task_array[DB_NAME];
    $task_address = $task_array[DB_ADDRESS];
    $task_postal_code = $task_array[DB_POSTAL_CODE];
    $task_owner = $task_array[DB_OWNER];
    $task_category = $task_array[DB_CATEGORY];
    $task_price = $task_array[DB_SUGGESTED_PRICE];
    $task_status = $task_array[DB_STATUS];

    $bidding_deadline = new DateTime($task_array[BIDDING_DEADLINE]);
    $bidding_deadline = $bidding_deadline->format('H:i d M Y');

    $end_dt = new DateTime($task_array[DB_END_DT]);
    $end_dt = $end_dt->format('H:i d M Y');

    $start_dt = new DateTime($task_array[DB_START_DT]);
    $start_dt = $start_dt->format('H:i d M Y');

//    // check if the user has voted, what is the winning bid etc.
//    // the first row is the winner, and this can be coloured
    require_once '../utils/constants.inc.php';
    $task_id = $_GET[TASK_ID];
    $user_email = $_SESSION[EMAIL];
    $owner_rating = get_user_avg_rating($dbh, $task_owner, 'tasker');

    $bid_err = $rate_err = '';
    if (isset($_POST['submit'])) {
        if (!isset($_POST[BID])) {
            $bid_err = 'Please set the bid amount';
        } else {
            $bid = htmlspecialchars($_POST[BID]);
            if (!is_numeric($bid) || $bid <= 0) {
                $bid_err = 'Please enter valid bid amount';
            } else {
                $bid = bid_for_task($dbh, $user_email, $task_id, $bid);
                if ($bid === false) {
                    $bid_err = 'Insertion into the database was unsuccessful';
                } else {
                    $top_bids = get_bids_and_ratings($dbh, $_GET[TASK_ID], 3);
                }
            }
        }
    } else if (isset($_POST['withdraw'])) {
        withdraw_bid($dbh, $user_email, $task_id);
    }

    if (!isset($top_bids))
        $top_bids = get_bids_and_ratings($dbh, $_GET[TASK_ID], 3);

    // show different UI depending on the state of the task
    if ($task_status === STATUS_OPEN) {
        if ($task_owner === $user_email) {
            $delete_button = "<div class=\"ml-4\">
                            <a class=\"btn btn-danger\" href=\"delete_task.php?task_id=$task_id\">Delete</a>
                            </div>";
        } else {
            $user_bid = find_user_bid_for_task($dbh, $user_email, $task_id);
        }
        $bidding_deadline_html = "<div class=\"text-warning col-11\"><h5>Bidding deadline: $bidding_deadline</h5></div>";

    } else if ($task_status === STATUS_BIDDING_CLOSED && $task_owner === $user_email) {
        // the owner should now decide the winner
        $choose_winner_button = "<div class=\"col-1 mr-5 \">
                            <a class=\"btn btn-primary\" href=\"choose_bid.php?task_id=$task_id\">Choose winner or close</a>
                            </div>";

    } else if ($task_status === STATUS_ASSIGNED) {
        $assigned_user_email = get_assigned_user($dbh, $task_id);
    } else {
        // closed
        $assigned_user_email = get_assigned_user($dbh, $task_id);
        if ($assigned_user_email === false) {
            unset($assigned_user_email); // unset so that it won't be shown
            $closed_message = "<div class=\"text-danger mb-2 mt-2\">The bidding for this task is closed.<br></div>";
        }
    }

    // ================================ rating ================================= //
    $was_assigned = isset($assigned_user_email) ? $assigned_user_email : false;
    if ($task_status === 'closed' && $was_assigned !== false) {
        $assigned_user_email = $was_assigned;
        if ($user_email === $assigned_user_email || $user_email === $task_owner) {
            $user_to_rate = ($user_email === $assigned_user_email) ? $task_owner: $assigned_user_email;
            $rating = get_user_rating_for_task($dbh, $task_id, $user_to_rate);
            if ($rating === 0) { // not rated the opposite party
                $rating_target = $user_to_rate;
            } else {
                $rating_message = "<div class=\"text-success mb-2 mt-2\">You rated $user_to_rate as $rating for this task.<br></div>";
            }
        }
    }

    if (isset($_POST['rate'])) {
        $post_rating = intval($_POST['rating']);
        if ($post_rating >= 1 && $post_rating <= 5) {
            $role = ($task_owner === $user_email) ? 'doer': 'tasker'; // role is the opposite: if owner, rate doer and vice versa
            $result = insert_user_rating_for_task($dbh, $task_id, $_POST['rating_target'], $role, $post_rating);
            if ($result === false) {
                $rate_err = 'insertion into database has failed';
            } else {
                header('Location: view_task.php?task_id='.$task_id); // refresh
                exit;
            }
        } else {
            $rate_err = 'rating must be between 1 and 5';
        }
    }

?>

<body style="background-color: #f9f9f9">
<?php
    include_once '../utils/html_parts/navbar.php';
?>

<div class="container">
    <div class="row m-2 mt-3" id="wrapper">

        <div class="col-6 rcorners" id="task_details">
            <div class="row">
                <div class="col-9">
                    <h2><i><?php echo $task_name?></i></h2>
                    <hr>
                </div>

                <div class="col-1">
                <?php
                    if(isset($delete_button))
                        echo $delete_button;
                ?>
                </div>
                <div class="col-11 mt-3">
                    <table class="table table_borderless">
                        <tr><th>Posted by: </th><td><?php echo $task_owner?></td></tr>
                        <tr><th>Owner rating</th><td><?php echo $owner_rating?></td></tr>
                        <tr><th>Category </th><td><?php echo $task_category?></td></tr>
                        <tr><th>Price </th><td><?php echo $task_price?></td></tr>
                    </table>
                </div>

                <div class="col-9 mb-2">
                    <h4 class="mt-1 mb-3"><i>Location</i></h4>
                    <hr>

                    <table class="table table_borderless">
                        <tr><th>Postal Code: </th><td><?php echo $task_postal_code?></td></tr>
                        <tr><th>Address:</th><td><?php echo $task_address?></td></tr>
                    </table>
                </div><!--location -->

                <div class="col-9 mb-2">
                    <h4><i>Date and Time</i></h4>
                    <hr>

                    <table class="table table_borderless">
                        <tr><th>Start date</th><td><?php echo $start_dt?></td></tr>
                        <tr><th>End date</th><td><?php echo $end_dt?></td></tr>
                    </table>
                </div>

                <div class="col-11 mb-2">
                    <h4><i>Description</i></h4>
                    <hr>
                    <p>
                        <?php echo $task_desc ?>
                    </p>
                </div>

            </div> <!-- row wrapper -->
        </div> <!-- task details -->

        <div class="col-6">
            <div class="row">
                <div class="col-11">
                    <h3 class="text-center mb-3">bidding board</h3>
                </div>
                <div class="col">
                <?php
                    require_once '../utils/html_parts/bid_table.php';
                    echo_bidding_board($top_bids);
                ?>
                </div>
            </div>

            <div class="row">
                <div class="col">
                <?php
                    require_once '../utils/html_parts/bid_form.php';
                    if (isset($bidding_deadline_html))
                        echo $bidding_deadline_html;

                    if (isset($user_bid)) {
                        echo_bid_form($bid_err, $user_bid);
                    } else if (isset($delete_button)) {
                        echo '<div class="text-success col-11 mt-3"><h5>You are the owner of this task!</h5></div>';
                    }

                    if (isset($closed_message)) {
                        echo $closed_message;
                    }


                    if (isset($assigned_user_email)) {
                        echo_assigned_user($assigned_user_email, $task_owner);
                    }

                    if (isset($rating_message)) {
                        echo $rating_message;
                    }

                    if (isset($choose_winner_button)) {
                        echo $choose_winner_button;
                    }

                    if (isset($rating_target)) {
                        require_once'../utils/html_parts/rate_user_form.php';
                        echo_user_rate_form($rating_target, $rate_err);
                    }

                ?>
                </div>
            </div> <!-- row -->

        </div> <!-- column -->

    </div> <!-- wrapper row -->

</div> <!-- container -->

<!--    make sure this order is correct, and placed near the end of body tag-->
<script type="text/javascript" src="../js/jquery-3.1.1.slim.min.js"></script>
<script type="text/javascript" src="../js/tether.min.js"></script>
<script type="text/javascript" src="../js/bootstrap.min.js"></script>

<!-- jQuery first, then Tether, then Bootstrap JS. -->
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

</body>
</html>