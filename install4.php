<?php
require_once("install_common.php");
check_not_installed_yet();

echo "
<html>
<head>
</head>
<body>
";

echo "Retrieving db name, db username and password...<br />";
isset($_REQUEST["dbname"]) or die("'dbname' not found");
isset($_REQUEST["dbuser"]) or die("'dbuser' not found");
isset($_REQUEST["dbpassword"]) or die("'dbpassword' not found");

$dbname = $_REQUEST["dbname"];
$dbuser = $_REQUEST["dbuser"];
$dbpassword = $_REQUEST["dbpassword"];

function retry($msg) 
{
	global $dbuser;
	global $dbpassword;
	echo "<p>$msg</p>";
	echo "<form action=\"install4.php\" method=\"post\">";
	echo "<br />Database name: <input type=\"text\" name=\"dbname\" value=\"$dbname\"></input>";
	echo "<br />Database username: <input type=\"text\" name=\"dbuser\" value=\"$dbuser\"></input>";
	echo "<br />Database password: <input type=\"password\" name=\"dbpassword\"></input>";
	echo "<input type=\"submit\" value=\"Retry\"></input>";
	echo "</form>";
	exit(1);
}

echo "Reading mysql db query...<br />";
$sqlfile = 'cfg/rl_team.sql';
$fd = fopen($sqlfile,'r') or retry("Can't open file $sqlfile!");
$team_sql = fread($fd, filesize($sqlfile));
fclose($fd);
$sqlfile = 'cfg/rl_review.sql';
$fd = fopen($sqlfile,'r') or retry("Can't open file $sqlfile!");
$review_sql = fread($fd, filesize($sqlfile));
fclose($fd);
$sqlfile = 'cfg/rl_file.sql';
$fd = fopen($sqlfile,'r') or retry("Can't open file $sqlfile!");
$file_sql = fread($fd, filesize($sqlfile));
fclose($fd);
$sqlfile = 'cfg/rl_remark.sql';
$fd = fopen($sqlfile,'r') or retry("Can't open file $sqlfile!");
$remark_sql = fread($fd, filesize($sqlfile));
fclose($fd);

echo "Writing database configuration file...<br />";
$dbdata = "<?php
function dbname() { return '$dbname'; }
function dbuser() { return '$dbuser'; }
function dbpassword() { return '$dbpassword'; }
?>
";
$dbaccessfile = dbconfigfile();
$fd = fopen($dbaccessfile,'w') or retry("Can't open file '$dbaccessfile'!");
fwrite($fd,$dbdata) or retry("Can't write to file '$dbaccessfile'!");
fclose($fd);

echo "Executing mysql db query...<br />";
try {
	require_once("common.php");
	require_once("db.php");
	R::exec($team_sql);
	R::exec($review_sql);
	R::exec($file_sql);
	R::exec($remark_sql);
} catch(Exception $e) {
	echo "<p><b>Fatal</b>: unable to configure database.</p>";
	var_dump($e->getMessage());
	exit(1);
}

echo "Creating/modifying admin user...<br />";
try {
	$user = R::findOne("rl_team", "name=?",array('admin'));
	if(!is_object($user))
		team_add('admin','admin');
	else {
		team_change_password($user->id,'admin','admin');
		echo "<p><b>Warning</b>: 'admin' user password reset to 'admin'.</p>";
	}
} catch(Exception $e) {
	echo "<p><b>Fatal</b>: unable to configure 'admin' user.</p>";
	var_dump($e->getMessage());
	exit(1);
}

?>
<b>
<p>
Installation successful!
</p>
<p>
Now login with username <b>admin</b> and password <b>admin</b>. Please change password immediately!
</p>
</b>

<?php session_destroy(); ?>
<form action="index.php" method="post">
	<input type="submit" value="Login"></input>
</form>
</body>
</html>
