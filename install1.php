<?php
/*
Copyright (c) 2014, Pieter Beyens (pieter.beyens@rtos.be, http://www.rtos.be)
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
Neither the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

require_once("install_common.php");
check_not_installed_yet();

function is_php_version_ok() {
	if(strnatcmp(phpversion(),'5.2') >= 0) 
		return 1;
	else 
		return 0;
}

function is_dir_ok($dir) {
	if(is_dir($dir) && is_readable($dir) && is_writable($dir))
		return 1;
	else
		return 0;
}

function dir_check($dir) {
	$ok = is_dir_ok($dir);
	if($ok == 1) {
		echo "'$dir' directory is readable and writable.";
		return;
	}
	else {
		echo "<b>'$dir' directory is not readable and/or not writable.</b> <br />";
		$web_user = posix_getpwuid(posix_getuid());
		$dir_user = posix_getpwuid(fileowner($dir));
		$web_user_name = $web_user['name'];
		$dir_user_name = $dir_user['name'];
		if(0 != strcmp($dir_user_name,$web_user_name)) {
			echo "Reason: your web user is not the owner of the '$dir' directory. <br />";
			echo "Your web user is '$web_user_name' while the '$dir' directory is owned by '$dir_user_name'. <br />";
		}
		else {
			echo "Reason: unknown. <br />";
		}
		if(!strlen($web_user_name)) $web_user_name = 'apache-user';
		echo "Solution1 (best): change the owner of the '$dir' to the web user (e.g. sudo chown $web_user_name $dir).<br />";
		echo "Solution2 (might have security issues): make the '$dir' dir readable/writable for anyone (e.g. sudo chmod 777 $dir).<br />";
		retry();
	}
}

function retry() 
{
	echo "<form action=\"install1.php\">";
	echo "<input type=\"submit\" value=\"Retry\"></input>";
	echo "</form>";
	exit(1);
}

?>
<html>
<head>
</head>
<body>
	<h1> Requirements </h1>
	<?php
		if(is_php_version_ok() == 1) {
				echo "<p>Your php version is >= 5.2.</p>";
		}
		else {
			echo "<p>";
			echo "Php version must be >= 5.2 (".phpversion()." found). <br />";
			echo "Solution: please upgrade php.";
			echo "</p>";
			retry();
		}
		if(ini_get('safe_mode')) {
			echo "<p>";
			echo "Php safe mode must be disabled. <br />";
			echo "Solution: disable safe mode.";
			echo "</p>";
			retry();
		}
		else {
			echo "<p>Php safe mode is disabled (which is good).</p>";
		}

		echo "<p>";
		dir_check('uploads');
		echo "</p>";
		echo "<p>";
		dir_check('cfg');
		echo "</p>";
	?>
	<hr />
	<i>All requirements have been met.</i>
	<form action="install2.php" method="post">
		<input type="submit" value="Proceed"></input>
	</form>
</body>
</html>
