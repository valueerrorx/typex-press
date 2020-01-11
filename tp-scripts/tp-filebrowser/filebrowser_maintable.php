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

header('Content-Type: text/html; charset=utf-8'); // sorgt f�r die korrekte Kodierung
header('Cache-Control: must-revalidate, pre-check=0, no-store, no-cache, max-age=0, post-check=0'); // wichtig wegen IE

include ('../include/config.php');
include ('../include/users.php');

$folder_tmp = $_POST['folder_tmp']; //die ajax scripts für folder maintable und folder list POSTen den aktuellen folder_tmp  >> rein damit in die session vars!!
$basedir = $_SESSION['basedir'];
$_SESSION['folder_tmp'] = "$folder_tmp";  //aktualisiert/setzt die variable folder_tmp im cookie
if ((!isset($basedir))|| ($basedir == " ") || (strpos($basedir,'..'))     ) {$stop = "stop";}   // falls aus irgendeinem grund mit basedir was faul ist STOP

$size_content = "";
$date_content = "";
$name_content = "";
 /////////////////////////////////////////////////////////////////
//////////////// MAKE and SORT directory array  /////////////////
function makedirarray($folderbase,&$folderarray)
{
		global $restricted;
		$handle = opendir($folderbase);
		$x=0;
			while ($subitem = readdir($handle)) 
			{
				$newitem = "$folderbase/$subitem";	
				if (($subitem != ".") && ($subitem != "..") && (!in_array($subitem,$restricted)))  //testet ob das neue item verboten ist
				{
                    date_default_timezone_set('UTC');
					$filedat = date ("d-m-y | H:i", filemtime("$newitem"));		//filedate
					$size = filesize("$newitem");					//filesize
					$size = round($size/1024);
					
					$type = strrchr($subitem, ".");				//filetype
					if (($type == "")&& (is_dir($newitem)))
					{
						$ext=false; 
						$type = "folder";
					}
					else
					{
						$ext="$type";
						$type="file";
					}
					@$folderarray[$folderbase][$x][path] = "$newitem";		//fill array()
					@$folderarray[$folderbase][$x][name] = "$subitem";
					@$folderarray[$folderbase][$x][date] = "$filedat";
					@$folderarray[$folderbase][$x][size] = "$size";
					@$folderarray[$folderbase][$x][type] = "$type";
					@$folderarray[$folderbase][$x][ext] = "$ext";
					$x++;
				}
			}
			closedir($handle);
}

$folderbase = $folder_tmp;
makedirarray($folderbase,$folderarray);		//erzeugt ein mehrdimensionales array in dem die gesamte verzeichnisstruktur abgebildet ist
@$foldercount = count($folderarray[$folderbase])-1;    //fangt bei 1 an..  == untypisch.. 0 ist der erste wert


/////////////////////////// sort files and change arrow ///////////////////////////
$tsort = $_POST['tsort'];
$prerest = strrchr($tsort,"-");

if ($prerest == "-asc"||$tsort == "undefined"){ $pre = "-desc";}
else { $pre = "-asc";}


