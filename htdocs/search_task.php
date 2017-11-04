<?php
require_once '../utils/login.inc.php';
login_validate_or_redirect();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Search task</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
          integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">

    <!-- Bootstrap CSS  -->
<!--    <link rel="stylesheet" href="../css/bootstrap.min.css">-->

    <style rel="stylesheet">
        .rcorners {
            border-radius: 5px;
            border: 1px solid #c9c9c9;
            padding: 20px;
            background-color: white;
        }

    </style>

</head>

<?php
require_once '../utils/html_parts/task_table.php';
require_once '../utils/db_con.inc.php';
require_once '../utils/db_func.inc.php';
require_once '../utils/category_listing.inc.php';
run_update_function($dbh);

$categories = get_task_categories($dbh);

$address_keywords = $task_keywords = $min_price = $max_price = $start_dt = $category = '';

if (isset($_POST['submit'])) {
    // filter search
    if (isset($_POST[CHECK_TASK_KEYWORDS])) {
        $task_keywords = $_POST[TASK_KEYWORDS];
    }

    if (isset($_POST[CHECK_ADDRESS_KEYWORDS])) {
        $address_keywords = $_POST[ADDRESS_KEYWORDS];
    }

    if (isset($_POST[CHECK_PRICE_MIN])) {
        $min_price = $_POST[PRICE_MIN];
    }

    if (isset($_POST[CHECK_PRICE_MAX])) {
        $max_price = $_POST[PRICE_MAX];
    }

    if (isset($_POST[CHECK_START_DATE])) {
        $start_dt = $_POST[START_DT];
    }

    if (isset($_POST[CHECK_CATEGORY])) {
        $category = $_POST[CATEGORY];
    }

    $open_tasks = get_all_open_tasks($dbh, $task_keywords, $address_keywords,$start_dt,$max_price,$min_price,$category);

} else {
    $open_tasks = get_all_open_tasks($dbh, '', '','','','','');
}

?>
<body style="background-color: #f9f9f9">
<?php
include_once '../utils/html_parts/navbar.php';
?>

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-11 mb-3">
            <h2 class="">Search for tasks</h2>
        </div>
    </div>
    <form action="" method="POST">
        <div class="row">
            <div class="col-11">
                <span class="text-success">To apply filters, tick the checkbox and enter values</span>
                <hr>
            </div>

            <div class="col-4">
                <fieldset about="task_details">
                    <legend class="text-center">Task details</legend>

                    <div class="form-group">
                        <label class="form-control-label" for="task_name">Task keywords: </label>
                        <span class="float-right">
                        <input type="checkbox" name="check_task_keywords" <?php echo !empty($task_keywords) ? 'checked="checked"': '' ?>></span>
                        <input class="form-control"
                               type="text" id="task_name" name="task_keywords" value="<?php echo $task_keywords; ?>"
                               placeholder="search name and description">
                    </div>

                    <?php make_select_from_array_with_checkbox($categories) ?>

                </fieldset> <!--details-->
            </div><!-- col -->

            <div class="col-4">
                <fieldset about="Date and location">
                    <legend class="text-center">Date and location</legend>

                    <div class="form-group">
                        <label class="form-control-label" for="address">Address keywords: </label>
                        <span class="float-right">
                        <input type="checkbox" name="check_address_keywords" <?php echo !empty($address_keywords) ? 'checked="checked"': '' ?>></span>
                        <input class="form-control"
                               type="text" id="address" name="address_keywords" value="<?php echo $address_keywords; ?>"
                               placeholder="search address">
                    </div>

                    <div class="form-group">
                        <label class="form-control-label" for="start_dt">Start date after: </label>
                        <span class="float-right">
                        <input type="checkbox" name="check_start_datetime" <?php echo !empty($start_dt) ? 'checked="checked"': '' ?>></span>
                        <input class="form-control"
                               id="start_dt" type="date" name="start_dt" value="<?php echo $start_dt; ?>"
                               >
                    </div>


                </fieldset> <!-- Location -->

            </div>

            <div class="col-4">
                <div class="row">
                    <div class="col">
                        <fieldset about="Bidding">
                            <legend class="text-center">Price</legend>
                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-control-label" for="min_price">Minimum price: </label>
                            <span class="float-right">
                            <input type="checkbox" name="check_min_price" <?php echo !empty($min_price) ? 'checked="checked"': '' ?>></span>
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input class="form-control"
                                       type="number" step="0.01" id="min_price" name="min_price"
                                       value="<?php echo $min_price; ?>"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-control-label" for="max_price">Maximum price: </label>
                            <span class="float-right">
                            <input type="checkbox" name="check_max_price" <?php echo !empty($max_price) ? 'checked="checked"': '' ?>></span>
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input class="form-control"
                                       type="number" step="0.01" id="max_price" name="max_price"
                                       value="<?php echo $max_price; ?>"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- col -->
        <div class="col--11">
            <div class="text-danger m-1">
                <?php echo isset($general_form_err) ? $general_form_err : '' ?>
            </div>
            <div class="form-group text-center">
                <input class="btn btn-primary" type="submit" name="submit" value="search"/>
            </div>

        </div><!-- col -->
    </form>
    <div class="row align-items-center">
        <div class="col-12 mt-3 rcorners">
            <?php
            echo_tasks_table_all($open_tasks);
            ?>
        </div>

    </div> <!-- row -->
</div> <!-- container -->


<!--    make sure this order is correct, and placed near the end of body tag-->
<!--<script type="text/javascript" src="../js/jquery-3.1.1.slim.min.js"></script>-->
<!--<script type="text/javascript" src="../js/tether.min.js"></script>-->
<!--<script type="text/javascript" src="../js/bootstrap.min.js"></script>-->

<!-- jQuery first, then Tether, then Bootstrap JS. -->
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

</body>
</html>
