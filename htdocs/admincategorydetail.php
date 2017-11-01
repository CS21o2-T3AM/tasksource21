<?php
session_start();
$categoryName = $_GET['categoryName'];
$targetuseremail=$_GET['targetuseremail'];
$userEmail= $_GET['useremail'];

$db= pg_connect("host=127.0.0.1 port=5432 dbname=tasksource21 user=postgres password=password");

//Authentication check
//if($userEmail==""){
//    header("Location: index.php");
//    exit;
//}
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <title>Viewing User</title>
</head>

<body>

<!--  Navigation Bar --->
<nav class="navbar navbar-default">
    <div class="container-fluid"  style="background-color:slategrey; color:ghostwhite;">

        <!--Logo-->
        <div class="navbar-header" style="color:white; float:left" >
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

<!--Header-->
<div class="container">
    <h2 class="'page-header"  style="color:darkslategrey"> Admin Control Panel</h2>
</div>

<!--Content-->
<div class="container">

    <div class="row">

        <div>

            <div><hr></div>

            <div><h2>Deleting and Updating Category</h2></div>

            <div><hr></div>

        </div>

    </div>

    <div class="container">
        <?php

        $result = pg_query($db, "SELECT * FROM task_categories WHERE name='$categoryName'");
        $row    = pg_fetch_assoc($result);
        echo "

       <form name='taskform' action=''  method='post' >  

    	<table class='table table-bordered table-striped table-hover col-xs-4 col-md-4 col-lg-4 col-col-xl-4 '>
    	<tr>
    	<td>Name:</td>
    	<td><input type='text' name='categoryName' value='$row[name]'  readonly style='border:none; background-color: transparent'></td>
    	</tr>
    	
    	<tr>
    	<td>Description:</td>
    	<td><textarea name='description' value='' style='background-color: transparent; width:600px; height:200px' >$row[description]</textarea></td>
    	</tr>
    	    	

    	
    	<table cellpadding='5' align='right'>
        <tr>
            <br/><br/>
             <td><input type='submit' name='back' value='Back' class='btn-default'/></td>
            <td><input type='submit' name='deleteCategory' value='Delete Category' class='btn-danger'/></td>
             <td><input type='submit' name='updateCategory' value='Update Category' class='btn-success'/></td>
         </tr>    
        </table>
    	</table>
    	</form>";

        //Update Task Button clicked
        if (isset($_POST['updateCategory'])){
            echo $_POST["description"];
            $description=$_POST["description"];

            //$name = $row[name];

            try {
                $result3 = pg_query($db, "UPDATE task_categories SET (description) = ('$description')
                                                     WHERE name='$row[name]'");
                if(empty($result3)){
                    echo "<script>alert('An error has occured, please try again later.');</script>";
                }else{
                    echo "<script>alert('Task Category details successfully updated!');</script>";
                }
                header("refresh:0");
            }
            catch(PDOException $ex){
                echo "<script>alert('An error has occured, please try again later.');</script>";
            }
            parent.window.location.reload();
        }

        //Delete Task Button Clicked
        if (isset($_POST['deleteCategory'])){
            date_default_timezone_set("Asia/Singapore");
            $bidamt = $_POST[bidamt];
            $biddateandtime= date("d/m/Y h:i:sa");
            //$name = $row[name];
            $status = "Open";
            try {
                $result3 = pg_query($db, "DELETE FROM task_categories
                                                     WHERE name='$row[name]'");
                echo "<script>alert('Task Category has been deleted.');</script>";
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
            header("Location: http://localhost/tasksource21/admincategories.php");
            exit;
        }

        ?>

    </div>

</div>
</body>
</html>