<?php
include('classes/DB.php');
if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
                if (password_verify($password, DB::query('SELECT password FROM users WHERE username=:username', array(':username'=>$username))[0]['password'])) {
                        echo "<script>window.open('profile.php?username=$username', '_self')</script>";
                        $cstrong = True;
                        $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
                        $user_id = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
                        DB::query('INSERT INTO login_tokens VALUES (\'\', :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
                        setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
                        setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
                } else {
                        echo "<div class='alert alert-warning alert-dismissable fade in container'>
                                <a href='#' class='close' data-dismiss='alert'  aria-label='close'>&times;</a>
                                <strong>Oops!</strong> Incorrect Password.
                              </div>";
                }
        } else {
          
                 echo "<div class='alert alert-info alert-dismissable fade in container'>
                                <a href='#' class='close' data-dismiss='alert'  aria-label='close'>&times;</a>
                                <strong>Sorry,</strong> That user doesn't exist. Sign up "."<a href='http://localhost:802/SocialNetwork2.0/create-account.php'>here</a>".
                       "</div>";
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

  <form class="login" action="login.php" method="post">

    <div class="tabs">
      <div class="tab tab_active">Log In</div>
      <div class="tab or-tab_active">or</div>
      <div class="tab"><a href="create-account.php">Create an Account</a></div>
    </div>
    <div class="login-row">
      <label class="login-label" for="email">Username</label>
      <input class="login-input" id="email" type="text" name="username" required="required">
    </div>
    <div class="login-row">
      <label class="login-label" for="password">Password</label>
      <input class="login-input" id="password" type="password" name="password" required="required">
    </div>
    <div class="login-row">
      <button class="login-button" type="submit" name="login">Sign in</button>
    </div>
    <a class="forgot-password" href="forgot-password.php">Forgot password?</a>


  </form>

</div>


</body>
</html>