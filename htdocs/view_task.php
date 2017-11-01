<?php
//require_once '../utils/login.inc.php';
//login_validate_or_redirect();
//
//if (empty($_GET[TASK_ID])) {
//    header('Location: home.php');
//    exit;
//}
//require_once '../utils/constants.inc.php';
//require_once '../utils/db_con.inc.php';
//$task_array = get_task_by_id($dbh, $_GET[TASK_ID]);
//if ($task_array === false) {
//    header('Location: home.php'); // set message
//    exit;
//}
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
    // the task attributes can be accessed from here
//    $task_desc = $task_array(DB_DESC);
//    $task_name = $task_array(DB_NAME);
//    $task_address = $task_array(DB_ADDRESS);
//    $task_postal_code = $task_array(DB_POSTAL_CODE);
//    $task_owner = $task_array(DB_OWNER);
//    $task_bidding_deadline = $task_array(DB_BIDDING_DEADLINE);
//    $task_category = $task_array(DB_CATEGORY);
//    $task_start_dt = $task_array(DB_START_DT);
//    $task_end_dt = $task_array(DB_END_DT);
//    $task_price = $task_array(DB_PRICE);
//    $task_status = $task_array(DB_STATUS);
//
//    // check if the user has voted, what is the winning bid etc.
//    // the first row is the winner, and this can be coloured
//    $top_biddings = get_bids_for_task($dbh, $_GET[TASK_ID], 3);
    $task_desc = 'this task is about ....';
    $task_name = 'Clean a ship';
    $task_address = 'Clean a ship';
    $task_postal_code = 'Clean a ship';
    $task_owner = 'Clean a ship';
//    $task_bidding_deadline = $task_array(DB_BIDDING_DEADLINE);
    $task_category = 'Clean a ship';
    $task_start_dt = 'Clean a ship';
    $task_end_dt = 'Clean a ship';
    $task_price = 'Clean a ship';
    $task_status = 'Clean a ship';

    // check if the user has voted, what is the winning bid etc.
    // the first row is the winner, and this can be coloured
//    $top_biddings = get_bids_for_task($dbh, $_GET[TASK_ID], 3);
?>

<body>
<?php
    include_once '../utils/navbar.php';
?>

<div class="container">
    <div class="row m-2" id="wrapper">

        <div class="col-7" id="task_details">
            <div class="row">
                <div class="col-11">
                    <h2><?php echo $task_name?></h2>
                    <hr>
                </div>

                <div class="col-11">
                    <h4>Posted by <?php echo $task_owner?></h4>
                </div>

                <div class="col-11">
                    Category: <?php echo $task_category?>
                </div>

                <div class="col-11">

                    Price: <?php echo $task_price?>
                </div>

                <div class="col-11">
                    <h4>Location</h4>
                    <p> Postal Code:
                        <?php echo $task_postal_code?>
                    </p><!--postal code-->

                    <p>
                        Address:
                        <?php echo $task_address?>
                    </p> <!--addess-->

                </div><!--location -->

                <div class="col-11">
                    <h4>Date and Time</h4>
                    <?php echo $task_start_dt?>
                    <?php echo $task_end_dt?>
                </div>

                <div class="col-11 ">
                    <h4>Description</h4>
                    <p>
                        <?php echo $task_desc ?>
                    </p>
                </div>

            </div> <!-- row wrapper -->
        </div> <!-- task details -->

        <div class="col-4">


            <div class="row">
                <div class="text-center">
                    <h3>Bidding</h3>
                </div>
                <?php
                    create_bidding_table($top_biddings);
                ?>


            </div>

            <div class="row">
                <form action="" method="POST">

                    <fieldset about="Bidding">
                        <div class="form-group  <?php echo isset($bid_err)? 'has-danger' : ''?>">
                            <label class="form-control-label" for="bid">Bid for this task: </label>
                            <div class="input-group">
                                <span class="input-group-addon">$</span><input class="form-control <?php echo isset($bid)? 'form-control-danger' : ''?>" type="number" step="0.01" id="bid" name="bid" value="<?php echo $bid;?>" placeholder="Place your bid">
                                <span class="error text-danger"><?php echo !empty($bid_err)? $bid_err : ''?></span>
                            </div>
                        </div>

                    </fieldset> <!--bidding-->

                    <div class="row text-danger">
                        <?php echo isset($general_form_err) ? $general_form_err : ''?>
                    </div>

                    <div class="form-group center">
                        <input class="btn btn-primary" type="submit" name="submit" value="Submit"/>
                    </div>
                </form>
            </div> <!-- form row -->

        </div> <!-- form column -->


    </div> <!-- wrapper row -->

</div> <!-- container -->

        <!--    make sure this order is correct, and placed near the end of body tag-->
    <script type="text/javascript" src="../js/jquery-3.1.1.slim.min.js"></script>
    <script type="text/javascript" src="../js/tether.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>

</body>
</html>