if (is_Array($folderarray[$folderbase])){
	switch($tsort)
	{
	   case "date-asc":
		$what = "date";
		$date_content="<img src='./images/down.png' height=10px width=10px>";
	   break;
	    case "name-asc":
		$what = "name";
		$name_content="<img src='./images/down.png' height=10px width=10px>";
	    break;
	   case "size-asc":
		$what = "size";
		$size_content="<img src='./images/down.png' height=10px width=10px>";
	   break;
	   case "date-desc":
		$what = "date";
		$date_content="<img src='./images/up.png' height=10px width=10px>";
	   break;
	   case "name-desc":
		$what = "name";
		$name_content="<img src='./images/up.png' height=10px width=10px>";
		
	   break;
	   case "size-desc":
		$what = "size";
		$size_content="<img src='./images/up.png' height=10px width=10px>";
	   break;
	   default:
		$what = "name";
		$name_content="<img src='./images/down.png' height=10px width=10px>";
	   break;
	}
	$sortArray = array();		//erstellt ein neues array (zum ordnen) aus der zweiten dimension des folderarray
	foreach($folderarray[$folderbase] as $key => $array) {
		$sortArray[$key] = $array[$what];
	}
	$sortArraylower = array_map('strtolower',$sortArray); 
	if ($pre == "-asc"){
		if ($what == "size"){
			array_multisort($sortArray, SORT_ASC, SORT_NUMERIC, $folderarray[$folderbase]); 
		}else{
			array_multisort($sortArraylower, SORT_ASC, SORT_STRING, $folderarray[$folderbase]); 
		}
	}
	else {
		if ($what == "size"){
		      array_multisort($sortArray, SORT_DESC, SORT_NUMERIC, $folderarray[$folderbase]);
		}else{
		      array_multisort($sortArraylower, SORT_DESC, SORT_STRING, $folderarray[$folderbase]); 
		}
	}
}
else{
$name_content="";
}
 //////////////// MAKE and SORT directory array  /////////////////
/////////////////////////////////////////////////////////////////


if (!isset($stop)){$stop="";}


