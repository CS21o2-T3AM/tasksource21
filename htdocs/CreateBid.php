<?php

$taskId= $_GET['taskid'];

$ownerEmail= $_GET['owneremail'];

$userEmail= $_GET['useremail'];

$db= pg_connect("host=127.0.0.1 port=5432 dbname=tasksource21 user=postgres password=password");

?>
    <!DOCTYPE html>

    <html lang="en">

    <head>

        <meta charset="utf-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Make Bid</title>
    </head>
    <body>
    <div class="container">

        <div class="row">

            <div>

                <div><hr></div>

                <div><h2>Bid</h2></div>

                <div><hr></div>

            </div>

        </div>

        <div class="row">
<?php
    $button="bid";
    $updatebid = pg_query($db, "SELECT c.*, b.* FROM create_task c Inner Join bid_task b On c.owneremail = b.owneremail AND c.taskid = b.taskid  
                              where c.owneremail = '$ownerEmail'AND c.taskid = '$taskId'");
    $updateRow = pg_fetch_assoc($updatebid);

    if(!$updateRow==""){
        $pevbidamt = $updateRow[bidamount];
        $button="updateBid";
        $failed = $updateRow[bidstatus];
        if(strcmp($failed,'failed')==0){
            $disbale=true;
        }
    }


    $result = pg_query($db, "SELECT c.*, u.name, u.phone FROM create_task c Inner Join users u On c.owneremail = u.email 
                              where c.owneremail = '$ownerEmail'AND c.taskid = '$taskId'");
    $row    = pg_fetch_assoc($result);
    echo "
 
       <div><h3>Task informtion</h3></div>

       <form name='Information' action=".'CreateBid.php?taskid='."$taskId&owneremail=$ownerEmail&useremail=$userEmail method='GET' >  
    	
    	<li>Task creator name:</li>  
    	<li><input type='text' name='ownername' value='$row[name]' disabled/></li>  
    	<br/><div></div>
    	
    	<li>Task creator email:</li>  
    	<li><input type='text' name='owneremail' value='$row[owneremail]'  disabled/></li>  
    	<br/><div></div>
    	
    	<li>Task name:</li><li><input type='text' name='taskname' value='$row[taskname]'  disabled/></li>  
    	<br/><div></div>
    	
    	<li>Date and time of task:</li>  
    	<li><input type='text' name='date&time' value='$row[taskdateandtime]' disabled/></li>  
    	<br/><div></div>
    	
    	 <li>Enter a task description:</li>
            <li><textarea name=\"taskDesc\" disabled style=\"width:400px; height:100px;\">$row[taskdesc]</textarea>
            <br/><div></div>
            
        <li>Bidding Close Date:</li>  
    	<li><input type='text' name='date' value='$row[biddingclose]' disabled/></li>  
    	<br/><div></div>   
    	
    	</form>";

        date_default_timezone_set("Asia/Singapore");
        echo "Today is " . date("d/m/Y h:i:sa"). "<br/>";
        echo "
      
            <form action=".'CreateBid.php?taskid='."$taskId&owneremail=$ownerEmail&useremail=$userEmail method='POST'>
            <li>Enter a Bid: <input type='text' name='bidamt'  value=$pevbidamt></li>
            <br/><div></div>";

        if($disbale==false){
           echo"
            <li><input type='submit' name=$button  value='Bid'/></li>
           
            <br/><div></div>";
            }
          echo"  
            <li><input type='submit' name='back' value='Back'/></li>
            ";


            if (isset($_POST['bid'])){

            date_default_timezone_set("Asia/Singapore");
            $bidamt = $_POST[bidamt];
            $biddateandtime= date("d/m/Y h:i:sa");
            //$name = $row[name];
            $status = "Open";

            try {
            $result2 = pg_query($db, "INSERT INTO bid_task (owneremail,taskid,bidderemail,bidamount,bidstatus,biddatetime) 
                        VALUES('$ownerEmail','$taskId','$userEmail','$bidamt','$status','$biddateandtime')");
                if(!$result2){
                echo "Have Already bid on this task";
                } else {
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

            if (isset($_POST['updateBid'])){
                date_default_timezone_set("Asia/Singapore");
                $bidamt = $_POST[bidamt];
                $biddateandtime= date("d/m/Y h:i:sa");
                //$name = $row[name];

                try {
                    $result2 = pg_query($db, "UPDATE bid_task b set bidamount='$bidamt',biddatetime='$biddateandtime' 
                                                     where b.taskid = '$taskId' And b.owneremail= '$ownerEmail' AND b.bidderemail ='$userEmail' ");

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