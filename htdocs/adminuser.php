<?php
$targetuseremail=$_GET['targetuseremail'];
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

    <title>Viewing User</title>
</head>
<body>
<div class="container">

    <div class="row">

        <div>

            <div><hr></div>

            <div><h2>Viewing  User Information</h2></div>

            <div><hr></div>

        </div>

    </div>

    <div class="row">
        <?php

        $result = pg_query($db, "SELECT * FROM users WHERE email='$targetuseremail'");
        $row    = pg_fetch_assoc($result);
        echo "

       <form name='taskform' action=''  method='post' >  
       <fieldset>
     
    	<table cellpadding='5'  style='border: 1px solid darkgrey; border-radius:6px; margin-left: 5px;'>
    	<tr>
    	<td>Email:</td>
    	<td><input type='text' name='targetuseremail' value='$row[email]'  disabled><td>
    	</tr>
    	
    	<tr>
    	<td>Name:</td>
    	<td><input type='text' name='name' value='$row[name]' ><td>
    	</tr>
    	
    	<tr>
    	<td>Date of Birth:</td>
    	<td><input type='text' name='dateofbirth' value='$row[dateofbirth]' ><td>
    	</tr>
    	
    	<tr>
    	<td>Phone No.:</td>
    	<td><input type='text' name='phone' value='$row[phone]'  ><td>
    	</tr>
    	
    	<tr>
    	<td>Password:</td>
    	<td><input type='password' name='password' value='$row[password]'  ><td>
    	</tr>
    	</fieldset>
    	
    	<table>
        <tr>
            <br/><br/>
        <td><input type='submit' name='deleteUser' value='Delete User' hidden disabled/></td>
         <td><input type='submit' name='updateUser' value='Update User'/></td>
         <td><input type='submit' name='back' value='Back'/></td>
         </tr>    
        </table>
    	</table>
    	</form>";

        //Update Task Button clicked
        if (isset($_POST['updateUser'])){
            $name=$_POST['name'];
            $dateofbirth = $_POST['dateofbirth'];
            $phone=$_POST['phone'];
            $password = $_POST['password'];
            //$name = $row[name];

            try {
                $result3 = pg_query($db, "UPDATE users SET (password,name, dateofbirth, phone) = ('$password','$name', '$dateofbirth', '$phone')
                                                     WHERE email='$row[email]'");
                echo "<script>alert('User details successfully updated!');</script>";
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
        if (isset($_POST['deleteUser'])){
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