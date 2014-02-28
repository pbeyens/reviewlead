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

