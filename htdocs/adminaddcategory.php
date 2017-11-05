<?php
session_start();
$categoryName = $_GET['categoryName'];
$targetuseremail=$_GET['targetuseremail'];
$userEmail= $_GET['useremail'];

$db= pg_connect("host=127.0.0.1 port=5432 dbname=tasksource21 user=postgres password=password");

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

    <title>Add Task Category</title>
</head>

<body>

<!--  Navigation Bar --->
<nav class="navbar navbar-default">
    <div class="container-fluid"  style="background-color:slategrey; color:ghostwhite;">

        <!--Logo-->
        <div class="navbar-header" style="color:white; float:left" >
            <h2  style="color:white">TASKSOURCE21 </h2>
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

       <form name='taskform' action=''  method='post' >  

    	<table class='table table-bordered table-striped table-hover col-xs-4 col-md-4 col-lg-4 col-col-xl-4 '>
    	<tr>
    	<td>Name:</td>
    	<td><input type='text' name='name' value='' style='; background-color: transparent' ></td>
    	</tr>
    	
    	<tr>
    	<td>Description:</td>
    	<td><textarea name='description'  style='; background-color: transparent; width:600px; height:200px' ></textarea></td>
    	</tr>
    	    	

    	
    	<table cellpadding='5' align='right'>
        <tr>
            <br/><br/>
             <td><input type='submit' name='back' value='Back' class='btn-default'/></td>
             <td><input type='submit' name='addCategory' value='Add Category' class='btn-success'/></td>
         </tr>    
        </table>
    	</table>
    	</form>
        <?php
        //Update Task Button clicked
        if (isset($_POST['addCategory'])){
            $name = $_POST["name"];
            $description=$_POST["description"];

            //$name = $row[name];

            try {
                $result3 = pg_query($db, "Insert into task_categories values ('$name','$description')");

                header("refresh:0");
                if(empty($result3)){
                    echo "<script>alert('An error has occured, please try again later!');</script>";
                }else{
                    echo "<script>alert('New Task Category successfully created!');</script>";
                }
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