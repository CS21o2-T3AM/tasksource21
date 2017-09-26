<?php
// Use session to pass information such as email.
session_start();
?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Register</title>


</head>

<body>


<?php
//Input Validation, ensure fields are not empty
if (isset($_POST['submit'])) {
    $counter=0;
    $email=$password=$dob=$name=$contact= "";
    if (empty($_POST[userid])) {
        $nameErr = "Field cannot be empty";
    }
    else {
        $email = $_POST[userid];
        $counter++;
    }

    if (empty($_POST[password])) {
        $nameErr1 = "Field cannot be empty";
    }
    else {
        if (empty($_POST[confirm]))  {
            $nameErr2 = "Field cannot be empty";
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
        $nameErr3= "Field cannot be empty";
    }
    else {
        $dob = $_POST[dob];
        $counter++;
    }

    if (empty($_POST[name]))  {
        $nameErr4 = "Field cannot be empty";
    }
    else {
        $name = $_POST[name];
        $counter++;
    }
    if (empty($_POST[contact]))  {
        $nameErr5 = "Field cannot be empty";
    }
    else {
        $contact = $_POST[contact];
        $counter++;
    }
}
?>

    <div class="container">

        <div class="row">

            <div>

                <div><hr></div>

                <div><h2>Register</h2></div>

                <div><hr></div>

            </div>

        </div>

        <div class="row">

            <!-- contact form -->

            <form action="Register" method="POST">

                <div class="col-lg-3">

                    <br />

                </div>

                <li>Enter Email: <input type="text" name="userid" value=<?php echo $email;?>>
                <span class="error"><?php echo $nameErr;?></span></li>
                <br/><div></div>

                <li>Enter Password: <input type="password" name="password" />
                <span class="error"><?php echo $nameErr1;?></span></li>
                <br/><div></div>

                <li>Enter re-Password: <input type="password" name="confirm" />
                <span class="error"><?php echo $nameErr2;?></span></li>
                <br/><div></div>

                <li>Date Of Birth: <input type="text" name="dob" value=<?php echo $dob;?>> DD/MM/YYYY
                <span class="error"><?php echo $nameErr3;?></span> </li>
                <br/><div></div>

                <li>Enter Name: <input type="text" name="name" value=<?php echo $name;?>>
                <span class="error"><?php echo $nameErr4;?></span></li>
                <br/><div></div>

                <li>Phone Number: <input type="text" name="contact" value=<?php echo $contact;?>>
                <span class="error"><?php echo $nameErr5;?></span></li>
                <br/><div></div>

                <li><input type="submit" name="submit" value="Submit"/></li>

                <?php
                if($counter == 5){
                   $userexist=false;
                    $db     = pg_connect("host=localhost port=5432 dbname=tasksource21 user=postgres password=jaspreet");
                    if($userexist==false){
                       //insert stmt to input information to DB
                        try {
                           $result = pg_query($db, "INSERT INTO users VALUES('$email','$password','$name','$dob','false','$contact')");
                           if(!$result){
                               echo "Insert failed!!";
                           } else {
                               //pass email and username to next page
                               $_SESSION['userName'] = $name;
                               $_SESSION['userEmail'] = $email;
                               header("Location: home.php"); //send user to the next page
                               exit;
                           }
                       }
                       catch(mysqli_sql_exception $ex){
                           echo "DB Error";
                        }

                   }
                }
                ?>

                <div>

                        <br /><br /><p class="text-center">Back to <a href="/index.php" >Login</a>

                </div>

            </form>

        </div>

    </div>


</body>

</html>