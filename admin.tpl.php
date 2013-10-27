<?php
$this->title = "Admin";
$this->display("header.tpl.php");
?>

<body>

<div id="center">

<div id="header">
	<div id="logo">
		<img src='images/admin.png' alt="Admin" />
	</div>

	<div class="menuitem">
		<a href="logout.php"><img src='images/logout.png' title="Logout" alt="logout" /></a>
	</div>

	<h1>Admin page</h1>

	<p>
	<?php
		$nbr = count($this->teams);
		if($nbr==1)
			echo "You have $nbr team.";
		else
			echo "You have $nbr teams.";
	?>
	</p>

	<?php if(strlen($this->error)) echo "<p class=\"error\">Error: ".htmlentities($this->error)."</p>"; ?>
	<?php if(strlen($this->info)) echo "<p class=\"info\">Success: ".htmlentities($this->info)."</p>"; ?>
</div>

<div id="content">
	<table>
	<tr><th>Team Name</th><th>Update Password</th></tr>
	<?php
	$i = 0;
	foreach ($this->teams as $team):
		if ($i == 0) {
			$i = 1;
			echo "<tr>";
		} else {
			$i = 0;
			echo "<tr class=\"alt\">";
		}
	?>
			<td><b><?php echo htmlentities($team->name); ?></b></td>
			<td>
				<form action="admin.php" method="post">
					<div>
					<input type="hidden" name="action" value="action_change_team_password" />
					<input type="hidden" name="team_id" value='<?php echo "$team->id"; ?>' />
					<input type="password" name="new_team_password" />
					<input type="submit" value="Change team password" />
					</div>
				</form>
			</td>
			<td class="delete">
				<form action="admin.php" method="post">
					<div>
					<input type="hidden" name="action" value="action_del_team" />
					<input type="hidden" name="team_id" value='<?php echo "$team->id"; ?>' />
					<input type="image" src="images/delete.png" alt="delete" onclick="return confirm_delete()" />
					</div>
				</form>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
</div>
			
<div id="sidebar">
	<form action="admin.php" method="post">
		<div>
			<input type="hidden" name="action" value="action_add_team" />
		</div>
		<div>
			<label>New team:</label>
			<input type="text" name="new_team" />
		</div>
		<div>
			<label>Password:</label>
			<input type="password" name="team_password" />
		</div>
		<div>
			<input type="submit" value="Add team" />
		</div>
	</form>
	<hr />
	<form action="admin.php" method="post">
		<div><input type="hidden" name="action" value="action_change_password" /></div>
		<div><label>New password:</label></div>
		<div><input type="password" name="new_password" /></div>
		<div><label>Confirm:</label></div>
		<div><input type="password" name="new_password_confirm" /></div>
		<div><input type="submit" value="Change admin password" /></div>
	</form>
</div>

<?php
	$this->display("footer.tpl.php");
?>
</div>

<script type="text/javascript">
	function confirm_delete() {
		var agree=confirm("Are you really really sure you want to delete this team (including all reviews, files and comments)?");
		if (agree)
                    return true ;
                else
                    return false ;
	}
</script>

</body>

</html>