if ($stop != "stop") {

############################################
############### top div   ##################

echo "<DIV class=folderlistheader>";

 /////////////////////////////////////////////////////////////////
//////////////// Icons (Folder operations)     //////////////////



echo "<div class=operations>
<img src=./images/folder-new.png alt='new folder' title='new folder' style=\"border:1px solid TRANSPARENT;\" width=16 height=16  onmouseover=\"this.style.border='1px solid #ffffff'\" onmouseout=\"this.style.border='1px solid TRANSPARENT'\" ONCLICK=\"operations('$folder_tmp','verzeichnis','')\">
<img src=./images/upload.png alt='upload' title='upload' style='border:1px solid TRANSPARENT' width=16 height=16 onmouseover=\"this.style.border='1px solid #ffffff'\" onmouseout=\"this.style.border='1px solid TRANSPARENT'\" ONCLICK=\"openuploadwindow('../tp-uploader/index.php','upload')\"> 
<img src=./images/reload.png alt='reload' title='reload' style='border:1px solid TRANSPARENT' width=16 height=16 onmouseover=\"this.style.border='1px solid #ffffff'\" onmouseout=\"this.style.border='1px solid TRANSPARENT'\" ONCLICK=\"thisreload()\">
<img src=./images/slide.png alt='gallery' title='gallery' style='border:1px solid TRANSPARENT' width=16 height=16 onmouseover=\"this.style.border='1px solid #ffffff'\" onmouseout=\"this.style.border='1px solid TRANSPARENT'\" ONCLICK=\"openuploadwindow('../tp-gallery/gallery.php','gallery')\">





</div>";
 //////////////// Icons (Folder operations)     //////////////////      functions_initiateRequest('$folder_tmp','upload','')
/////////////////////////////////////////////////////////////////


 //////////////////////////////////////////
////////////////  PATH  //////////////////
$pfad_link = $folder_tmp;
$pfad_ausgabe[0] = $pfad_link;
$pfad_anzahl = substr_count($folder_tmp,"/");
for($x = 0; $x < $pfad_anzahl; $x ++)
{
	$rest = strrchr($pfad_link,"/");
	$rest_len = strlen($rest);
	$pfad_link = substr($pfad_link, 0, -$rest_len);
	$pfad_ausgabe[] = $pfad_link;
}
$pfad_anzahl = count($pfad_ausgabe);


//ersetzt .."basedir" durch "/home" und versteckt den "userspace" folder

echo "<div class=path><b>Path: </b>";
for($x = $pfad_anzahl; $x >= 0; $x--)
{
	if (@$pfad_ausgabe[$x] == "../".$basedir)    
	{
		echo "<a href='javascript:setRequest(\"$pfad_ausgabe[$x]\")' >";
		echo "<b>/home</b></a>";
	}
	elseif ((@$pfad_ausgabe[$x] == "../".$userspace)||(@$pfad_ausgabe[$x] == "../..")){
	echo "";
	}
	else
	{
		echo @"<a href='javascript:setRequest(\"$pfad_ausgabe[$x]\")' onclick='showsubonly(\"$pfad_ausgabe[$x]\")'  >";
		@$raus = strrchr($pfad_ausgabe[$x],"/");
		echo "$raus</a>";
	}
}
echo "</div>";
 ////////////////  PATH  //////////////////
//////////////////////////////////////////


echo "</div>";

############### top div   ##################
############################################








################################################
#################   main div ###################

echo "<DIV class=folderlist >";
echo "<table class=maintable>";


//////////////////////////////////////
////////////// HEADINGS  ////////////
echo "<thead class=tablehead><tr>
<td id=sortname  onclick=setRequest('$folder_tmp','name$pre'); onmouseover=this.style.cursor='pointer'>
<b>&nbsp;&nbsp;&nbsp;&nbsp; Name</b> $name_content 
</td>
<td id=sortsize onclick=setRequest('$folder_tmp','size$pre'); onmouseover=this.style.cursor='pointer'>
<b>Size</b> $size_content 
</td>
<td id=sortdate onclick=setRequest('$folder_tmp','date$pre') onmouseover=this.style.cursor='pointer'>
<b>Date</b> $date_content
</td>
<td id=operation  ><b>Operations</b></td>
</tr></thead> ";
////////////// HEADINGS  //////////////
//////////////////////////////////////


/////////////////////////////////////////////
//////////   PARENT DIRECTORY   ////////////
if ($folder_tmp != "../".$basedir) {
$upperdir= dirname($folder_tmp);

	echo "
	<tr>
	<td class=maintable><div>
	<a href='javascript:setRequest(\"$upperdir\")'>
	<img src=./images/back.png width=16 height=16 align='left' border='0'>&nbsp;parent directory</a>
	</div></td>
		<td><div>   ..   </div> </td>
		<td><div>   &nbsp;     </div> </td>
		<td><div>   &nbsp;     </div> </td>
	</tr>";
}
//////////   PARENT DIRECTORY   ////////////
///////////////////////////////////////////


///////////////////////////////////////////////
//////////////   FOLDERLIST   ////////////////
for($x = $foldercount; $x>=0; $x--) {
	@$newitem = $folderarray[$folderbase][$x][path];
	@$subitem = $folderarray[$folderbase][$x][name];
	@$filedat = $folderarray[$folderbase][$x][date];
	@$size = $folderarray[$folderbase][$x][size];
	@$type = $folderarray[$folderbase][$x][type];
	@$ext = $folderarray[$folderbase][$x][ext];

		
//  ICONS-LINKS	

	if ($type == "folder"){   // achtung: classname: folderlistfolder wird fuers sammeln im dragndrop script gebraucht
		echo "<tr class=maintable>";
		echo "<td><div dragName='droptarget' dragId='$newitem' class='folderlistfolder'>
		<a href='javascript:setRequest(\"$newitem\")' onclick='showsubonly(\"$newitem\")'> 
		<img src='./images/folderclose.png' width=16 height=16 align='left' border='0'>&nbsp;$subitem</a> 
		</div></td>";

//  SIZE & DATE	

	echo "<td> <div>folder</div> </td>";
	echo "<td> <div>$filedat</div> </td>";

//  OPERATIONS  

		echo "	<td>	&nbsp;&nbsp;
				<a ONCLICK=\"operations('$folder_tmp','delete','$newitem')\">
				<img src=./images/delete-a.png width=16 height=16 alt=delete title=delete onmouseover=\"this.src='./images/delete.png'\" onmouseout=\"this.src='./images/delete-a.png'\">
				</a>
				<a ONCLICK=\"operations('$folder_tmp','rename','$newitem')\">
				<img src=./images/rename-a.png width=16 height=16 alt=rename title=rename onmouseover=\"this.src='./images/rename.png'\" onmouseout=\"this.src='./images/rename-a.png'\">
				</a> 
				<a ONCLICK=document.location.href=\"./download_folder_zip.php?foldertozip=$newitem&basefolder=$folder_tmp&foldername=$subitem\">
				<img src=./images/receive.png width=16 height=16 alt='download as zip' title='download as zip' onmouseover=\"this.src='./images/receive-a.png'\" onmouseout=\"this.src='./images/receive.png'\">
				</a> 
				
				
			</td>";
			
			

	echo "</tr>";
	}
}
//////////////   FOLDERLIST   ////////////////
///////////////////////////////////////////////


////////////////////////////////////////////
//////////////   FILELIST   ////////////////
for($x = $foldercount; $x>=0; $x--) {
	@$newitem = $folderarray[$folderbase][$x][path];
	@$subitem = $folderarray[$folderbase][$x][name];
	@$filedat = $folderarray[$folderbase][$x][date];
	@$size = $folderarray[$folderbase][$x][size];
	@$type = $folderarray[$folderbase][$x][type];
	@$ext = $folderarray[$folderbase][$x][ext];

	

// ICONS-LINKS
	if ($type == "file"){
		$previevfunc=""; $imgclass=""; $rel = "";
		
		if($size > 1000){
			$size = round($size/1000 ,2);
			$index = "MB";
		}else {
			$index = "KB";
		}
		
		
		if( in_array($ext,$picture)){ 
			$img = "./images/filepicture.png"; 
			$previevfunc = "onmouseover=prev(this)";
			$imgclass = "class=imagetype";  //for imagepreview and fancybox
			$rel="rel=group1";
		}
		elseif ($ext == ".js") { $img = "./images/filejs.png";}
		elseif ($ext == ".html" || $ext == ".php") { $img = "./images/filehtml.png";}
		else { $img = "./images/file.png";}

		echo "<tr>";
		echo "<td>
			<div dragName='dragelement' dragId='$newitem' class='folderlistfile'>
				<a title='download' alt='download' href=$newitem target=_blank><img src='$img' align='left' width=16 height=16 border='0'></a>
				<a title='$subitem' alt='$subitem' $imgclass $rel href=$newitem target=_blank>&nbsp;$subitem</a>
			</div>
		</td>";

// SIZE & DATE	

	echo "<td> <div>$size $index</div> </td>";
	echo "<td> <div>$filedat</div> </td>";

// OPERATIONS

		echo "	<td>	&nbsp;&nbsp;

				<a ONCLICK=\"operations('$folder_tmp','delete','$newitem')\">
				<img src=./images/delete-a.png width=16 height=16 alt=delete title=delete onmouseover=\"this.src='./images/delete.png'\" onmouseout=\"this.src='./images/delete-a.png'\" >
				</a>
				<a ONCLICK=\"operations('$folder_tmp','rename','$newitem')\">
				<img src=./images/rename-a.png width=16 height=16 alt=rename title=rename onmouseover=\"this.src='./images/rename.png'\" onmouseout=\"this.src='./images/rename-a.png'\" >
				</a>";


			//if( in_array($ext,$editable)) { echo "
			//	<a href=\"javascript:openEditor('.$newitem');\">
			//	<img src=./images/edit-a.png width=16 height=16 alt=edit title=edit style='border:1px dotted #cccccc' onmouseover=\"this.src='./images/edit.png'\" onmouseout=\"this.src='./images/edit-a.png'\" >
			//	</a>";
			//}
				
		echo "	</td>";
		echo "</tr>";
	}
}
//////////////   FILELIST   ////////////////
////////////////////////////////////////////



echo "</table>
</div>
";


}



#################   main div ###################
################################################
?>






















