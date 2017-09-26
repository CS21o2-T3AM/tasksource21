<php?
//Start the session
session_start();
?>

<html>
<head>
    <title>Welcome to tasksource21</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<h1>Task Source 21</h1>
<h3>The leading web app for sourcing tasks!</h3>
<h2>Login</h2>
<ul style="list-style: none">
<form name="home" action="index.php" method="POST">
    <li>Email:
    <input type="text" name="email"/>
    <li>Password:
    <input type="password"name="inputPassword"/></li>
    <li><input type="submit" name="submit"></li>
</form>
</ul>
<a href="register.php">Register</a>

<?php
// Connect to the database. Please change the password in the following line accordingly
$db     = pg_connect("host=localhost port=5432 dbname=tasksource21 user=postgres password=password");
$result = pg_query($db, "SELECT * FROM users where email = '$_POST[email]'");		// Query template
$row    = pg_fetch_assoc($result);		// To store the result row

if (isset($_POST['submit'])) {
    $serverPassword = $row[password];
    $inputPassword = $_POST['inputPassword'];

    if($inputPassword==$serverPassword){
        header("Location: home.php");
        exit;
    }
    else {
        echo"<h4 style='color: darkred'>Incorrect username/password, please try again!</h4>";
    }
}
if (isset($_POST['new'])) {	// Submit the update SQL command
    $result = pg_query($db, "UPDATE book SET book_id = '$_POST[bookid_updated]',  
    name = '$_POST[book_name_updated]',price = '$_POST[price_updated]',  
    date_of_publication = '$_POST[dop_updated]'");
    if (!$result) {
        echo "Update failed!!";
    } else {
        echo "Update successful!";
    }
}
?>

</body>
</html>