<?php
session_start();



$haystack = $_SERVER['REQUEST_URI'];
$needle1 = "tp-uploader";
$needle2 = "tp-filebrowser";
$needle3 = "tp-umanager";

$pos1 = strpos($haystack,$needle1);
$pos2 = strpos($haystack,$needle2);
$pos3 = strpos($haystack,$needle3);



$thisname = "../index.php";

// wenn fuer upload ein neues fenster aufgemacht wird ist der session key ein anderer als der im cookie gespeicherte 
// somit wird man beim upload durch den vergleich der keys immer ausgeloggt

//echo $_SESSION['key'];
// echo " " ;
//echo $_COOKIE['d0rn_1!'];

if (($_COOKIE['d0rn_1!'] != $_SESSION['key']) || !isset($_SESSION['key']))
{

	if(($pos1 !== false)||($pos2 !== false)) {  //falls im filebrowser oder uploader
		//echo "<script type=\"text/javascript\">top.window.close();</script>";
		echo "<script type=\"text/javascript\">location.href='$thisname';</script>";
		die ("<br> You have been logged out!");
	}else{
		echo "<script type=\"text/javascript\">location.href='$thisname';</script>";
		die ("<br> You have been logged out!");
	}
}
else {
$cvalue = $_COOKIE['d0rn_1!'];
$to = time()+1800;
setcookie ("d0rn_1!", $cvalue, $to,"/");
}

/*
// prevent admin to go other sites than tp-umanager (to much logic to much risk)
$adminuser = $_SESSION['username'];
if(($pos3 == false)&&($adminuser == 'admin')) {
	$cvalue = $_COOKIE['d0rn_1!'];
	$to = time()-31800;
	setcookie ("d0rn_1!", "$cvalue",$to,"/"); 
	echo "<script type=\"text/javascript\">location.href='$thisname';</script>";
}
*/


// logout action
@$status = $_GET['status'];
if ($status != "" ){ 
	$cvalue = $_COOKIE['d0rn_1!'];
	$to = time()-31800;
	setcookie ("d0rn_1!", "$cvalue",$to,"/"); 
	echo "<script type=\"text/javascript\">location.href='$thisname';</script>";
}





?>