<?php
require_once '../utils/login.inc.php';
login_validate_or_redirect();
require_once '../utils/db_con.inc.php';
require_once '../utils/db_func.inc.php';
$task_array = get_task_array_or_redirect($dbh);
if ($_SESSION[EMAIL] !== $task_array[DB_OWNER] || $task_array[DB_STATUS] !== STATUS_OPEN) {
    // id does not exist or the user is not the owner or the task has already been bidded/assigned/completed
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
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <title>Edit task</title>
</head>

<body>

<?php
require_once '../utils/category_listing.inc.php';

$categories = get_task_categories($dbh);
$php_to_html_date_format = 'Y-m-d\TH:i';
$postgres_to_php_format = 'Y-m-d H:i:sP';
$php_to_postgres_format = 'Y-m-d H:i:s';
// get values from database first
$task_desc = $task_array[DB_DESC];
$task_id = $_GET[TASK_ID];
$task_name = $task_array[DB_NAME];
$address = $task_array[DB_ADDRESS];
$postal_code = $task_array[DB_POSTAL_CODE];
$task_owner = $task_array[DB_OWNER];
$task_category = $task_array[DB_CATEGORY];
$start_dt = DateTime::createFromFormat($postgres_to_php_format, $task_array[DB_START_DT]);
//var_dump(DateTime::getLastErrors()); this will output array so you must use var_dump, not echo.
$end_dt = DateTime::createFromFormat($postgres_to_php_format, $task_array[DB_END_DT]);
$suggested_price = $task_array[DB_SUGGESTED_PRICE];
$suggested_price = preg_replace("/[^0-9.]/", "", $suggested_price);
$bidding_deadline = DateTime::createFromFormat($postgres_to_php_format, $task_array[DB_BIDDING_DEADLINE]);
$user_email = $_SESSION[EMAIL];

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
        $task_desc = htmlspecialchars($_POST[TASK_DESC]);
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
        $address = htmlspecialchars($_POST[ADDRESS]);
    }

    if (empty($_POST[CATEGORY])) {
        $isAllDataValid = false;
    } else {
        if (in_array($_POST[CATEGORY], $categories)) {
            $category = $_POST[CATEGORY];
        } else {
            $isAllDataValid = false;
            var_dump($categories);
            echo $_POST[CATEGORY];
        }
    }


    /// time-related inputs ///
    $current_time = new DateTime();
    if (empty($_POST[START_DT])) {
        $isAllDataValid = false;
        $start_dt_err = true;
    } else {
        $start_dt = new DateTime(htmlspecialchars($_POST[START_DT]));
        if ($current_time > $start_dt) {
            $isAllDataValid = false;
            $start_dt_err = true;
        }
    }

    if (empty($_POST[END_DT])) {
        $isAllDataValid = false;
        $end_dt_err = true;
    } else {
        $end_dt = new DateTime(htmlspecialchars($_POST[END_DT]));
        if ($current_time > $end_dt) {
            $isAllDataValid = false;
            $end_dt_err = true;
        }
    }

    if (empty($_POST[BIDDING_DEADLINE])) {
        $isAllDataValid = false;
        $bidding_deadline_err = true;
    } else {
        $bidding_deadline = new DateTime(htmlspecialchars($_POST[BIDDING_DEADLINE]));
//        echo $bidding_deadline->format('Y-m-d H:i');
        if ($current_time > $bidding_deadline) {
            $isAllDataValid = false;
            $bidding_deadline_err = true;
        }
    }

    // check the relative time, and also that it is more than now
    if ($end_dt < $start_dt) {
        $isAllDataValid = false;
        $end_dt_err = true;
        $start_err = true;
    }

    if ($start_dt < $bidding_deadline) {
        $isAllDataValid = false;
        $start_dt_err = true;
        $bidding_deadline_err = true;
    }

    if (!isset($_POST[PRICE])) {
        $isAllDataValid = false;
        $price_err = '';
    } else {
        $suggested_price = htmlspecialchars($_POST[PRICE]);
        if (!is_numeric($suggested_price)) {
            $isAllDataValid = false;
            $price_err = 'Price must be numeric';
        } else {
            $suggested_price = floatval($suggested_price);
            if (floatval($suggested_price) < 0) {
                $price_err = 'Price must not be negative';
                $isAllDataValid = false;
            }
        }
    }

    if ($isAllDataValid === true) {
        $bidding_deadline = $bidding_deadline->format($php_to_postgres_format);
        $start_dt = $start_dt->format($php_to_postgres_format);
        $end_dt = $end_dt->format($php_to_postgres_format);
        $params = array($task_name, $user_email, $task_desc, $category, $postal_code,
                        $address, $start_dt, $end_dt, $suggested_price, $bidding_deadline, $task_id);
        $result = update_task($dbh, $params);
        if ($result !== false) {
            header('Location: view_task.php?task_id='.$task_id); // maybe some message to display?
            exit;
        } else {
            $general_form_err = 'Update to the database has failed. Please try again';
        }
    } else {
        $general_form_err = 'One or more mandatory fields contain invalid values';
    }
}
?>

