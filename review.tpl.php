<?php
function add_td($team,$review_id,$file_id,$content,$htmlentities=1,$file_state=0) {
	if($file_state == 1)
		echo "<td id=\"file_state_".$file_id."\">";
	else
		echo "<td>";
	echo "<a href=\"file-review.php?team=$team&amp;review_id=$review_id&amp;file_id=$file_id\">";
	if($htmlentities == 1)
		echo htmlentities($content);
	else
		echo $content;
	echo "</a></td>";
}
function add_tr($tpl,$review,$file,$is_alt) {
		if($is_alt==0) echo "<tr id=\"file_row_$file->id\">";
		else echo "<tr id=\"file_row_$file->id\" class=\"alt\">";

		if($file->state == 1)
			add_td($tpl->team,$review->id,$file->id,"<img src=\"images/ok-full.png\" alt=\"check\" />",0,1);
		else
			add_td($tpl->team,$review->id,$file->id,"<img src=\"images/empty.png\" alt=\"\" />",0,1);

		add_td($tpl->team,$review->id,$file->id,get_filename($file->name));

		if($tpl->remarks_count[$file->id] == 1) $comments = "comment";
		else $comments = "comments";
		add_td($tpl->team,$review->id,$file->id,$tpl->remarks_count[$file->id]." $comments");
		echo "<td>";
		echo "<input type=\"button\" value=\"download\" onclick=\"file_download($file->id)\" />";
		echo "</td>";

		if(has_access2review(login_id(),$tpl->review->id,RW)) {
			if($file->state == 1) $val = "uncheck";
			else $val = "check";
			echo "<td>";
			echo "<input id=\"toggle_".$file->id."\" type=\"button\" value=\"$val\" onclick=\"toggle_reviewed($file->id)\" />";
			echo "</td>";
	
			echo "<td class=\"delete\"><a href=\"review.php?team=$tpl->team&amp;action=action_delete_file&amp;review_id=$review->id&amp;file_id=$file->id\"><img src=\"images/delete.png\" onclick=\"return confirm_delete()\" alt=\"delete\" /></a></td>";
		}
		echo "</tr>";
}
?>

<?php
$this->title = "Review";
$this->display("header.tpl.php");
?>

<body>
<div id="center">

<div id="header">
	<div id="logo"><img src='images/review.png' alt="Review" /></div>
	<div class="menuitem"><a href="logout.php"><img src='images/logout.png' title="<?php if(!strcmp($this->login,"")) echo "Login"; else echo "Logout"; ?>" alt="logout" /></a></div>
	<div class="menuitem"><a href="team.php?team=<?php echo $this->team?>"><img src='images/home.png' title="Home" alt="team" /></a></div>
	<?php
		if(!strcmp($this->login,"")) echo "<div class=\"menuitem\"><i>You are not logged in.</i></div>";
		else if(strcmp($this->login,$this->team)) echo "<div class=\"menuitem\"><i>You are logged in as <a href=\"team.php?team=$this->login\">team " . htmlentities($this->login) . "</a>.</i></div>";
	?>
		
	<h1>Review page</h1>
	<p>
	Created on <b><?php echo ''.date('Y-m-d',$this->review->timestamp); ?></b>
	at <b><?php echo ''.date('H:i:s',$this->review->timestamp); ?>:</b><br />
	</p>
	<p>
	<?php
		$lines = explode("\n", $this->review->description);
		$rows = count($lines);
	?>
	<textarea rows="<?php echo $rows; ?>" readonly="readonly" cols="40" style="border: 0px solid #cccccc;background: #F3F2ED;"><?php echo htmlentities($this->review->description); ?></textarea>
	</p>
	<?php if(strlen($this->error)) echo "<p class=\"error\">Error: ".htmlentities($this->error)."</p>"; ?>
	<?php if(strlen($this->info)) echo "<p class=\"info\">Success: ".htmlentities($this->info)."</p>"; ?>
</div>

<div id="content">
	<table id="hover">
		<tr><th>  </th><th style="width:50%">File</th><th>Comments</th><th>Download</th>
			<?php
			if(has_access2review(login_id(),$this->review->id,RW)) {
			?>
				<th>Update Status</th>
			<?php
			}
			?>
		</tr>
		<?php
		$i=0;
		foreach ($this->files as $file) {
			add_tr($this,$this->review,$file,$i%2);
			++$i;
		}
		?>
	</table>
</div>

<div id="sidebar">
<?php
if(has_access2review(login_id(),$this->review->id,RW)) {
?>
	<form action="review.php" method="post" enctype="multipart/form-data">
		<div><label>Description</label></div>
		<div><input type="hidden" name="team" value="<?php echo $this->team; ?>" /></div>
		<div><input type="hidden" name="action" value="action_update_review" /></div>
		<div><input type="hidden" name="review_id" value="<?php echo $this->review->id; ?>" /></div>
		<div><textarea name="review_description" rows="5" cols="40"><?php echo htmlentities($this->review->description); ?></textarea></div>
		<div><label>Files</label></div>
		<div><input size="15%" type="file" name="code[]" class="multi" /></div>
		<div><input type="submit" name="upload" value="Update" /></div>
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
//<![CDATA[
function confirm_delete() {
	var agree=confirm("Are you sure you want to delete this file (including all comments)?");
	if (agree)
		return true ;
	else
		return false ;
}

function toggle_reviewed(file_id) {
	old_state = $("#toggle_"+file_id).val();
	$("#toggle_"+file_id).val("saving");
	$.post('review-ajax.php', {"action":"srv_toggle_state","review_id": $.toJSON(<?php echo $this->review->id ?>),"file_id":$.toJSON(file_id)}, function(res){
		var response = $.evalJSON(res);
		if(response[0]=="ok" && response[1]==0) {
			$("#file_state_"+file_id+" > a").empty().html("<img src='images/empty.png' alt='' />");
			$("#toggle_"+file_id).val("check");
		}
		else if(response[0]=="ok" && response[1]==1) {
			$("#file_state_"+file_id+" > a").empty().html("<img src='images/ok-full.png' alt='check' />");
			$("#toggle_"+file_id).val("uncheck");
		}
		else {
			$("#toggle_"+file_id).val(old_state);
			alert(response[1]);
		}
	});
}

function file_download(id) {
	window.location = "file-download.php?file_id="+id;
}

//]]>
</script>

</body>
</html>
