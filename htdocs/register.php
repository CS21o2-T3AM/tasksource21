<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Register to Tasksource21!</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">

</head>

<body>

<?php

session_start();

// initialize form inputs and error messages
$email = $password = $dob = $name = $contact = '';
$userId_err = $password_err = $confirm_pass_err = $dob_err = $name_err = $contact_err = '';

if (isset($_POST['submit'])) {
    $field_empty_format = "%s cannot be empty";
    $isAllDataValid = true;

    // ========== define all the constants ========== //
    require_once('../utils/constants.php');

    // ========== checking userID/email ========== //
    if (empty($_POST[USER_ID])) {
        $userId_err = sprintf($field_empty_format, 'email');
        $isAllDataValid = false;
    } else {
        $email = htmlspecialchars($_POST[USER_ID]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // if valid email
            $isAllDataValid = false;
            $userId_err = 'Provided email is invalid';
        }
    }

    // ============ checking password, and that it satisfies the password format ========== //
    if (empty($_POST[PASSWORD])) {
        $password_err = sprintf($field_empty_format, PASSWORD);
        $isAllDataValid = false;
    } else {
        if (empty($_POST[PASS_CONFIRM])) {
            $confirm_pass_err = sprintf($field_empty_format, 'confirm password field');
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

    // ============ checking date of birth ========== //
    if (empty($_POST[DATE_OF_BIRTH])) {
        $dob_err = sprintf($field_empty_format, 'date of birth');
        $isAllDataValid = false;
    } else {
        $dob = htmlspecialchars($_POST[DATE_OF_BIRTH]);
    }

    // name field
    if (empty($_POST[NAME])) {
        $name_err = sprintf($field_empty_format, NAME);
        $isAllDataValid = false;
    } else {
        $name = $_POST[NAME];
    }

    // ============ checking the contact number ========== //
    if (empty($_POST[CONTACT])) {
        $contact_err = sprintf($field_empty_format, CONTACT);
        $isAllDataValid = false;
    } else {
        $contact = $_POST[CONTACT];
    }

    // in PHP we cannot do things like this (early return for error checking).
    // Either use functions, or include files within which you can return early (returning inside included
    // files will return the control back to the caller

//    if ($isAllDataValid === false) {
//        die;
//    }

    if ($isAllDataValid === true) {
        $is_user_exists = false;

        require_once('../utils/db_con.php');

        $statement = 'checking for duplicate user';
        $query = 'SELECT * FROM users WHERE email=$1';

        $result = pg_prepare($dbh, $statement, $query);

        $params = array($email);
        $result = pg_execute($dbh, $statement, $params);

        if ($result !== false && pg_numrows($result) === 0) {
            $statement = 'inserting new user';

            $query = 'INSERT INTO users (email, password, name, dateOfBirth, admin, phone) VALUES ($1, $2, $3, $4, $5, $6)';
            $result = pg_prepare($dbh, $statement, $query);

            $params = array($email, $password, $name, $dob, 'false', $contact);
            $result = pg_execute($dbh, $statement, $params);

            if ($result !== false) { // don't check $result===true as if successful, does NOT return a boolean
                $_SESSION['userName'] = $name;
                $_SESSION['userEmail'] = $email;
                header("Location: home.php"); //send user to his/her homepage
                exit;
            } else {
                echo "Insert failed. Please try again later";
            }

        } else {
            $userId_err = "A user with the same email already exists";
        }

        /// close the connection to database
        if (isset($dbh)) {
            pg_close($dbh);
        }

    }
}

?>
<?php
    include_once '../utils/navbar.html';
?>
    <div class="container">

        <div class="row align-items-center">

            <div class="col-5 offset-1">
                <div class="text-center"><h2>Register</h2></div>
                <hr>

                <form action="" method="POST">

                    <div class="form-group row">
                    <label class="form-control-label" for="name">Name: </label>
                    <input class="form-control" type="text" id="name" name="name" value="<?php echo $name;?>" placeholder="Your full name">
                    <span class="error text-danger"><?php echo $name_err;?></span>
                    </div>

                    <div class="form-group row">
                    <label class="form-control-label" for="userid">Email: </label>
                    <input class="form-control" type="email" id="email" name="userid" value="<?php echo $email;?>" placeholder="enter email">
                    <span class="error text-danger"><?php echo $userId_err;?></span>
                    </div>

                    <div class="form-group row">
                    <label class="form-control-label" for="password">Password: </label>
                    <input class="form-control" id="password" type="password" name="password"/>
                    <span class="error text-danger"><?php echo $password_err;?></span>
                    </div>

                    <div class="form-group row">
                    <label class="form-control-label" for="confirm_pass">Confirm password: </label>
                    <input class="form-control" id="confirm_pass" type="password" name="confirm" />
                    <span class="error text-danger"><?php echo $confirm_pass_err;?></span>
                    </div>

                    <div class="form-group row">
                    <label class="form-control-label" for="dob">Date of Birth: </label>
                    <input class="form-control" id="dob" type="date" name="dob" value="<?php echo $dob;?>">
                    <span class="error text-danger"><?php echo $dob_err;?></span>
                    </div>

                    <div class="form-group row">
                    <label class="form-control-label" for="contact">Contact number: </label>
                    <input class="form-control" type="tel" id="contact" name="contact" value="<?php echo $contact;?>" placeholder="phone number">
                    <span class="error text-danger"><?php echo $contact_err;?></span>
                    </div>

                    <div class="form-group row">
                    <input class="btn btn-primary" type="submit" name="submit" value="Submit"/>
                    </div>
                </form>

            </div>

            <div class="col-5 display-5">
                <p class="text-center">Already a user?  <a href="index.php" >Login here</a>
            </div>
        </div>


    </div>

    <!--    make sure this order is correct, and placed near the end of body tag-->
    <script type="text/javascript" src="../js/jquery-3.1.1.slim.min.js"></script>
    <script type="text/javascript" src="../js/tether.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>

</body>

</html>