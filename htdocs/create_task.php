<?php
//require_once '../utils/login.inc.php';
//login_validate_or_redirect();
// use this file for edits as well
?>

<!DOCTYPE html>

<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <title>Create Task</title>
</head>

<body>

<?php
require_once '../utils/constants.inc.php';
require_once '../utils/category_listing.inc.php';
//    require_once '../utils/db_con.inc.php';

//    $categories = get_task_categories($dbh);
$categories = array('1', '2', '3'); // stub
$task_name = $task_desc = $postal_code = $start_dt = $end_dt = $address = $category = $bid = '';
$user_id = $_SESSION[USER_ID];
$bidding_deadline = NULL; // NULL if no deadline set.

// make sure the selected categories is in the database
if (isset($_POST['submit'])) {
    $isAllDataValid = true;
    if (empty($_POST[TASK_NAME])) {
        $isAllDataValid = false;
        $task_name_err = true;
    } else {
        $task_name = htmlspecialchars($_POST[TASK_NAME]);
    }

    if (empty($_POST[TASK_DESC])) {
        $isAllDataValid = false;
        $task_desc_err = true;
    } else {
        $task_name = htmlspecialchars($_POST[TASK_DESC]);
    }

    if (empty($_POST[POSTAL_CODE])) {
        $isAllDataValid = false;
        $postal_code_err = '';
    } else {
        $postal_code = htmlspecialchars($_POST[POSTAL_CODE]);
        if (is_numeric($postal_code) === false) {
            $isAllDataValid = false;
            $postal_code_err = 'postal code must be numeric';
        } else {
            $postal_code = intval($postal_code);
        }
    }

    if (empty($_POST[ADDRESS])) {
        $isAllDataValid = false;
        $address_err = true;
    } else {
        $postal_code = htmlspecialchars($_POST[ADDRESS]);
    }

    if (empty($_POST[CATEGORY])) {
        $isAllDataValid = false;
    } else {
        if (in_array($_POST[CATEGORY], $categories)) {
            $category = $_POST[CATEGORY];
        } else {
            $isAllDataValid = false;
        }
    }

    if (empty($_POST[START_DT])) {
        $isAllDataValid = false;
        $start_dt_err = true;
    } else {
        $start_dt = date($_POST[START_DT]);
    }

    if (empty($_POST[END_DT])) {
        $isAllDataValid = false;
        $end_dt_err = true;
    } else {
        $start_dt = date($_POST[START_DT]);
    }

    if (empty($_POST[PRICE])) {
        $isAllDataValid = false;
        $price_err = '';
    } else {
        $bid = htmlspecialchars($_POST[PRICE]);
        if (!is_numeric($bid)) {
            $isAllDataValid = false;
            $price_err = 'Price must be numeric';
        } else {
            $bid = floatval($bid);
        }
    }

    if ($isAllDataValid === true) {
        // attempt to insert into the database
        if (!empty($_POST[BIDDING_DEADLINE])) {
            $bidding_deadline = htmlspecialchars($_POST[BIDDING_DEADLINE]);
        } else {
            $bidding_deadline = NULL;
        }

        $statement = 'inserting the task into db';
        $query = 'INSERT INTO tasks (name, owner, description, category, postal_code, address, start_datetime, end_datetime, price, bidding_deadline)
                      VALUES($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)';
        $result = pg_prepare($dbh, $statement, $query);
        $params = array($task_name, $user_id, $task_desc, $category, $postal_code, $address, $start_dt, $end_dt, $bid, $bidding_deadline);
        $result = pg_execute($dbh, $statement, $params);

        if ($result !== false) {
            // added successfully. header?
            header('Location: index.php'); // maybe some message to display?
            exit;
        } else {
            $general_form_err = 'Insertion into the database has failed. Please try again';
        }
    } else {
        $general_form_err = 'One or more mandatory fields are not set and/or contains invalid values';
    }
}
?>

<?php
include_once '../utils/navbar.php';
?>

