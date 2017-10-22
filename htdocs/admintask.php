<?php
$taskId= $_GET['taskid'];

$ownerEmail= $_GET['owneremail'];
$userEmail= $_GET['useremail'];

$db= pg_connect("host=127.0.0.1 port=5432 dbname=tasksource21 user=postgres password=password");

//Authentication check
if($userEmail==""){
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Viewing Task</title>
</head>
<body>
<div class="container">

    <div class="row">

        <div>

            <div><hr></div>

            <div><h2>Viewing Task Information</h2></div>

            <div><hr></div>

        </div>

    </div>

    <div class="row">
        <?php
        //Here, we are looking at whether the user has already bidded anything.
        //If the user has not bidded anything, the button will show as 'bid'
//        $button="bid";
//        $updatebid = pg_query($db, "SELECT c.*, b.* FROM create_task c Inner Join bid_task b On c.owneremail = b.owneremail AND c.taskid = b.taskid
//                              where c.owneremail = '$ownerEmail'AND c.taskid = '$taskId'");
//        $updateRow = pg_fetch_assoc($updatebid);
//
//        //If the user already bid something, we change the button to display 'updatebid'
//        if(!$updateRow==""){
//            $pevbidamt = $updateRow[bidamount];
//            $button="updateBid";
//            $failed = $updateRow[bidstatus];
//            if(strcmp($failed,'failed')==0){
//                $disable=true;
//            }
//        };
        //Get the task information
        $result = pg_query($db, "SELECT c.*, u.name, u.phone FROM create_task c INNER JOIN users u ON c.owneremail = u.email 
                                  WHERE c.owneremail = '$ownerEmail'AND c.taskid = '$taskId'");
        $row    = pg_fetch_assoc($result);
        echo "

       <form name='taskform' action=''  method='post' >  
       <fieldset>
     
    	<table cellpadding='5'  style='border: 1px solid darkgrey; border-radius:6px; margin-left: 5px;'>
    	<tr>
    	<td>Task Id:</td>
    	<td><input type='text' name='taskId' value='$row[taskid]'  disabled><td>
    	</tr>
    	
    	<tr>
    	<td>Task Name:</td>
    	<td><input type='text' name='taskname' value='$row[taskname]' ><td>
    	</tr>
    	
    	<tr>
    	<td>Task Category:</td>
    	<td><input type='text' name='taskcategory' value='$row[taskcategory]' ><td>
    	</tr>
    	
    	<tr>
    	<td>Creator Name:</td>
    	<td><input type='text' name='ownername' value='$row[name]'  disabled><td>
    	</tr>
    	
    	<tr>
    	<td>Creator Email:</td>
    	<td><input type='text' name='owneremail' value='$row[owneremail]'  /></td>
    	</tr>
    	
    	
    	<tr>
    	<td>Date and time of task:</td>
    	<td><input type='text' name='taskdateandtime' value='$row[taskdateandtime]' /></td>
    	</tr>
    	
    	<tr>
       <td>Bidding Close Date:</td>
    	<td><input type='text' name='biddingclose' value='$row[biddingclose]' /></td>
        </tr>
    	
    	<tr>
    	<td>Description:</td>
        <td><textarea name=\"taskdesc\"  style=\"width:400px; height:100px;\">$row[taskdesc]</textarea></td>
        </tr>
            
        <tr>
       <td>Winning Bidder Email:</td>
    	<td><input type='text' name='winningbidemail' value='$row[winningbidemail]' /></td>
        </tr>
        
        <tr>
       <td>Bid Status:</td>
    	<td><input type='text' name='status' value='$row[status]' /></td>
        </tr>
    	</fieldset>
    	
    	<table>
        <tr>
            <br/><br/>
        <td><input type='submit' name='deleteTask' value='Delete Task'/></td>
         <td><input type='submit' name='updateTask' value='Update Task'/></td>
         <td><input type='submit' name='back' value='Back'/></td>
         </tr>    
        </table>
    	</table>
    	</form>";

        //Update Task Button clicked
        if (isset($_POST['updateTask'])){
            $taskId = $_POST['taskId'];
            $ownerEmail = $_POST['owneremail'];
            $taskname = $_POST['taskname'];
            $taskdesc = $_POST['taskdesc'];
            $taskcategory = $_POST['taskcategory'];
            $taskdateandtime = $_POST['taskdateandtime'];
            $status = $_POST['status'];
            $winningbidemail = $_POST['winningbidemail'];
            $biddingclose = $_POST['biddingclose'];
            //$name = $row[name];

            try {
                $result3 = pg_query($db, "UPDATE create_task SET (owneremail,  taskname, taskdesc, taskcategory, taskdateandtime,  status, winningbidemail, biddingclose) = ('$ownerEmail', '$taskname', '$taskdesc', '$taskcategory',
                                                      '$taskdateandtime','$status', '$winningbidemail','$biddingclose')
                                                     WHERE taskid='$row[taskid]'");
                    echo "<script>alert('Task successfully updated!');</script>";
                header("refresh:0");
            }
            catch(PDOException $ex){
                echo "<script>alert('An error has occured, please try again later.');</script>";
            }
//            $_SESSION['userName'] = $name;
//            $_SESSION['userId'] = $email;
//            $_SESSION['taskId']=$taskId;
//            header("Location: admintask.php");
            parent.window.location.reload();
        }

        //Delete Task Button Clicked
        if (isset($_POST['deleteTask'])){
            date_default_timezone_set("Asia/Singapore");
            $bidamt = $_POST[bidamt];
            $biddateandtime= date("d/m/Y h:i:sa");
            //$name = $row[name];
            $status = "Open";
            try {
                $result3 = pg_query($db, "UPDATE create_task SET (status) = ('deleted')
                                                     WHERE taskid='$row[taskid]'");
                echo "<script>alert('Task Status set to Deleted.');</script>";
                header("refresh:0");
            }
            catch(PDOException $ex){
                echo "<script>alert('An error has occured, please try again later.');</script>";
            }
            parent.window.location.reload();
        }

        //Back Button Clicked
        if (isset($_POST['back'])){
            $_SESSION['userName'] = $name;
            $_SESSION['userId'] = $email;
            header("Location: admin.php");
            exit;
        }

        ?>

    </div>

</div>
</body>
</html>