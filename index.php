<!DOCTYPE html>
<html>

<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Comment.php');
$showTimeline = False;
$username = "";
$posts = "";
$fullpost = "";

if (Login::isLoggedIn()) {
        $userid = Login::isLoggedIn();
        $showTimeline = True;
} else {
        die('Not logged in');
}
$username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];


if (isset($_GET['postid'])) {
        Post::likePost($_GET['postid'], $userid);
}
if (isset($_POST['comment'])) {
        Comment::createComment($_POST['commentbody'], $_GET['postid'], $userid);
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

?>

<?php
$followingposts = DB::query('SELECT posts.id, posts.title, posts.body, posts.likes, posts.post_date, users.`username` FROM users, posts, followers
WHERE posts.user_id = followers.user_id
AND users.id = posts.user_id
AND follower_id = :userid
ORDER BY posts.likes DESC;', array(':userid'=>$userid));
foreach($followingposts as $post) {
        $fullpost .= "<form action='index.php?postid=".$post['id']."' method='post'>";
        $fullpost .= "<h1 class='container' style='color:#fff;font-weight:300'>Timeline</h1>";
        $fullpost .= "<blockquote class='container w3-card-2 post'>";
        $fullpost .="<h3>".$post['title']."</h3>";
        $fullpost .="<hr/>";
        $fullpost .="<h6>".$post['body']."</h6>";
        $fullpost .=        "<br/><br/>";
        if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['id'], ':userid'=>$userid))) {
                $fullpost .=           "<button class='btn btn-default' type='submit' name='like' style='color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;'>";
                $fullpost .=           "<i class='em em-heart'></i><span>"." ".$post['likes']." Like</span>";
        } else {
                $fullpost .= "<button class='btn btn-default' type='submit' name='unlike' style='color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;'>";
                $fullpost .=           "<i class='em em-heart'></i><span>"." ".$post['likes']." Unlike</span>";
        }
        $fullpost .=        "</button>";
        $fullpost .=      "<button class='btn btn-default comment' type='button' style='color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;'>";
        $fullpost .=      "<i class='em em-speech_balloon'></i>";
        $fullpost .=          "<span style='color:#f9d616;'> Comments</span>";
        $fullpost .=    "</button>";
        $fullpost .= "<footer class='date'><sub >Posted by ".$post['username']." on"." ".$post['post_date']."</sub>";
        $fullpost .=     "</footer>";
        $fullpost .=   "</blockquote>";
        $fullpost .=  "</form>";
        /*echo "<form action='index.php?postid=".$post['id']."' method='post'>
        <textarea name='commentbody' rows='3' cols='50'></textarea>
        <input type='submit' name='comment' value='Comment'>
        </form>
        ";
        Comment::displayComments($post['id']);
        echo "
        <hr /></br />";*/
}
?>

<head>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="bootstrap/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="bootstrap/js/bootstrap.min.js"></script>

<link href='css/home.css' rel="stylesheet" type="text/css"/>

<link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>

<link href="https://afeld.github.io/emoji-css/emoji.css" rel="stylesheet">

<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

<link rel="stylesheet" href="css/Footer-Dark.css">


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
                        <li role="presentation"><a href="profile.php?username=<?php echo $username; ?>" ><?php echo $username; ?></a></li>
                        <li class="divider" role="presentation"></li>
                        <li role="presentation"><a href="index.php" class="active">Timeline </a></li>
                        <li role="presentation"><a href="my-messages.php">Messages </a></li>
                        <li role="presentation"><a href="#">Notifications </a></li>
                        <li role="presentation"><a href="my-account.php">Edit Account</a></li>
                        <li role="presentation"><a href="logout.php">Logout </a></li>
                </ul>

        </nav>
</form>



<h1><?php echo $fullpost ?></h1>
 

 <div class="footer-dark navbar-fixed-bottom">
        <footer>
            <div class="container">
                <p class="copyright">Momentum 2017 &copy;</p>
            </div>
        </footer>
    </div>



</body>
</html>
