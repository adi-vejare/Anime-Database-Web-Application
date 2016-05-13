<html>
<head>
<style>
table{
	width:100%;
	border-collapse: collapse;
}
th{
border: 1px solid black;
}
	th,td{
		padding:8px;
		border-bottom:1px solid #ddd;
	}
	tr:hover{
		background-color:#f5f5f5
	}
</style>
</head>
</html>
<?php
session_start();
session_regenerate_id();
include_once('/var/www/html/project/project-lib.php');
include_once('/var/www/html/project/header.php');
connect($db);

if(!isset($_SESSION['authenticated'])){
        authenticate($db,$postUser,$postPass);
}

checkAuth();

icheck($s);
icheck($A_id);
icheck($S_id);
icheck($G_id);
icheck($userid);
echo"<div>";
if($_SESSION['userid']==1){
	echo"<a href=add.php?s=5> Add New Anime </a> | <a href=add.php?s=8> Add studio </a> |";
}
echo"<a href=add.php?s=0> My List</a> | <a href=add.php?s=3> Add Anime to list </a> | <a href=add.php?s=4> Top Anime </a><br></div>";

switch($s){

    case 0;
                echo"<center><h2>My List</h2></center>";
		echo"<center><table>";
		echo"<th>Rank</th><th>Anime</th><th>Image</th><th>Score</th><th>Your Score</th><th>Status</th>";
                if($stmt=mysqli_prepare($db, "SELECT DISTINCT a.animeid, a.title, a.url, ar.rating, r.rating, l.status from anime a, avg_rating ar, rating r, List l where l.animeid=a.animeid and ar.animeid=a.animeid and r.animeid=a.animeid and r.userid=? order by ar.rating desc")){
                        mysqli_stmt_bind_param($stmt,"s", $_SESSION[userid]);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $A_id, $title,$url,$score,$rating,$stats);
			$count=0;
                        while(mysqli_stmt_fetch($stmt)){
                                $A_id=htmlspecialchars($A_id);
                                $title=htmlspecialchars($title);
				$url=htmlspecialchars($url);
                                $score=htmlspecialchars($score);
                                $rating=htmlspecialchars($rating);
                                $stats=htmlspecialchars($stats);
				$count++;
                                echo"<tr><td style= font-size:2.5em;>$count</td><td><a href=add.php?s=1&A_id=$A_id> $title </a></td><td><img src=$url  style=width:147px;height:208px></td><td> $score</td><td>$rating</td><td>$stats</td></tr>";
                        }
                }
                echo "</table></center>";

	break;

	case 3;
		echo"<center><h2> Select Anime to be added </h2></center>";
		echo"<left><ul style=list-style-type:circle;>";
                if($stmt=mysqli_prepare($db, "SELECT animeid, title from anime  order by title")){
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $A_id, $title);
                        while(mysqli_stmt_fetch($stmt)){
                                $A_id=htmlspecialchars($A_id);
                                $title=htmlspecialchars($title);
                                echo"<li><a href=add.php?s=1&A_id=$A_id> $title </a></li>";
                       	}
		mysqli_stmt_close($stmt);
                }
	echo"</left>";
	break;

	case 1;
                echo"<center>";
                echo"<table border: 1px solid black;><left>";
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
                                echo"<tr><h3>$title</h3> <td><img src=$url></td><td><b>Synopsis:</b><br> $description <br><br><b>Release Date:</b> $date <br><br><b> No. of Episodes:</b>$episodes <br><br><b>Status:</b>$status<br><br>";
                        }
                mysqli_stmt_close($stmt);
                }
                echo"<b> Genre:</b>";
                if($stmt=mysqli_prepare($db, "SELECT g.genreid, g.type FROM genre g, genre_type t WHERE (t.animeid=?) AND (g.genreid=t.genreid)")){
                        mysqli_stmt_bind_param($stmt, "s", $A_id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $G_id, $type);
                        while(mysqli_stmt_fetch($stmt)){
                               	$G_id=htmlspecialchars($G_id);
                               	$type=htmlspecialchars($type);
                               	echo" $type | ";
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
                               	echo" $name ";
                        }
                }
                mysqli_stmt_close;


        echo"<form action=add.php?s=2&$i&A_id=$A_id&$j method=POST>";
	 echo"<b>Add to List: </b>";
        echo"<select name=j>";
        echo"<option value=\> Select..";
        echo"<option value=Watching>Watching</option>";
        echo"<option value=Completed>Completed</option>";
        echo"<option value=On-Hold>On-Hold</option>";
        echo"<option value=Dropped>Dropped</option>";
        echo"<option value=Plan to Watch>Plan to Watch</option>";
        echo"</select>";

	echo"<br><b>Rating: </b>";
        echo"<select name=i>";
        echo"<option value=\> Select..";
        echo"<option value=10>(10) Masterpiece</option>";
        echo"<option value=9>(9) Great</option>";
	echo"<option value=8>(8) Very Good</option>";
        echo"<option value=7>(7) Good</option>";
	echo"<option value=6>(6) Fine</option>";
        echo"<option value=5>(5) Average</option>";
	echo"<option value=4>(4) Bad</option>";
        echo"<option value=3>(3) Very Bad</option>";
	echo"<option value=2>(2) Horrible</option>";
        echo"<option value=1>(1) Appaling</option>";
        echo"</select>";
        echo"<br><input type=submit value=Submit>";
        echo"</form>";

	        echo "</td></tr></left></table>";
	break;

	case 2;

		if($stmt=mysqli_prepare($db, "select exists(select 1 from rating where animeid=? and userid=?)")){
                                        mysqli_stmt_bind_param($stmt,"ss", $A_id, $_SESSION[userid]);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_bind_result($stmt, $b);
                                        while(mysqli_stmt_fetch($stmt)){
                                                $b=htmlspecialchars($b);
                                	}
                                        mysqli_stmt_close($stmt);
                                }
		if($b==0){
			if($stmt=mysqli_prepare($db, "Insert into rating set Rid='',animeid=?,userid=?,rating=?")){
                                        mysqli_stmt_bind_param($stmt,"sss", $A_id, $_SESSION[userid], $i);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_close($stmt);
                                }

		} else{
			 if($stmt=mysqli_prepare($db, "Update rating set rating=? where animeid=? AND userid=?")){
                                        mysqli_stmt_bind_param($stmt,"sss", $i, $A_id, $_SESSION[userid]);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_close($stmt);
                                }
		}

		if($stmt=mysqli_prepare($db, "select count(animeid) from rating where animeid=?")){
                	mysqli_stmt_bind_param($stmt,"s", $A_id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $c);
                        while(mysqli_stmt_fetch($stmt)){
                        	$c=htmlspecialchars($c);
                        }
                  	mysqli_stmt_close($stmt);
                 }
		 if($stmt=mysqli_prepare($db, "select sum(rating) from rating where animeid=?")){
                 	mysqli_stmt_bind_param($stmt,"s", $A_id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $sum);
                        while(mysqli_stmt_fetch($stmt)){
                        	$sum=htmlspecialchars($sum);
                        }
                        mysqli_stmt_close($stmt);
                 }
		$avg=($sum/$c);

		 if($stmt=mysqli_prepare($db, "select exists(select 1 from avg_rating where animeid=?)")){
                                       	mysqli_stmt_bind_param($stmt,"s", $A_id);
                                       	mysqli_stmt_execute($stmt);
                                        mysqli_stmt_bind_result($stmt, $d);
                                       	while(mysqli_stmt_fetch($stmt)){
                                               	$d=htmlspecialchars($d);
                                        }
                                        mysqli_stmt_close($stmt);
                                }
                if($d==0){
                        if($stmt=mysqli_prepare($db, "Insert into avg_rating set ARid='',animeid=?, rating=?")){
                                        mysqli_stmt_bind_param($stmt,"ss", $A_id, $avg);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_close($stmt);
                                }

                } else{
                         if($stmt=mysqli_prepare($db, "Update avg_rating set rating=? where animeid=?")){
                                        mysqli_stmt_bind_param($stmt,"ss", $avg, $A_id);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_close($stmt);
                               	}
               	}


		if($stmt=mysqli_prepare($db, "select exists(select 1 from List where animeid=? and userid=?)")){
                                        mysqli_stmt_bind_param($stmt,"ss", $A_id, $_SESSION[userid]);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_bind_result($stmt, $e);
                                        while(mysqli_stmt_fetch($stmt)){
                                                $e=htmlspecialchars($e);
                                        }
                                       	mysqli_stmt_close($stmt);
                                }
                if($e==0){
                        if($stmt=mysqli_prepare($db, "Insert into List set Lid='',animeid=?,userid=?,status=?")){
                                        mysqli_stmt_bind_param($stmt,"sss", $A_id, $_SESSION[userid], $j);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_close($stmt);
                                }

                } else{
                         if($stmt=mysqli_prepare($db, "Update List set status=? where animeid=? AND userid=?")){
                                       	mysqli_stmt_bind_param($stmt,"sss", $j, $A_id, $_SESSION[userid]);
                                       	mysqli_stmt_execute($stmt);
                                        mysqli_stmt_close($stmt);
                                }
                }


	 echo"<center><h2>My List</h2></center>";
                echo"<center><table>";
                echo"<th  style= font-size:2.5em;>Rank</th><th>Anime</th><th>Image</th><th>Score</th><th>Your Score</th><th>Status</th>";
                if($stmt=mysqli_prepare($db, "SELECT DISTINCT a.animeid, a.title, a.url, ar.rating, r.rating, l.status from anime a, avg_rating ar, rating r, List l where l.animeid=a.animeid and ar.animeid=a.animeid and r.animeid=a.animeid and r.userid=? order by ar.rating desc")){
                        mysqli_stmt_bind_param($stmt,"s", $_SESSION[userid]);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $A_id, $title,$url,$score,$rating,$stats);
                        $count=0;
                        while(mysqli_stmt_fetch($stmt)){
                                $A_id=htmlspecialchars($A_id);
                                $title=htmlspecialchars($title);
                                $url=htmlspecialchars($url);
                                $score=htmlspecialchars($score);
                               	$rating=htmlspecialchars($rating);
                                $stats=htmlspecialchars($stats);
                                $count++;
                                echo"<tr><td>$count</td><td><a href=add.php?s=1&A_id=$A_id> $title </a></td><td><img src=$url  style=width:147px;height:208px></td><td> $score</td><td>$rating</td><td>$stats</td></tr>";
                        }
                }
                echo "</table></center>";

	break;

	case 4;
		echo"<center><h2>Top Anime</h2></center>";
                echo"<center><table>";
                echo"<th>Rank</th><th>Anime</th><th>Image</th><th>Score</th>";
                if($stmt=mysqli_prepare($db, "SELECT DISTINCT a.animeid, a.title, a.url, ar.rating from anime a, avg_rating ar where ar.animeid=a.animeid order by ar.rating desc")){
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $A_id, $title,$url,$score);
                        $count=0;
                        while(mysqli_stmt_fetch($stmt)){
                                $A_id=htmlspecialchars($A_id);
                                $title=htmlspecialchars($title);
                                $url=htmlspecialchars($url);
                                $score=htmlspecialchars($score);
                                $count++;
                                echo"<tr><td  style= font-size:2.5em;>$count</td><td><a href=add.php?s=1&A_id=$A_id> $title </a></td><td><img src=$url  style=width:147px;height:208px></td><td> $score</td>";
                        }
                }
                echo "</table></center>";
