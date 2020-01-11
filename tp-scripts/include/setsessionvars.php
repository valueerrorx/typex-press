<?php include "./include/check_login.php";
 
// zum einfachen setzen bzw. entfernen von session vars per ajax (siehe openEditor(); )


$action = $_POST['action'];
$htmlfile = $_POST['htmlfile'];


switch($action) {

	case "set":
		if ($htmlfile !=""){
			$_SESSION['htmlfile'] = "$htmlfile";
		}
	break;

	case "del":

	break;

	default:
	break;
}








?>