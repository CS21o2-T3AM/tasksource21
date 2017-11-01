<?php
require_once '../utils/login.inc.php';
login_validate_or_redirect();
require_once '../utils/db_con.inc.php';
require_once '../utils/db_func.inc.php';
$task_array = get_task_array_or_redirect($dbh);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View task</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
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
    $owner_rating = get_rating_as_owner($dbh, $task_owner);

    $bid_err = '';
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
            $edit_button = "<div class=\"col-1 mr-5 \">
                            <a class=\"btn btn-primary\" href=\"edit_task.php?task_id=$task_id\">Edit</a>
                            </div>";
        } else {
            $user_bid = find_user_bid_for_task($dbh, $user_email, $task_id);
        }
        $bidding_deadline_html = "<div class=\"text-warning col-11\"><h5>Bidding deadline: $bidding_deadline</h5></div>";

    } else if ($task_status === STATUS_BIDDING_CLOSED && $task_owner === $user_email) {
        // the owner should now decide the winner
        $choose_winner_button = "<div class=\"col-1 mr-5 \">
                            <a class=\"btn btn-primary\" href=\"choose_bid.php?task_id=$task_id\">Choose winner</a>
                            </div>";

    } else if ($task_status === STATUS_ASSIGNED) {
        $assigned_user_email = get_assigned_user($dbh, $task_id);
    } else {
        // closed
        $assigned_user_email = get_assigned_user($dbh, $task_id);
        if ($assigned_user_email === false) {
            unset($assigned_user_email); // unset so that it won't be shown
            $closed_message = "<span class=\"text-danger\">The bidding for this task is closed</span>";
        }
    }
?>

<body>
<?php
    include_once '../utils/html_parts/navbar.php';
?>

<div class="container">
    <div class="row m-2 mt-3" id="wrapper">

        <div class="col-6" id="task_details">
            <div class="row">
                <div class="col">
                    <h2><?php echo $task_name?></h2>
                    <hr>
                </div>

                <?php
                    if(isset($edit_button))
                        echo $edit_button;
                ?>
                <hr>
                <div class="col-11 mt-3">
                    <h4>Posted by:</h4> <h5><?php echo $task_owner.": rating $owner_rating"?></h5>
                </div>

                <div class="col-11 mt-3">
                    <b>Category:</b> <?php echo $task_category?>
                </div>

                <div class="col-11 mt-2">
                    <b>Price: </b> <?php echo $task_price?>
                    <hr>
                </div>


                <div class="col-11 mb-2">
                    <h4 class="mt-1 mb-3">Location</h4>
                    <p><b>Postal Code:</b>
                        <?php echo $task_postal_code?>
                    </p><!--postal code-->

                    <p>
                        <b>Address: </b>
                        <?php echo $task_address?>
                    </p> <!--addess-->
                    <hr>
                </div><!--location -->

                <div class="col-11">
                    <h4>Date and Time</h4>
                    <?php echo $start_dt?>
                    <?php echo $end_dt?>
                    <hr>
                </div>

                <div class="col-11 ">
                    <h4>Description</h4>
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
                    } else {
                        echo '<div class="text-success col-11 mt-3"><h5>You are the owner of this task!</h5></div>';
                    }

                    if (isset($closed_message)) {
                        echo $closed_message;
                    }

                    if (isset($assigned_user_email)) {
                        echo_assigned_user($assigned_user_email, $task_owner);
                    }

                    if (isset($choose_winner_button)) {
                        echo $choose_winner_button;
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

</body>
</html>