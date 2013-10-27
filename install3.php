<?php
require_once("install_common.php");
check_not_installed_yet();
?>

<html>
<head>
</head>
<body>
	<h1> Mysql database setup </h1>
	<h2> Create database and configure user access </h2>
	Create database 'reviewlead' and -for security reasons- configure a single user who can access this database from localhost. You can use the command line or phpMyAdmin.<br />
	In case of web hosting you should use the tools that are provided by the hosting company. <br />

	<h3> command line example </h3>
	E.g. command line:
		<pre>
			$ mysql -u root -p
			mysql> create database reviewlead;
			mysql> grant usage on *.* to reviewlead_user@localhost identified by 'reviewlead_password';
			mysql> grant all privileges on reviewlead.* to reviewlead_user@localhost ;
			mysql> quit
		</pre>
	In order to test this setup try:
		<pre>
			$ mysql -u reviewlead_user -p reviewlead
			mysql> quit
		</pre>

	<h2> Submit mysql username and password </h2>
	<p>
	Database username and password will be saved in the cfg/ directory.
	</p>
	<p>
	<form action="install4.php" method="post">
		Database name: <input type="text" name="dbname"></input><br />
		Database username: <input type="text" name="dbuser"></input><br />
		Database password: <input type="password" name="dbpassword"></input><br />
		<input type="submit" value="Submit"></input>
	</form>
	</p>

</body>
</html>
