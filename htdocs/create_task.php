<?php
require_once '../utils/login.inc.php';
login_validate_or_redirect();
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">

    <!-- jQuery first, then Tether, then Bootstrap JS. -->
    <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
    <title>Create Task</title>
</head>

<body>

<?php
require_once '../utils/constants.inc.php';
require_once '../utils/db_func.inc.php';
require_once '../utils/category_listing.inc.php';
require_once '../utils/db_con.inc.php';

$categories = get_task_categories($dbh);
$task_name = $task_desc = $postal_code = $address = $category = $suggested_price = '';
$user_email = $_SESSION[EMAIL];
$php_to_html_date_format = 'Y-m-d\TH:i';

// default values for time. Tomorrow, Day after tomorrow, two days after tommorow
$bidding_deadline = new DateTime(); $bidding_deadline->add(new DateInterval('P1D'));
$start_dt = new DateTime(); $start_dt->add(new DateInterval('P2D'));
$end_dt = new DateTime(); $end_dt->add(new DateInterval('P3D'));

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
            if (strlen($postal_code) !== 6) {
                $isAllDataValid = false;
                $postal_code_err = 'Postal code must be Singaporean 6-digit';
            }
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

    if (empty($_POST[PRICE])) {
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
        $php_to_postgres_format = 'Y-m-d H:i:s';
        $bidding_deadline = $bidding_deadline->format($php_to_postgres_format);
        $start_dt = $start_dt->format($php_to_postgres_format);
        $end_dt = $end_dt->format($php_to_postgres_format);
        $params = array($task_name, $user_email, $task_desc, $category, $postal_code, $address, $start_dt, $end_dt, $suggested_price, $bidding_deadline);
        $result = insert_new_task($dbh, $params);
        if ($result !== false) {
            header('Location: home.php'); // maybe some message to display?
            exit;
        } else {
            $general_form_err = 'Insertion into the database has failed. Please try again';
        }
    } else {
        $general_form_err = 'One or more mandatory fields contain invalid values';
    }
}
?>

<?php
include_once '../utils/html_parts/navbar.php';
?>

<div class="container mt-3">

    <div class="row align-items-center">

        <div class="col-9 offset-1">
            <div class="text-center mt-4"><h2>Create a Task</h2></div>
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
                                    <input  class="form-control <?php echo isset($suggested_price) ? 'form-control-danger' : '' ?>"
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