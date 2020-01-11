<?php

$dir= $_GET['foldertozip'];   // which folder to zip ?
$basefolder= $_GET['basefolder']; // folder to save temp-zip file
$foldername= $_GET['foldername']; // folder name for zipfilename
$zip_file = tempnam("$basefolder", "tempname");		// create actual temp zipfile 



//$zip_file = tempnam("$basefolder", "tempname") . ".zip"; // to create linkable zip 



function createZipFromDir($dir, $zip_file, $basefolder) {
	$zip = new ZipArchive; 
	if (true !== $zip->open($zip_file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE)) {
			return false;
	}
	$dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	$dirs = array($dir); 
	while (count($dirs)) { 
		$dir = current($dirs); // gibt den wert des arrays aus auf dem der pointer gerade steht (current)
		$dirx=  str_replace("$basefolder", '', $dir);  //no need for whole server file structure in zip file
	
		$zip -> addEmptyDir($dirx); // add directory(name without basefolder) to zipfile
		$dh = opendir($dir); 
		while($file = readdir($dh)) 
		{ 
			if ($file != '.' && $file != '..') 
			{ 
				if (is_file($dir.$file)) {
					$zip -> addFile($dir.$file, $dirx.$file);  // add file dir.file to zipfile as dirx.file (without basefolder)
				} 
				elseif (is_dir($dir.$file)) {
					$dirs[] = $dir.$file."/"; 
				}
			} 
		} 
		closedir($dh); 
		array_shift($dirs); //gibt das erste array element aus und entfernt es aus dem array. (shiftet das ganze array nach links)
	} 
	$zip -> close(); 
}




createZipFromDir("$dir", "$zip_file", "$basefolder");

header("Content-type: application/zip");
header("Content-Disposition: attachment; filename=$foldername.zip");
header('Content-Length: ' . filesize($zip_file));
header("Pragma: no-cache");
header("Expires: 0");

readfile("$zip_file");

unlink($zip_file);
exit;

?>
