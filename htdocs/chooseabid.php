
<?php
$taskID= $_GET['taskid'];
$_SESSION['userEmail'] = $_GET['owneremail'];
$_SESSION['userName'] = $_GET['username'];
$taskId= $_GET['taskid'];

$ownerEmail= $_GET['owneremail'];
$username= $_GET['username'];
try {
    $dbuser = 'postgres';
    $dbpass = 'password';
    $host = 'localhost';
    $dbname='tasksource21';

    $connec = new PDO("pgsql:host=$host;dbname=$dbname", $dbuser, $dbpass);;
}catch (PDOException $e) {
    echo "Error : " . $e->getMessage() . "<br/>";
    die();
}
try {
    $result5 = pg_query($db, "select * from bid_task");
    if($result5){
        echo "Already selected bidder";
    }else{
        echo"else";
        echo $result5;
    }
}catch(mysqli_sql_exception $ex){
    echo "DB Error";
}
$db= pg_connect("host=127.0.0.1 port=5432 dbname=tasksource21 user=postgres password=M@pler0ck");

$sql = 'select * from create_task where taskid = '."'$taskID'";
foreach ($connec->query($sql) as $row)
{
$taskName = $row['taskname'] ;
$taskDesc = $row['taskdesc'] ;
$taskCategory = $row['taskcategory'] ;
$taskDateTime = $row['taskdateandtime'] ;
$taskBiddingCloseDate = $row['biddingclose'] ;
}

if (isset($_POST['back'])){
    $_SESSION['userName'] = $name;
    $_SESSION['userId'] = $email;
    header("Location: home.php");
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

<div class="container">

    <div class="row">
            <div><h2>Selecting Bid</h2></div>
    </div>

    <div class="row">

        <!-- contact form -->

        <form action="Register" method="POST">

            <div class="col-lg-3">

                <br />

            </div>

            <li>TaskID :<input type="text" name="taskID" value=<?php echo $taskID;?> disabled></li>
            <br/><div></div>

            <li>Task Name: <input type="text" name="taskName" value=<?php echo $taskName;?> disabled></li>
            <br/><div></div>

            <li>Task Catageory: <input type="text" name="taskCatageory" value=<?php echo $taskCategory;?> disabled></li>
            <br/><div></div>
            <li>Task Description: <textarea name="taskDesc" disabled style="width:400px; height:100px;"><?php echo $taskDesc;?></textarea></li>
            <br/><div></div>

            <li>Task Date & Time: <input type="text" name="taskDateTime" value=<?php echo $taskDateTime;?> disabled></li>
            <br/><div></div>

            <li>Bidding close Date: <input type="text" name="taskDateTime" value=<?php echo $taskBiddingCloseDate;?> disabled></li>
            <br/><div></div>


        </form>
        <form name="home" action="home.php" method="POST">
            <li><input type="submit" name="back" value="Back" style="position: absolute; left: 0;"/></li>
        </form>

    </div>
    <div class="row">
        <div><h3>List of current bid:</h3></div>

<?php


if (isset($_POST['Select'])) {
    if (isset($_POST['rdb'])) {
        echo "You have selected :" . $_POST['rdb'];
        $bidderemail = $_POST['rdb'];
        try {
            $result = pg_query($db, "update bid_task set bidstatus = 'selected' where taskid ='$taskId'and bidderemail ='$bidderemail'");
            $result2 = pg_query($db, "update bid_task set bidstatus = 'failed' where taskid ='$taskId'and bidderemail !='$bidderemail'");
            $result3 = pg_query($db, "update create_task set status = 'closed',winningbidemail ='$bidderemail' where taskid ='$taskId'");
        }catch(mysqli_sql_exception $ex){
            echo "DB Error";
        }
    }
}
$sql = 'select * from bid_task where taskid =' ."'$taskID'"."order by bidderemail";
echo "<form  method='POST'>";
echo "<table>";
echo "<tr>";
echo "<th align='center' width='200'>Select</th>";
echo "<th align='center' width='200'>Bidder Email</th>";
echo "<th align='center' width='200'>Bid Amount</th>";
echo "<th align='center' width='200'>Bid Status</th>";
echo "<th align='center' width='200'>Bid Date & Time</th>";
foreach ($connec->query($sql) as $row)
{

    echo "<tr>";
    echo "<td align='center' width='200'><input type='radio' name='rdb' value='$row[bidderemail]'/></td>";
    //echo "<td align='center' width='200'><a href=\"bid.php?taskid=".$row['taskid'].$row['owneremail'].">". $row['taskname'] ."</a></td>";
    echo "<td align='center' width='200'>" . $row['bidderemail'] . "</td>";
    echo "<td align='center' width='200'>" . $row['bidamount'] . "</td>";
    echo "<td align='center' width='200'>" . $row['bidstatus'] . "</td>";
    echo "<td align='center' width='200'>" . $row['biddatetime'] . "</td>";
    echo "</tr>";}

echo "</table>";
echo    "<li><input type='submit' name='Select' value='Select bidder' style='position: absolute; left: 0;'/></li>";
echo        "</form>";

?>

    </div>


</div>
