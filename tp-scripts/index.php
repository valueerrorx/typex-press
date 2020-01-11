<?php 
session_start();
/*
::::::::::::::::::::::::::::: << COPYING >> ::::::::::::::::::::::::::::::::

	Copyright (C)2007 Andreas Braun && Thomas Michael Weissel

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

::::::::::::::::::::::::::::: << COPYING >> ::::::::::::::::::::::::::::::
*/

function securepw($password){		// make (at least) passwords uncrackable  (passwords must be created with securepw() function)
	$salt = md5("1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");   
	$hash = $password . $salt;
	for ( $i = 0; $i < 10000; $i++ ) {
		$hash = md5( $hash );
	}
	return $hash;
}
$loginmessage = "";

if (isset($_POST['phrase_']) && isset($_POST['username_']))	//falls _phrase und _username existieren = uebermittelt wurden...
{
	include_once ('./include/config.php');	//...inkludiere die Konfigurationsdatei, in der die Einstellungen gespeichert sind
	include_once ('./include/users.php');
	$userarray = json_decode($userarray,true);  // since usermanagement console saves userarray json_encoded
	$usercount = count($userarray);

	for($x=0;$x<=$usercount;$x++){
		@$username = $userarray[$x][username];
		@$password = $userarray[$x][password];
		$basedir = $username;			// since its much easier basedir is set to username from the beginning!!!
		$absolutebasedir = "$userspace/$basedir";  



		//admin acountname is hardcoded to "admin" !
		if (($_POST['username_'] == 'admin')&&(securepw($_POST['phrase_']) == $password)){
			$_SESSION['key'] = md5(time()*time());
			$code = $_SESSION['key'];
			$bis = time() + 1600;
			setcookie ("d0rn_1!", $code, $bis,"/");
			$_SESSION['basedir'] = "../";		//admin hat vollzugriff auf das verzeichnis um gesamte webseite zu verwalten (tp-scripts ist restricted in config.php)
			$_SESSION['username'] = "$username";

			echo "	<script language='javascript' type='text/javascript'>
				self.location.href='./tp-umanager/index.php';
				</script>";

		}
		elseif (($_POST['username_'] == $username)&&(securepw($_POST['phrase_']) == $password)&&($username != '')){
				
			$_SESSION['key'] = md5(time()*time());
			$_SESSION['basedir'] = "$absolutebasedir";	// das user- basedir ist grundvorraussetzung
			$_SESSION['username'] = "$username";

			$code = $_SESSION['key'];  	//um session key ins cookie zu schreiben fuer spaeteren vergleich
			$bis = time() + 3600;		//gültigkeit = 1 Stunde

			setcookie ("d0rn_1!", $code, $bis,"/");
			
			if (!preg_match("/[^a-z0-9\.\)\(\-\_]/i","$basedir")){
				if(!is_dir("$absolutebasedir")) {
					mkdir ($absolutebasedir, 0777);
				}
			}

			$getin = $_POST['getin'];
			// oeffnet edtior window und ruft sich selbst auf ohne $_POST vars die zu verwirrung fuehren.
			if ($getin == 'wide'){
				echo "	<script language='javascript' type='text/javascript'>
				self.location.href='./tp-filebrowser/filebrowser.php';
				</script>";
			}else{
			echo "	<script language='javascript' type='text/javascript'>
				self.location.href='./tp-filebrowser/filebrowser.php';
				</script>";
			}
		}
		else {
			$loginmessage= "<br>Wrong Username or Password.<br> Please try again!<br><br>";
		}
	}

}
?>


<html>
<head>
<title>typeX-press | login</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="./include/style.css">
<script language="javascript" type="text/javascript" src="./include/jquery/jquery-1.4.4.min.js"></script>
<script language="javascript" type="text/javascript" src="./include/jquery/jquery-ui-1.8.9.custom.min.js"></script>
<link rel="stylesheet" href="./include/jquery/jquery-ui-1.8.9.custom.css">


<script type="text/javascript">


$(document).ready(function() {
	$("#lbut").hover(
	function () {
		$(this).toggleClass("xhover");
	}
	);
});
    


</script>



</head>


<body id=setup onload=document.login.stop.focus();>
<div id="wrap">	
	<div id="page-header">
		<h1>typeX-press | login</h1>
	</div>
<div id="page-body">
		<div id="tabs">
			<ul>
				<li id="activetab"><a ><span>Login</span></a></li>
			</ul>
		</div>	
		<div id="acp">	
		<div class="panel">
	
				<div id="content">	
					<div id="menu"> 
						<div class="logindiv">
						<form id=login method="post" action="index.php" name="login">
						<div id=inputs>
							<input title="enter your username" id='stop' class="login" name="username_" size="18" type="text" onclick="this.value='';this.style.color='#000'" value="username"><br>
							<input title="enter your password" class="login2" name="phrase_" size="18" type="password" onclick="this.value='';this.style.color='#000'" value="username"><br>
							<input type=hidden id=getin name=getin value="">
							<input type=submit class=loginbutton value=login id=lbut title=login>

							


						</div>


						</form>
						</div>
					</div>
					<div id="ajaxcontent"> 	
						<div id="loginmessage">
							<? echo $loginmessage; ?>
						</div>
					</div>
				</div>
			
		</div>
		</div>
	</div>
</div>


</body>