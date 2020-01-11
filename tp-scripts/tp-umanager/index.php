<?php include "../include/check_login.php"; ?>
<!--::::::::::::::::::::::::::::: << COPYING >> ::::::::::::::::::::::::::::::::-->
<!--
	Copyright (C)2011 Thomas Michael Weissel

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->
<!--::::::::::::::::::::::::::::: << COPYING >> ::::::::::::::::::::::::::::::::-->
<html>
<head>
    <title>typeX-press | usermanagment</title>
    <link rel="stylesheet" type="text/css" href="./include/umanager_styles.css">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<script  language="javascript" type="text/javascript" src="../include/jquery/jquery-1.4.4.min.js"></script>
	<script  language="javascript" type="text/javascript" src="../include/jquery/jquery-ui-1.8.9.custom.min.js"></script>
	<link rel="stylesheet" href="../include/jquery/jquery-ui-1.8.9.custom.css">
	<script language="javascript" type="text/javascript">
		function info(divid) {
			if ( $(divid).is(':hidden')){$(divid).show("fast");}else{ $(divid).hide("fast");}
		}

		function disable(ziel){
			if (document.getElementById(ziel).disabled==false){
			document.getElementById(ziel).disabled=true;
			}
		}
		function enable(ziel){
			if (document.getElementById(ziel).disabled==true){
			document.getElementById(ziel).disabled=false;
			}
		}


	</script>
<?php
include_once ('../include/config.php');

function securepw($password){		// make (at least) passwords uncrackable
	$salt = md5("1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");   
	$hash = $password . $salt;
	for ( $i = 0; $i < 10000; $i++ ) {
		$hash = md5( $hash );
	}
	return $hash;
}


function delfolder($newitem){
	##folder in array ls einlesen
	$deltarget = dir("$newitem");
	while ($var1 = $deltarget->read()){$ls[] = $var1;}
	$fileanzahl = count($ls);
		
	for($x = 0 ; $x < $fileanzahl ; $x++){
		if (($ls[$x] != ".") && ($ls[$x] != "..")){	
			$file = "$newitem/$ls[$x]";
			if (is_dir($file)){ 
				if (!@rmdir($file)) { delfolder ($file); }
			}else{
				@unlink ($file);
			}
		}
	}
	@rmdir ($newitem);
}







@$option = $_POST['option'];

