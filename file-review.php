<?php

require_once("common.php");
require_once("db.php");

try {
	list($team_name,$review_id,$file_id) = validate_request_vars(array(
		'team'=>array('illegal'=>$illegal_chars),
		'review_id'=>array('is_int'=>1),
		'file_id'=>array('is_int'=>1)
	));
	if(!has_access2file2(login_id(),$file_id,$review_id,RO))
		redirect2index();

	$team = team_by_name($team_name);
	$review = R::load("rl_review",$review_id);
	if(!$review->id)
		throw new Exception("review not found");
	$file = R::load("rl_file",$file_id);
	if(!$file->id)
		throw new Exception("file not found");

	$fcontent = htmlentities(file_get_contents($file->name));

	/* jquery-ui selectable */
	$lines = explode("\n",$fcontent);
	$result = array();
	$result[] = "<ol id='selectable'>";
	$index = 0;
	foreach($lines as $line) {
		$result[] = "<li id='L$index' class='ui-widget-content'>".$line."\n"."</li>";
		++$index;
	}
	$result[] = "</ol>";
	$fcontent = implode($result);

} catch(Exception $e) {
	$tpl->error = "Action failed (reason: " . $e->getMessage() . ").";
}

$tpl->team = $team->name;
$tpl->login = login_name();
$tpl->review = $review;
$tpl->review_id = $review_id;
$tpl->file = $file;
$tpl->fcontent = $fcontent;
$tpl->display("file-review.tpl.php");
?>
