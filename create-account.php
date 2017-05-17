<?php
include('classes/DB.php');
include('classes/Mail.php');
if (isset($_POST['createaccount'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
                if (strlen($username) >= 3 && strlen($username) <= 32) {
                        if (preg_match('/[a-zA-Z0-9_]+/', $username)) {
                                if (strlen($password) >= 6 && strlen($password) <= 60) {
                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                if (!DB::query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))) {
                                        DB::query('INSERT INTO users VALUES (\'\', :username, :password, :email, \'0\', \'\')', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email));
                                        echo "<script>window.open('profile.php?username=$username', '_self')</script>";
                                        Mail::sendMail('Welcome to our Social Network!', "Hey ".$username.", Dave Here"."<br/><br/>"."\n I just wanted to be the first to tell you how, us at momentum.org are so super stoked! That you joined the momentum family! :)<hr/>Head on over to the <a href='http://localhost:802/SocialNetwork2.0/login.php'><b>Login Page</b></a> and generate <em>Momentum </em>!", $email);
                                        
                                } else {
                                        echo "<div class='alert alert-info alert-dismissable fade in container'>
                                <a href='#' class='close' data-dismiss='alert'  aria-label='close'>&times;</a>
                                <strong>Sorry,</strong> That email is already in use.
                       </div>";
                                }
                        } else {
                                         echo "<div class='alert alert-info alert-dismissable fade in container'>
                                <a href='#' class='close' data-dismiss='alert'  aria-label='close'>&times;</a>
                                <strong>Sorry,</strong> That email is invalid.
                       </div>";
                                }
                        } else {
                                 echo "<div class='alert alert-info alert-dismissable fade in container'>
                                <a href='#' class='close' data-dismiss='alert'  aria-label='close'>&times;</a>
                                <strong>Sorry,</strong> That password is invalid.
                       </div>";
                        }
                        } else {
                                echo "<div class='alert alert-info alert-dismissable fade in container'>
                                <a href='#' class='close' data-dismiss='alert'  aria-label='close'>&times;</a>
                                <strong>Sorry,</strong> That username is invalid.
                       </div>";
                        }
                } else {
                         echo "<div class='alert alert-info alert-dismissable fade in container'>
                                <a href='#' class='close' data-dismiss='alert'  aria-label='close'>&times;</a>
                                <strong>Sorry,</strong> That username is invalid.
                       </div>";
                }
        } else {
                 echo "<div class='alert alert-info alert-dismissable fade in container'>
                                <a href='#' class='close' data-dismiss='alert'  aria-label='close'>&times;</a>
                                <strong>Sorry,</strong> That user already exists.
                       </div>";
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

  <form class="login" action="create-account.php" method="post">

    <div class="tabs">
      <div class="create-tab tab_active">Create your account</div>
      <div class="create-tab or-tab_active">or</div>
      <div class="create-tab"><a href="login.php">Log In</a></div>
    </div>
    <div class="login-row">
      <label class="login-label" for="username">Username</label>
      <input class="login-input" id="email" type="text" name="username" required="required">
    </div>
    <div class="login-row">
      <label class="login-label" for="password">Password</label>
      <input class="login-input" id="password" type="password" name="password" required="required">
    </div>
    <div class="login-row">
      <label class="login-label" for="email">Email</label>
      <input class="login-input" id="email" type="email" name="email" required="required">
    </div>
    <div class="login-row">
      <button class="login-button" type="submit" name="createaccount">Create my Account!</button>
    </div>
    <a class="forgot-password" href="forgot-password.php">Forgot password?</a>

  </form>

</div>


</body>
</html>