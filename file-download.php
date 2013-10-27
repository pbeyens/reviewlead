<?php
require_once("common.php");
require_once("db.php");

try {
	list($file_id) = validate_request_vars(array(
		'file_id'=>array('is_int'=>1)
	)); 
	if(!has_access2file(login_id(),$file_id,RO))
		throw new Exception("permission denied");
	$file = R::load("rl_file",$file_id);
	if(!$file->id)
		throw new Exception("file not found");
} catch(Exception $e) {
	redirect2index();
	exit(1);
}

header('Content-disposition: attachment; filename='.get_filename($file->name));
header('Content-type: text/plain');
readfile($file->name);
?>
