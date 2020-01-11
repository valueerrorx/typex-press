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

// hilfswerkzeuge anzeigen true or false
$plugins = 1;

//diese verzeichnisse und dateien werden nicht gelistet im filebrowser
$restricted = array(".htacces","tp-scripts");

// handle as picture
$picture = array(".gif",".png",".jpg",".jpeg",".bmp",".gif",".JPG",".PNG",".GIF",".psd");



// limit upload filesize
$size_limit = "yes";
$limit_size = "10000000";		// in bytes

// limit uploading to following extensions
$limit_ext = "yes";
$extensions = array(".html", ".htm", ".js", ".txt",".zip",".rar",".gz",".mp3",".wav",".ogg",".jpg",".JPG",".jpeg",".png",".gif",".pln",".pla",".dwg",".dxf",".GIF",".psd",".doc",".ods",".odt",".pdf",".xcf",".bmp");

// resize values for ajax uploader (optional)
$resize_width=800;
$resize_height=600;
$resize_quality=90;  // jpg compression quality 



############################################################################
$userspace = "../tp-users";   // DO NOT EDIT !! contains userdirectories
$editable = array(".html", ".htm", ".js", ".txt");  // handle as editable source files

$filebrowserversion = "v.1.0";
$uploaderversion = "v.0.6";
$editorversion = "v.1.0";
$galleryversion = "v.0.3";
$umanagerversion= "v.0.3";
############################################################################


?>