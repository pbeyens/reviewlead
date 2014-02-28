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
