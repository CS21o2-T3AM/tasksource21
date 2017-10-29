<?php
?>
<!DOCTYPE html>
<head>
    <title>Admin Control Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>li {list-style: none;}</style>
</head>
<body>
<h1>Admin Control Panel</h1>
<h3 align="right">Welcome back, <?php echo $name; ?>!</h3>
<form name="home" action="home.php" method="POST">
    <input type="submit" name="logout" value="Logout" style="position: absolute; right: 0;"/>
</form>

<?php
?>
<style>
    table {
        border: 2px solid lightgray;
        border-radius: 5px;
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

    //All USERS
    echo "<h2>User Accounts</h2>";
    echo "<form name='searchUsers' action='' method='POST'>";
    echo   "<li>Search Users(Email/Name): ";
    echo      "<input type='text' name='userName' value=''/>";
    echo    "<input type='submit' name='searchUsers' value='Search'>";
    echo"</form>";

    $_POST['searchUsers'] = true;
    if (isset($_POST['searchUsers'])) {
        echo $_POST['userName'];

        $userInput = $_POST['userName'];
        //Dynamically display bids
        echo "<div STYLE=\" height: 300px; width: auto; font-size: 16px; overflow: auto; border:solid 2px darkgray; border-radius:5px;\">";
        //Display all by default
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
            echo "Showing All Users";
            $sql = 'select * from users';
        }

        echo "<table>";
        echo "<tr>";
        echo "<th align='center' width='200'>Email</th>";
        echo "<th align='center' width='200'>Name</th>";
        echo "<th align='center' width='100'>Date Of Birth</th>";
        echo "<th align='center' width='10'>Administrative Rights</th>";
        echo "<th align='center' width='50'>Phone</th>";

        foreach ($connec->query($sql) as $row)
        {
            echo "<tr>";
            echo "<td align='center' width='200'><a href=\"CreateBid.php?taskid={$row['taskid']}&owneremail={$row['owneremail']}&useremail={$email}\">".$row['email']."</a></td>";
            echo "<td align='center' width='200'>" . $row['name'] . "</td>";
            echo "<td align='center' width='200'>" . $row['dateofbirth'] . "</td>";
            echo "<td align='center' width='200'>" . $row['admin'] . "</td>";
            echo "<td align='center' width='200'>" . $row['phone'] . "</td>";
            echo "</tr>";}

        echo "</table>";
        echo "</div>";
    }

    echo "</table>";
    echo "</div>";

    echo "<br/>";
    echo "<br/>";


    //ALL BIDS
    echo "<h2>User Bids</h2>";
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
        echo "<div STYLE=\" height: 300px; width: auto; font-size: 16px; overflow: auto;border:2px solid darkgray; border-radius:5px;\">";
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

        echo "<div STYLE=\" height: 300px; width: auto; font-size: 16px; overflow: auto;border:2px solid darkgray; border-radius:5px;\">";
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
