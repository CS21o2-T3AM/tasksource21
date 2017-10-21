<?php
session_start();
?>

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
    require_once '../utils/constants.php';

    if (empty($_POST[EMAIL]) || empty($_POST[PASSWORD])) {
        // either the email or password is not filled in
        $login_err = 'Please enter both email and the password';
    } else {
        // Connect to the database.
        require_once '../utils/db_con.php';

        // get the user info from db.
        $statement = 'selecting user';
        $query = 'SELECT * FROM users WHERE email = $1';

        $result = pg_prepare($dbh, $statement, $query);
        $params = array($_POST[EMAIL]);
        $result = pg_execute($dbh, $statement, $params);

        if ($result === false)
            die("Connection to database failed");

        $row = pg_fetch_assoc($result);

        $server_password = $row[PASSWORD];
        $input_password = $_POST[PASSWORD];
        $is_admin = $row[ADMIN];

        if ($is_admin === "t" && $input_password === $server_password) {
            require_once '../utils/login.php';
            set_session_cookie($_POST[EMAIL], $input_password);

            header("Location: admin.php");
            exit;
        } else if ($input_password === $server_password) {
            require_once '../utils/login.php';
            set_session_cookie($_POST[EMAIL], $input_password);

            header("Location: home.php");
            exit;
        } else {
            $login_err = 'Incorrect username/password, please try again!';
        }
    }
}

?>
<?php
include_once '../utils/navbar.html';
?>

    <div class="container">
        <div class="row justify-content-center">

                <div class="col-5">
                    <form action="index.php" method="POST">
                        <div class="text-center"><h2>Login</h2><br></div>
                        <fieldset about="Login">

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