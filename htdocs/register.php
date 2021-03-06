<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register to Tasksource21!</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">

    <style rel="stylesheet">
        .rcorners {
            border-radius: 5px;
            border: 1px solid #c9c9c9;
            padding: 40px;
            background-color: white;
        }
    </style>

</head>

<?php
// initialize form inputs and error messages
$email = $password = $name = $contact = '';

if (isset($_POST['submit'])) {
    $isAllDataValid = true;

    // ========== define all the constants ========== //
    require_once('../utils/constants.inc.php');

    // ========== checking email ========== //
    if (empty($_POST[EMAIL])) {
        $email_err = '';
        $isAllDataValid = false;
    } else {
        $email = htmlspecialchars($_POST[EMAIL]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // if valid email
            $isAllDataValid = false;
            $email_err = 'Provided email is invalid';
        }
    }

    // ============ checking password, and that it satisfies the password format ========== //
    if (empty($_POST[PASSWORD])) {
        $password_err = true;
        $isAllDataValid = false;
    } else {
        if (empty($_POST[PASS_CONFIRM])) {
            $confirm_pass_err = '';
            $isAllDataValid = false;
        } else {
            if ($_POST[PASSWORD] === $_POST[PASS_CONFIRM]) {
                $password = htmlspecialchars($_POST[PASSWORD]);
            } else {
                $confirm_pass_err = 'passwords do not match';
                $isAllDataValid = false;
            }
        }
    }

    // ============ checking the name number ========== //
    if (empty($_POST[NAME])) {
        $name_err = true;
        $isAllDataValid = false;
    } else {
        $name = htmlspecialchars($_POST[NAME]);
    }

    // ============ checking the contact number ========== //
    if (empty($_POST[CONTACT])) {
        $isAllDataValid = false;
        $contact_err = '';
    } else {
        $contact = $_POST[CONTACT];
        if (!is_numeric($contact)) {
            $contact_err = 'Contact number must not include non-digit characters';
        } else {
            if (strlen($contact) !== 8) {
                $isAllDataValid = false;
                $contact_err = 'Phone number must be Singaporean 8-digit';
            }
        }
    }

    if ($isAllDataValid === true) {
        require_once '../utils/db_con.inc.php';
        require_once '../utils/db_func.inc.php';

        if (check_user_not_exist($dbh, $email) === true) {
            require_once '../utils/db_func.inc.php';
            $password_hash = hash('sha256', $password, false);
            $params = array($email, $password_hash, $name, $contact);
            $result = insert_new_user($dbh, $params);

            if ($result !== false) { // don't check $result===true as if successful, does NOT return a boolean
                require_once '../utils/login.inc.php';
                set_session_and_redirect($email, false);
            } else {
                echo "Insert failed. Please try again later";
            }
        } else {
            $email_err = "A user with the same email already exists";
        }
    } else {
        $general_form_err = 'One or more fields contain invalid values';
    }
}

?>
<body style="background-color: #f9f9f9">
<?php
    include_once '../utils/html_parts/navbar.php';
?>
    <div class="container">

        <div class="row align-items-center">

            <div class="col-5 offset-1 mt-3 rcorners">

                <form action="" method="POST">

                    <fieldset about="register">
                        <legend class="text-center">Register</legend>
                    <div class="form-group row <?php echo isset($name_err)? 'has-danger' : ''?>">
                        <label class="form-control-label" for="name">Name: </label>
                        <input class="form-control <?php echo isset($name_err)? 'form-control-danger' : '' ?>" type="text" id="name" name="name" value="<?php echo $name;?>" placeholder="Your full name">
                    </div>

                    <div class="form-group row <?php echo isset($email_err)? 'has-danger' : ''?>">
                        <label class="form-control-label" for="email">Email: </label>
                        <input class="form-control <?php echo isset($email_err)? 'form-control-danger' : ''?>" type="email" id="email" name="email" value="<?php echo $email;?>" placeholder="enter email">
                        <span class="error text-danger"><?php echo !empty($email_err)? $email_err : ''?></span>
                    </div>

                    <div class="form-group row <?php echo isset($password_err)? 'has-danger' : ''?>">
                        <label class="form-control-label" for="password">Password: </label>
                        <input class="form-control <?php echo !empty($password_err)? 'form-control-danger' : ''?>" id="password" type="password" name="password"/>
                    </div>

                    <div class="form-group row <?php echo isset($confirm_pass_err)? 'has-danger' : ''?>">
                        <label class="form-control-label" for="confirm_pass">Confirm password: </label>
                        <input class="form-control <?php echo isset($confirm_pass_err)? 'form-control-danger' : ''?>" id="confirm_pass" type="password" name="confirm" />
                        <span class="error text-danger"><?php echo !empty($confirm_pass_err)? $confirm_pass_err: '';?></span>
                    </div>

                    <div class="form-group row <?php echo isset($contact_err)? 'has-danger' : ''?>">
                    <label class="form-control-label" for="contact">Contact number (8-digit)    : </label>
                    <input class="form-control <?php echo isset($contact_err)? 'form-control-danger' : ''?>" type="tel" id="contact" name="contact" value="<?php echo $contact;?>" placeholder="phone number">
                    <span class="error text-danger"><?php echo !empty($contact_err)? $contact_err: '';?></span>
                    </div>

                    </fieldset>

                    <div class="row text-danger">
                    <?php echo !empty($general_form_err) ? $general_form_err : ''?>
                    </div>

                    <div class="form-group row mt-2">
                    <input class="btn btn-primary" type="submit" name="submit" value="Submit"/>
                    </div>
                </form>

            </div>

            <div class="col-5 display-5">
                <p class="text-center">Already a user?  <a href="index.php" >Login here</a>
            </div>
        </div>


    </div>

<!--    <!--    make sure this order is correct, and placed near the end of body tag-->-->
<!--    <script type="text/javascript" src="../js/jquery-3.1.1.slim.min.js"></script>-->
<!--    <script type="text/javascript" src="../js/tether.min.js"></script>-->
<!--    <script type="text/javascript" src="../js/bootstrap.min.js"></script>-->

    <!-- jQuery first, then Tether, then Bootstrap JS. -->
    <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

</body>

</html>