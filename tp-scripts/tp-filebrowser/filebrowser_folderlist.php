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
include ('../include/users.php');




$basedir = $_SESSION['basedir'];
if ((!isset($basedir))|| ($basedir == " ") || (strpos($basedir,'..'))     ) {$stop = "stop";}
else {
	$basedir = "../".$basedir;
	$folder_tmp = $_POST['folder_tmp']; 
	$folderbase = $basedir;
}  








 ////////////////////////////////////////////////////////////////////////
//////////////// MAKE and SORT simple directory array  /////////////////

function makedirarray($folderbase,&$folderarray){
	global $restricted;
	$handle = opendir($folderbase);
	$x=0;
	while ($subitem = readdir($handle)){
		$newitem = "$folderbase/$subitem";	
		if (($subitem != ".") && ($subitem != "..") && (!in_array($subitem,$restricted)) && (is_dir($newitem)))  //testet ob das neue item verboten ist
		{
				@$folderarray[$folderbase][$x][path] = "$newitem";		//fill array()
				@$folderarray[$folderbase][$x][name] = "$subitem";
				$x++;
				makedirarray($newitem,$folderarray);  // ruf dich selbst auf um alle subfolder ebenfalls zu archivieren und zu ordnen
		}
	}
	closedir($handle);
	if (@is_Array($folderarray[$folderbase])){
		$sortArray = array();		//erstellt ein neues array (zum ordnen) aus der zweiten dimension des folderarray
		foreach($folderarray[$folderbase] as $key => $array) {
			$sortArray[$key] = $array['name'];
		}
		array_multisort($sortArray, SORT_DESC, SORT_STRING, $folderarray[$folderbase]); 
	}
}
makedirarray($folderbase,$folderarray);		//erzeugt ein mehrdimensionales array in dem die gesamte verzeichnisstruktur abgebildet ist

 //////////////// MAKE and SORT simple directory array  //////////
/////////////////////////////////////////////////////////////////


function listdirs($basedir){
	global $restricted,$image_path,$folder_tmp,$folderarray,$stop;
	@$foldercount = count($folderarray[$basedir])-1;    //fangt bei 1 an..  == untypisch.. 0 ist der erste wert
	for($x = $foldercount; $x>=0; $x--)
	{
		@$newitem = $folderarray[$basedir][$x][path];
		@$subitem = $folderarray[$basedir][$x][name];
		$newfolder = "$basedir/$subitem";
		if(strstr($folder_tmp,$newfolder)) {    //macht den pfad zum aktuellen folder (folder_tmp) sichtbar
			$visible = "table";
		} else {
			$visible = "none";
		}// achtung: classname: folderlistfolder wird fuers sammeln im dragndrop script gebraucht
			echo "	<div class=folder>
				<a href='javascript:setRequest(\"$newfolder\")' onclick='showsub(\"$newfolder\")' class='folderlistfolder' name='droptarget' dragId='$newfolder'>  
				<IMG src='./images/folderclose.png' width=16 height=16 border=0 align=top>&nbsp;$subitem</a>
				<table id='$newfolder' class=transparenttable style=\"display:$visible\" cellspacing=0 cellpadding=0>
				<tr><td>";
				listdirs ($newfolder);   //newfolder wird als neue basedir und x uebergeben
			echo "</td></tr></table></div>";
	}
}


if (!isset($stop)){$stop="";}


if ($stop != "stop") {
	echo "<div class=homefolder ><a href='#' onclick=setRequest('$basedir') alt=home title=home name='droptarget' dragId='$basedir'> <IMG src='./images/folder-home.png' width=16 height=16 border=0 align=top> <span style='font-weight: bold;'> /home</span>      </a></div>";   //home link
	listdirs ($basedir);
}


?> 