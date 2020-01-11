<?php
include "../include/check_login.php";
##::::::::::::::::::::::::::::: << COPYING >> ::::::::::::::::::::::::::::::::##
#
#	Copyright (C)2007 Thomas Michael Weissel &&  Andreas Braun
#
#	This program is free software: you can redistribute it and/or modify
#	it under the terms of the GNU General Public License as published by
#	the Free Software Foundation, either version 3 of the License, or
#	(at your option) any later version.
#
#	This program is distributed in the hope that it will be useful,
#	but WITHOUT ANY WARRANTY; without even the implied warranty of
#	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#	GNU General Public License for more details.
#
#	You should have received a copy of the GNU General Public License
#	along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
##::::::::::::::::::::::::::::: << COPYING >> ::::::::::::::::::::::::::::::::##

include ('../include/config.php');

$typ = $_POST['typ'];
$ordner = $_POST['ordner'];
$newitem = $_POST['newitem'];
// $inhalt = $_POST['inhalt'];   //(für "save as")?? // wird für "delete" missbraucht um yes or no zu transportieren


$prefix = $ordner;
$basedir = $_SESSION['basedir'];
$prefix=str_replace("../$basedir","/home",$prefix);


$thisname = strrchr($newitem,"/");
$thisname = preg_replace("/\//", "", $thisname);






switch($typ)
{
	case "datei": case "verzeichnis": 
	#form create file oder folder
	echo "<center><br>

	<form method='POST' action=javascript:functions_doitRequest('$ordner','$typ','','','$ordner'); name='filefolder' enctype='multipart/form-data'>"; #$ordner wird hier als $wert übergeben für den folderlist reload
		if ($typ == "datei") echo "New Filename: ";
		if ($typ == "verzeichnis") echo "New Foldername:";

	// der wert des inputfeldes "name" mit der id=foobar wird im entsprechenden ajax skript per get elementbyid.value geholt
	echo "<br>
	<b>$prefix/</b><br>
	<input maxlength=20 name=name type=text id=foobar value=''>";

	if ($typ == "datei") {
		echo "<select name='extension' id=foo>";
		$editablecount = count($editable);
		for ($i=0; $i<$editablecount; $i++){
			$thiseditable = $editable[$i];
			echo "<option value='$thiseditable'>$thiseditable &nbsp; </option>";
		}
		echo "</select> ";
	}
	echo "	<input type=submit value=save class=button><br>";
	echo "allowed characters: [a-z 0-9 . - _ ]<br><br>";
	

	echo "
	</form>
	</center>";
	break;





	case "delete":
	echo "<center><br>
	<form method='POST' action='' enctype='multipart/form-data'>";
	if (is_dir($newitem)) 
	{
	echo "CAUTION!!<br>";
	echo "Do you really want to delete the Folder: <br> <b>  $thisname/ </b> <br> with all its contents?<br>";
	}
	else 
	{
	echo "Delete File <br><b> '$thisname' </b> ?<br>";
	}
	echo "<br>
	<input name=name id=foobar class=button type=button value=yes onclick=functions_doitRequest('$ordner','$typ','yes','$newitem','$ordner');>
	<input name=name class=button type=button value=no onclick=functions_doitRequest('$ordner','$typ','no','$newitem','$ordner');>
	</form>
	</center>";
	break;





	case "rename":
	echo "<center><br>
	<form method='POST' action=javascript:functions_doitRequest('$ordner','$typ','','$newitem','$ordner'); name=fname enctype='multipart/form-data'>";
	echo "rename: <b>\"$thisname\"</b><br><br>";
	if(is_dir($newitem)) 
	{
		echo "New Foldername: <br>
		<input maxlength=20 name=name type=text id=foobar>
		";
	}
	else
	{
	$type = strrchr($newitem, ".");	
		echo "New Filename:<br>
		<input maxlength=20 name=name type=text id=foobar>$type
		";	
	}
	echo "<br>
	<input type=submit class=button value=rename>
	</form></center>";
	break;




	case "copy":   //wird vom dragndrop script aufgerufen (mouseUp)
	echo "<center><br><br>
	<form method='POST' action='' enctype='multipart/form-data'>";
	echo "Copy File <br><b> '$thisname' </b><br> to<br> <b>'$prefix'</b>?<br>";

	$reloadfolder = dirname($newitem);  //$ordner ist in diesem fall der zielordner - es soll aber der ausgangsordner sichtbar bleiben
	echo "<br>
	<input name=name id=foobar class=button type=button value=copy onclick=functions_doitRequest('$ordner','copy','','$newitem','$reloadfolder');>
	<input name=name id=foobar class=button type=button value=move onclick=functions_doitRequest('$ordner','move','','$newitem','$reloadfolder');>
	<input name=name class=button type=button value=cancel onclick=functions_doitRequest('$ordner','cancel','','$newitem','$reloadfolder');>
	</form>
	</center>";
	break;
}

?>