<?php
include_once '../utils/html_parts/navbar.php';
?>

<div class="container">

    <div class="row align-items-center">

        <div class="col-9 offset-1">
            <div class="text-center mt-4"><h2>Edit task</h2></div>
            <hr>

            <form action="" method="POST">
                <div class="row">
                    <div class="col-6">
                        <fieldset about="task_details">
                            <legend class="text-center">Task details</legend>

                            <div class="form-group  <?php echo isset($task_name_err) ? 'has-danger' : '' ?>">
                                <label class="form-control-label" for="task_name">Task Name: </label>
                                <input class="form-control <?php echo isset($task_name_err) ? 'form-control-danger' : '' ?>"
                                       type="text" id="task_name" name="task_name" value="<?php echo $task_name; ?>"
                                       placeholder="Task name">
                            </div>

                            <div class="form-group ">
                                <label class="form-control-label" for="creator">Created By: </label>
                                <input class="form-control" type="text" id="creator" name="creator" readonly="readonly"
                                       value="<?php echo $_SESSION[EMAIL]; ?>">
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
                            <legend class="text-center">Location</legend>
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
                            <legend class="text-center">Date and Time</legend>

                            <div class="form-group <?php echo isset($start_dt_err) ? 'has-danger' : '' ?>">
                                <label class="form-control-label" for="start_dt">Start Date/Time: </label>
                                <input class="form-control <?php echo isset($start_dt_err) ? 'form-control-danger' : '' ?>"
                                       id="start_dt" type="datetime-local" name="start_dt"
                                       value="<?php echo isset($start_dt)? $start_dt->format($php_to_html_date_format): 'a'; ?>">
                            </div>

                            <div class="form-group <?php echo isset($end_dt_err) ? 'has-danger' : '' ?>">
                                <label class="form-control-label" for="end_dt">End Date/Time: </label>
                                <input class="form-control <?php echo isset($end_dt_err) ? 'form-control-danger' : '' ?>"
                                       id="end_dt" type="datetime-local" name="end_dt"
                                       value="<?php echo isset($end_dt)? $end_dt->format($php_to_html_date_format): 'a'; ?>">
                            </div>


                        </fieldset> <!-- date and time -->
                    </div>
                </div> <!-- row -->
                <div class="row">
                    <div class="col-6">
                        <fieldset about="Bidding">
                            <legend class="text-center">Bidding</legend>
                            <div class="form-group <?php echo isset($price_err) ? 'has-danger' : '' ?>">
                                <label class="form-control-label" for="price">Set a base price for this task: </label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input  class="form-control <?php echo isset($price_err) ? 'form-control-danger' : '' ?>"
                                            type="number" step="0.01" id="price" name="price"
                                            value="<?php echo $suggested_price; ?>"
                                            placeholder="Suggest a price">
                                    <span class="error text-danger"><?php echo !empty($price_err) ? $price_err : '' ?></span>
                                </div>
                            </div>

                            <div class="form-group <?php echo isset($bidding_deadline_err) ? 'has-danger' : '' ?>">
                                <label class="form-control-label" for="bidding_deadline">Set your bidding deadline: </label>
                                <input class="form-control <?php echo isset($bidding_deadline_err) ? 'form-control-danger' : '' ?>"
                                       id="bidding_deadline" type="datetime-local" name="bidding_deadline"
                                       value="<?php echo isset($bidding_deadline)? $bidding_deadline->format($php_to_html_date_format): 'a'; ?>">
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
                        <div class="">
                            <p class="text-center">Or <a href="home.php" >Cancel</a>
                        </div>

                    </div><!-- col -->
                </div> <!-- row -->
            </form>

        </div>

    </div> <!-- row -->

</div>


<!--    make sure this order is correct, and placed near the end of body tag-->
<script type="text/javascript" src="../js/jquery-3.1.1.slim.min.js"></script>
<script type="text/javascript" src="../js/tether.min.js"></script>
<script type="text/javascript" src="../js/bootstrap.min.js"></script>

</body>

</html>