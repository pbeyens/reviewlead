<?php

require_once("common.php");
require_once("db.php");

function action_del_review()
{
	global $tpl;
	list($review_id) = validate_request_vars(array(
		'review_id'=>array('is_int'=>1)
	)); 
	$review = R::load("rl_review",$review_id);
	if(!$review->id)
		throw new Exception("review not found");
	if($review->team_id != login_id())
		throw new Exception("permission denied");
	review_del($review_id);
	$tpl->info = "review deleted";
}

try {
	list($team_name) = validate_request_vars(array(
		'team'=>array('illegal'=>$illegal_chars)
	)); 
	$team = team_by_name($team_name);
	exec_action(array("action_del_review"));
} catch(Exception $e) {
	redirect2index();
	exit(1);
	$tpl->error = $e->getMessage();
}

try{
	list($offset,$count) = validate_request_vars(array(
		'offset'=>array('is_int'=>1,'min'=>0),
		'count'=>array('is_int'=>1,'min'=>1)
	)); 
} catch(Exception $e) {
	$offset = 0;
	$count = 10;
}

if(isset($_SESSION['tpl_error']) && strlen($_SESSION['tpl_error'])) {
	if(strlen($tpl->error))
		$tpl->error = $_SESSION['tpl_error'] . ", " . $tpl->error;
	else
		$tpl->error = $_SESSION['tpl_error'];
	$_SESSION['tpl_error'] = "";
	unset($_SESSION['tpl_error']);
}

$tpl->nbr_of_reviews = reviews_count($team->id,RO);

$tpl->team = $team->name;
$tpl->login = login_name();
$tpl->reviews = reviews_limit($team->id,$offset,$count,RO);
foreach($tpl->reviews as $review) {
	$tpl->nbr_of_files[$review->id] = files_count($review->id);
	$tpl->nbr_of_files_reviewed[$review->id] = files_reviewed_count($review->id);
}
$tpl->offset = $offset;
if($offset+$count >= $tpl->nbr_of_reviews)
	$tpl->offset_next = $offset;
else
	$tpl->offset_next = $offset+$count;
if($offset-$count < 0)
	$tpl->offset_prev = 0;
else
	$tpl->offset_prev = $offset-$count;
$tpl->count = $count;
$tpl->display("team.tpl.php");

?>
