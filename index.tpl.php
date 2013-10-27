<?php
$this->title = "Login";
$this->display("header.tpl.php");
?>

<body>

<div id="login">
	<img src="images/logo-352x77.png" alt="Reviewlead" />
	<form action="index.php" method="post">
		<div>
			<input type="hidden" name="action" value="action_login" />
		</div>
		<div>
			<label>Team:</label>
			<input type="text" name="teamname" />
		</div>

		<div>
			<label>Password:</label>
			<input type="password" name="password" />
		</div>

		<div>
			<label>&nbsp;</label>
			<input type="submit" value="Login" />
		</div>
	</form>
	<?php if(strlen($this->info)) echo "<div class=\"info\">".htmlentities($this->info)."</div>"; ?>
	<?php if(strlen($this->error)) echo "<div class=\"error\">Error: " . htmlentities($this->error)."</div>"; ?>
	<div class="version">v1.3.1</div>
</div>

</body>

</html>
