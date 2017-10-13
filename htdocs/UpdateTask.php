<?php

$taskId= $_GET['taskid'];

$ownerEmail= $_GET['owneremail'];

$db= pg_connect("host=127.0.0.1 port=5432 dbname=tasksource21 user=postgres password=password");

?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Task</title>
</head>
<body>
<div class="container">

    <div class="row">

        <div>

            <div><hr></div>

            <div><h2>Task Settings</h2></div>

            <div><hr></div>

        </div>

    </div>

    <div class="row">
        <?php

        $result = pg_query($db, "SELECT c.*, u.name, b.count FROM (create_task c Inner Join users u On c.owneremail = u.email) 
                                      Left Join bidCount b ON c.owneremail = b.owneremail AND c.taskid=b.taskid 
                                      where c.owneremail = '$ownerEmail'AND c.taskid = '$taskId'");
        $row    = pg_fetch_assoc($result);

        $bidcount = $row[count];
        if($bidcount==NULL){
            $bidcount = 0;
        }
        echo "

       <div><h3>Task informtion</h3></div>

       <form name='Information' action=".'UpdateTask.php?taskid='."$taskId&owneremail=$ownerEmail method='POST' >  
    	
    	<li>Name:</li>  
    	<li><input type='text' name='ownername' disabled value='$row[name]'/></li>  
    	<br/><div></div>
    	
    	<li>Your email:</li>  
    	<li><input type='text' name='owneremail' disabled value='$row[owneremail]'/></li>  
    	<br/><div></div>
    	
    	<li>Task Status:</li>  
    	<li><input type='text' name='status' disabled value='$row[status]'/></li>  
    	<br/><div></div>
    	
    	<li>Task name:</li><li><input type='text' name='taskname' value='$row[taskname]'/></li>  
    	<br/><div></div>
    	
    	<li>Date and time of task:</li>  
    	<li><input type='text' name='dateNtime' value='$row[taskdateandtime]'/></li>  
    	<br/><div></div>
    	
    	    <script type=\"text/javascript\">
                function showfield(name){
                    if(name=='Other')document.getElementById('div1').innerHTML='Other: <input type=\"text\" name=\"other\" />';
                    else document.getElementById('div1').innerHTML='';
                }
            </script>
    	
    
    	<li>Task Category:
                <select name=\"cat\" onchange=\"showfield(this.options[this.selectedIndex].value)\">
                    <option selected=\"selected\">$row[taskcategory]</option>
                    <option value=\"Minor repair\">Minor repair</option>
                    <option value=\"House Cleaning\">House Cleaning</option>
                    <option value=\"Home Improvement\">Home Improvement</option>
                    <option value=\"Furniture assembly\">Furniture assembly</option>
                    <option value=\"House Cleaning\">House Cleaning</option>
                    <option value=\"Moving & packing\">Moving & packing</option>
                    <option value=\"Other\">Other</option>
                </select>
                <div><div id=\"div1\"></div></div>
                <?php echo $nameErr2;?></span>
            </li>
            <br/><div></div>
    	
    	 <li>Enter a task description:</li>
            <li><textarea name=\"taskDesc\" style=\"width:400px; height:100px;\">$row[taskdesc]</textarea>
            <br/><div></div>
            
        <li>Bidding Close Date:</li>  
    	<li><input type='text' name='bidclosedate' value='$row[biddingclose]'/></li>  
    	<br/><div></div>   
    	
    	<li>Number of Bids:</li>  
    	<li><input type='text' name='bidscount' disabled value='$bidcount'/></li>  
    	<br/><div></div> ";
    	if($row[status]=="closed") {
    	    echo " <li>Winner bid email:</li>  
    	    <li><input type='text' name='winner' disabled value='$row[winningbidemail]'/></li>  
    	    <br/><div></div>";
        }
        echo "
     
            <li><input type='submit' name='update' value='Update Task'/>
            
           <input type='submit' name='delete' value='Delete Task'/>
            <br/><div></div><br/></li>
            
            <li><input type='submit' name='back' value='Back'/></li>
            
            ";

// need to add this part
        if (isset($_POST['update'])){
            $name = $row[name];


            $taskName= $_POST[taskname];
            $taskDesc= $_POST[taskDesc];
            if($_POST[cat]=='Other'){
                $taskCat =  $_POST[other];
            }
            else{
                $taskCat= $_POST[cat];
            }
            $taskDateNTime= $_POST[dateNtime];
            $EndBid = $_POST[bidclosedate];
            echo $taskName . " " . $taskDesc. " ". $taskCat. " ". $taskDateNTime. " ". $EndBid . "testing2" ;

            try {
                $result4 = pg_query($db, "UPDATE create_task c set taskName ='$taskName', taskDesc='$taskDesc', taskDateAndTime='$taskDateNTime',
                                    biddingClose='$EndBid', taskCategory =  '$taskCat' where c.taskid = '$taskId' And c.owneremail= '$ownerEmail'");
               if($result4){
                    $_SESSION['userName'] = $name;
                    $_SESSION['userId'] = $email;
                    header("Location: home.php");
                    exit;
                }
            }
            catch(mysqli_sql_exception $ex){
                echo "DB Error";
            }

        }
        if (isset($_POST['delete'])){
            date_default_timezone_set("Asia/Singapore");
            $name = $row[name];
            $status = "Deleted";
            try {
                $result3 = pg_query($db, "UPDATE bid_task b set bidstatus ='deleted' where b.taskid = '$taskId' And b.owneremail= '$ownerEmail'");
                $result2 = pg_query($db, "UPDATE create_task c set status ='deleted' where c.taskid = '$taskId' And c.owneremail= '$ownerEmail'");

                if($result2 && $result3)
                    $_SESSION['userName'] = $name;
                    $_SESSION['userId'] = $email;
                    header("Location: home.php");
                    exit;

            }
            catch(mysqli_sql_exception $ex){
                echo "DB Error";
            }
        }

        if (isset($_POST['back'])){
            $_SESSION['userName'] = $name;
            $_SESSION['userId'] = $email;
            header("Location: home.php");
            exit;
        }

        ?>

        </form>

    </div>

</div>
</body>
</html>