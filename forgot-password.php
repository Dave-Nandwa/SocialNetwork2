<?php
include('./classes/DB.php');
include('./classes/Mail.php');
if (isset($_POST['resetpassword'])) {
        $cstrong = True;
        $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
        $email = $_POST['email'];
        $user_id = DB::query('SELECT id FROM users WHERE email=:email', array(':email'=>$email))[0]['id'];
        DB::query('INSERT INTO password_tokens VALUES (\'\', :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
        Mail::sendMail('Forgot Password!', "<a href='http://localhost/tutorials/sn/change-password.php?token=$token'>http://localhost/tutorials/sn/change-password.php?token=$token</a>", $email);
        
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

  <form action="forgot-password.php" method="post" class="login">

    <div class="tabs">
      <div class="tab tab_active">Forgot your Password?</div>
    </div>
    <div class="login-row">
      <label class="login-label" for="email">Email:</label>
      <input class="login-input" id="email" type="text"  name="email" value=""  required="required">
    </div>
    <div class="login-row">
      <button class="login-button" type="submit" name="resetpassword">Recover Password</button>
    </div>


  </form>

</div>


</body>
</html>

<h1>Forgot Password</h1>
<form action="forgot-password.php" method="post">
        <input type="text" name="email" value="" placeholder="Email ..."><p />
        <input type="submit" name="resetpassword" value="Reset Password">
</form>
