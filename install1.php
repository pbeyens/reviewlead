<?php

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
