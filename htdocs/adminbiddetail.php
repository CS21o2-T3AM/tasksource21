<?php
session_start();
$taskId= $_GET['taskid'];
$ownerEmail= $_GET['owneremail'];
$userEmail= $_GET['useremail'];
$bidderEmail = $_GET['bidderemail'];

$db= pg_connect("host=127.0.0.1 port=5432 dbname=tasksource21 user=postgres password=password");


require_once '../utils/login.inc.php';
admin_login_validate_or_redirect()
?>
    <!DOCTYPE html>

    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Viewing Bid</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </head>


    <body>
    <!--  Navigation Bar --->
    <nav class="navbar navbar-default">
        <div class="container-fluid"  style="background-color:slategrey; color:ghostwhite;">

            <!--Logo-->
            <div class="navbar-header" style="color:white; float:left; size: 30px" >
                <h2 href="#" style="color:white">TASKSOURCE21 </h2>
            </div>

            <!--Menu Items-->
            <div style='float: right; margin-right:10px; margin-top: 18px' >
                <form name="home" action="index.php" method="POST">
                    <button type="submit" name="logout" style="background-color:white; color:grey; border-radius: 5px;  align-content: center; vertical-align: middle;">Log Out</button>
                </form>
            </div>

        </div>
    </nav>


    <div class="container">
        <div class="row">
            <div>
                <div><hr></div>
                <div><h2>Viewing Bid for Task <?php echo $taskId; ?></h2></div>
                <div><hr></div>
            </div>
        </div>

        <div class="row">
