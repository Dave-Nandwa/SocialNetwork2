<!DOCTYPE html>
<html>

<?php
include('./classes/DB.php');
include('./classes/Login.php');
if (Login::isLoggedIn()) {
        $userid = Login::isLoggedIn();
} else {
        echo "<script>window.open('login.php', '_self')</script>";
}
$username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];
$notification = "";

if (DB::query('SELECT * FROM notifications WHERE receiver=:userid', array(':userid'=>$userid))) {
        $notifications = DB::query('SELECT * FROM notifications WHERE receiver=:userid ORDER BY id DESC', array(':userid'=>$userid));
        foreach($notifications as $n) {
                if ($n['type'] == 1) {
                        $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
                        if ($n['extra'] == "") {
                                $notification .= "You got a notification!<hr />";
                        } else {
                                $extra = json_decode($n['extra']);
                                $notification .=  "<div class='w3-panel w3-card-4 container'><p>".$senderName." mentioned you in a post! <i class='em em-smiley'></i></p></div>";
                        }
                } else if ($n['type'] == 2) {
                        $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
                        $notification .=   "<div class='w3-panel w3-card-4 container'><p>".$senderName." liked your post!<i class='em em-smiley'></i>' </p></div>";
                       
                }
                
        }
}
?>

<head>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="bootstrap/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="bootstrap/js/bootstrap.min.js"></script>


<link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>

<link href="https://afeld.github.io/emoji-css/emoji.css" rel="stylesheet">

<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

<link href='css/home.css' rel="stylesheet" type="text/css"/>

</head>

<body>

        <form action="index.php?username=<?php echo $username; ?>" method="post" class="container-fluid">
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
                        <li role="presentation"><a href="index.php" class="active">Timeline </a></li>
                        <li role="presentation"><a href="#">Messages </a></li>
                        <li role="presentation"><a href="#">Notifications </a></li>
                        <li role="presentation"><a href="#">My Account</a></li>
                        <li role="presentation"><a href="#">Logout </a></li>
                </ul>

        </nav>
</form>
<h1 style="color:#fff;margin-left:5px;position:relative">Notifications: </h1><hr/>

<h4><?php echo $notification?></h4>

</body>

</html>