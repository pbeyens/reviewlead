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