<?php
//    $button="bid";
//    $updatebid = pg_query($db, "SELECT c.*, b.* FROM create_task c  bid_task b On c.owneremail = b.owneremail AND c.taskid = b.taskid
//                              where c.owneremail = '$ownerEmail'AND t.taskid = '$taskId'");
//    $updateRow = pg_fetch_assoc($updatebid);
//
//    if(!$updateRow==""){
//        $pevbidamt = $updateRow[bidamount];
//        $button="updateBid";
//        $failed = $updateRow[bidstatus];
//        if(strcmp($failed,'failed')==0){
//            $disable=true;
//        }
//    }

    $result = pg_query($db, "
                              SELECT t.*, t.name AS tname, u1.*, u2.*, u1.name AS oname, u1.email AS oemail, u1.phone as ophone, u2.name AS bname, u2.email AS bemail, u2.phone as bphone, bt.*
                              FROM tasks t, bid_task bt, users u1, users u2
                              WHERE  t.id = '$taskId'  AND bt.task_id = '$taskId' 
                              AND  u2.email = '$bidderEmail' AND u1.email = t.owner_email 
                              ");
    $row    = pg_fetch_assoc($result);

    echo "
        <div class='container' name='main-content'>

       <form name='Information'  method='POST'  action=''>  
    	
    	<div name='bid-container'>
    	<div name='bid-information-container' class='col-sm-6 col-md-6 col-lg-6 col-xl-6'>
    	 	 <h3>Bid Information</h3>
    	    <table class='table table-bordered table-striped table-hover'>
    	    
    	        <tr><td>Amount: </td><td><input type='text' name='bidamount' value='$row[bid_amount]' ></td></tr>
    	        <tr><td>Bidded On: </td><td><input type='text' name='bidtime' value='$row[bid_time]' style='background-color: transparent; border:none; ' readonly></td></tr>
    	        <tr><td>Bidding Deadline: </td><td><input type='text' name='biddingdeadline' value='$row[bidding_deadline]' style='background-color: transparent; border:none; ' readonly></td></tr>
    	        
    	    </table>
    	 </div>
    	 
    	 <div name='bidder-information-container' class='col-sm-6 col-md-6 col-lg-6 col-xl-6'>
    	 	 <h3>Task Bidder</h3>
    	    <table class='table table-bordered table-striped table-hover'>
    	    
    	        <tr><td>Email: </td><td><input type='text' name='bemail' value='$row[bemail]' style='background-color: transparent; border:none; ' readonly></td></tr>
    	        <tr><td>Name: </td><td><input type='text' name='bname' value='$row[bname]' style='background-color: transparent; border:none; ' readonly></td></tr>
    	        <tr><td>Phone: </td><td><input type='text' name='bphone' value='$row[bphone]' style='background-color: transparent; border:none; ' readonly></td></tr>
    	        
    	    </table>
    	 </div>
    	 
    	        	 <div class='clearfix visible-xl visible-l'></div>
    	 
    	 </div><!--bidcontainer div-->
    	    
    	  <div name='task-information-container' class='col-sm-6 col-md-6 col-lg-6 col-xl-6'>
    	  <h3>Task Information</h3>
    	    <table class='table table-bordered table-striped table-hover'>
    	        <tr><td>ID:</td><td><input type='text' name='taskid' value='$row[id]' style='background-color: transparent; border:none; ' readonly></td></tr>
    	        <tr><td>Name: </td><td><input type='text' name='taskname' value='$row[tname]' style='background-color: transparent; border:none; ' readonly></td></tr>
    	        <tr><td>Description: </td><td><textarea type='text' name='taskdesc'  style='background-color: transparent; border:none; width:300px; height:100px;' readonly>$row[description]</textarea></td></tr>
    	        <tr><td>Start: </td><td><input type='text' name='tstartdate' value='$row[start_datetime]' style='background-color: transparent; border:none; ' readonly></td></tr>
    	        <tr><td>End: </td><td><input type='text' name='tenddate' value='$row[end_datetime]' style='background-color: transparent; border:none; ' readonly></td></tr>
    	        <tr><td>Suggested Price:</td><td><input type='text' name='suggestedprice' value='$row[suggested_price]' style='background-color: transparent; border:none; ' readonly></td></tr>
    	        <tr><td>Status: </td><td><input type='text' name='status' value='$row[status]' style='background-color: transparent; border:none; ' readonly></td></tr>    	        
    	    </table>
    	 </div>
    	 

    	 <div name='task-owner-information-container' class='col-sm-6 col-md-6 col-lg-6 col-xl-6'>
    	  <h3>Task Owner</h3>
    	    <table class='table table-bordered table-striped table-hover'>
    	        <tr><td>Email: </td><td><input type='text' name='oemail' value='$row[oemail]' style='background-color: transparent; border:none; ' readonly></td></tr>
    	        <tr><td>Name: </td><td><input type='text' name='oname' value='$row[oname]' style='background-color: transparent; border:none; ' readonly></td></tr>
    	        <tr><td>Phone: </td><td><input type='text' name='ophone' value='$row[ophone]' style='background-color: transparent; border:none; ' readonly></td></tr>
    	    </table>
    	    
    	 
    	        <div name='menu' align='right' style='padding-left: 5px'>
    	            <button type='submit' class='btn-default' name='back' id='back' >Back</button>
    	            <button type='submit' class='btn-danger' name='deleteBid' id='deleteBid'>Delete Bid</button>
    	           <button type='submit' class='btn-success' name='updateBid' id='updateBid'>Update Bid</button>
                </div>
    	 </div>
    	
    	
    	
    	</form><!--Close the form-->
    	</div>
    	";

            if (isset($_POST['updateBid'])){
                date_default_timezone_set("Asia/Singapore");
                $bidamount = $_POST['bidamount'];
                $bemail = $_POST['bemail'];
               // $biddateandtime= date("d/m/Y h:i:sa");

                try {
                    $result2 = pg_query($db, "UPDATE bid_task b set bid_amount='$bidamount'
                                                     where b.task_id = '$taskId'  AND b.bidder_email ='$bemail'");
                    if(empty($result3)){
                        echo "<script>alert('An error has occured, please try again later.');</script>";
                    }else{
                        echo "<script>alert('Bid details successfully updated!');</script>";
                    }
                    echo "<meta http-equiv='refresh' content='0'>";
                }
                catch(mysqli_sql_exception $ex){
                    echo "DB Error";
                }
                }

if (isset($_POST['deleteBid'])){
    date_default_timezone_set("Asia/Singapore");
    $bidamount = $_POST['bidamount'];
    $bemail = $_POST['bemail'];
    // $biddateandtime= date("d/m/Y h:i:sa");

    try {
        $result2 = pg_query($db, "DELETE FROM  bid_task b
                                                     where b.task_id = '$taskId'  AND b.bidder_email ='$bemail'");
        echo"<script>alert('Bid successfully updated');</script>";
        echo "<meta http-equiv='refresh' content='0'>";
    }
    catch(mysqli_sql_exception $ex){
        echo "DB Error";
    }
}


            if (isset($_POST['back'])){
            echo '<script>window.location = "/tasksource21/adminbids.php";</script>';
            exit;
            }
            ?>
            </form>

        </div>

    </div>
    </body>
</html>