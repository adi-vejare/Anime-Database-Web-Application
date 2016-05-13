<html>
<head>
<title> Internet Anime Database  </title>
</head>
<style>
	body{
		background-image:url("http://wallpaperlayer.com/img/2015/9/simple-anime-background-5982-6251-hd-wallpapers.jpg");
		no-repeat center center fixed;
		-webkit-background-size: cover;
		-moz-background-size: cover;
		-o-background-size: cover;
		background-size: cover;

	}
 	h1{
                color:blue;
                text-align:center;
        }
        p{
                text-align:right;
                vertical-align:top;
        }
        header{
                width:100%;
                height: 50px;
                line-height:50px;
                margin:0;
                padding:0;
                background-color:#ff8080;
               	color:white;
        }
</style>
<body>
 <h1><a href=index.php> Internet Anime Database</a></h1>
          <header><h2><a style='text-align: left' href=index.php?s=1> Full Anime List </a> | <a href=index.php?s=3> Genres </a> <a style='float:right' href=add.php> My Account   </a></h2></header><br>

<center>
 <form method=post action=add.php>
        <table><tr> <td> Username: </td> <td> <input type=text name=postUser>  </td> </tr>
        <tr> <td> Password: </td> <td> <input type=password name=postPass>  </td> </tr>
        </table>
	<input type=submit name=submit value=Login>
        </form>
	New User? <a href=signup.php>Signup</a>
</center>
</body>
</html>
