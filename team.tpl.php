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
function add_td($team_name,$review_id,$content,$htmlentities=1) {
	echo "<td>";
	echo "<a href=\"review.php?team=$team_name&amp;review_id=$review_id\">";
	if($htmlentities == 1)
		echo htmlentities($content);
	else
		echo $content;
	echo "</a></td>";
}
function add_tr($tpl,$review,$is_alt) {
	if($is_alt==0) echo "<tr>";
	else echo "<tr class=\"alt\">";

	$total=$tpl->nbr_of_files[$review->id];
	$reviewed=$tpl->nbr_of_files_reviewed[$review->id];
	if(($reviewed != 0) && ($reviewed == $total))
		add_td($tpl->team,$review->id,"<img src=\"images/ok-full.png\" alt=\"check\" />",0);
	else
		add_td($tpl->team,$review->id,"<img src=\"images/empty.png\" alt=\"\" />",0);

	add_td($tpl->team,$review->id,review_short_description($review->description));
	if(!strcmp($tpl->login,$tpl->team)) {
		if($review->teamspermissions == 4) $val = "ro";
		else if($review->teamspermissions == 6) $val = "rw";
		else $val = "--";
		echo "<td>";
		echo "<input id=\"toggle_teams_permissions_".$review->id."\" type=\"button\" value=\"$val\" onclick=\"toggle_permissions($review->id,'teams')\" />";
		echo "</td>";
		if($review->otherpermissions == 4) $val = "ro";
		else if($review->otherpermissions == 6) $val = "rw";
		else $val = "--";
		echo "<td>";
		echo "<input id=\"toggle_other_permissions_".$review->id."\" type=\"button\" value=\"$val\" onclick=\"toggle_permissions($review->id,'other')\" />";
		echo "</td>";
	}
	add_td($tpl->team,$review->id,date('Y-m-d H:i:s',$review->timestamp));
	add_td($tpl->team,$review->id,$tpl->nbr_of_files_reviewed[$review->id]." of ".$tpl->nbr_of_files[$review->id]." files");
	if(!strcmp($tpl->login,$tpl->team)) {
		echo "<td class=\"delete\"><a href=\"team.php?team=$tpl->team&amp;action=action_del_review&amp;review_id=$review->id\"><img src=\"images/delete.png\" onclick=\"return confirm_delete()\" alt=\"delete\" /></a></td>";
	}
	echo "</tr>";
}
?>

<?php
$this->title = "Team";
$this->display("header.tpl.php");
?>

<body>

<div id="center">

<div id="header">
	<div id="logo"><img src='images/team.png' alt="Team" /></div>
	<div class="menuitem"><a href="logout.php"><img src='images/logout.png' title="<?php if(!strcmp($this->login,"")) echo "Login"; else echo "Logout"; ?>" alt="logout" /></a></div>
	<div class="menuitem"><a href="team.php?team=<?php echo $this->team?>"><img src='images/home.png' title="Home" alt="team" /></a></div>
	<?php
		if(!strcmp($this->login,"")) echo "<div class=\"menuitem\"><i>You are not logged in.</i></div>";
		else if(strcmp($this->login,$this->team)) echo "<div class=\"menuitem\"><i>You are logged in as <a href=\"team.php?team=$this->login\">team " . htmlentities($this->login) . "</a>.</i></div>";
	?>
		
	<h1>Team <?php echo htmlentities($this->team); ?></h1>
	<p>
	<?php
		if($this->nbr_of_reviews == 1)
			echo "The team has 1 review.";
		else
			echo "The team has $this->nbr_of_reviews reviews.";
	?>
	</p>
	<?php if(strlen($this->error)) echo "<p class=\"error\">Error: ".htmlentities($this->error)."</p>"; ?>
	<?php if(strlen($this->info)) echo "<p class=\"info\">Success: ".htmlentities($this->info)."</p>"; ?>
</div>

<div id="content">
	<table id="hover">
	<tr>
		<th>  </th><th style="width:60%">Review description</th>
			<?php
			if(!strcmp($this->login,$this->team))
				echo "<th>Teams access</th><th>Public access</th>";
			?>
		<th>Timestamp</th><th>Reviewed</th>
	</tr>
	<?php
		$i = 0;
		foreach ($this->reviews as $review) {
			add_tr($this,$review,$i%2);	
			++$i;
		}
	?>
	</table>

	<div id="prevnext">
<?php
	if(!$this->offset == 0)
		echo "<a href=\"team.php?team=$this->team&amp;offset=$this->offset_prev&amp;count=$this->count\"><img src='images/prev.png' alt='prev' /></a>";
	else
		echo "<img src='images/empty.png' alt='' />";
	if($this->offset != $this->offset_next)
		echo "<a href=\"team.php?team=$this->team&amp;offset=$this->offset_next&amp;count=$this->count\"><img src='images/next.png' alt='next' /></a>";
?>
	</div>
</div>

<div id="sidebar">
<?php
if(!strcmp($this->login,$this->team)) {
?>
	<form action="review.php" method="post" enctype="multipart/form-data">
		<div><input type="hidden" name="team" value="<?php echo "$this->team"; ?>" /></div>
		<div><input type="hidden" name="action" value="action_add_review" /></div>
		<div><label>Description</label></div>
		<div><textarea name="review_description" rows="5" cols="40"></textarea></div>
		<div><label>Files</label></div>
		<div><input size="15%" type="file" name="code[]" class="multi" /></div>
		<div><input type="submit" name="upload" value="Add Review" /></div>
	</form>
<?php
} else echo "Actions disabled (read-only mode).";
?>
</div>

	<?php $this->display("footer.tpl.php"); ?>
</div>

<script src="js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="js/jquery.MultiFile.pack.js" type="text/javascript"></script>
<script src="js/jquery.json-2.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
	function confirm_delete() {
		var agree=confirm("Are you sure you want to delete this review (including all files and comments)?");
		if (agree)
			return true ;
		else
			return false ;
	}

	function toggle_permissions(review_id,which) {
		old_state = $("#toggle_"+which+"_permissions_"+review_id).val();
		$.post('team-ajax.php', {"action":"srv_toggle_permissions","which":$.toJSON(which),"review_id": $.toJSON(review_id)}, function(res){
			var response = $.evalJSON(res);
			if(response[0]=="ok") {
				var p = "--";
				if(response[1] == 4) p = "ro";
				else if(response[1] == 6) p = "rw";
				$("#toggle_"+which+"_permissions_"+review_id).val(p);
			}
			else {
				$("#toggle_"+which+"_permissions_"+review_id).val(old_state);
				alert(response[1]);
			}
		});
	}

</script>

</body>

</html>