if ($option == "update userdata"){

	include ('../include/users.php');  	//old userarray "$userarray" (json encoded) is imported
	$userarray = json_decode($userarray,true);  //decode old array to work with it
	$thisusercount = count($userarray);			//count old user entries
		
	$new_userarray = $_POST['user'];  //new userarray "new_userarray"
	$new_usercount = count($new_userarray);
		
    $info_userdel = "";
    $error = "";
    
	for ($i=0; $i<$new_usercount; $i++){   //creates "finaluserarray"
		@$new_username = $new_userarray[$i][username];
		@$new_password = $new_userarray[$i][password];
			
		@$old_username = $userarray[$i][username];
		@$old_password = $userarray[$i][password];
			
		// if password has changed - rehash it - otherwise leave old values alone  (hash is 32 characters - passwd max chars = 10)
		if (strlen($new_password)!= 32){$new_password = securepw($new_password);}else{ $new_password = $old_password;}   
			
			
		### OLD ##
		if (($new_username == $old_username)&&(!empty($old_username))){  //keep old values
			@$finaluserarray[$i][username] = $old_username;
			@$finaluserarray[$i][password] = $new_password;  //use $new_password in case it changed
					
		### NEW ##
		}else{ //this is either a new user or a renamed user
			
			//set some variables
			if (empty($old_username)){ $new_user=1; }  // is this a complete new user?
			
			$old_absolutebasedir = "../$userspace/$old_username"; 		//this will point to $userspace in case of a completely new user !
			$new_absolutebasedir = "../$userspace/$new_username"; 
				
			### VALID USERNAME ##
			if ( (!preg_match("/[^a-z0-9\.\)\(\-\_]/i","$new_username")) && ($new_username != "admin") && (!empty($new_username)) ){ //perform validation
				
				//check if username is already taken !!!
				$already_taken=0;
				for ($k=0; $k<$thisusercount; $k++){   //cycle through old stored userarray from users.php
					@$check_old_username = $userarray[$k][username];
					if ($new_username == $check_old_username){$already_taken = 1;}
				}
				if ($already_taken == 0){
					if($new_user==1){  //this user is completely new // create directory
						if( !is_dir("$new_absolutebasedir") ) {
							mkdir ($new_absolutebasedir, 0777);
							$info_userdel .= "Directory: '$new_username' created!<br>";
						}
					}else{    //this user is already in the system // rename directory
						if( is_dir("$old_absolutebasedir") ) {
							rename("$old_absolutebasedir","$new_absolutebasedir");
							$info_userdel .= "User/directory: '$new_username' renamed!<br>";
						}
					}
					//USERS -  set (or rename) username
					$finaluserarray[$i]['username'] = $new_username;
					$finaluserarray[$i]['password'] = $new_password;
				}else{
					$info_userdel .= "Username: '$new_username' is already taken!<br>";
					if ($old_username != ""){
						// if there was an old username keep it
						$finaluserarray[$i][username] = $old_username;
						$finaluserarray[$i][password] = $old_password;
						$info_userdel .= "Invalid Username. Keeping old value '$old_username'.<br>";
					}else{
						// if there was no old username
						// add nothing to array (this will be filtered out later)
						$finaluserarray[$i] = "";
						$finaluserarray[$i] = "";
						$info_userdel .= "Invalid Username. Discarding information'.<br>";
					}
				}	
				
			### INVALID USERNAME ##
			}else{
					
				$last_char_special= substr($new_username, -1);	// if there are special characters cut last character for later comparison with delete trigger '#'
					
				if($last_char_special=="#")  {  // invalid username - check if directory should be deleted
					
					//check if directory exists an if it's not linking to the basedir because of an nonexistant old dir
					if( is_dir("$old_absolutebasedir") && ($old_absolutebasedir!="../$userspace/")  ) {
						$orig_username=substr($new_username, 0, -1);
						delfolder($old_absolutebasedir);
						$info_userdel .= "Userdirectory: '$orig_username' deleted!<br>";
					}
						
						
					$orig_username=substr($new_username, 0, -1);
					$info_userdel .= "User: '$orig_username' deleted!<br>";
						
					//add nothing to array (this will be filtered out later)
					$finaluserarray[$i] = "";
					$finaluserarray[$i] = "";
						
				}else{
					if ($old_username != ""){
						// if there was an old username keep it
						$finaluserarray[$i]['username'] = $old_username;
						$finaluserarray[$i]['password'] = $old_password;
						$info_userdel .= "Invalid Username. Keeping old value '$old_username'.<br>";
					}else{
						// if there was no old username
						// add nothing to array (this will be filtered out later)
						$finaluserarray[$i] = "";
						$finaluserarray[$i] = "";
						$info_userdel .= "Invalid Username.<br>";
					}
				}
			}
		}
	}
	
	
	// remove invalid entries in $finaluserarray  (empty ones)
	$finaluserarray = array_values(array_filter($finaluserarray));
	
	//print_r($finaluserarray);
	echo "$error";
	
	
	$alluserdata = json_encode($finaluserarray);
	$userconf = fopen("../include/users.php","w+");		// oeffne datei zum schreiben (w+, a+ etc. siehe php info)
	$alluserdata = "<?php \$userarray = '$alluserdata'; ?>";
	fputs($userconf, "$alluserdata");		//schreib inhalt in data
	fclose($userconf);
}

if ($option == "save configuration"){
	
	$plugins = $_POST['plugins']; 
	$restricted = $_POST['restricted'];
	$picture = $_POST['picture'];
	$size_limit = $_POST['size_limit'];
	$limit_size = $_POST['limit_size'];
	$limit_ext = $_POST['limit_ext'];
	$extensions = $_POST['extensions'];
	$resize_width = $_POST['resize_width'];
	$resize_height = $_POST['resize_height'];
	$resize_quality = $_POST['resize_quality'];  
	$userspace = $_POST['userspace'];  
	$editable = $_POST['editable'] ;  
	$filebrowserversion = $_POST['filebrowserversion'];
	$uploaderversion = $_POST['uploaderversion'];
	$editorversion = $_POST['editorversion'];
	$galleryversion = $_POST['galleryversion'];
	$umanagerversion= $_POST['umanagerversion'];

	// prepare restricted array for saving in plaintext
	$prep_restricted="array(";
	$restricted = array_values(array_filter($restricted)); //strip empty fields
	foreach ($restricted as $ext){
		$prep_restricted .= "\"$ext\",";
	}
	$prep_restricted=substr($prep_restricted, 0, -1);
	$prep_restricted .= ")";
	
	// prepare picture array for saving in plaintext
	$prep_picture="array(";
	$picture = array_values(array_filter($picture)); //strip empty fields
	foreach ($picture as $ext){
		$prep_picture .= "\"$ext\",";
	}
	$prep_picture=substr($prep_picture, 0, -1);
	$prep_picture .= ")";
	
	// prepare extensions array for saving in plaintext
	$prep_extensions="array(";
	$extensions = array_values(array_filter($extensions)); //strip empty fields
	foreach ($extensions as $ext){
		$prep_extensions .= "\"$ext\",";
	}
	$prep_extensions=substr($prep_extensions, 0, -1);
	$prep_extensions .= ")";

	
	// prepare editable array for saving in plaintext
	$prep_editable="array(";
	$editable = array_values(array_filter($editable)); //strip empty fields
	foreach ($editable as $ext){
		$prep_editable .= "\"$ext\",";
	}
	$prep_editable=substr($prep_editable, 0, -1);
	$prep_editable .= ")";
	
	
$allconfigdata = "<?php
\$plugins = $plugins; 
\$restricted = $prep_restricted;
\$picture = $prep_picture;
\$size_limit = \"$size_limit\";
\$limit_size = \"$limit_size\";
\$limit_ext = \"$limit_ext\";
\$extensions = $prep_extensions;
\$resize_width=$resize_width;
\$resize_height=$resize_height;
\$resize_quality=$resize_quality;  
\$userspace = \"$userspace\";  
\$editable =$prep_editable ;  
\$filebrowserversion = \"$filebrowserversion\";
\$uploaderversion = \"$uploaderversion\";
\$editorversion = \"$editorversion\";
\$galleryversion = \"$galleryversion\";
\$umanagerversion= \"$umanagerversion\";
?>";


	$dataconf = fopen("../include/config.php","w+");		// oeffne datei zum schreiben (w+, a+ etc. siehe php info)
	fputs($dataconf, "$allconfigdata");		//schreib inhalt in data
	fclose($dataconf);
	

}





