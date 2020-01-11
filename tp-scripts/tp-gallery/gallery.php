<?php
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

include "../include/check_login.php";
include ('../include/config.php');
$folder_tmp = $_SESSION['folder_tmp'];
?>


<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=UTF-8'> 
<link href='http://fonts.googleapis.com/css?family=Rock Salt' rel='stylesheet' type='text/css'>
<link href='./include/gallery.css' rel='stylesheet' type='text/css'>
<title>gallery</title>
<script type="text/javascript" src="../include/jquery/jquery-1.4.4.min.js"></script>


<script type="text/javascript" src="../include/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="../include/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="../include/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<link rel="stylesheet" href="../include/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />	
<script language="javascript" type="text/javascript" src="../include/jquery/jquery-ui-1.8.9.custom.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		$("a.grouped_elements").fancybox({
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'overlayShow'	:	true
		});
		
		
	
		
	});
	
	
	
function rotateimg(what,where,ext,deg){

	$.ajax({  
		type: "POST",  
		url: "rotate_images.php",  
		data: "img="+what+"&ext="+ext+"&deg="+deg+"",   
		success: function(msg){  
			$('#'+where+'').attr("src",""+what+"?"+new Date()+"");
			
			//$('#rotated').fadeIn('slow').delay(3000).fadeOut('fast');
		}  
	});  
}  
	
	
	
</script>

</head>




<body>

<div id="rotated">
rotated - you may have to reload the gallery 
<br> to see the result
</div>


	<div id="page-header">
		<h1>typeX-press | gallery
		<span id="version"><?echo $galleryversion;?></span>
		</h1>
	</div>
<div id="tabs">
	<ul><li id="activetab"><a ><span>gallery</span></a></li>
	</ul>
</div>



<?php



$handle = opendir($folder_tmp);

while (false !== ($file = readdir($handle))) {
	if ($file !== "." OR $file !== ".." OR !is_dir($file)){
		$files[] = $file;
	}
}
closedir($handle); 


echo "<div id='gallerycontent'>";

foreach ($files as $file){			//fuer jeden eintrag im array files erstelle eine variable $file
	if (!is_dir($file)){
		if ($file!=="mover" AND $file !=="votes"){		// ueberspringe verzeichnisse und definierte dateien 
			
			$path_parts = pathinfo($file);		//die funktion pathinfo() gibt uns ein array mit informationen ueber die datei
			$dirname= $path_parts['dirname'];
			$basename = $path_parts['basename'];
			@$ext = $path_parts['extension'];
			$filename=$path_parts['filename'];     // im von pathinfo() erzeugten array liegt auf "filename" der dateiname

			if(in_array(".$ext",$picture)){ 
				echo "			
					<div class='galleryimagediv shadow'>
							<span class=galleryimagetitle>$basename</span>
					
							<a id=single_image class=grouped_elements rel=group1 href='$folder_tmp/$basename'>
								<div >
								<img id=$filename class='galleryimage' src='$folder_tmp/$basename'>
								</div>
							</a>

							<div class=controls>  
								<img id=saveImage src=./images/rotatel.png onclick=rotateimg('$folder_tmp/$basename','$filename','$ext','90'); title=rotate border=0> 
								<img id=saveImage src=./images/rotater.png onclick=rotateimg('$folder_tmp/$basename','$filename','$ext','-90'); title=rotate border=0>
								
							</div>  
				</div>
				";
			}
		}
	}
}
echo "<br class=clear></div>";

?>




</body>
</html>

