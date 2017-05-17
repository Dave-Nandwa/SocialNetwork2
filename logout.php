<?php
include('./classes/DB.php');
include('./classes/Login.php');
if (!Login::isLoggedIn()) {
        echo "<script>window.open('login.php', '_self')</script>";
}
if (isset($_POST['confirm'])) {
        if (isset($_POST['alldevices'])) {
                DB::query('DELETE FROM login_tokens WHERE user_id=:userid', array(':userid'=>Login::isLoggedIn()));
        } else {
                if (isset($_COOKIE['SNID'])) {
                        DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SNID'])));
                }
                setcookie('SNID', '1', time()-3600);
                setcookie('SNID_', '1', time()-3600);
        }
}
?>


<!DOCTYPE html>
<html>

<head>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<link href='css/main.css' rel="stylesheet" type="text/css"/>

<link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
</head>
<body>


<div class="wrapper">

  <form action="logout.php" method="post" class="login">

    <div class="tabs">
      <div class="tab tab_active">Log Out</div>
    </div>
    <div class="login-row-ask">
      <label class="login-label" for="password">Logout of all devices?</label>
      <input class="login-input" id="logout" type="checkbox" name="alldevices"/>
    </div>
    <div class="login-row">
      <button class="login-button" type="submit" name="confirm">Log Out</button>
    </div>
    <a class="forgot-password" href="#">Are you sure you'd like to logout?</a>


  </form>

</div>


</body>
</html>