// overwrite POST values of variables with stored ones (not really necessary in production but nice for testing)

if ($option=="load default values") {
	include('../include/config_default.php');
}



?>
</head>  















<body>
<div id="page-header"><h1>typeX-press | user managment <span id="version"><?echo $umanagerversion;?></span></h1></div>

<div id="left">

	<div class="div1">
		<span class=heading>Information</span><br><br> 
		This is the user managment console. You can add and delete typeX-press users here!<br>
		<input type="button" class="button" onclick="info('#info10');" value="more info"><br>
		<a href="../index.php">> exit user managment</a><br>

		<div id="info10" class="info" style="display:none;">
			<ul>
				<li>Click on button "add user field" to add more input fields.</li>
				<li>For erasing a user and all userfiles append a # at the end of the "username".</li>
				<li>Special characters are not allowed in username.</li>
				<li>The username is also used as the userdirectory name.  </li>
				<li>Type in plaintext passwords - they will be encrypted automagically.  </li>
				<li>save configuration to get more blank input fields  </li>
				<li>basedir value must be relative to /tp-scripts/index.php  </li>
			</ul>
		</div>

	</div>

	<div class="div0">
		<span class=heading>Users</span><br><br> 

		<form action="index.php" method="post" name="userform" id="userform"> 
		
			<?php
				include ('../include/users.php');
				$userarray = json_decode($userarray,true);
				$thisusercount = count($userarray);
			?>
			
			<script language="javascript" type="text/javascript">
				var users = <?php echo $thisusercount;?>;
				var divinhalt = "";
				function newuser(){
					divinhalt += "\
					<INPUT name='user["+users+"][username]'  type='text' size='40'  maxlength='10' value=''> <b>username</b><br> \
					<INPUT name='user["+users+"][password]' type='text' size='40'  maxlength='10' value=''> <b>password</b><br><br> \
					";
					users++;
					document.getElementById('newuser').innerHTML = divinhalt;
				}
			</script>
			
			<?php
				$usernumber = 0;
				for ($i=0; $i<$thisusercount; $i++){
					@$username = $userarray[$i][username];
					@$password = $userarray[$i][password];

					if ($username == "admin"){
						echo "<INPUT name='user[$usernumber][username]' id='username$i' type='hidden' size='40'  maxlength='10' value='$username'> <b>admin</b><br>";
					}else{
						echo "<INPUT name='user[$usernumber][username]' id='username$i' type='text' size='40'  maxlength='10' value='$username'> <b>username</b><br>";
					}
					echo "<INPUT name='user[$usernumber][password]' id='password$i' type='text' size='40'  maxlength='10' value='$password'> <b>password</b><br>";		
					echo "<br>";
					$usernumber++;
				}
			?>
			
			<div id=newuser><!-- gets filled by newuser() function --> </div>

			<input type="button" class="button" onclick="newuser();" value="add user field"><br><br>
			<input name="option" class="button" type="submit"  value="update userdata"> 
			
		</form> 


		<br>
		<div id="stored" class="info1" style="display:none;">
			<span class=heading>User Data updated ! <br> <? echo $info_userdel; ?></span>
		</div>
		
		<?php
			if ($option == "update userdata"){
				echo "<script language='javascript' type='text/javascript'>
				window.setTimeout(\"info('#stored');\",500);
				</script>	";
			}
		?>
		
	</div>
</div>






















<div id="right">
<div class="div1">

<span class=heading>typeX-press configuration</span><br><br> 

<form action="index.php" method="post" name="configform" id="configform"> 


<?php
	//include ('../include/config.php');
	//$userarray = json_decode($userarray,true);
	//$thisusercount = count($userarray);
	$thispicturecount = count($picture)+2;
	$thisrestrictedcount =  count($restricted)+2;
	$thisextcount = count($extensions)+2;
	$thiseditablecount = count($editable)+2;

?>


<!--- hidden ... no user input needed  -->
<INPUT name="userspace" type="text" size="20" maxlength="40" value="<?php echo $userspace;?>" > basedir<br>
<!--- hidden ... no user input needed  -->

<INPUT name="plugins" type="text" size="20" maxlength="40" value="<?php echo $plugins;?>" > editor plugins (1 - true / 0 - false)<br>
 <br>
 
 
 
 
<INPUT name="filebrowserversion" type="hidden" size="20"  maxlength="40" value="<?php echo $filebrowserversion;?>" >filebrowser  <?php echo $filebrowserversion;?>  <br>
<INPUT name="uploaderversion" type="hidden" size="20"  maxlength="40" value="<?php echo $uploaderversion;?>" >uploader  <?php echo $uploaderversion;?> <br>
<INPUT name="editorversion" type="hidden" size="20"  maxlength="40" value="<?php echo $editorversion;?>" >editor  <?php echo $editorversion;?>  <br>
<INPUT name="galleryversion" type="hidden" size="20"  maxlength="40" value="<?php echo $galleryversion;?>" >gallery  <?php echo $galleryversion;?>  <br>
<INPUT name="umanagerversion" type="hidden" size="20"  maxlength="40" value="<?php echo $umanagerversion;?>" >umanager  <?php echo $umanagerversion;?>  <br>






<?php
echo " <br>picture (what should be handled as image)<br>";
for ($i=0; $i<$thispicturecount; $i++){
	@$pictureextension = $picture[$i];
	echo "<INPUT name=picture[$i] type=text size=5 maxlength=5 value='$pictureextension' > ";
}
echo "<br><br>restricted (do not list in browser)<br>";
for ($i=0; $i<$thisrestrictedcount; $i++){
	@$thisrestricted = $restricted[$i];
	echo "<INPUT name=restricted[$i] type=text size=10 maxlength=25 value='$thisrestricted' > ";
}

echo "<br><br>Editable filetypes<br>";
for ($i=0; $i<$thiseditablecount; $i++){
	@$editableext = $editable[$i];
	echo "<INPUT name=editable[$i] type=text size=5 maxlength=5 value='$editableext' > ";
}


?>



<br><br>

<span class=heading>Upload configuration</span><br><br>

limit filesize? (recommended)<br>
<?php
if ($size_limit=='yes'){
	echo "<input type=radio name='size_limit' value='yes' id=size1 checked > <label for=size1> yes </label>
	<input type=radio name='size_limit' value=no id=size2 > <label for=size2> no </label> <br>
	";
}else{
	echo "<input type=radio name='size_limit' value='yes' id=size1 > <label for=size1> yes </label>
	<input type=radio name='size_limit' value=no id=size2 checked > <label for=size2> no </label> <br>
	";
}
echo "
<INPUT name=limit_size id=limits type=text size=11 maxlength=40 value=$limit_size> Sizelimit (in bytes) 
";

?>

<br><br>

limit extensions?<br>
<?php
if ($limit_ext=='yes'){
	echo "<input type=radio name=limit_ext value=yes id=lim1 checked > <label for=lim1> yes </label>
	<input type=radio name=limit_ext value=no id=lim2> <label for=lim2> no </label> <br>
	";
}else{
	echo "<input type=radio name=limit_ext value=yes id=lim1  > <label for=lim1> yes </label>
	<input type=radio name=limit_ext value=no checked id=lim2> <label for=lim2> no </label> <br>
	";
}
?>

<?php

for ($i=0; $i<$thisextcount; $i++){
	@$uploadextension = $extensions[$i];
	echo "<INPUT name=extensions[$i] type=text size=5 maxlength=5 value='$uploadextension' > ";
}
?>

<br><br>

uploader resize settings<br>
<?php

echo "<INPUT name=resize_width type=text size=5 maxlength=5 value='$resize_width' >px resize_width<br>";
echo "<INPUT name=resize_height type=text size=5 maxlength=5 value='$resize_height' >px resize_height<br> ";
echo "<INPUT name=resize_quality type=text size=5 maxlength=5 value='$resize_quality' >% resize_quality <br>";


?>

<br><br>
<input name="option" class="button" type="submit"  value="save configuration"> 
<input name="option" class="button" type="submit"  value="load default values"> 

</form> 

</div>
</div>





</body>
</html>