break;

	case 5;
		if($_SESSION['userid']==1){
			echo"<form action=add.php?s=6 method=post>";
                	echo"<b>Add Anime</b><br>";
                	echo"Title <input type=text name=title><br>";
			echo"Description  <input type=text name=description><br>";
			echo"Release Date <input type=date(yyyy-mm-dd) name=release_date><br>";
			echo"Number of Episodes <input type=text name=episode><br>";
			echo"Status: ";
                	echo"Finished Airing<input type=radio name=status value=Finished>";
                	echo"Ongoing<input type=radio name=status value=Ongoing><br>";
			echo"Picture URL <input type=text name=url><br>";
                	if($stmt=mysqli_prepare($db, "SELECT S_id, name from studios order by name")){
                        	mysqli_stmt_execute($stmt);
                        	mysqli_stmt_bind_result($stmt, $S_id ,$studio);
                        	echo"<select name=S_id>";
				echo"<option value=\> Select..";
	                       	while(mysqli_stmt_fetch($stmt)){
                        	$S_id=htmlspecialchars($S_id);
                        	$studio=htmlspecialchars($studio);
                        	echo"<option value=$S_id>$studio</option>";
                        	}
                        	echo"</select><br>";
			}
                	mysqli_stmt_close($stmt);

			echo"<input type=submit value=Submit>";
                	echo"</form>";
		}
	break;

	case 6;
		if($_SESSION['userid']==1){
	        	$title=mysqli_real_escape_string($db, $title);
			$description=mysqli_real_escape_string($db,$description);
                	$release_date=mysqli_real_escape_string($db, $release_date);
                	$episodes=mysqli_real_escape_string($db, $episodes);
			$S_id=mysqli_real_escape_string($db, $S_id);
			$status=mysqli_real_escape_string($db, $status);
			$url=mysqli_real_escape_string($db, $url);
			if($stmt=mysqli_prepare($db, "INSERT INTO anime set animeid='', title=?, description=?, date=?, episodes=?, S_id=?, status=?, url=?")){
                	        mysqli_stmt_bind_param($stmt, "sssssss", $title, $description, $release_date, $episode, $S_id ,$status, $url);
                	        mysqli_stmt_execute($stmt);
                	        mysqli_stmt_close($stmt);
      			}
			if($stmt = mysqli_prepare($db, "SELECT animeid from anime where title=? order by animeid desc limit 1")){
                	        mysqli_stmt_bind_param($stmt, "s", $title);
                	        mysqli_stmt_execute($stmt);
                	        mysqli_stmt_bind_result($stmt, $A_id);

                        while(mysqli_stmt_fetch($stmt)){
                                $A_id=htmlspecialchars($A_id);
                        }
                 	mysqli_stmt_close($stmt);
                	} else {
                        	echo "Error with Query";
                	}

       			echo"<form action=add.php?s=7&A_id=$A_id&G_id=$G_id method=post>";
                	$A_id=mysqli_real_escape_string($db, $A_id);
	                if($stmt=mysqli_prepare($db, "SELECT distinct(t.genreid), g.type FROM genre g, genre_type t  WHERE t.genreid NOT IN (SELECT genreid FROM genre_type  WHERE animeid=?) AND (g.genreid=t.genreid)")){
	                        mysqli_stmt_bind_param($stmt,"s", $A_id);
	                       	mysqli_stmt_execute($stmt);
	                       	mysqli_stmt_bind_result($stmt, $G_id ,$type);
	                        echo"<select name=G_id>";
	                        while(mysqli_stmt_fetch($stmt)){
		                        $G_id=htmlspecialchars($G_id);
		                        $type=htmlspecialchars($type);
                		        echo"<option value=$G_id>$type</option>";
                        	}
                        	echo"</select>";
                        	echo"<input type=submit value=submit>";
                	}
                	echo"</form>";
        	}else{
			echo"ERROR: Funtionality available to admin only.<br>";
		}
	break;

	case 7;
                $A_id=mysqli_real_escape_string($db, $A_id);
                $G_id=mysqli_real_escape_string($db, $G_id);
                if($stmt=mysqli_prepare($db, "INSERT INTO genre_type set id='', animeid=?, genreid=?" )){
                               	mysqli_stmt_bind_param($stmt, "ss", $A_id, $G_id);
                               	mysqli_stmt_execute($stmt);
                }
                mysqli_stmt_close($stmt);

        	if($stmt=mysqli_prepare($db, "SELECT distinct(t.genreid), g.type FROM genre g, genre_type t  WHERE t.genreid NOT IN (SELECT genreid FROM genre_type  WHERE animeid=?) AND (g.genreid=t.genreid)")){
         	       mysqli_stmt_bind_param($stmt,"s", $A_id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $G_id ,$type);
			echo("<form action=add.php?s=7&G_id=$G_id&A_id=$A_id method=post>");
                        echo"<select name=G_id>";
                        while(mysqli_stmt_fetch($stmt)){
                	        $G_id=htmlspecialchars($G_id);
                        	$type=htmlspecialchars($type);
                        	echo"<option value=$G_id>$type</option>";
                        }
                        echo"</select>";

                  if(!empty($G_id)){
                       	echo"<input type=submit value=submit>";
                  }
                mysqli_stmt_close($stmt);
               	echo"</form>";
		}
               	echo"<a href=index.php?s=2&A_id=$A_id><br>Done<br></a>";
        break;

 	case 8;
		if($_SESSION['userid']==1){
                	echo"<form action=add.php?s=9 method=post>";
                	echo"<b>Add Studios</b><br>";
                	echo"Studio <input type=text name=studio><br>";
                	echo"<input type=submit value=Submit>";
                	echo"</form>";
		}
        break;

        case 9;
                $studio=mysqli_real_escape_string($db, $studio);
                if($stmt=mysqli_prepare($db, "INSERT INTO studios set S_id='', name=?")){
                        mysqli_stmt_bind_param($stmt, "s", $studio);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                }
        break;


        case 10;
                logout();
        break;


        case 11;
                if($_SESSION['userid']==1){
                        echo"<form action=add.php?s=12 method=post>";
                        echo"<b>Add New User</b><br>";
                        echo"User Name <input type=text name=postUser><br>";
                        echo"Password <input type=password name=postPass><br>";
                        echo"Email <input type=text name=postEmail><br>";
                        echo"<input type=submit value=Submit><br>";
                        echo"</form>";
                } else {
                        echo"ERROR: Functionality available to admin only<br>";
                }
        break;

	case 12;
                if($postUser!=NULL){
                        if($postPass!=NULL){
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
                                } else {
                                        echo "Error with Query<br>";
                                }
                        } else {
                        echo "ERROR:Invalid Password<br>";
                        }
                } else {
                echo "ERROR:Invalid Username<br>";
                }

        break;

        case 13;
                if($_SESSION['userid']==1){
                echo"<table><tr><th><b>User</b></th><th>Email</th></tr><br>";
               	echo"<b><u>List of Users</u></b><br>";
                if($stmt=mysqli_prepare($db, "SELECT username, email FROM users")){
                       	mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $postUser, $postEmail);
                        while(mysqli_stmt_fetch($stmt)){
                                $postUser=htmlspecialchars($postUser);
                                $postEmail=htmlspecialchars($postEmail);
                                echo"<tr><td>$postUser</td><td>$postEmail</td></tr>";
                        }
                mysqli_stmt_close($stmt);
                }
                echo "</table><br><br>";
                } else{
                echo"ERROR: Functionality available to admin only<br>";
                }
        break;

	case 14;
                if($_SESSION['userid']==1){
                echo "<form action=add.php?s=15 method=post>";
                if($stmt=mysqli_prepare($db, "SELECT userid, username FROM users")){
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $userid, $postUser);
                        echo"<b>Update Password</b><br>";
                        echo"Select User who's password is to be updated: ";
                        echo"<select name=userid>";
                        while(mysqli_stmt_fetch($stmt)){
                                $userid=htmlspecialchars($userid);
                                $title=htmlspecialchars($postUser);
                                echo"<option value=$userid >$postUser</option>";
                        }
                        echo"</select><br>";
                        echo"Enter New Password:  <input type=password name=postPass><br>";
                        echo"<input type=submit value='Submit'>";
                }
                mysqli_stmt_close($stmt);
                echo"</form>";
                } else{
                echo"ERROR: Functionality available to admin only<br>";
                }
        break;

        case 15;
                $rand=rand();
                $salt=hash('sha256',$rand);
                $ePass=hash('sha256',$postPass.$salt);
                $salt=mysqli_real_escape_string($db, $salt);
                $epass=mysqli_real_escape_string($db, $epass);
                if($stmt=mysqli_prepare($db, "UPDATE users set password=?, salt=? WHERE userid=?")){
                        mysqli_stmt_bind_param($stmt, "sss", $ePass, $salt, $userid);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                } else {
                        echo "Error with Query";
                }
        break;

	case 16;
                if($_SESSION['userid']==1){
                        echo"<table><tr><th><b>IP</b></th><th>Number of failed logins</th></tr><br>";
                        if($stmt=mysqli_prepare($db, "SELECT ip, count(*) FROM login where action='fail' group by ip")){
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_bind_result($stmt, $ip,$count);
                                while(mysqli_stmt_fetch($stmt)){
                                        $ip=htmlspecialchars($ip);
                                        echo"<tr><td>$ip</td> <td><center>$count</center></td></tr>";
                                }
                        }
                echo"</table><br>";
                mysqli_stmt_close($stmt);
                } else {
                        echo"ERROR: Functionality available to admin only<br>";
                }
        break;
}

echo"<br><form action=add.php?s=10 method=post>";
echo"<tr> <td colspan=2> <center> <input type=submit name=submit value=Logout> </center></td></tr>";
echo"</form>";

if($_SESSION['userid']==1){
        echo"<br><header><h3><a href=add.php?s=11> Add New User </a> | <a href=add.php?s=13> List of Users </a> | <a href=add.php?s=14> Update Password </a> | <a href=add.php?s=16> Failed logins </a></h3></header>";
}

?>

