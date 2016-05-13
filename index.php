<html>
<head>
<style>

li:hover{
color:red;
}
img:hover{
border: 3px solid white;
}
</style>
</head>
</html>
<?php
//Name:    project/index.php
//Purpose: Internet Anime Database
//Author:  Aditi Vejare adve1519@colorado.edu
//Version: 1.0
//Date:    2015/04/19
include_once('/var/www/html/project/project-lib.php');
include_once('/var/www/html/project/header.php');
connect($db);
icheck($s);
icheck($A_id);
icheck($G_id);
icheck($S_id);

switch($s){
	case 0;
		echo"<center>";
                echo "<h3>Popular Anime</h3>";
		echo"<ul style= list-style:none;>";
                if($stmt=mysqli_prepare($db, "SELECT a.animeid, a.title, a.url from anime a, avg_rating ar where ar.animeid=a.animeid order by ar.rating desc limit 10")){
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $A_id, $title,$url);
                        while(mysqli_stmt_fetch($stmt)){
                                $A_id=htmlspecialchars($A_id);
                                $title=htmlspecialchars($title);
                                $url=htmlspecialchars($url);
                                //echo"<li><tr>$count</tr><tr><a href=index.php?s=2&A_id=$A_id> $title </a></tr><tr><img src=$url style=width:147px;height:208px></tr></li>";
				echo"<li style=display:inline;><a href=index.php?s=2&A_id=$A_id><img src=$url title='$title' style=width:147px;height:208px></a></li>";
                        }
                }
                echo "</ul>";
                echo"</center>";

	break;

	case 1;
               	echo"<center>";
                echo "<h3>Full Anime List</h3></center>";

		echo"<left><ul style=list-style-type:circle;>";
                if($stmt=mysqli_prepare($db, "SELECT animeid, title, url from anime order by title")){
                        mysqli_stmt_bind_param($stmt, "s", $G_id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $A_id, $title,$url);
			mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $A_id, $title,$url);
                        while(mysqli_stmt_fetch($stmt)){
				$A_id=htmlspecialchars($A_id);
                                $title=htmlspecialchars($title);
				$url=htmlspecialchars($url);
 	 			echo"<li><a href=index.php?s=2&A_id=$A_id> $title </a></li>";
			}
                }
                echo "</ul>";
                echo"</left>";
        break;

	case 2;
                echo"<center>";
                echo"<table border: 1px solid black;>";
                $A_id=mysqli_real_escape_string($db, $A_id);
                if($stmt=mysqli_prepare($db, "SELECT title, description, date, episodes, status, url FROM anime WHERE animeid = ?")){
                        mysqli_stmt_bind_param($stmt, "s", $A_id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $title, $description, $date, $episodes, $status, $url);
                        while(mysqli_stmt_fetch($stmt)){
                                $title=htmlspecialchars($title);
				$description=htmlspecialchars($description);
				$date=htmlspecialchars($date);
				$episodes=htmlspecialchars($episodes);
				$status=htmlspecialchars($status);
				$url=htmlspecialchars($url);
                                echo"<tr><h3>$title</h3> <td><img src=$url></td><td><b>Synopsis:</b><br> $description <style=tect-align:justify;><br><br><b>Release Date:</b> $date <br><br><b> No. of Episodes:</b> $episodes <br><br><b> Status:</b> $status <br><br>";
                        }
                mysqli_stmt_close($stmt);
                }
		echo"<b>Genre:</b>";
		if($stmt=mysqli_prepare($db, "SELECT g.genreid, g.type FROM genre g, genre_type t WHERE (t.animeid=?) AND (g.genreid=t.genreid)")){
			mysqli_stmt_bind_param($stmt, "s", $A_id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $G_id, $type);
			while(mysqli_stmt_fetch($stmt)){
				$G_id=htmlspecialchars($G_id);
				$type=htmlspecialchars($type);
				echo"<a href=index.php?s=4&G_id=$G_id> $type </a> | ";
			}
		}
		mysqli_stmt_close;

		echo"<br><br><b>Studio:</b>";
                if($stmt=mysqli_prepare($db, "SELECT s.S_id, s.name FROM studios s, anime a WHERE (a.animeid=?) AND (s.S_id=a.S_id)")){
                        mysqli_stmt_bind_param($stmt, "s", $A_id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $S_id, $name);
                        while(mysqli_stmt_fetch($stmt)){
                                $S_id=htmlspecialchars($S_id);
                                $name=htmlspecialchars($name);
                                echo"<a href=index.php?s=6&S_id=$S_id> $name  </a>";
                        }
                }
               	mysqli_stmt_close;

		echo "</td></tr></table>";
                echo"</center>";
        break;


	case 3;
		echo"<center>";
                echo"<h3>Genres</h3></center>";
		echo"<left><ul style= list-style-type:square;>";
                if($stmt=mysqli_prepare($db, "SELECT genreid, type FROM genre order by type" )){
                       	mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $G_id, $type);
                        while(mysqli_stmt_fetch($stmt)){
				$G_id=htmlspecialchars($G_id);
                                $type=htmlspecialchars($type);
                                echo"<li><a href=index.php?G_id=$G_id&s=4> $type</a> </li>";
                        }
                mysqli_stmt_close($stmt);
                }
                echo "</ul>";
                echo"</left>";
	break;

	case 4;
		$G_id=mysqli_real_escape_string($db, $G_id);
		if($stmt=mysqli_prepare($db, "SELECT type FROM genre WHERE genreid=?")){
			mysqli_stmt_bind_param($stmt,"s",$G_id);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $type);
		 while(mysqli_stmt_fetch($stmt)){
		$type=htmlspecialchars($type);
		}
		mysqli_stmt_close($stmt);
		}
		echo"<center><h3>$type</h3></center>";
		echo"<left><table>";
		if($stmt=mysqli_prepare($db, "SELECT a.animeid, a.title,a.url from anime a, genre_type t WHERE (t.genreid=?) AND (a.animeid=t.animeid) order by a.title")){
			mysqli_stmt_bind_param($stmt, "s", $G_id);
			mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $A_id, $title,$url);
                        while(mysqli_stmt_fetch($stmt)){
				$A_id=htmlspecialchars($A_id);
                                $title=htmlspecialchars($title);
				$url=htmlspecialchars($url);
                                echo"<tr><td><a href=index.php?s=2&A_id=$A_id>$title</a></td><td><img src=$url style=width:147px;height:208px;></td></tr>";
                        }
                mysqli_stmt_close($stmt);
		}
		echo"</table></left>";
	break;

	case 5;
		if($postUser!=NULL){
			if($postPass!=NULL){
				if($stmt=mysqli_prepare($db, "SELECT EXISTS(SELECT 1 FROM users WHERE username=?)")){
					mysqli_stmt_bind_param($stmt,"s", $postUser);
					mysqli_stmt_execute($stmt);
                        		mysqli_stmt_bind_result($stmt, $a);
                        		while(mysqli_stmt_fetch($stmt)){
                                		$a=htmlspecialchars($a);
              			        }
                			mysqli_stmt_close($stmt);
                		}

				if($a==0){
               				$rand=rand();
                        		$salt=hash('sha256',$rand);
                        		$ePass=hash('sha256',$postPass.$salt);
                        		$postUser=mysqli_real_escape_string($db, $postUser);
                        		$ePass=mysqli_real_escape_string($db, $ePass);
                        		$salt=mysqli_real_escape_string($db, $salt);
                        		$postEmail=mysqli_real_escape_string($db, $postEmail);
                        		if($stmt=mysqli_prepare($db, "INSERT INTO users set userid='', username=?, password=?, salt=?, email=?")){
                        			mysqli_stmt_bind_param($stmt, "ssss", $postUser, $ePass, $salt, $postEmail);
                        		        mysqli_stmt_execute($stmt);
                        		        mysqli_stmt_close($stmt);
						echo"Account Created.<a href=login.php> Login</a>";
                        		} else {
                        		        echo "Error with Query<br>";
                        		}
		 		} else{
					echo "ERROR: Username is already used. Use unique user name.<br>";
					echo"<a href=signup.php>Signup</a>";
				}
			} else {
     	            	      	echo "ERROR:Invalid Password<br>";
        		}
		} else {
               		echo "ERROR:Invalid Username<br>";
		}
	break;

	case 6;

		$S_id=mysqli_real_escape_string($db, $S_id);
		 if($stmt=mysqli_prepare($db, "SELECT name FROM studios WHERE S_id=?")){
                        mysqli_stmt_bind_param($stmt,"s",$S_id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $name);
                 while(mysqli_stmt_fetch($stmt)){
                $name=htmlspecialchars($name);
                }
                mysqli_stmt_close($stmt);
                }
                echo"<center><h3>Studio: $name</h3></center>";

		echo"<table><left>";
                if($stmt=mysqli_prepare($db, "SELECT a.animeid, a.title, a.url from anime a, studios s WHERE (s.S_id=?) AND (a.S_id=s.S_id) order by a.title")){
                       	mysqli_stmt_bind_param($stmt, "s", $S_id);
                       	mysqli_stmt_execute($stmt);
                       	mysqli_stmt_bind_result($stmt, $A_id, $title,$url);
                       	while(mysqli_stmt_fetch($stmt)){
                               	$A_id=htmlspecialchars($A_id);
                                $title=htmlspecialchars($title);
				$url=htmlspecialchars($url);
                                //echo"<a href=index.php?s=2&A_id=$A_id> $title</a><br>";
				echo"<tr><td><a href=index.php?s=2&A_id=$A_id>$title</a></td><td><img src=$url style=width:147px;height:208px;></td></tr>";
                        }
                mysqli_stmt_close($stmt);
                }
                echo"</table></left>";

	break;
}
?>


