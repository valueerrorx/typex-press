<? 

$filename = $_POST['img'];
$ext = $_POST['ext'];


$degrees = $_POST['deg'];


// Content type
header('Content-type: image/jpeg');



// Output
if ($ext=="png"){
	$source = imagecreatefrompng($filename);
	$rotate = imagerotate($source, $degrees, 0);
	imagepng($rotate,$filename);
}
elseif ($ext=="jpg"||$ext=="jpeg"){
	$source = imagecreatefromjpeg($filename);
	$rotate = imagerotate($source, $degrees, 0);
	imagejpeg($rotate,$filename);
}
else {
	return;
}

imagedestroy($rotate);

# echo "<img class='galleryimage' src='$filename'> "

?>