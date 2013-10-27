<?php

require_once("common.php");
require_once("db.php");

header("Content-Type: text/html; charset=UTF-8");

function remark2array($remark) {
	return array($remark->id,$remark->first_line,$remark->last_line,$remark->style,$remark->content);
}

function remarks2array($remarks) {
	$r = array();
	foreach($remarks as $remark)
		$r[] = remark2array($remark);
	return $r;
}

function srv_get_remarks() {
	try{
		list($file_id,$id) = validate_request_vars(array(
			'file_id'=>array('is_int'=>1),
			'id'=>array('is_int'=>1)
			)); 
	} catch(Exception $e) {
		echo json_encode(array());
		return;
	}

	$file_id = json_decode($file_id);
	$id = json_decode($id);

	if($id==-1) {
		if(!has_access2file(login_id(),$file_id,RO)) {
			echo json_encode(array());
			return;
		}
		$remarks = remarks($file_id);
		echo json_encode(remarks2array($remarks));
		return;
	}
	else {
		if(!has_access2remark(login_id(),$id,RO)) {
			echo json_encode(array("nok","permission denied"));
			return;
		}
		$remark = R::load("rl_remark",$id);
		if(!$remark->id) {
			echo json_encode(array("nok","remark not found"));
			return;
		}
		echo json_encode(array(remark2array($remark)));
		return;
	}
}

function srv_new_remark() {
	try{
		list($file_id,$first_line,$last_line) = validate_request_vars(array(
			'file_id'=>array('is_int'=>1),
			'first_line'=>array('is_int'=>1),
			'last_line'=>array('is_int'=>1)
		)); 
	} catch(Exception $e) {
		echo json_encode(array("nok","invalid params"));
		return;
	}

	$file_id = json_decode($file_id);
	$first_line = json_decode($first_line);
	$last_line = json_decode($last_line);

	if(!has_access2file(login_id(),$file_id,RW)) {
		echo json_encode(array("nok","permission denied"));
		return;
	}
	$remark = R::dispense("rl_remark");
	$remark->file_id = $file_id;
	$remark->content = "";
	$remark->first_line = $first_line;
	$remark->last_line = $last_line;
	$remark->style = "light";
	$id = R::store($remark);
	echo json_encode(array("ok",$id));
}

function srv_del_remark() {
	try{
		list($id) = validate_request_vars(array(
			'id'=>array('is_int'=>1)
		)); 
	} catch(Exception $e) {
		echo json_encode(array("nok","invalid params"));
		return;
	}

	$id = json_decode($id);
	if(!has_access2remark(login_id(),$id,RW)) {
		echo json_encode(array("nok","permission denied"));
		return;
	}
	$remark = R::load( "rl_remark", $id);
	if(!$remark->id) {
		echo json_encode(array("nok","remark not found"));
		return;
	}
	R::trash($remark);
	echo json_encode(array("ok"));
}

function srv_change_style() {
	try{
		list($id,$style) = validate_request_vars(array(
			'id'=>array('is_int'=>1),
			'style'=>array()
		)); 
	} catch(Exception $e) {
		echo json_encode(array("nok","invalid params"));
		return;
	}

	$id = json_decode($id);
	$style = json_decode($style);
	if(!has_access2remark(login_id(),$id,RW)) {
		echo json_encode(array("nok","permission denied"));
		return;
	}
	$remark = R::load( "rl_remark", $id);
	if(!$remark->id) {
		echo json_encode(array("nok","remark not found"));
		return;
	}
	$remark->style = $style;
	R::store($remark);
	echo json_encode(array("ok"));
}

function srv_change_content() {
	try {
		list($id,$content) = validate_request_vars(array(
			'id'=>array('is_int'=>1),
			'content'=>array('illegal'=>array(),'max'=>1024)
		)); 
	} catch(Exception $e) {
		echo json_encode(array("nok","invalid params"));
		return;
	}

	$id = json_decode($id);
	$content = json_decode($content);
	if(!has_access2remark(login_id(),$id,RW)) {
		echo json_encode(array("nok","permission denied"));
		return;
	}
	$remark = R::findOne("rl_remark", "id=?",array($id));
	if(!is_object($remark)) {
		echo json_encode(array("nok","remark not found"));
		return;
	}
	$remark->content = $content;
	R::store($remark);
	echo json_encode(array("ok"));
}

function srv_change_file_remark() {
	try {
		list($file_id,$content) = validate_request_vars(array(
			'file_id'=>array('is_int'=>1),
			'content'=>array('illegal'=>array(),'max'=>1024)
		)); 
	} catch(Exception $e) {
		echo json_encode(array("nok","invalid params"));
		return;
	}

	$file_id = json_decode($file_id);
	$content = json_decode($content);

	if(!has_access2file(login_id(),$file_id,RW)) {
		echo json_encode(array("nok","permission denied"));
		return;
	}

	$file = R::findOne("rl_file", "id=?",array($file_id));
	if(!is_object($file)) {
		echo json_encode(array("nok","file not found"));
		return;
	}
	$file->remark = $content;
	R::store($file);
	echo json_encode(array("ok"));
}


try {
	exec_action(array("srv_get_remarks","srv_new_remark","srv_del_remark","srv_change_style","srv_change_content","srv_change_file_remark"));
} catch(Exception $e) {
	echo json_encode(array("nok","action failed"));
	exit();
}

?>
