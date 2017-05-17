<?php
include('./classes/DB.php');
include('./classes/Login.php');
//include('./classes/Image.php');
if (Login::isLoggedIn()) {
        $userid = Login::isLoggedIn();
} else {
        echo "<script>window.open('login.php', '_self')</script>";
}
$username = "";
$username .= DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];

$email = "";
$email .= DB::query('SELECT email FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['email'];


if (isset($_POST['editdetails'])) {
        //Image::uploadImage('profileimg', "UPDATE users SET profileimg = :profileimg WHERE id=:userid", array(':userid'=>$userid));
        DB::query('UPDATE users SET username=:username WHERE id=:userid', array(':userid'=>$userid,':username'=>$_POST['username']));
        DB::query('UPDATE users SET email=:email WHERE id=:userid', array(':userid'=>$userid,':email'=>$_POST['email']));
        echo "<script>window.open('my-account.php', '_self')</script>";
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

<link href='css/home.css' rel="stylesheet" type="text/css"/>

<link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
</head>
<body>

<form action="index.php?username=<?php echo $username; ?>" method="post" class="container-fluid navi-bar">
        <div class="input-group search-bar">
                <input type="text" class="form-control" placeholder="Search" name="searchbox">
                <div class="input-group-btn">
                        <button class="btn btn-default" type="submit" name="search">
                                <i class="glyphicon glyphicon-search"></i>
                        </button>
                </div>
        </div>
        <nav class="navigation">
                <ul>
                        <li role="presentation"><a href="profile.php?username=<?php echo $username; ?>"><?php echo $username; ?></a></li>
                        <li class="divider" role="presentation"></li>
                        <li role="presentation"><a href="index.php">Timeline </a></li>
                        <li role="presentation"><a href="my-messages.php">Messages </a></li>
                        <li role="presentation"><a href="#">Notifications </a></li>
                        <li role="presentation"><a href="my-account.php" class="active">Edit Account</a></li>
                        <li role="presentation"><a href="logout.php">Logout </a></li>
                </ul>

        </nav>
</form>



<div class="wrapper">

 <form action="my-account.php" method="post" enctype="multipart/form-data" class="login">

    <div class="tabs">
      <div class="tab tab_active">Edit your Profile</div>
    </div>
     <div class="login-row">
      <label class="login-label" for="usr">Username:</label>
      <input class="login-input" id="usr" type="text" name="username" value="<?php echo $username;?>">
    </div>
    <div class="login-row">
      <label class="login-label" for="email">Email:</label>
      <input class="login-input" id="email" type="email" name="email" value="<?php echo $email;?>">
    </div>
     <!--<div class="login-row">
      <label class="login-label" for="pwd">Password</label>
      <input class="login-input" id="pwd" type="password" name="password" value="">
    </div>
    <div class="login-row">
      <label class="login-label" for="conpwd">Confirm Password</label>
      <input class="login-input" id="conpwd" type="password" name="confirmpassword" value="">
    </div>-->
    <div class="login-row">
      <label class="login-label" for="image">Profile Image:</label>
      <input class="login-input" id="image"  type="file" name="profileimg" />
    </div>
    <div class="login-row">
      <button class="login-button" type="submit" name="editdetails">Save</button>
    </div>


  </form>

</div>


</body>
</html>