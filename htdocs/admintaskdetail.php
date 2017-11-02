<?php
session_start();
date_default_timezone_set('Asia/Singapore');
$taskId= $_GET['taskid'];
$ownerEmail= $_GET['owneremail'];
$userEmail= $_GET['useremail'];

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
    <title>Viewing Task</title>
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
            <div><h2>Viewing Task <?php echo $taskId;?></h2></div>
            <div><hr></div>
        </div>
    </div>

    <div class="row">
        <?php
        $result = pg_query($db, "SELECT t.*, t.name AS tname, u.name AS uname, u.phone FROM tasks t INNER JOIN users u ON t.owner_email = u.email 
                                  WHERE t.owner_email = '$ownerEmail'AND t.id = '$taskId'");
        $row    = pg_fetch_assoc($result);

        $categories_result = pg_query($db, "SELECT * FROM task_categories");
       // $categories = pg_fetch_assoc($categories_result);

        echo "


       <div name='content-container' class='col-xs-6'>  
       
       <form name='taskform' method='post' >  
       
       <h3>Task Information</h3>
       
    	<table class='table table-bordered table-striped table-hover'>
    	<tr>
    	<td>Task Id:</td>
    	<td><input type='text' name='taskId' value='$row[id]'   style='border:none; background-color: transparent'readonly></td>
    	</tr>
    	
    	<tr>
    	<td>Name:</td>
    	<td><input type='text' name='tname' value='$row[tname]' ></td>
    	</tr>
    	
	    <tr>
    	<td>Category:</td>
    	<!--<td><input type='text' name='category' value='$row[category]' /></td>-->
    	<td>
    	";

        //Testing Drop Down List for Categories
        $active = '';
        echo"<select name='category_dropdown' id='category_dropdown''>";
        while($row_categories=pg_fetch_assoc($categories_result)){
            $active = '';
            $display = $row_categories["name"];
            if($display ==  $row[category]){$active='selected="selected"';}
        echo"<option value = '$display' $active>$display</option>'";
        }
        echo"</select>";

        echo"
        </td>
    	</tr>
    	
    	<tr>
    	<td>Description:</td>
    	<td><textarea type='text' name='description'  style='height:200px; width:400px '>$row[description]</textarea></td>
    	</tr>
    	
    	<tr>
    	<td>Start:</td>
    	<td><input type='text' name='startdatetime' value='$row[start_datetime]' style='border:none; background-color: transparent'readonly /></td>
    	</tr>
    	
    	<tr>
    	<td>End:</td>
    	<td><input type='text' name='enddatetime' value='$row[end_datetime]' style='border:none; background-color: transparent'readonly /></td>
    	</tr>
    	
    	<tr>
       <td>Suggested Price:</td>
    	<td><input type='text' name='suggestedprice' value='$row[suggested_price]' /></td>
        </tr>
    	
        <tr>
       <td>Status:</td>
    	<td><input type='text' name='status' value='$row[status]' /></td>
        </tr>
            
        <tr>
       <td>Bidding Deadline:</td>
    	<td><input type='text' name='biddingdeadline' value='$row[bidding_deadline]' style='border:none; background-color: transparent'readonly /></td>
        </tr>
        
        <tr>
       <td>Last Updated:</td>
    	<td><input type='text' name='datetimeupdated' value='$row[datetime_updated]' style='border:none; background-color: transparent'readonly/></td>
        </tr>
    	</table>
    	
    	</table>
    	</div> <!--task info div-->
    	
    	<div id='owner-info' class='col-xs-6'>
    	<table class='table table-bordered table-striped table-hover'>
    	<h3>Owner Information</h3>
    	    <tr>
                <td>Owner Email:</td>
    	        <td><input type='text' name='owneremail' value='$row[owner_email]' style='border:none; background-color: transparent'readonly/></td>
            </tr>
            
            <tr>
                <td>Name:</td>
    	        <td><input type='text' name='ownername' value='$row[uname]' style='border:none; background-color: transparent'readonly/></td>
            </tr>
            
            <tr>
                <td>Phone No.:</td>
    	        <td><input type='text' name='ownerphone' value='$row[phone]' style='border:none; background-color: transparent'readonly /></td>
            </tr>
        </table>
        </div>
    	
    	<div class='container' align='right'>
    	    <button type='submit' class='btn-default' name='back' id='back' >Back</button>
    	    <button type='submit' class='btn-danger' name='deleteTask' id='deleteTask'>Delete Task</button>
    	    <button type='submit' class='btn-success' name='updateTask' id='updateTask'>Update Task</button>
        </div>
        </form>   
            	
        </div>
    	<br/>
        ";

        //Update Task Button clicked
        if (isset($_POST['updateTask'])){
            $taskId = $_POST['taskId'];
            $tname = $_POST['tname'];
            $category = $_POST['category_dropdown'];
            $description = $_POST['description'];
            //$startdatetime = $_POST['startdatetime'];
             // $enddatetime = $_POST['enddatetime'];
            $suggested_price = $_POST['suggestedprice'];
            $status = $_POST['status'];
            //$biddingdeadline = $_POST['bidding_deadline'];
            //$lastupdated =  date("Y-m-d H:i:s O");

//            echo $taskId;
//            echo $tname;
//            echo $category;
//            echo $lastupdated;
//            echo $suggested_price;
//            echo $status;

            try {
                $result3 = pg_query($db, "UPDATE tasks SET (name, category, description, suggested_price, status, datetime_updated) = ('$tname', '$category', '$description',
                                                      '$suggested_price', '$status', now()::timestamp(0)) WHERE id='$taskId'");
                if(empty($result3)){
                    echo "<script>alert('An error has occured, please try again later.');</script>";
                }else{
                    echo "<script>alert('Task successfully updated!');</script>";
                }
                echo "<meta http-equiv='refresh' content='0'>";
            }
            catch(PDOException $ex){
                echo "<script>alert('An error has occured, please try again later.');</script>";
            }
            parent.window.location.reload();
            echo "<meta http-equiv='refresh' content='0'>";
        }

        //Delete Task Button Clicked
        if (isset($_POST['deleteTask'])){
            date_default_timezone_set("Asia/Singapore");
            $bidamt = $_POST[bidamt];
            $biddateandtime= date("d/m/Y h:i:sa");
            //$name = $row[name];
            $status = "Open";
            try {
                $result3 = pg_query($db, "DELETE FROM tasks 
                                                     WHERE id='$taskId'");
                echo "<script>alert('Task Deleted.');</script>";
            }
            catch(PDOException $ex){
                echo "<script>alert('An error has occured, please try again later.');</script>";
            }
            echo "<meta http-equiv='refresh' content='0'>";
            parent.window.location.reload();
        }

        //Back Button Clicked
        if (isset($_POST['back'])){
            echo "going back";
            echo '<script>window.location = "/tasksource21/admintasks.php";</script>';
            exit;
        }

        parent.window.location.reload();
        ?>

    </div>

</div>
</body>
</html>