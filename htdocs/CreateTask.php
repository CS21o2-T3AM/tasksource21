<?php
// Use session to pass infomation such as email.
//Note input validation not done yet
session_start();
$email=$_SESSION["userEmail"];
$name=$_SESSION["userName"];
?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Create Task</title>


</head>

<body>

<?php
/*
//INput Validation
if (isset($_POST['submit'])) {
    $counter=0;
    $email=$password=$dob=$name=$contact= "";
    if (empty($_POST[userid])) {
        $nameErr = "Field canont be empty";
    }
    else {
        $email = $_POST[userid];
        $counter++;
    }
    if (empty($_POST[password])) {
        $nameErr1 = "Field canont be empty";
    }
    else {
        if (empty($_POST[confirm]))  {
            $nameErr2 = "Field canont be empty";
        }
        else {
            if($_POST[password] == $_POST[confirm]){
                $password =  $_POST[password];
                $counter++;
            }
            else {
                echo "Password do not Match";
            }
        }
    }
    if (empty($_POST[dob]))  {
        $nameErr3= "Field canont be empty";
    }
    else {
        $dob = $_POST[dob];
        $counter++;
    }
    if (empty($_POST[name]))  {
        $nameErr4 = "Field canont be empty";
    }
    else {
        $name = $_POST[name];
        $counter++;
    }
    if (empty($_POST[contact]))  {
        $nameErr5 = "Missing";
    }
    else {
        $contact = $_POST[contact];
        $counter++;
    }
}*/
?>

<div class="container">

    <div class="row">

        <div>

            <div><hr></div>

            <div><h2>Create Task</h2></div>

            <div><hr></div>

        </div>

    </div>

    <div class="row">

        <!-- contact form -->

        <form action="CreateTask" method="POST">

            <div class="col-lg-3">

                <br />

            </div>

            <li>Enter Task Name: <input type="text" name="taskName" />
                <span class="error"><?php echo $nameErr;?></span></li>
            <br/><div></div>

            <li>Enter a task description:</li>
            <li><textarea name="taskDesc" style="width:400px; height:100px;"></textarea>
                <span class="error"><?php echo $nameErr1;?></span></li>
            <br/><div></div>


            <script type="text/javascript">
                function showfield(name){
                    if(name=='Other')document.getElementById('div1').innerHTML='Other: <input type="text" name="other" />';
                    else document.getElementById('div1').innerHTML='';
                }
            </script>

            <li>Select task Category:
                <select name="cat" onchange="showfield(this.options[this.selectedIndex].value)">
                    <option selected="selected">Please select ...</option>
                    <option value="Minor repair">Minor repair</option>
                    <option value="House Cleaning">House Cleaning</option>
                    <option value="Home Improvement">Home Improvement</option>
                    <option value="Furniture assembly">Furniture assembly</option>
                    <option value="House Cleaning">House Cleaning</option>
                    <option value="Moving & packing">Moving & packing</option>
                    <option value="Other">Other</option>
                </select>
                <div><div id="div1"></div></div>
                <?php echo $nameErr2;?></span>
            </li>
            <br/><div></div>

            <li>Date & Time of Task: <input type="text" name="date" />
                <span class="error"><?php echo $nameErr3;?></span> </li>
            <br/><div></div>

            <li>Date to end bids: <input type="text" name="bids" /> DD/MM/YYYY
                <span class="error"><?php echo $nameErr4;?></span></li>
            <br/><div></div>

            <li><input type="submit" name="submit" value="Submit"/></li>
            <br/><div></div>
            <li><input type="submit" name="back" value="Back"/></li>

            <?php
            if (isset($_POST['submit'])){
                $taskName= $_POST[taskName];
                $taskDesc= $_POST[taskDesc];
                if($_POST[cat]=='Other'){
                    $taskCat =  $_POST[other];
                }
                else{
                    $taskCat= $_POST[cat];
                }
                $taskDateNTime= $_POST[date];
                echo "$taskDateNTime";
                $EndBid = $_POST[bids];
                $status = "open";
                $db     = pg_connect("host=127.0.0.1 port=5432 dbname=tasksource21 user=postgres password=Mapler0ck");
                if($userexist==false){
                    try {
                        $result = pg_query($db, "INSERT INTO create_task (ownerEmail,taskName,taskDesc,taskCategory,taskDateAndTime,status,biddingClose) 
                        VALUES('$email','$taskName','$taskDesc','$taskCat','$taskDateNTime','$status','$EndBid')");
                        if(!$result){
                            echo "Update failed!!";
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