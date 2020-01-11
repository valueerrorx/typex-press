 
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

</head>  



<body>
<div id="page-header"><h1>typeX-press | create password hash</h1></div>

<br>
<br>
<br><br>


		<span class=heading>Password</span><br><br> 



		<form action="password.php" method="post" name="userform" id="userform"> 
<INPUT name='password'  type='text' size='40'  maxlength='10' value=''> <b>plaintext password</b><br> 
<input type=submit value="create hash">
</form>


<?php

$password=$_POST['password'];

function securepw($password){		// make (at least) passwords uncrackable  -- FIX THIS !! if the same salt is applied it doesn't secure anything !  store unique salt for every user
	$salt = md5("1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");   
	$hash = $password . $salt;
	for ( $i = 0; $i < 10000; $i++ ) {
		$hash = md5( $hash );
	}
	echo "password hash: <b> $hash </b>";
}
securepw($password);
?>
























</div>
</div>





</body>
</html>