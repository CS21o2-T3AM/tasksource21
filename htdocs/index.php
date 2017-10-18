<!DOCTYPE html>
<html lang="en">
<head>
    <title>Welcome to tasksource21</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>

<?php
session_start();
$login_err = '';

if (isset($_POST['submit'])) {
    require_once '../utils/constants.php';

    if(empty($_POST[EMAIL]) || empty($_POST[PASSWORD])) {
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

        $_SESSION['userName'] = $row[NAME];
        $_SESSION['userEmail'] = $row[EMAIL];

        if ($is_admin === "t" && $input_password === $server_password) {
            header("Location: admin.php");
            exit;
        } else if ($input_password === $server_password) {
            header("Location: home.php");
            exit;
        } else {
            $login_err = "<h4 style='color: darkred'>Incorrect username/password, please try again!</h4>";
        }
    }
}

?>

<h1>Task Source 21</h1>
<h3>The leading web app for sourcing tasks!</h3>
<h2>Login</h2>
<ul style="list-style: none">
<form name="home" action="index.php" method="POST">
    <li>Email:
        <input type="email" name="email"/></li>
    <li>Password:
        <input type="password"name="password"/></li>
    <?php echo $login_err; ?>
    <li><input type="submit" name="submit" value="Login"></li>


</form>
</ul>
<a href="register.php">Register</a>

</body>
</html>