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
