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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<title> typeX-press u.upload</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

<link rel="stylesheet" href="./include/uploader.css">

<script type="text/javascript" src="../include/jquery/jquery.min.js"></script>

<script type="text/javascript" src="./include/uploader_functions.js"></script>
<script type="text/javascript" src="./include/js/plupload.js"></script>
<script type="text/javascript" src="./include/js/plupload.html4.js"></script>
<script type="text/javascript" src="./include/js/plupload.html5.js"></script>
<script type="text/javascript" src="./include/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>



<?php
include ('../include/config.php');
include ('../include/users.php');
// werte aus dem cookie holen
$folder = $_SESSION['folder_tmp'];
$basedir = $_SESSION['basedir'];
// basedir namen durch home ersetzen
$prefix = str_replace("../$basedir","/home",$folder);


// prepare extensions for flash & ajax uploader
if ($limit_ext == "yes") { 
	// flash
	$allextensions="";
	foreach ($extensions as $ext){
	$allextensions .= "*$ext;";
	}
	// ajax
	$allextensionsajax="";
	foreach ($extensions as $ext){
	$ext = preg_replace("/\./", "", $ext);
	$allextensionsajax .= "$ext,";
	}
}
if ($size_limit == "yes") { 
	$limit_size = $limit_size/1000/1000;
}


?>

<script type="text/javascript">


var resizewidth="<?php echo $resize_width; ?>";
var resizeheight="<?php echo $resize_height; ?>";
var resizequality="<?php echo $resize_quality; ?>";




$(function() {
	
	function uploader(){
		if ($('#resize').is(':checked')){ 
			resizeit = {width: resizewidth, height: resizeheight, quality: resizequality};
		}
		else{ resizeit = false;}
		
		$("#uploader").pluploadQueue({
			// General settings
			runtimes: 'html5,html4',
			url: './upload.php',
			max_file_size: '<?php echo $limit_size;?>mb',
			chunk_size: '1mb',
			unique_names: false,
			resize: resizeit,
			// Specify what files to browse for
			filters: [
				{title: "Image files", extensions: "jpg,gif,png"},
				{title: "Zip files", extensions: "zip"},
				{title: "typexpress", extensions:"<?php echo $allextensionsajax;?>"}
			],
			// PreInit events, bound before any internal events
			preinit: {
				UploadFile: function(up, file) {
					up.settings.url = 'upload.php?basedir=<?php echo "$folder";?>';//give upload.php the path to the upload directory (relative path)
				}
				
			},
			// Post init events, bound after the internal events
			init: {
				FileUploaded: function(up, file, info) {
					// Called when a file has finished uploading
					parent.opener.setRequest('<?php echo $folder; ?>');
				}
			}
		});
	}
		
	
	uploader();
	
	$('#clear').click(function(e) {
		uploader();
	});
	
	$('#resize').click(function(e) {
		uploader();
	});
	

	
});



</script>

</head>





<body id=setup>

<div id="page-header">
	<h1>typeX-press | uploader
	<span id="version"><?echo $uploaderversion;?></span>
	</h1>
</div>

<div id="tabs">
	<ul><li class="activetab"><a href=""><span>uploader</span></a></li></ul>
</div>	

<div id="content">	

	Upload file to:<br>
	<div class='pathname'><?php echo "$prefix";?>/</div>
	<input type=checkbox name=resize id=resize checked> resize <span class=hint>(resize images to <?php echo $resize_width;?>x<?php echo $resize_height;?> before upload)</span>
	<br/><br/>
	<div id="uploader" style="width: 450px; ">You browser doesn't support upload.</div>


	
	<br/>
	<a id="clear" href="#">> clear queue</a><br/>
	<a href="#" onclick="toggledisplay('info');">> info</a><br/>
	
			
		
	
	
	
	
	
	<div id="info" style="display:none;">
		<b>php.ini:</b><div class=hint>
		- 'upload_max_filesize' is <?php echo ini_get('upload_max_filesize');?> per file<br/>
		- 'post_max_size' is <?php echo ini_get('post_max_size');?> per submit<br/>
		</div>
		<?php
			if ($size_limit == "yes") { 
		
			echo "<b>typeX-press:</b> <div class=hint>- filesize limit is set to ".$limit_size."M per file <br>";
			}

			if ($limit_ext == "yes") { 
				echo "- allowed file extensions are: ";
				echo "$allextensionsajax";
			}
		?><br/>
			- allowed characters [^a-z0-9\.\)\(\-\_] (every blank space or special char will be convertet to _)
			</div>
	</div>

</div>




</body>
</html>
