<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Image.php');
include('./classes/Notify.php');
$username = "";
$verified = False;
$isFollowing = False;
$deleted = "";
if (isset($_GET['username'])) {
        if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))) {
                $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
                $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
                $verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];
                $followerid = Login::isLoggedIn();
                if (isset($_POST['follow'])) {
                        if ($userid != $followerid) {
                                if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                                        if ($followerid == 6) {
                                                DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$userid));
                                        }
                                        DB::query('INSERT INTO followers VALUES (\'\', :userid, :followerid)', array(':userid'=>$userid, ':followerid'=>$followerid));
                                } else {
                                        echo 'Already following!';
                                }
                                $isFollowing = True;
                        }
                }
                if (isset($_POST['unfollow'])) {
                        if ($userid != $followerid) {
                                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                                        if ($followerid == 6) {
                                                DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid'=>$userid));
                                        }
                                        DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid));
                                }
                                $isFollowing = False;
                        }
                }
                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                        //echo 'Already following!';
                        $isFollowing = True;
                }
                if (isset($_POST['deletepost'])) {
                        if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))) {
                                DB::query('DELETE FROM posts WHERE id=:postid and user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
                                DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid'=>$_GET['postid']));
                                 $deleted .= "<div class='alert alert-success alert-dismissable fade in container'>
                                <a href='#' class='close' data-dismiss='alert'  aria-label='close'>&times;</a>
                                <strong>Post Deleted</strong>".
                       "</div>";
                        }
                }
                if (isset($_POST['post'])) {
                        if ($_FILES['postimg']['size'] == 0) {
                                Post::createPost($_POST['title'], $_POST['postbody'], Login::isLoggedIn(), $userid);
                        } else {
                                $postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                                Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid'=>$postid));
                        }
                }

                if (isset($_POST['searchbox'])) {
        $tosearch = explode(" ", $_POST['searchbox']);
        if (count($tosearch) == 1) {
                $tosearch = str_split($tosearch[0], 2);
        }
        $whereclause = "";
        $paramsarray = array(':username'=>'%'.$_POST['searchbox'].'%');
        for ($i = 0; $i < count($tosearch); $i++) {
                $whereclause .= " OR username LIKE :u$i ";
                $paramsarray[":u$i"] = $tosearch[$i];
        }
        $users = DB::query('SELECT users.username FROM users WHERE users.username LIKE :username '.$whereclause.'', $paramsarray);
        print_r($users);
        $whereclause = "";
        $paramsarray = array(':body'=>'%'.$_POST['searchbox'].'%');
        for ($i = 0; $i < count($tosearch); $i++) {
                if ($i % 2) {
                $whereclause .= " OR body LIKE :p$i ";
                $paramsarray[":p$i"] = $tosearch[$i];
                }
        }
        $posts = DB::query('SELECT posts.body FROM posts WHERE posts.body LIKE :body '.$whereclause.'', $paramsarray);
        echo '<pre>';
        print_r($posts);
        echo '</pre>';
}

                if (isset($_GET['postid']) && !isset($_POST['deletepost'])) {
                        Post::likePost($_GET['postid'], $followerid);
                }
                $posts = Post::displayPosts($userid, $username, $followerid);
        } else {
                die('User not found!');
        }
}
?>

<!DOCTYPE html>
<html>
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

<link rel="stylesheet" href="css/home.css">


</head>


<body>

<form action="profile.php?username=<?php echo $username; ?>" method="post" class="container-fluid">
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
                        <li role="presentation"><a href="profile.php?username=<?php echo $username; ?>" class="active"> <?php echo $username; ?><?php if ($verified) { echo " <i class='em em-white_check_mark' style='font-size:9px'></i>"; } ?> </a></li>
                        <li class="divider" role="presentation"></li>
                        <li role="presentation"><a href="index.php">Timeline </a></li>
                        <li role="presentation"><a href="my-messages.php">Messages </a></li>
                        <li role="presentation"><a href="#">Notifications </a></li>
                        <li role="presentation"><a href="my-account.php">Edit Account</a></li>
                        <li role="presentation"><a href="logout.php">Logout </a></li>
                </ul>

        </nav>
</form>

</hr>


<form action="profile.php?username=<?php echo $username; ?>" method="post">
        <?php
        if ($userid != $followerid) {
                if ($isFollowing) {
                        echo '<input type="submit" name="unfollow" value="Unfollow">';
                } else {
                        echo '<input type="submit" name="follow" value="Follow">';
                }
        }
        ?>
</form>



<div class="container create-post">
    <div class="form-group">
    <form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
  	<input type="text" class="form-control" id="usr" name="title" placeholder="Post Title...(optional)" size="82" >
      <textarea name="postbody" rows="8" cols="80" class="form-control" id="comment" placeholder="What's on your mind <?php echo $username; ?>?"></textarea>
      <label for="fil">or post an image</label>
      <input type="file" class="form-control" id="fil" name="postimg">
      <button type="submit" name="post" class="post-button">Post</button>
    </div>
  </form>
</div>

<div class="del">
        <?php echo $deleted; ?>
</div>

<div class="posts">
        <?php echo $posts; ?>
</div>

</body>
</html>