<?php
require_once '../utils/login.inc.php';
admin_login_validate_or_redirect();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Control Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        thead {
            position: relative;
        }

        tr:hover{background-color:#f5f5f5}
    </style>

</head>

<body>

<!--  Navigation Bar --->
<nav class="navbar navbar-default">
    <div class="container-fluid"  style="background-color:slategrey; color:ghostwhite;">

        <!--Logo-->
        <div class="navbar-header" style="color:white; float:left; size: 30px" >
            <h2 style="color:white">TASKSOURCE21 </h2>
        </div>

        <!--Menu Items-->
        <div style='float: right; margin-right:10px; margin-top: 18px' >
            <form name="home" action="index.php" method="POST">
                <button type="submit" name="logout" style="background-color:white; color:grey; border-radius: 5px;  align-content: center; vertical-align: middle;">Log Out</button>
            </form>
        </div>

    </div>
</nav>

<!--Header-->
<div class="container">
    <div class="'page-header">
    <h2  style="color:darkslategrey">Admin Control Panel</h2>
    </div>
</div>

<div class="container" id="page-content-wrapper">

    <!-- Menu -->
    <div  align='right' class='container' id="wrapper" style="">
        <div class='btn-group btn-group-lg' role="group">
           <a href="admin.php" class="btn btn-default active">Users</a>
           <a href="admintasks.php" class="btn btn-default">Tasks</a>
           <a href="adminbids.php" class="btn btn-default">Bids</a>

            <a href="admincategories.php" class="btn btn-warning ">Task Categories</a>
        </div>


    </div>

    <?php
    //Connect to Database
    try {
        $dbuser = 'postgres';
        $dbpass = 'password';
        $host = '127.0.0.1';
        $dbname='tasksource21';

        $connec = new PDO("pgsql:host=$host;dbname=$dbname", $dbuser, $dbpass);;
    }catch (PDOException $e) {
        echo "Error : " . $e->getMessage() . "<br/>";
        die();
    }

    //Log out button pressed
    if (isset($_POST['logout'])){
        //pass email and username to next page
        header("Location: index.php"); //send user to the next page
        exit;
    }

    //DISPLAY All USERS

    echo "<h2>User Accounts</h2>";
    //Search Bar
    echo "<form name='searchUsers' action='' method='POST'>";
    echo   "<br/> <small>Search Users (Email/ Name)</small><br/>";
    echo      "<input type='text' name='userName' value='' style=''/>";
    echo    "<button type='submit' name='searchUsers' value='Search' ><span class='glyphicon glyphicon-search'></span></button>";
    echo"</form>";


    echo "<br/>";

    if (isset($_POST['searchUsers'])) {

        $userInput = $_POST['userName'];
        //Dynamically display Tables

        //Display all Users by default
        $sql = 'select * from users ORDER BY email ASC';

        if(strpos($userInput, '@')){
            //Search by email
            echo "Searching by Email: ".$userInput;
            //Query using ILIKE
            $sql = 'select * from users where email ILIKE '."'%".$userInput."%' ORDER BY email ASC";
        }
        else if(!empty($userInput)){
            //Search by bidName
            echo "Searching by User's Name: ".$userInput;
            $sql = 'select * from users where name ILIKE '."'%".$userInput."%' ORDER BY email ASC";
        }
        else{
            //If all else fails, display default
            $sql = 'select * from users ORDER BY email ASC ';
        }

        echo "<div style='height: 300px; width: auto ; font-size: 16px; overflow: auto;border:2px solid darkgray; border-radius:5px;'>";
        echo "<table class='table table-bordered table-striped table-hover'>";
        echo "<tr>";
        echo "<th align='center' width='100'>Email</th>";
        echo "<th align='center' width='50'>Name</th>";
        echo "<th align='center' width='20'>Phone</th>";
        echo "<th align='center' width='5'>Admin</th>";

        foreach ($connec->query($sql) as $row)
        {
            echo "<tr>";
            echo "<td align='center' width='100'><a href=\"adminuserdetail.php?targetuseremail={$row['email']}&useremail={$email}\">".$row['email']."</a></td>";
            echo "<td align='center' width='50'>" . $row['name'] . "</td>";
            echo "<td align='center' width='20'>" . $row['phone'] . "</td>";
            echo "<td align='center' width='5'>" . $row['is_admin'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    }
    else{
        $sql = 'select * from users ORDER BY email ASC ';

    echo "<div style='height: 300px; width: auto ; font-size: 16px; overflow: auto;border:2px solid darkgray; border-radius:5px;'>";
    echo "<table class='table table-bordered table-striped table-hover'>";
    echo "<tr>";
    echo "<th align='center' width='100'>Email</th>";
    echo "<th align='center' width='50'>Name</th>";
    echo "<th align='center' width='20'>Phone</th>";
    echo "<th align='center' width='5'>Admin</th>";

    foreach ($connec->query($sql) as $row)
    {
        echo "<tr>";
        echo "<td align='center' width='100'><a href=\"adminuserdetail.php?targetuseremail={$row['email']}&useremail={$email}\">".$row['email']."</a></td>";
        echo "<td align='center' width='50'>" . $row['name'] . "</td>";
        echo "<td align='center' width='20'>" . $row['phone'] . "</td>";
        echo "<td align='center' width='5'>" . $row['is_admin'] . "</td>";
        echo "</tr>";
    }
    }
    echo "</div>";//user wrapper div
    ?>

</div> <!--page-content-wrapper div-->
</body>
</html>
