<?php

require_once("common.php");
require_once("db.php");

function action_add_review()
{
	global $team,$tpl,$review;
	list($description) = validate_request_vars(array(
		'review_description'=>array('show'=>'Description','min'=>0,'max'=>200)
	)); 
	if(login_id() != $team->id) {
		$_SESSION['tpl_error'] = "permission denied";
		header("Location: team.php?team=$team->name");
		exit();
	}
	$review_id = review_add(login_id(),$description);
	$review = R::load("rl_review",$review_id);
	if(!$review->id)
		throw new Exception("review not found");
	upload();
	$tpl->info = "review created";
}

function action_update_review()
{
	global $team,$tpl,$review;
	list($new_description) = validate_request_vars(array(
		'review_description'=>array('show'=>'Description','min'=>0,'max'=>200)
	)); 
	if(!has_access2review(login_id(),$review->id,RW)) {
		$_SESSION['tpl_error'] = "permission denied";
		header("Location: review.php?team=$team->name&review_id=$review->id");
		exit();
	}
	if(strcmp($review->description,$new_description)) {
		$review->description = $new_description;
		R::store($review);
	}
	upload();
	$tpl->info = "review updated";
}

function upload()
{
	global $team,$tpl,$review;
	if(!isset($_POST['upload']))
		return;
	$uploaddir = 'uploads/';
	foreach ($_FILES["code"]["error"] as $key => $error)
	{
		$name = $_FILES["code"]["name"][$key];
		if ($error == UPLOAD_ERR_OK)
		{
			$tmp_name = $_FILES["code"]["tmp_name"][$key];
			$uploadfile = $uploaddir . $review->team_id . "_" . $review->id . "_" . basename($name) . ".rl";

			if (!move_uploaded_file($tmp_name, $uploadfile)) {
				if(strlen($tpl->error)) $tpl->error = $tpl->error . ', ';
				$tpl->error = $tpl->error . "file '$name' cannot be uploaded (file cannot be moved)";
				unlink($tmp_name);
				continue;
			}

			try{
				file_add($review->id,$uploadfile);
				if(strlen($tpl->info)) $tpl->info = $tpl->info . ', ';
				$tpl->info = $tpl->info . "file '$name' uploaded";
			} catch(Exception $e) {
				if(strlen($tpl->error)) $tpl->error = $tpl->error . ', ';
				$tpl->error = $tpl->error . $e->getMessage() . " ('$name')";
				continue;
			}
		}
		else if($error == UPLOAD_ERR_INI_SIZE) {
			if(strlen($tpl->error)) $tpl->error = $tpl->error . ', ';
			$tpl->error = $tpl->error . "file '$name' cannot be uploaded (max upload size exceeded)";
		}
		else if($error == UPLOAD_ERR_FORM_SIZE) {
			if(strlen($tpl->error)) $tpl->error = $tpl->error . ', ';
			$tpl->error = $tpl->error . "file '$name' cannot be uploaded (max form size exceeded)";
		}
		else if($error == UPLOAD_ERR_PARTIAL) {
			if(strlen($tpl->error)) $tpl->error = $tpl->error . ', ';
			$tpl->error = $tpl->error . "file '$name' cannot be uploaded (only partially uploaded)";
		}
		else if($error == UPLOAD_ERR_NO_FILE) {
			/* default case if no file was added to the form */
		}
		else if($error == UPLOAD_ERR_NO_TMP_DIR) {
			if(strlen($tpl->error)) $tpl->error = $tpl->error . ', ';
			$tpl->error = $tpl->error . "file '$name' cannot be uploaded (no tmp folder)";
		}
		else if($error == UPLOAD_ERR_CANT_WRITE) {
			if(strlen($tpl->error)) $tpl->error = $tpl->error . ', ';
			$tpl->error = $tpl->error . "file '$name' cannot be uploaded (can't write)";
		}
		else if($error == UPLOAD_ERR_EXTENSION) {
			if(strlen($tpl->error)) $tpl->error = $tpl->error . ', ';
			$tpl->error = $tpl->error . "file '$name' cannot be uploaded (ext stopped upload)";
		}
		else {
			if(strlen($tpl->error)) $tpl->error = $tpl->error . ', ';
			$tpl->error = $tpl->error . "file '$name' cannot be uploaded (unknown error $error)";
		}
	}
	if(strlen($tpl->info))
		$_SESSION['tpl_info'] = $tpl->info;
	if(strlen($tpl->error))
		$_SESSION['tpl_error'] = $tpl->error;
	header("Location: review.php?team=$team->name&review_id=$review->id");
	exit(1);
}

function action_delete_file()
{
	global $team,$tpl,$review;
	list($file_id) = validate_request_vars(array(
		'file_id'=>array('is_int'=>1)
	)); 
	if(!has_access2file2(login_id(),$file_id,$review->id,RW))
		throw new Exception("permission denied");
	file_del($file_id);
	$tpl->info = "file deleted";
}

try {
	list($team_name) = validate_request_vars(array(
		'team'=>array('illegal'=>$illegal_chars)
	)); 
	$team = team_by_name($team_name);
} catch(Exception $e) {
	$team = null;
	$tpl->error = $e->getMessage();
}

try {
	list($review_id) = validate_request_vars(array(
		'review_id'=>array('is_int'=>1)
	)); 
	if(!has_access2review(login_id(),$review_id,RO))
		redirect2index();
	$review = R::load("rl_review",$review_id);
	if(!$review->id)
		throw new Exception("review not found");
} catch(Exception $e) {
	$review = null;
}

try {
	exec_action(array("action_add_review","action_update_review","action_delete_file"));
} catch(Exception $e) {
	$tpl->error = $e->getMessage();
}

if($review == null) {
	if(strlen($tpl->error)) $prev_error = "Error: " . $tpl->error . ".";
	else $prev_error = "";
	$tpl->error = "Unexpected failure: not possible to get review object. " . $prev_error;
	$tpl->display("failure.tpl.php");
	exit(1);
}

if(isset($_SESSION['tpl_info']) && strlen($_SESSION['tpl_info'])) {
	if(strlen($tpl->info))
		$tpl->info = $_SESSION['tpl_info'] . ", " . $tpl->info;
	else
		$tpl->info = $_SESSION['tpl_info'];
	$_SESSION['tpl_info'] = "";
	unset($_SESSION['tpl_info']);
}

if(isset($_SESSION['tpl_error']) && strlen($_SESSION['tpl_error'])) {
	if(strlen($tpl->error))
		$tpl->error = $_SESSION['tpl_error'] . ", " . $tpl->error;
	else
		$tpl->error = $_SESSION['tpl_error'];
	$_SESSION['tpl_error'] = "";
	unset($_SESSION['tpl_error']);
}

$tpl->team = $team->name;
$tpl->login = login_name();
$tpl->review = $review;
$tpl->files = files($review->id);
$tpl->remarks_count = array();
foreach($tpl->files as $file) {
	$tpl->remarks_count[$file->id] = remarks_count($file->id);
	if(strlen($file->remark)) ++$tpl->remarks_count[$file->id];
}
$tpl->display("review.tpl.php");

?>
