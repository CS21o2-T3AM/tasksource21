<?php
// Use session to pass information such as email.
//Note input validation not done yet
require_once '../utils/login.inc.php';
admin_login_validate_or_redirect();
?>
<!DOCTYPE html>
<html>


<head>
    <title>Admin Control Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
    <script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        thead {
            position: relative;
        }

        tr:hover{background-color:#f5f5f5}
    </style>

</head>

<body>

<!--  Navigation Bar --->
<nav class="navbar navbar-default">
    <div class="container-fluid"  style="background-color:slategrey; color:ghostwhite;">

        <!--Logo-->
        <div class="navbar-header" style="color:white; float:left; size: 30px" >
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
    <div class="'page-header">
    <h2  style="color:darkslategrey">Admin Control Panel</h2>
    </div>
</div>

<div class="container" >
    <!--welcome message-->
    <h3 align="right">Welcome back, <?php echo $email; ?>!</h3>

    <!-- Menu -->
    <div  align='right' class='container' id="wrapper" style="">
        <div class='btn-group btn-group-lg' role="group"    >
            <a href="admin.php" class="btn btn-default">Users</a>
            <a href="admintasks.php" class="btn btn-default active">Tasks</a>
            <a href="adminbids.php" class="btn btn-default">Bids</a>

            <a href="admincategories.php" class="btn btn-warning ">Task Categories</a>
        </div>
    </div>

    <?php
    //Connect to Database
    try {
        $dbuser = 'postgres';
        $dbpass = 'password';
        $host = '127.0.0.1';
        $dbname='tasksource21';

        $connec = new PDO("pgsql:host=$host;dbname=$dbname", $dbuser, $dbpass);;
    }catch (PDOException $e) {
        echo "Error : " . $e->getMessage() . "<br/>";
        die();
    }

    //Log out button pressed
    if (isset($_POST['logout'])){
        //pass email and username to next page
        header("Location: index.php"); //send user to the next page
        exit;
    }

    $db= pg_connect("host=127.0.0.1 port=5432 dbname=tasksource21 user=postgres password=password");
    $categories_result = pg_query($db, "SELECT * FROM task_categories");

       //DISPLAY ALL TASKS
    echo "<div class='container' id='tasks-wrapper' >";
    echo "<h2>Tasks</h2>";
    echo "<form name='searchTasks' action='' method='POST'>";
    echo   "<br/> <small>Search Tasks (Tasker's Email/ Task Name)</small><br/>";
    echo      "<input type='text' name='taskName' value=''/>";
    echo    "<button type='submit' name='searchTasks' value='Search'><span class='glyphicon glyphicon-search'></span></button>
                    <button type='submit' name='advancedSearch' value='advancedSearch'><span class='glyphicon glyphicon-eye-close'></span></button>
     
     <br/><br/>
     <!-- Search by Date also -->
     <div><small>Specify Date Range: </small></div>
     <div style='float:left; height:20px; width:220px; margin-right:5px'>
        <div class='form-group'>
            <div class='input-group date' id='datetimepicker6'>
                <input type='text' class='form-control' name='startDate' id='startDate' value=''/>
                <span class='input-group-addon'>
                    <span class='glyphicon glyphicon-calendar'></span>
                </span>
            </div>
        </div>
    </div>
    <div style='float:left'><h5> to </h5></div>
    <div style='float:left; height:20px; width:220px; margin-left:5px'>
        <div class='form-group'>
            <div class='input-group date' id='datetimepicker7'>
                <input type='text' class='form-control' name='endDate' id='endDate' value=''/>
                <span class='input-group-addon'>
                    <span class='glyphicon glyphicon-calendar'></span>
                </span>
            </div>
        </div>
    </div>
    
    <br/><br/>
    
         <div><small>Specify Price Range: </small></div>
         <div style='float:left'><h5>$</h5></div>
     <div style='float:left; height:20px; width:120px; margin-right:5px'>
        <div class='form-group'>
            <div class='input-group' id='pricepicker1'>
               <input type='text' class='form-control' name='lowPrice' id='lowPrice' value=''/>
            </div>
        </div>
    </div>
    <div style='float:left; margin-right:5px;'><h5> to </h5></div>
    <div style='float:left;'><h5>$</h5></div>
    <div style='float:left; height:20px; width:120px;'>
        <div class='form-group'>
            <div class='input-group date' id='pricepicker2'>
               <input type='text' class='form-control' name='highPrice' id='highPrice' value=''/>
            </div>
        </div>
    </div>

<br/><br/>

<div><small>Specify Category</small></div>
<div>
";
    //Drop Down List for Categories
    echo"<select name='category_dropdown' id='category_dropdown''>
                <option value='' selected='selected'></option>
    ";
    while($row_categories=pg_fetch_assoc($categories_result)){
        $display = $row_categories["name"];
        echo"<option value = '$display'>$display</option>'";
    }
    echo"</select>";

echo"
</div>

</form>
</div> <!--close the Entire Menu container-->

<script type='text/javascript'>
    $(function () {
        $('#datetimepicker6').datetimepicker({format: 'DD/MM/YYYY HH:mm'});
        $('#datetimepicker7').datetimepicker({format: 'DD/MM/YYYY HH:mm',
            useCurrent: false 
        });
        $('#datetimepicker6').on('dp.change', function (e) {
            $('#datetimepicker7').data('DateTimePicker').minDate(e.date);
        });
        $('#datetimepicker7').on('dp.change', function (e) {
            $('#datetimepicker6').data('DateTimePicker').maxDate(e.date);
        });
    });
</script>
     ";
    echo "<br/>";

    $_POST['searchTasks'] = true;
    if (isset($_POST['searchTasks'])) {
        $userInput =  $_POST['taskName'];
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
        $lowPrice = $_POST['lowPrice'];
        $highPrice = $_POST['highPrice'];
        $category = $_POST['category_dropdown'];

        //Display all Tasks by default
        $sql = 'select * from tasks ';
        $order = 'ORDER BY name DESC ';
        $searchDateRange = ' start_datetime >= \''.$startDate.'\' AND end_datetime <= \''.$endDate.'\' ';
        $searchPriceRange = ' suggested_price BETWEEN CAST('.$lowPrice.' AS money)  AND CAST('.$highPrice.' AS money) ';
        $searchCategory = ' category = \''.$category.'\' ';
        //SEARCH BY EMAIL OR NAME?
        if(strpos($userInput, '@')){
            //Add search by owner_email
            echo "Searching by Owner Email: ".$userInput;
            $sql = $sql.' where owner_email ILIKE '."'%".$userInput."%' ";
        }
        else if(!empty($userInput)){
            //Add search by task name
            echo "Searching by Task Name: ".$userInput;
            $sql = $sql.' where name ILIKE '."'%".$userInput."%'";
        }

        //Add Date Range Condition
        if(!empty($startDate) && !empty($endDate)){
            //startDate will be < endDate, as already checked by JavaScript
            if(empty($userInput)){//If no email or taskname provided
                $sql = $sql.' where '.$searchDateRange;
            }else{
                $sql = $sql.' and '.$searchDateRange;
            }
        }

        if(!empty($lowPrice) && !empty($highPrice)){
            if(empty($userInput) && empty($startDate) && empty($endDate)){
                $sql = $sql.' where '.$searchPriceRange;
            }else{
                $sql = $sql.' and '.$searchPriceRange;
            }
        }

        if(!empty($category)){
            if(empty($userInput) && empty($startDate) && empty($endDate) && empty($lowPrice) && empty($highPrice)){
                $sql = $sql.' where '.$searchCategory;
            }else{
                $sql = $sql.' AND '.$searchCategory;
            }
        }

        $sql = $sql.$order;
        //echo"$sql";

        if(isset($_POST['advancedSearch'])){
            echo '<script>window.location = "/tasksource21/admintasks.php";</script>';
        }
        echo "<br/>";
        //Dynamic Task display
        echo "<div style='height: 300px; width: auto; font-size: 16px; overflow: auto;border:2px solid darkgray; border-radius:5px;''>";
        echo "<table class='table table-bordered table-striped table-hover'>";
        echo "<tr>";
        echo "<th align='center' width='200'>ID</th>";
        echo "<th align='center' width='200'>Task Name</th>";
        echo "<th align='center' width='200'>Owner</th>";
        echo "<th align='center' width='200'>Category</th>";
        echo "<th align='center' width='200'>Start</th>";
        echo "<th align='center' width='200'>End</th>";
        echo "<th align='center' width='200'>Suggested Price</th>";
        echo "<th align='center' width='200'>Status</th>";
        echo "<th align='center' width='200'>Bidding Deadline</th>";
        echo "<th align='center' width='200'>Updated</th>";
        foreach ($connec->query($sql) as $row)
        {
            echo "<tr>";
            echo "<td align='center' width='200'>" . $row['id']. "</td>";
            echo "<td align='center' width='200'><a href='admintaskdetail.php?taskid={$row['id']}&owneremail={$row['owner_email']}&useremail=$email'>". $row['name'] ."</a></td>";
            echo "<td align='center' width='200'>" . $row['owner_email'] . "</td>";
            echo "<td align='center' width='200'>" . $row['category'] . "</td>";
            echo "<td align='center' width='200'>" . $row['start_datetime'] . "</td>";
            echo "<td align='center' width='200'>" . $row['end_datetime'] . "</td>";
            echo "<td align='center' width='200'>" . $row['suggested_price'] . "</td>";
            echo "<td align='center' width='200'>" . $row['status'] . "</td>";
            echo "<td align='center' width='200'>" . $row['bidding_deadline'] . "</td>";
            echo "<td align='center' width='200'>" . $row['datetime_updated'] . "</td>";
            echo "</tr>";}
        echo "</table>";
        echo "</div>";
    }
    echo "<br/>";
    echo "<br/>";

        ?>

</div> <!--page-content-wrapper div-->
</body>
</html>
