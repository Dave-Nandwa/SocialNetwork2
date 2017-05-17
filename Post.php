<?php
class Post {
        public static function createPost($postTitle, $postbody, $loggedInUserId, $profileUserId) {
                if (strlen($postbody) > 160 || strlen($postbody) < 1) {
                        die('Incorrect length!');
                }
                $topics = self::getTopics($postbody);
                if ($loggedInUserId == $profileUserId) {
                        if (count(Notify::createNotify($postbody)) != 0) {
                                foreach (Notify::createNotify($postbody) as $key => $n) {
                                                $s = $loggedInUserId;
                                                $r = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$key))[0]['id'];
                                                if ($r != 0) {
                                                        DB::query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra)', array(':type'=>$n["type"], ':receiver'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));
                                                }
                                        }
                                }
                        DB::query('INSERT INTO posts VALUES (\'\', :posttitle, :postbody, NOW(), :userid, 0, \'\', :topics, NOW() )', array(':postbody'=>$postbody, ':posttitle'=>$postTitle,  ':userid'=>$profileUserId, ':topics'=>$topics));
                } else {
                        die('Incorrect user!');
                }
        }
        public static function createImgPost($postbody, $loggedInUserId, $profileUserId) {
                if (strlen($postbody) > 160) {
                        die('Incorrect length!');
                }
                $topics = self::getTopics($postbody);
                if ($loggedInUserId == $profileUserId) {
                        if (count(Notify::createNotify($postbody)) != 0) {
                                foreach (Notify::createNotify($postbody) as $key => $n) {
                                                $s = $loggedInUserId;
                                                $r = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$key))[0]['id'];
                                                if ($r != 0) {
                                                        DB::query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra)', array(':type'=>$n["type"], ':receiver'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));
                                                }
                                        }
                                }
                        DB::query('INSERT INTO posts VALUES (\'\', \'\', :postbody, NOW(), :userid, 0, \'\', :topics, NOW()  )', array(':postbody'=>$postbody, ':userid'=>$profileUserId, ':topics'=>$topics));
                        $postid = DB::query('SELECT id FROM posts WHERE user_id=:userid ORDER BY ID DESC LIMIT 1;', array(':userid'=>$loggedInUserId))[0]['id'];
                        return $postid;
                } else {
                        die('Incorrect user!');
                }
        }

        public static function createLikeNotify($text = "", $postid = 0) {
                $text = explode(" ", $text);
                if (count($text) == 1 && $postid != 0) {
                        $temp = DB::query('SELECT posts.user_id AS receiver, post_likes.user_id AS sender FROM posts, post_likes WHERE posts.id = post_likes.post_id AND posts.id=:postid', array(':postid'=>$postid));
                        $r = $temp[0]["receiver"];
                        $s = $temp[0]["sender"];
                        DB::query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra)', array(':type'=>2, ':receiver'=>$r, ':sender'=>$s, ':extra'=>""));
                }
        }


         public static function likePost($postId, $likerId) {
                if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {
                        DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postId));
                        DB::query('INSERT INTO post_likes VALUES (\'\', :postid, :userid)', array(':postid'=>$postId, ':userid'=>$likerId));
                       self::createLikeNotify("", $postId);
                } else {
                        DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postId));
                        DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId));
                }
        }

        public static function getTopics($text) {
                $text = explode(" ", $text);
                $topics = "";
                foreach ($text as $word) {
                        if (substr($word, 0, 1) == "#") {
                                $topics .= substr($word, 1).",";
                        }
                }
                return $topics;
        }
        public static function link_add($text) {
                $text = explode(" ", $text);
                $newstring = "";
                foreach ($text as $word) {
                        if (substr($word, 0, 1) == "@") {
                                $newstring .= "<a href='profile.php?username=".substr($word, 1)."'>".htmlspecialchars($word)."</a> ";
                        } else if (substr($word, 0, 1) == "#") {
                                $newstring .= "<a href='topics.php?topic=".substr($word, 1)."'>".htmlspecialchars($word)."</a> ";
                        } else {
                                $newstring .= htmlspecialchars($word)." ";
                        }
                }
                return $newstring;
        }
        public static function displayPosts($userid, $username, $loggedInUserId) {
                $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
                $posts = "";
                foreach($dbposts as $p) {
                        if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$loggedInUserId))) {
                                $posts.= "<blockquote class='container w3-card-2 post'>
                                <h3>".$p['title']."</h3>
                                <hr/>
                                <h6>"."<img src='".$p['postimg']."'>".self::link_add($p['body'])."</h6>
                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                
                                <button class='btn btn-default' type='submit' name='like' style='color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;'>
                                <i class='em em-heart'></i><span>"." ".$p['likes']." Like</span>
                                </button>
                               ";
                                if ($userid == $loggedInUserId) {
                                        $posts .= "<input type='submit' name='deletepost' value='x' />";
                                }
                                $posts .= "
                                <footer class='date'><sub>Posted on"." ".$p['post_date']."</sub><br/>
                                </form></footer></blockquote>
                                 </br />
                                ";
                        } else {
                                 $posts.= "<blockquote class='container w3-card-2 post'>
                                <h3>".$p['title']."</h3>
                                <hr/>
                                <h6>"."<img src='".$p['postimg']."'>".self::link_add($p['body'])."</h6>
                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                <button class='btn btn-default' type='submit' name='unlike' style='color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;'>
                                <i class='em em-heart'></i><span>"." ".$p['likes']." Like</span>
                                </button>
                               ";
                                if ($userid == $loggedInUserId) {
                                        $posts .= "<input type='submit' name='deletepost' value='x' />";
                                }
                                $posts .= "
                                <footer class='date'><sub>Posted on"." ".$p['post_date']."</sub><br/>
                                </form></footer></blockquote>
                                 </br />
                                ";
                        }
                }
                return $posts;
        }
}
?>