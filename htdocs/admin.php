<?php
// Use session to pass information such as email.
//Note input validation not done yet
session_start();
$_SESSION[EMAIL] = "admin@gmail.com";
$_SESSION[PASSWORD]='password';

$email=$_SESSION["EMAIL"];

//Authentication check
//if($email==""){
//    header("Location: index.php");
//    exit;
//}

?>
<!DOCTYPE html>


<head>
    <title>Admin Control Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>li {list-style: none;}</style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet'  type='text/css'>

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
    <div class="container-fluid"  style="background-color:slategrey; color:ghostwhite; border-radius: 5px">

        <!--Logo-->
        <div class="navbar-header" style="color:white; float:left" >
            <h2 href="#" style="color:white">TASKSOURCE21 <small  style="color:lightgrey"> - Admin Control Panel</small></h2>
        </div>

        <!--Menu Items-->
        <div>
            <form name="home" action="index.php" method="POST">
                <button type="submit" name="logout" style="color:white; border-radius: 5px">Log Out</button>
            </form>
        </div>
    </div>
</nav>


<div class="container" name="page-content-wrapper">
    <!--welcome message-->
    <h3 align="right">Welcome back, <?php echo $name; ?>!</h3>

    <!-- Sidebar -->
    <div id="wrapper">
        <ul class="sidebar-nav">
            <li>Users</li>
            <li>Tasks</li>
            <li>Bids</li>
        </ul>
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
    echo "<div class='container' name='user-wrapper' id='user-wrapper'>";
    echo "<h2>User Accounts</h2>";
    echo "<form name='searchUsers' action='' method='POST'>";
    echo   "<li>Search Users(Email/Name): ";
    echo      "<input type='text' name='userName' value=''/>";
    echo    "<input type='submit' name='searchUsers' value='Search'>";
    echo"</form>";
    echo "<br/>";

    $_POST['searchUsers'] = true;
    if (isset($_POST['searchUsers'])) {
        echo $_POST['userName'];

        $userInput = $_POST['userName'];
        //Dynamically display Tables
        echo "<div style='height: 300px; width: auto; font-size: 16px; overflow: auto;border:2px solid darkgray; border-radius:5px;''>";
        //Display all Users by default
        $sql = 'select * from users';

        if(strpos($userInput, '@')){
            //Search by email
            echo "Searching by Email: ".$userInput;
            //Query using ILIKE
            $sql = 'select * from users where email ILIKE '."'%".$userInput."%'";
        }
        else if(!empty($userInput)){
            //Search by bidName
            echo "Searching by User's Name: ".$userInput;
            $sql = 'select * from users where name ILIKE '."'%".$userInput."%'";
        }
        else{
            //If all else fails, display default
            echo "<small style='color: lightgrey; text-align: center' >Showing all Users</small>";
            $sql = 'select * from users';
        }


        echo "<table class='table table-bordered table-striped table-hover'>";
        echo "<tr>";
        echo "<th align='center' width='200'>Email</th>";
        echo "<th align='center' width='200'>Name</th>";
        echo "<th align='center' width='50'>Phone</th>";
        echo "<th align='center' width='10'>Administrative Rights</th>";

        foreach ($connec->query($sql) as $row)
        {
            echo "<tr>";
            echo "<td align='center' width='200'><a href=\"adminuser.php?targetuseremail={$row['email']}&useremail={$email}\">".$row['email']."</a></td>";
            echo "<td align='center' width='200'>" . $row['name'] . "</td>";
            echo "<td align='center' width='200'>" . $row['phone'] . "</td>";
            echo "<td align='center' width='200'>" . $row['is_admin'] . "</td>";
            echo "</tr>";}

        echo "</table>";
        echo "</div>";
    }
    echo "</table>";
    echo "</div>";//user wrapper div
    echo "<br/>";
    echo "<br/>";

    //DISPLAY ALL TASKS
    echo "<div class='container' name='task-wrapper'>";
    echo "<h2>Tasks</h2>";
    echo "<form name='searchTasks' action='' method='POST'>";
    echo   "<li>Search Tasks: ";
    echo      "<input type='text' name='taskName'/>";
    echo    "<input type='submit' name='searchTasks' value='Search'>";
    echo "<br/>";

    $_POST['searchTasks'] = true;
    if (isset($_POST['searchTasks'])) {
        $userInput =  $_POST['taskName'];

        //Display all Tasks by default
        $sql = 'select * from tasks';
        if(strpos($userInput, '@')){
            //Search by email
            echo "Searching by Owner Email: ".$userInput;
            //Query using ILIKE
            $sql = 'select * from tasks where owner_email ILIKE '."'%".$userInput."%'";
        }
        else if(!empty($userInput)){
            //Search by bidName
            echo "Searching by Task Name: ".$userInput;
            $sql = 'select * from tasks where name ILIKE '."'%".$userInput."%'";
        }
        else{
            //If all else fails, display default
            echo "<small style='color: lightgrey; text-align: center' >Showing all Tasks</small>";
            $sql = 'select * from tasks';
        }

        //Dynamic Task display
        echo "<div style='height: 300px; width: auto; font-size: 16px; overflow: auto;border:2px solid darkgray; border-radius:5px;''>";
        echo "<table class='table table-bordered table-striped table-hover'>";
        echo "<tr>";
        echo "<th align='center' width='200'>ID</th>";
        echo "<th align='center' width='200'>Task Name</th>";
        echo "<th align='center' width='200'>Owner</th>";
        echo "<th align='center' width='200'>Category</th>";
        echo "<th align='center' width='200'>Start</th>";
        echo "<th align='center' width='200'>End</th>";
        echo "<th align='center' width='200'>Suggested Price</th>";
        echo "<th align='center' width='200'>Status</th>";
        echo "<th align='center' width='200'>Bidding Deadline</th>";
        echo "<th align='center' width='200'>Updated</th>";
        foreach ($connec->query($sql) as $row)
        {
            echo "<tr>";
            echo "<td align='center' width='200'>" . $row['id']. "</td>";
            echo "<td align='center' width='200'><a href=\"admintask.php?task_id={$row['id']}&owner_email={$row['owner_email']}&admin_email=$email\">". $row['name'] ."</a></td>";
            echo "<td align='center' width='200'>" . $row['owner_email'] . "</td>";
            echo "<td align='center' width='200'>" . $row['category'] . "</td>";
            echo "<td align='center' width='200'>" . $row['start_datetime'] . "</td>";
            echo "<td align='center' width='200'>" . $row['end_datetime'] . "</td>";
            echo "<td align='center' width='200'>" . $row['suggested_price'] . "</td>";
            echo "<td align='center' width='200'>" . $row['status'] . "</td>";
            echo "<td align='center' width='200'>" . $row['bidding_deadline'] . "</td>";
            echo "<td align='center' width='200'>" . $row['datetime_updated'] . "</td>";
            echo "</tr>";}
        echo "</table>";
        echo "</div>";
    }
    echo "<br/>";
    echo "<br/>";

    //DISPLAY ALL BIDS
    echo"<div class='container' name='bids-wrapper' id='bids-wrapper'>";
    echo "<h2>Bids</h2>";
    echo "<form name='searchBids' action='' method='POST'>";
    echo   "<li>Search Bids (Email/Bid Name): ";
    echo     "<input type='text' name='bidName' value=''/>";
    echo    "<input type='submit' name='searchBids' value='Search'>";
    echo"</form>";
    echo "<br/>";

    $_POST['searchBids'] = true;
    if (isset($_POST['searchBids'])) {
        echo $_POST['bidName'];

        $userInput = $_POST['bidName'];
        //Dynamically display bids
        echo "<div style='height: 300px; width: auto; font-size: 16px; overflow: auto;border:2px solid darkgray; border-radius:5px;''>";
        //Display all by default
        $sql = 'select * from bid_task bt, tasks  t where bt.task_id = t.id';

        if(strpos($userInput, '@')){
            //Search by email
            echo "Searching by Email: ".$userInput;
            //Query using ILIKE
            $sql = 'select * from bid_task bt, tasks t where bt.task_id = t.id '."AND bt.bidder_email ILIKE'%".$userInput."%'";
        }
        else if(!empty($userInput)){
            //Search by bidName
            echo "Searching by Bid Name: ".$userInput;
            $sql = 'select * from bid_task bt, tasks t Where bt.task_id = t.id AND t.name ILIKE'."'%".$userInput."%'";
        }
        else{
            //If all else fails, display default
            echo "<small style='color: lightgrey; text-align: center' >Showing all Bids</small>";
            $sql = 'select * from bid_task bt, tasks t WHERE bt.task_id = t.id';
        }

        echo "<table class='table table-bordered table-striped table-hover'>";
        echo "<tr>";
        echo "<th align='center' width='200'>Task ID</th>";
        echo "<th align='center' width='200'>Name</th>";
        echo "<th align='center' width='200'>Category</th>";
        echo "<th align='center' width='200'>Start</th>";
        echo "<th align='center' width='200'>End</th>";
        echo "<th align='center' width='200'>Status</th>";
        echo "<th align='center' width='200'>Bidding Deadline</th>";
        echo "<th align='center' width='200'>Bid Amount</th>";
        echo "<th align='center' width='200'>Bidded On</th>";
        echo "<th align='center' width='200'>Winner</th>";

        foreach ($connec->query($sql) as $row)
        {
            echo "<tr>";
            echo "<td align='center' width='200'>" . $row['task_id'] . "</td>";
            echo "<td align='center' width='200'><a href=\"adminbid.php?task_id={$row['task_id']}&owner_email={$row['owner_email']}&admin_email={$email}\">".$row['name']."</a></td>";
            echo "<td align='center' width='200'>" . $row['category'] . "</td>";
            echo "<td align='center' width='200'>" . $row['start_datetime'] . "</td>";
            echo "<td align='center' width='200'>" . $row['end_datetime'] . "</td>";
            echo "<td align='center' width='200'>" . $row['status'] . "</td>";
            echo "<td align='center' width='200'>" . $row['bidding_deadline'] . "</td>";
            echo "<td align='center' width='200'>" . $row['bid_amount'] . "</td>";
            echo "<td align='center' width='200'>" . $row['bid_time'] . "</td>";
            echo "<td align='center' width='200'>" . $row['is_winner'] . "</td>";
            echo "</tr>";}

        echo "</table>";
        echo "</div>";
    }

    echo "</table>";
    echo "</div>";//Bids wrapper div

    echo "<br/>";
    echo "<br/>";
    ?>
</div> <!--page-content-wrapper div-->
</body>
</html>
