<?php
include "../include/check_login.php";
include ('../include/config.php');
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

@$inhalt = $_POST['inhalt']; 		// wird für "delete" missbraucht um yes or no zu transportieren - ueber functionsdoitRequest() 
@$name = $_POST['name'];
@$newitem = $_POST['newitem'];
@$extension = $_POST['extension'];



if (!isset($_POST['typ']))
{
	$typ = $_GET['typ'];
	$ordner = $_GET['ordner'];
}
else
{
	$typ = $_POST['typ'];
	$ordner = $_POST['ordner'];
}
	

#check if $inhalt is empty and unset $inhalt inhalt sollte umbenannt werden wird soweit nur mehr bei delete function verwendet für yes/no .. nicht aussagekraftig
if ($inhalt == ""){
unset($inhalt);
}


function delfolder ($newitem){
	##folder in array ls einlesen
	$deltarget = dir("$newitem");
	while ($var1 = $deltarget->read())
	{
		$ls[] = $var1;
	}
	$fileanzahl = count($ls);
		
	for($x = 0 ; $x < $fileanzahl ; $x++)
	{
		if (($ls[$x] != ".") && ($ls[$x] != ".."))
		{	
			$file = "$newitem/$ls[$x]";
			if (is_dir($file)){
				if (!@rmdir($file)) { delfolder ($file); }
			}
			else 
			{
				@unlink ($file);
			}
		}
	}
	@rmdir ($newitem);
}



switch($typ){

//new file / folder
	case "datei": case "verzeichnis":

	if ($name){
		if (!preg_match("/[^a-z0-9\.\)\(\-\_]/i","$name")){
			if(file_exists($ordner."/".$name.$extension) OR is_dir("$ordner/$name.$extension"))			{
				echo "<br><br><center><h1>Warning! <br> File/Folder already exists!</h1></center>";
			}
			else  {
				if ($typ == "datei") {

		########  Create File
						touch ($ordner."/".$name.$extension);
						echo "<br><br><center><h1>New file '$name$extension' created!</h1></center>";
						
				}

		########  Create Folder
				if ($typ == "verzeichnis") {
					mkdir ($ordner."/".$name, 0777);
					echo "<br><br><center><h1>New folder created!</h1></center>";
				}
			}
		}
		else {
			echo "<br><br><center><h1>Filename must not contain specialcharacters!!</h1></center>";
		}
	}
	else {
		echo "<br><br><center><h1>choose filename</h1></center>";
	}
	break;


	case "delete":

	if ($inhalt == 'yes')
	{
	    if (is_dir($newitem))  
	    {
		delfolder ($newitem);
	    }
	    else 
	    {
		$thisname = strrchr($newitem,"/");
		$thisname = preg_replace("/\//", "", $thisname);
		unlink($newitem);
	    }
		    echo "<br><br><center><h1>Done!</h1></center>";
	}
	else 
	{
		echo "<br><br><center><h1>Canceled!</h1></center>";
		echo "	<script language='JavaScript' type='text/javascript'>
			window.setTimeout(\"document.getElementById('hiddentoolsdiv').style.display='none'\", 1000);
			</script>";
	}
	break;


	case "rename":

	if ($name)
	{
		if (!preg_match("/[^a-z0-9\.\)\(\-\_]/i","$name"))    // ausschliesslich diese zeichen dürfen enthalten sein
		{
			$type = strrchr($newitem, ".");	
			$newname = "$ordner/$name$type";     		//neuer dateiname setzt sich zusammen aus ordner und name
			$thisname = strrchr($newitem,"/");		//alter dateiname ohne ordner
			$thisname = preg_replace("/\//", "", $thisname);

			if(is_dir($newitem)){$newname = "$ordner/$name";}


			if((file_exists($newitem)) || (is_dir($newitem))) 	//falls datei existent 
			{
				if((file_exists($newname)) || (is_dir($newname))) 	//falls neuer dateiname bereits vorhanden
				{
				echo "<br><br><center><h1>file exists!</h1></center>";
				}
				else
				{
						if (rename($newitem,$newname))			//falls keine style und head dateien vorhanden
						{
							echo "<br><br><center><h1>Done!</h1></center>";
						}
						else
						{
							echo "<br><br><center><h1>error</h1></center>";	
						}
				}
			}
		}
		else 
		{
			echo "<br><br><center><h1>Filename must not contain specialcharacters!!</h1></center>";
		}
	}
	else 
	{
		echo "<br><br><center><h1>please enter a new filename</h1></center>";
	}

	break;




	case "copy":
		$thisname = strrchr($newitem,"/");   //newitem ist die zu kopierende datei
		$thisname = preg_replace("/\//", "", $thisname);
		$newname = "$ordner/$thisname";   //in diesem fall ist $ordner der zielordner nicht der basisordner
		  
		if((file_exists($newname)) || (is_dir($newname))) 
		{
			echo "<br><br><center><h1>file exists!</h1></center>";
		}
		else
		{	
				if (copy($newitem, $newname))
				{
					echo "<br><br><center><h1>Done!</h1></center>";
				}
				else
				{
				echo "<br><br><center><h1>Could not copy file to folder</h1></center>";	
				}
		}
	break;
	
	case "move":

		$thisname = strrchr($newitem,"/");   //newitem ist die zu kopierende datei
		$thisname = preg_replace("/\//", "", $thisname);
		$newname = "$ordner/$thisname";   //in diesem fall ist $ordner der zielordner nicht der basisordner
		
		if((file_exists($newname)) || (is_dir($newname))) 
		{
			echo "<br><br><center><h1>file exists!</h1></center>";
		}
		else
		{
				if (copy($newitem, $newname))
				{
					unlink($newitem);
					echo "<br><br><center><h1>File moved!</h1></center>";
				}
				else
				{
				echo "<br><br><center><h1>Could not move file to folder</h1></center>";	
				}
		}
	break;




	case "cancel":

		echo "<br><br><center><h1>Canceled!</h1></center>";
		echo "<script language='JavaScript' type='text/javascript'>
			window.setTimeout(\"document.getElementById('hiddentoolsdiv').style.display='none'\", 2000);
			</script>";
	break;

}





?>

