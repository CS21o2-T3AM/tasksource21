<?php
// Use session to pass infomation such as email.
//Note input validation not done yet
session_start();
$email=$_SESSION["userEmail"];
$name=$_SESSION["userName"];
echo $name."<br/>";
date_default_timezone_set("Asia/Singapore");
echo "Today " . date("d/m/Y h:i:sa"). "<br/>";
?>
<!DOCTYPE html>
<head>
    <title>Admin Control Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>li {list-style: none;}</style>
</head>
<body>
<h1>Admin Control Panel</h1>
<h2>Welcome back, Administrator!</h2>
<form name="home" action="home.php" method="POST">
    <li><input type="submit" name="logout" value="Logout" style="position: absolute; right: 0;"/></li>
</form>
<h2>All Bids</h2>

<?php

if (isset($_POST['logout'])){
    //pass email and username to next page
    header("Location: index.php"); //send user to the next page
    exit;
}
?>
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

    tr:hover{background-color:#f5f5f5}
</style>
<div STYLE=" height: 300px; width: auto; font-size: 18px; overflow: auto;">
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

    //SHOW BIDS
    echo "<form name='searchBids' action='' method='POST'>";
    echo   "<li>Search Bids (Email/Bid Name): ";
    echo      "<input type='text' name='bidName' value=''/>";
    echo    "<input type='submit' name='searchBids' value='Search'>";
    echo"</form>";

    $_POST['searchBids'] = true;
    if (isset($_POST['searchBids'])) {
        echo $_POST['bidName'];

        $userInput = $_POST['bidName'];
        //Dynamically display bids
        echo "<div STYLE=\" height: 300px; width: auto; font-size: 18px; overflow: auto;\">";
        //Display all by default
        $sql = 'select * from bid_task, create_task where bt.taskid = ct.taskid';

        if(strpos($userInput, '@')){
            //Search by email
            echo "Searching by Email: ".$userInput;
                //Query using ILIKE
            $sql = 'select * from bid_task bt, create_task ct where bt.taskid = ct.taskid '."AND bt.bidderemail ILIKE'%".$userInput."%'";
        }
        else if(!empty($userInput)){
            //Search by bidName
            echo "Searching by Bid Name: ".$userInput;
            $sql = 'select * from bid_task bt, create_task ct Where bt.taskid = ct.taskid AND ct.taskname ILIKE'."'%".$userInput."%'";
        }
        else{
            //If all else fails, display default
            echo "Showing All Bids";
            $sql = 'select * from bid_task bt, create_task ct WHERE bt.taskid = ct.taskid';
        }

        echo "<table>";
        echo "<tr>";
        echo "<th align='center' width='200'>Task Name</th>";
        echo "<th align='center' width='200'>Task Category</th>";
        echo "<th align='center' width='200'>Date Time</th>";
        echo "<th align='center' width='200'>Status</th>";
        echo "<th align='center' width='200'>Bidding close</th>";
        echo "<th align='center' width='200'>Bid Amount</th>";

        foreach ($connec->query($sql) as $row)
        {
            echo "<tr>";
            echo "<td align='center' width='200'><a href=\"CreateBid.php?taskid={$row['taskid']}&owneremail={$row['owneremail']}&useremail={$email}\">".$row['taskname']."</a></td>";
            echo "<td align='center' width='200'>" . $row['taskcategory'] . "</td>";
            echo "<td align='center' width='200'>" . $row['taskdateandtime'] . "</td>";
            echo "<td align='center' width='200'>" . $row['status'] . "</td>";
            echo "<td align='center' width='200'>" . $row['biddingclose'] . "</td>";
            echo "<td align='center' width='200'>" . $row['bidamount'] . "</td>";
            echo "</tr>";}

        echo "</table>";
        echo "</div>";
    }

    echo "</table>";
    echo "</div>";

    echo "<br/>";
    echo "<br/>";

    //ALL TASKS
    echo "<h2>User Tasks</h2>";
    echo "<form name='searchTasks' action='' method='POST'>";
    echo   "<li>Search Tasks: ";
    echo      "<input type='text' name='taskName'/>";
    echo    "<input type='submit' name='searchTasks' value='Search'>";

    $_POST['searchTasks'] = true;
    if (isset($_POST['searchTasks'])) {
        echo $_POST['taskName'];

        echo "<div STYLE=\" height: 300px; width: auto; font-size: 18px; overflow: auto;\">";
        $sql = 'SELECT * FROM create_task where owneremail != '."'$email'"."AND taskname ILIKE'%".$_POST['taskName']."%'";
        echo "<table>";
        echo "<tr>";
        echo "<th align='center' width='200'>Owner Email</th>";
        echo "<th align='center' width='200'>Task Name</th>";
        echo "<th align='center' width='200'>Task Category</th>";
        echo "<th align='center' width='200'>Date Time</th>";
        echo "<th align='center' width='200'>Status</th>";
        //echo "<th align='center' width='200'>Winning User</th>";
        echo "<th align='center' width='200'>Bidding close</th>";
        foreach ($connec->query($sql) as $row)
        {
            echo "<tr>";
            echo "<td align='center' width='200'>" . $row['owneremail'] . "</td>";
            echo "<td align='center' width='200'><a href=\"CreateBid.php?taskid={$row['taskid']}&owneremail={$row['owneremail']}&useremail=$email\">". $row['taskname'] ."</a></td>";
            echo "<td align='center' width='200'>" . $row['taskcategory'] . "</td>";
            echo "<td align='center' width='200'>" . $row['taskdateandtime'] . "</td>";
            echo "<td align='center' width='200'>" . $row['status'] . "</td>";
            //  echo "<td align='center' width='200'>" . $row['winningbidemail'] . "</td>";
            echo "<td align='center' width='200'>" . $row['biddingclose'] . "</td>";
            echo "</tr>";}
        echo "</table>";
        echo "</div>";
    }


    ?>

</body>
</html>
