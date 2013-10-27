<?php

require_once("common.php");
require_once("db.php");

header("Content-Type: text/html; charset=UTF-8");

function srv_toggle_permissions() {

	try{
		list($review_id,$which) = validate_request_vars(array(
			'review_id'=>array('is_int'=>1),
			'which'=>array()
		)); 
	} catch(Exception $e) {
		echo json_encode(array("nok","invalid params"));
		return;
	}

	$review_id = json_decode($review_id);
	$which = json_decode($which);

	$review = R::load("rl_review",$review_id);
	if(!$review->id) {
		echo json_encode(array("nok","invalid review"));
		return;
	}
	if($review->team_id != login_id()) {
		echo json_encode(array("nok","permission denied"));
		return;
	}
	if(!strcmp($which,"teams")) {
		if($review->teamspermissions == 0) $next = 4;
		else if($review->teamspermissions == 4) $next = 6;
		else $next = 0;
		$review->teamspermissions = $next;
	}
	else if (!strcmp($which,"other")) {
		if($review->otherpermissions == 0) $next = 4;
		else if($review->otherpermissions == 4) $next = 6;
		else $next = 0;
		$review->otherpermissions = $next;
	}
	else {
		echo json_encode(array("nok","invalid review"));
		return;
	}
	R::store($review);
	echo json_encode(array("ok",$next));
}

try {
	exec_action(array("srv_toggle_permissions"));
} catch(Exception $e) {
	echo json_encode(array("nok","action failed"));
	exit();
}

?>
