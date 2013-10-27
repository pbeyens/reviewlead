<?php

require_once("common.php");
require_once("db.php");

header("Content-Type: text/html; charset=UTF-8");

function srv_toggle_state() {
	try{
		list($review_id,$file_id) = validate_request_vars(array(
			'review_id'=>array('is_int'=>1),
			'file_id'=>array('is_int'=>1)
		)); 
	} catch(Exception $e) {
		echo json_encode(array("nok"));
		return;
	}

	$review_id = json_decode($review_id);
	$file_id = json_decode($file_id);

	if(!has_access2file2(login_id(),$file_id,$review_id,RW)) {
		echo json_encode(array("nok","permission denied"));
		return;
	}
	$file = R::load("rl_file",$file_id);
	if(!$file->id) {
		echo json_encode(array("nok","file not found"));
		return;
	}
	if($file->state == 0)
		$file->state = 1;
	else
		$file->state = 0;
	R::store($file);
	echo json_encode(array("ok",$file->state));
}

try {
	exec_action(array("srv_toggle_state"));
} catch(Exception $e) {
	echo json_encode(array("nok","action failed"));
	exit();
}

?>
