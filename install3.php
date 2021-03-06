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
