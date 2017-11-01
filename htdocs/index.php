<!DOCTYPE html>
<html lang="en">

<head>
    <title>Welcome to tasksource21</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>

<?php
$login_err = '';

if (isset($_POST['submit'])) {
    require_once '../utils/constants.inc.php';

    if (empty($_POST[EMAIL]) || empty($_POST[PASSWORD])) {
        // either the email or password is not filled in
        $login_err = 'Please enter both email and the password';
    } else {
        // Connect to the database.
        require_once '../utils/db_con.inc.php';
        require_once '../utils/db_func.inc.php';
        $result = check_user_login($dbh, $_POST[EMAIL], $_POST[PASSWORD]);
        if ($result === false)
            die("Connection to database failed");

        if ($result === 2) { // admin
            require_once '../utils/login.inc.php';
            set_session_and_redirect($_POST[EMAIL], true);
        } else if ($result === 1) { // normal user
            require_once '../utils/login.inc.php';
            echo 'logged in';
            set_session_and_redirect($_POST[EMAIL], false);
        } else { // user not registered
            $login_err = 'Incorrect username/password, please try again!';
        }
    }
}

?>
<?php
include_once '../utils/html_parts/navbar.php';
?>

    <div class="container">
        <div class="row justify-content-center">

            <div class="col-5 mt-5">
                <form action="index.php" method="POST">
                    <fieldset about="Login">
                        <legend class="text-center">Login</legend>

                    <div class="form-group">
                        <label for="email" class="form-control-label">Email:</label>
                        <input class="form-control" id = "email" type="email" name="email" placeholder="Your email"/>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-control-label">Password: </label>
                        <input class="form-control" id="password" type="password" name="password"/>
                        <span class="error text-danger"><?php echo $login_err; ?></span>
                    </div>

                    <input class="btn btn-primary" type="submit" name="submit" value="Login">
                    <span class="m-3">Or <a href="register.php">Register</a></span>
                    </fieldset>
                </form>

            </div><!--col-->
        </div><!--row -->
    </div>

    <!--    make sure this order is correct, and placed near the end of body tag-->
    <script type="text/javascript" src="../js/jquery-3.1.1.slim.min.js"></script>
    <script type="text/javascript" src="../js/tether.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
</body>
</html>