<?php
isset($_REQUEST['s']) ? $s = strip_tags($_REQUEST['s']) : $s="";
isset($_REQUEST['S_id']) ? $S_id = strip_tags($_REQUEST['S_id']) : $S_id="";
isset($_REQUEST['G_id']) ? $G_id = strip_tags($_REQUEST['G_id']) : $G_id="";
isset($_REQUEST['A_id']) ? $A_id = strip_tags($_REQUEST['A_id']) : $A_id="";
isset($_REQUEST['A']) ? $A = strip_tags($_REQUEST['A']) : $A="";
isset($_REQUEST['userid']) ? $userid = strip_tags($_REQUEST['userid']) : $userid="";
isset($_REQUEST['studio']) ? $studio = strip_tags($_REQUEST['studio']) : $studio="";
isset($_REQUEST['episode']) ? $episode = strip_tags($_REQUEST['episode']) : $episode="";
isset($_REQUEST['release_date']) ? $release_date = strip_tags($_REQUEST['release_date']) : $release_date="";
isset($_REQUEST['title']) ? $title = strip_tags($_REQUEST['title']) : $title="";
isset($_REQUEST['type']) ? $type = strip_tags($_REQUEST['type']) : $type="";
isset($_REQUEST['status']) ? $status = strip_tags($_REQUEST['status']) : $status="";
isset($_REQUEST['stats']) ? $stats = strip_tags($_REQUEST['stats']) : $stats="";
isset($_REQUEST['description']) ? $description = strip_tags($_REQUEST['description']) : $description="";
isset($_REQUEST['url']) ? $url = strip_tags($_REQUEST['url']) : $url="";
isset($_REQUEST['postUser']) ? $postUser = strip_tags($_REQUEST['postUser']) : $postUser="";
isset($_REQUEST['postPass']) ? $postPass = strip_tags($_REQUEST['postPass']) : $postPass="";
isset($_REQUEST['postEmail']) ? $postEmail = strip_tags($_REQUEST['postEmail']) : $postEmail="";
isset($_REQUEST['i']) ? $i = strip_tags($_REQUEST['i']) : $i="";
isset($_REQUEST['j']) ? $j = strip_tags($_REQUEST['j']) : $j="";
isset($_REQUEST['a_z']) ? $a_z = strip_tags($_REQUEST['a_z']) : $a_z="";

function connect(&$db){
        $mycnf = "/etc/project-mysql.conf";
        if (!file_exists($mycnf)) {
                exit("Error file not found: $mycnf");
        }
        $mysql_ini_array = parse_ini_file($mycnf);
        $db_host = $mysql_ini_array["host"];
        $db_user = $mysql_ini_array["user"];
        $db_pass = $mysql_ini_array["pass"];
        $db_port = $mysql_ini_array["port"];
        $db_name = $mysql_ini_array["dbName"];
        $db = mysqli_connect($db_host, $db_user,$db_pass, $db_name, $db_port);
        if(!$db){
                print "Error connecting to DB:" . mysqli_connect_error();
                exit;
        }

}

function icheck($i) {
        if($i != null){
                if(!is_numeric($i)){
                print "<b> Error: </b> Invalid Syntax. ";
                exit;
                }
        }
}

function authenticate($db, $postUser, $postPass){
        $_SESSION['ip']=$_SERVER['REMOTE_ADDR'];
	$_SESSION['HTTP_USER_AGENT']=md5($SERVER['SERVER_ADDR'].$_SERVER['HTTP_USER_AGENT']);
	$_SESSION['created']=time();
        $whitelist=array("198.18.2.102","198.18.2.54");
        if(in_array($_SESSION['ip'],$whitelist)){
                $count=0;
        }
        else{
                $query="select ip from login where (DATE_SUB(NOW(), interval 60 minute)<date) AND (action='fail')";
                $count=0;
                if($stmt = mysqli_prepare($db, $query)){
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $ipn);
                        while(mysqli_stmt_fetch($stmt)){
                                $ipn=$ipn;
                        if($ipn==$_SESSION['ip'])
                        {
                                $count++;
                        }
                }
                mysqli_stmt_close($stmt);
                }
        }
        if($count<5){
                $query="select userid, email, password, salt from users where username=?";
                if($stmt = mysqli_prepare($db, $query)){
                        mysqli_stmt_bind_param($stmt,"s",$postUser);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $userid, $email, $password, $salt);
                        while(mysqli_stmt_fetch($stmt)){
                                $userid=$userid;
                                $password=$password;
                                $salt=$salt;
                                $email=$email;
                        }
                        mysqli_stmt_close($stmt);
                        $epass=hash('sha256',$postPass.$salt);
                        if($epass==$password){
				session_regenerate_id();
                                $_SESSION['userid']=$userid;
                                $_SESSION['email']=$email;
                                $_SESSION['authenticated']="yes";
                                $ip=mysqli_real_escape_string($db, $_SESSION['ip']);
                                $postUser=mysqli_real_escape_string($db, $postUser);
                                if($stmt=mysqli_prepare($db, "INSERT INTO login set loginid='', ip=?, users=?, action='pass'")){
                                        mysqli_stmt_bind_param($stmt, "ss", $ip, $postUser);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_close($stmt);
                                } else{
                                        echo"ERROR in query";
                                }
                        } else {
                                $ip=mysqli_real_escape_string($db, $_SESSION['ip']);
                                $postUser=mysqli_real_escape_string($db, $postUser);
                                if($stmt=mysqli_prepare($db, "INSERT INTO login set loginid='', ip=?, users=?, action='fail'")){
                                        mysqli_stmt_bind_param($stmt, "ss", $ip, $postUser);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_close($stmt);
                                } else{
                                echo"ERROR in query";
                                }

                                echo "Failed to Login";
				//error_log("**ERROR**: Tolkien App has failed login from" . $_SERVER['REMOTE_ADDR'],0);
                                header("Location: /project/login.php");
                                exit;
                        }
                }
        } else {
                echo"TOO MANY FAILED LOGIN ATTEMPTS. ACCESS BLOCKED";
                exit;
        }
}

function logout(){
                session_destroy();
                header('Location: login.php');
                exit;
}


function checkAuth(){
	if(isset($_SESSION['HTTP_USER_AGENT'])){
		if ($_SESSION['HTTP_USER_AGENT']!= md5($SERVER['SERVER_ADDR'].$_SERVER['HTTP_USER_AGENT'])){
			logout();
		}
	} else {
		logout();
	}

	if (isset($_SESSION['created'])){
		if( time() - $_SESSION['created'] > 1800 ) {
			logout();
		}
	} else {
		logout();
	}

	if(isset($_SESSION['ip'])){
		if($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']){
			logout();
		}
	} else{
		logout();
	}


}



?>