<div class="container">

    <div class="row align-items-center">

        <div class="col-9 offset-1">
            <div class="text-center"><h2>Create a Task</h2></div>
            <hr>

            <form action="" method="POST">
                <div class="row">
                    <div class="col-6">
                        <fieldset about="task_details">
                            <legend>Task details</legend>

                            <div class="form-group  <?php echo isset($task_name_err) ? 'has-danger' : '' ?>">
                                <label class="form-control-label" for="task_name">Task Name: </label>
                                <input class="form-control <?php echo isset($task_name_err) ? 'form-control-danger' : '' ?>"
                                       type="text" id="task_name" name="task_name" value="<?php echo $task_name; ?>"
                                       placeholder="Task name">
                            </div>

                            <div class="form-group ">
                                <label class="form-control-label" for="creator">Created By: </label>
                                <input class="form-control" type="text" id="creator" name="creator" readonly="readonly"
                                       value="<?php echo $_SESSION[USER_ID]; ?>">
                            </div>

                            <?php make_select_from_array($categories) ?>

                            <div class="form-group <?php echo isset($task_desc_err) ? 'has-danger' : '' ?>">
                                <label class="form-control-label" for="task_desc">Task Description: </label>
                                <textarea rows="6"
                                          class="form-control <?php echo isset($task_desc_err) ? 'form-control-danger' : '' ?>"
                                          type="text" id="task_desc" name="task_desc"
                                          placeholder="Describe your task"><?php echo $task_desc; ?></textarea>
                            </div>


                        </fieldset> <!--details-->
                    </div><!-- col -->

                    <div class="col-6">
                        <fieldset about="Location">
                            <legend>Location</legend>
                            <div class="form-group <?php echo isset($postal_code_err) ? 'has-danger' : '' ?>">
                                <label class="form-control-label" for="postal_code">Postal Code: </label>
                                <input class="form-control <?php echo isset($postal_code_err) ? 'form-control-danger' : '' ?>"
                                       type="text" id="postal_code" name="postal_code"
                                       value="<?php echo $postal_code; ?>" placeholder="postal code">
                                <span class="error text-danger"><?php echo !empty($postal_code_err) ? $postal_code_err : ''; ?></span>
                            </div>

                            <div class="form-group  <?php echo isset($address_err) ? 'has-danger' : '' ?>">
                                <label class="form-control-label" for="address">Address: </label>
                                <input class="form-control <?php echo isset($address_err) ? 'form-control-danger' : '' ?>"
                                       type="text" id="address" name="address" value="<?php echo $address; ?>"
                                       placeholder="address">
                            </div>

                        </fieldset> <!-- Location -->

                        <fieldset about="Date and Time">
                            <legend>Date and Time</legend>

                            <div class="form-group <?php echo isset($start_dt_err) ? 'has-danger' : '' ?>">
                                <label class="form-control-label" for="start_dt">Start Date/Time: </label>
                                <input class="form-control <?php echo isset($start_dt_err) ? 'form-control-danger' : '' ?>"
                                       id="start_dt" type="datetime-local" name="start_dt"
                                       value="<?php echo $start_dt; ?>"/>
                            </div>

                            <div class="form-group <?php echo isset($end_dt_err) ? 'has-danger' : '' ?>">
                                <label class="form-control-label" for="end_dt">End Date/Time: </label>
                                <input class="form-control <?php echo isset($end_dt_err) ? 'form-control-danger' : '' ?>"
                                       id="end_dt" type="datetime-local" name="start_dt" value="<?php echo $end_dt; ?>">
                            </div>

                        </fieldset> <!-- date and time -->
                    </div>
                </div> <!-- row -->
                <div class="row">
                    <div class="col-6">
                        <fieldset about="Bidding">
                            <legend>Bidding</legend>
                            <div class="form-group <?php echo isset($price_err) ? 'has-danger' : '' ?>">
                                <label class="form-control-label" for="price">Offer price for your task: </label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span><input
                                            class="form-control <?php echo isset($bid) ? 'form-control-danger' : '' ?>"
                                            type="number" step="0.01" id="price" name="price"
                                            value="<?php echo $bid; ?>"
                                            placeholder="phone number">
                                    <span class="error text-danger"><?php echo !empty($price_err) ? $price_err : '' ?></span>
                                </div>
                            </div>

                            <div class="form-group ">
                                <label class="form-control-label" for="bidding_deadline">Set bidding deadline for your
                                    task: </label>
                                <span>If you want to manually pick the doer, leave this field blank</span>
                                <input class="form-control" type="datetime-local" id="bidding_deadline"
                                       name="bidding_deadline">
                            </div>

                        </fieldset> <!--price-->
                    </div> <!-- col -->
                    <div class="col-6">
                        <div class="text-danger m-1">
                            <?php echo isset($general_form_err) ? $general_form_err : '' ?>
                        </div>
                        <div class="form-group text-center">
                            <input class="btn btn-primary" type="submit" name="submit" value="Submit"/>
                        </div>
                    </div><!-- col -->
                </div> <!-- row -->
            </form>

        </div>

        <!--        <div class="col-5 display-5">-->
        <!--            <p class="text-center"><a href="home.php" >Cancel</a>-->
        <!--        </div>-->
    </div> <!-- row -->

</div>


<!--    make sure this order is correct, and placed near the end of body tag-->
<script type="text/javascript" src="../js/jquery-3.1.1.slim.min.js"></script>
<script type="text/javascript" src="../js/tether.min.js"></script>
<script type="text/javascript" src="../js/bootstrap.min.js"></script>

</body>

</html>