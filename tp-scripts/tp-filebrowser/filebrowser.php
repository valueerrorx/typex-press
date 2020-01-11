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
?>
<html>
<head>
<title>typeX-press | filebrowser</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<link rel="stylesheet" href="./include/filebrowser.css" type="text/css" />
<link rel="stylesheet" href="../include/jquery/jquery-ui-1.8.9.custom.css" type="text/css" /> 
<link rel="stylesheet" href="../include/fancybox/jquery.fancybox-1.3.4.css" type="text/css"  />	

<script language="javascript" type="text/javascript" src="./include/filebrowser_functions.js"></script>
<script language="javascript" type="text/javascript" src="./include/filebrowser_ajax.js"></script>
<script language="javascript" type="text/javascript" src="../include/jquery/jquery-1.4.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../include/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script language="javascript" type="text/javascript" src="../include/fancybox/jquery.easing-1.3.pack.js"></script>
<script language="javascript" type="text/javascript" src="../include/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script language="javascript" type="text/javascript" src="../include/jquery/jquery-ui-1.8.9.custom.min.js"></script>
<script language="javascript" type="text/javascript" src="../include/jquery/imgpreview.full.jquery.js"></script>

<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$('#hiddentoolsdiv').draggable( { iframeFix: true });
		$('#content').click(function(e){ 	//positioniert den hidentoolsdiv immer zur maus
			htop = e.pageY+2;
			$('#hiddentoolsdiv').css("top",htop+"px");
		}); 
		
			$('#menuwrapper').resizable({
				resize: function(event, ui) { },
				start: function(event, ui) { },
				stop: function(event, ui) { },
				handles: 'e'
			});
	});
</script>
</head>


<!-- onload wird per ajax httprequest die folderleiste und der maintable geladen-->

<?php
include ('../include/config.php');
include ('../include/users.php');

$basedir = $_SESSION['basedir'];
$username = $_SESSION['username'];

// falls folder_tmp leer oder sonst was faul setz folder_tmp auf basedir - wenn nicht schau im cookie nach wie der aktuelle folder heisst
if ((!isset($folder_tmp))|| ($folder_tmp == " ") || (strpos($folder_tmp,'..'))){ 	$folder_tmp = "../".$basedir; }
else {	$folder_tmp = $_SESSION['folder_tmp']; }

if ((!isset($basedir))|| ($basedir == " ") || (strpos($basedir,'..'))     ) {$stop = "stop";}else{$stop = "";}   // falls aus irgendeinem grund mit basedir was faul ist STOP
if ($stop == "stop") {echo "<body> no basedirectory !";}else{echo "<body onload=\"setRequest('$folder_tmp','name-asc'); folderlistRequest('$folder_tmp');\">";}
?>


	<div id="page-header">
		<h1>typeX-press | filebrowser
		<span id="version"><?phpecho $filebrowserversion;?></span>
		</h1>
	</div>
<div id="tabs">
	<ul><li id="activetab"><a ><span>filebrowser</span></a></li>
		<li><a href="./filebrowser.php?status='logout'"><img class="tabimage" src=./images/user-invisible.png><span>Logout <?echo $username?> &nbsp;</span></a></li>
		
	</ul>
</div>


	<div id="hiddentoolsdiv" style="left:300px; top:120px; display:none;">
		<div id='titleb'>
			<div class=folderlistfile  id=movehandle><!--wird per js gesetzt--></div>	
			<IMG class="close" onclick="toggle('#hiddentoolsdiv');" src='./images/close.png' height=10px width=10px>
		</div>
		<div id=toolscontent class=toolscontent><!--wird per ajax bei bedarf geholt--></div>	
	</div>



			
				<div id="content">	
				<div id="menuwrapper">
					<div id="menu"> <!--wird per ajax geholt--> </div></div>
					<div id="ajaxcontent"> <!--wird per ajax geholt-->  </div>
				</div>
			
		
	
	



</body>
</html>








