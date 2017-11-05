<?php
// Use session to pass information such as email.
//Note input validation not done yet
require_once '../utils/login.inc.php';
admin_login_validate_or_redirect();
?>
<!DOCTYPE html>


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
            <h2  style="color:white">TASKSOURCE21 </h2>
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

<div class="container" >

    <!-- Menu -->
    <div  align='right' class='container' id="wrapper" style="">
        <div class='btn-group btn-group-lg' role="group"    >
            <a href="admin.php" class="btn btn-default">Users</a>
            <a href="admintasks.php" class="btn btn-default">Tasks</a>
            <a href="adminbids.php" class="btn btn-default active">Bids</a>

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

      //DISPLAY ALL BIDS
    echo"<div class='container' id='bids-wrapper' class='main-content' >";
    echo "<h2>Bids</h2>";
    echo "<form name='searchBids' action='' method='POST'>";
    echo   "<br/> <small>Search Bids (Bidder's Email/ Task Name)</small><br/>";
    echo      "<input type='text' name='bidName' value=''/>";
    echo     "<button type='submit' name='searchBids' value=''><span class='glyphicon glyphicon-search'></span></button>";
    echo"</form><br/>";

    $_POST['searchBids'] = true;
    if (isset($_POST['searchBids'])) {

        $userInput = $_POST['bidName'];
        //Dynamically display bids
        //Display all by default
        $sql = 'select * from bid_task bt, tasks  t where bt.task_id = t.id ORDER BY bt.task_id ASC';

        if(strpos($userInput, '@')){
            //Search by email
            echo "Searching by Email: ".$userInput;
            //Query using ILIKE
            $sql = 'select * from bid_task bt, tasks t where bt.task_id = t.id '."AND bt.bidder_email ILIKE'%".$userInput."%' ORDER BY bt.task_id ASC";
        }
        else if(!empty($userInput)){
            //Search by bidName
            echo "Searching by Task Name: ".$userInput;
            $sql = 'select * from bid_task bt, tasks t Where bt.task_id = t.id AND t.name ILIKE'."'%".$userInput."%' ORDER BY bt.task_id ASC";
        }
        else{
            //If all else fails, display default
            $sql = 'select * from bid_task bt, tasks t WHERE bt.task_id = t.id ORDER BY bt.task_id ASC';
        }
        echo "<div style='height: 300px; width: auto; font-size: 16px; overflow: auto;border:2px solid darkgray; border-radius:5px;'>";
        echo "<table class='table table-bordered table-striped table-hover'>";
        echo "<tr>";
        echo "<th align='center' width='200'>Task ID</th>";
        echo "<th align='center' width='500'>Name</th>";
        echo "<th align='center' width='200'>Bidder Email</th>";
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
            echo "<td align='center' width='500'><a href=\"adminbiddetail.php?taskid={$row['id']}&owneremail={$row['owner_email']}&bidderemail={$row['bidder_email']}&useremail={$email}\">".$row['name']."</a></td>";
            echo "<td align='center' width='200'>" . $row['bidder_email'] . "</td>";
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
