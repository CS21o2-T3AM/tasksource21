<!DOCTYPE html>
<head>
    <title>UPDATE PostgreSQL data with PHP</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>li {list-style: none;}</style>
</head>
<body>
<h1>Welcome</h1>
<h2>My task</h2>
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
$sql = 'SELECT * FROM create_task where owneremail = '."'vdumphy1@japanpost.jp'";
echo "<table>";
echo "<tr>";
echo "<th align='center' width='200'>Owner Email</th>";
echo "<th align='center' width='200'>Task Name</th>";
echo "<th align='center' width='200'>Task Category</th>";
echo "<th align='center' width='200'>Date Time</th>";
echo "<th align='center' width='200'>Status</th>";
echo "<th align='center' width='200'>Winning User</th>";
echo "<th align='center' width='200'>Bidding close</th>";
foreach ($connec->query($sql) as $row)
{

    echo "<tr>";
    //echo "<td align='center' width='200'><a href=\"bid.php?taskid=".$row['taskid'].$row['owneremail'].">". $row['taskname'] ."</a></td>";
    echo "<td align='center' width='200'>" . $row['owneremail'] . "</td>";
    echo "<td align='center' width='200'><a href=\"makeabid.php?taskid={$row['taskid']}&owneremail={$row['owneremail']}\">". $row['taskname'] ."</a></td>";
    echo "<td align='center' width='200'>" . $row['taskcategory'] . "</td>";
    echo "<td align='center' width='200'>" . $row['taskdateandtime'] . "</td>";
    echo "<td align='center' width='200'>" . $row['status'] . "</td>";
    echo "<td align='center' width='200'>" . $row['winningbidemail'] . "</td>";
    echo "<td align='center' width='200'>" . $row['biddingclose'] . "</td>";
    echo "</tr>";}

echo "</table>";
echo "</div>";
echo "<h2>Avalable task</h2>";
echo "<br/>";

echo "<div STYLE=\" height: 300px; width: auto; font-size: 18px; overflow: auto;\">";
$sql = 'SELECT * FROM create_task where owneremail != '."'vdumphy1@japanpost.jp'";
echo "<table>";
echo "<tr>";
echo "<th align='center' width='200'>Owner Email</th>";
echo "<th align='center' width='200'>Task Name</th>";
echo "<th align='center' width='200'>Task Category</th>";
echo "<th align='center' width='200'>Date Time</th>";
echo "<th align='center' width='200'>Status</th>";
echo "<th align='center' width='200'>Winning User</th>";
echo "<th align='center' width='200'>Bidding close</th>";
foreach ($connec->query($sql) as $row)
{
    echo "<tr>";
    echo "<td align='center' width='200'>" . $row['owneremail'] . "</td>";
    echo "<td align='center' width='200'>" . $row['taskname'] . "</td>";
    echo "<td align='center' width='200'>" . $row['taskcategory'] . "</td>";
    echo "<td align='center' width='200'>" . $row['taskdateandtime'] . "</td>";
    echo "<td align='center' width='200'>" . $row['status'] . "</td>";
    echo "<td align='center' width='200'>" . $row['winningbidemail'] . "</td>";
    echo "<td align='center' width='200'>" . $row['biddingclose'] . "</td>";
    echo "</tr>";}
echo "</table>";
echo "</div>";
?>

</body>
</html>
