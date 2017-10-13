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
    <title>Home Page</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>li {list-style: none;}</style>
</head>
<body>
<h1>Welcome</h1>
<form name="home" action="home.php" method="POST">
    <li><input type="submit" name="logout" value="Logout" style="position: absolute; right: 0;"/></li>
</form>
<h2>My task</h2>
<form name="home" action="home.php" method="POST">
    <li><input type="submit" name="Addtask" value="Add task" style="position: absolute; right: 0;"/></li>
</form>

<?php
if (isset($_POST['Addtask'])){
    //pass email and username to next page
    $_SESSION['userName'] = $name;
    $_SESSION['userEmail'] = $email;
    header("Location: CreateTask.php"); //send user to the next page
    exit;
}
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
    thead {
        position: relative;
    }

    tr:hover{background-color:#f5f5f5}
</style>
<div STYLE=" height: 300px; width: auto; font-size: 18px; overflow: auto;">
    <?php

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
    $sql = 'select *, ct.taskid as cttask ,ct.owneremail as oe , count as bidcount from  create_task ct left join bidcount bc on ct.taskid = bc.taskid where ct.owneremail = '."'$email'"." order by count asc";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th align='center' width='200'>Owner Email</th>";
    echo "<th align='center' width='200'>Task Name</th>";
    echo "<th align='center' width='200'>Task Category</th>";
    echo "<th align='center' width='200'>Date Time</th>";
    echo "<th align='center' width='200'>Status</th>";
    //echo "<th align='center' width='200'>Winning User</th>";
    echo "<th align='center' width='200'>Bidding close</th>";
    echo "<th align='center' width='200'>Bidder Count</th>";
    echo "</tr>";
    echo "</thead>";
    foreach ($connec->query($sql) as $row)
    {

        echo "<tr>";
        //echo "<td align='center' width='200'><a href=\"bid.php?taskid=".$row['taskid'].$row['owneremail'].">". $row['taskname'] ."</a></td>";
        echo "<td align='center' width='200'>" . $row['oe'] . "</td>";
        echo "<td align='center' width='200'><a href=\"CreateBid.php?taskid={$row['cttask']}&owneremail={$row['oe']}&useremail={$email}\">". $row['taskname'] ."</a></td>";
        echo "<td align='center' width='200'>" . $row['taskcategory'] . "</td>";
        echo "<td align='center' width='200'>" . $row['taskdateandtime'] . "</td>";
        echo "<td align='center' width='200'>" . $row['status'] . "</td>";
        // echo "<td align='center' width='200'>" . $row['winningbidemail'] . "</td>";
        echo "<td align='center' width='200'>" . $row['biddingclose'] . "</td>";
        $count = $row['bidcount'];
        if($row['bidcount'] === NULL){
            $count= 0;
        }
        echo "<td align='center' width='200'><a href=\"chooseabid.php?taskid={$row['cttask']}&owneremail={$row['oe']}&username={$name}\">".$count."</td>";
        echo "</tr>";}

    echo "</table>";
    echo "</div>";
    echo "<h2>Avaliable task</h2>";
    echo "<br/>";
    echo "<ul style='list-style: none'>";
    echo "<form name='search' action='' method='POST'>";
    echo   "<li>Task Name:";
    echo      "<input type='text' name='taskName'/>";
    echo    "<li><input type='submit' name='search' value='Search'></li>";
    echo"</form>";
    echo "</ul>";
    $_POST['search'] = true;
    if (isset($_POST['search'])) {
        echo $_POST['taskName'];

        echo "<div STYLE=\" height: 300px; width: auto; font-size: 18px; overflow: auto;\">";
        $sql = 'select * , ct.taskid as cttask ,ct.owneremail as oe , count as bidcount  from create_task ct left join bidcount bc on ct.taskid = bc.taskid where ct.owneremail != '."'$email'"."AND ct.taskname ILIKE'%".$_POST['taskName']."%'"." order by count asc";
        echo "<table>";
        echo "<tr>";
        echo "<th align='center' width='200'>Owner Email</th>";
        echo "<th align='center' width='200'>Task Name</th>";
        echo "<th align='center' width='200'>Task Category</th>";
        echo "<th align='center' width='200'>Date Time</th>";
        echo "<th align='center' width='200'>Status</th>";
        // echo "<th align='center' width='200'>Winning User</th>";
        echo "<th align='center' width='200'>Bidding close</th>";
        echo "<th align='center' width='200'>Bidder Count</th>";
        foreach ($connec->query($sql) as $row)
        {
            echo "<tr>";
            echo "<td align='center' width='200'>" . $row['oe'] . "</td>";
            echo "<td align='center' width='200'><a href=\"CreateBid.php?taskid={$row['cttask']}&owneremail={$row['oe']}&useremail={$email}\">". $row['taskname'] ."</a></td>";
            echo "<td align='center' width='200'>" . $row['taskcategory'] . "</td>";
            echo "<td align='center' width='200'>" . $row['taskdateandtime'] . "</td>";
            echo "<td align='center' width='200'>" . $row['status'] . "</td>";
            // echo "<td align='center' width='200'>" . $row['winningbidemail'] . "</td>";
            echo "<td align='center' width='200'>" . $row['biddingclose'] . "</td>";
            $count = $row['bidcount'];
            if($row['bidcount'] === NULL){
                $count= 0;
            }
            echo "<td align='center' width='200'>" . $count . "</td>";
            echo "</tr>";}
        echo "</table>";
        echo "</div>";

    }
    echo "<h2>My bids</h2>";
    echo "<div STYLE=\" height: 300px; width: auto; font-size: 18px; overflow: auto;\">";
    $sql = 'select * from bid_task bt, create_task ct Where bt.taskid = ct.taskid AND bt.bidderEmail = '."'$email'";
    echo "<table>";
    echo "<tr>";
    echo "<th align='center' width='200'>Task Name</th>";
    echo "<th align='center' width='200'>Task Category</th>";
    echo "<th align='center' width='200'>Date Time</th>";
    echo "<th align='center' width='200'>Status</th>";
    //echo "<th align='center' width='200'>Winning User</th>";
    echo "<th align='center' width='200'>Bidding close</th>";
    echo "<th align='center' width='200'>Bid Amount</th>";
    foreach ($connec->query($sql) as $row)
    {

        echo "<tr>";
        //echo "<td align='center' width='200'><a href=\"bid.php?taskid=".$row['taskid'].$row['owneremail'].">". $row['taskname'] ."</a></td>";
        echo "<td align='center' width='200'><a href=\"CreateBid.php?taskid={$row['taskid']}&owneremail={$row['owneremail']}&useremail={$email}\">".$row['taskname']."</a></td>";
        echo "<td align='center' width='200'>" . $row['taskcategory'] . "</td>";
        echo "<td align='center' width='200'>" . $row['taskdateandtime'] . "</td>";
        echo "<td align='center' width='200'>" . $row['status'] . "</td>";
        // echo "<td align='center' width='200'>" . $row['winningbidemail'] . "</td>";
        echo "<td align='center' width='200'>" . $row['biddingclose'] . "</td>";
        echo "<td align='center' width='200'>" . $row['bidamount'] . "</td>";
        echo "</tr>";}

    echo "</table>";
    echo "</div>";


    ?>

</body>
</html>
