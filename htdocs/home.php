<?php
session_start();
?>

<html>
<body>Welcome, <?php echo $_SESSION["userName"]?> to HOME.PHP</body>
<p>You are currently logged in with <?php echo $_SESSION["userEmail"];?></p>
</html>