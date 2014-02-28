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

function team_by_name($name)
{
	$team = R::findOne("rl_team", "name=?",array($name));
	if(!is_object($team))
		throw new Exception("team '$name' not found");
	return $team;
}

function team_del($team_id)
{
	$team = R::load( "rl_team", $team_id);
	if(!$team->id)
		throw new Exception("team not found");
	$reviews = reviews($team_id);
	foreach($reviews as $review)
		review_del($review->id);
	R::trash($team);
}

function team_add($name,$password)
{
	$password = trim($password);
	$team = R::findOne("rl_team", "name=?",array($name));
	if(is_object($team))
		throw new Exception("team '$name' already exists");
	$team = R::dispense("rl_team");
	$team->name = trim($name);
	$team->password = md5($password);
	return R::store($team);
}

function team_change_password($team_id,$newpassword,$newpassword_confirm) {
	$newpassword = trim($newpassword);
	$newpassword_confirm = trim($newpassword_confirm);
	if(strcmp($newpassword,$newpassword_confirm))
		throw new Exception("password mismatch");
	$team = R::load( "rl_team", $team_id);
	if(!$team->id)
		throw new Exception("team not found");
	$team->password = md5($newpassword);
	R::store($team);
}

function teams()
{
	$teams = array();
	try {
		$teams = R::find("rl_team", "name!='Admin'");
	} catch(Exception $e) {
		var_dump($e->getMessage());
	}
	return $teams;
}

function review_add($team_id,$description)
{
	$review = R::dispense("rl_review");
	$review->team_id = $team_id;
	$review->description = $description;
	$review->timestamp = time();
	return R::store($review);
}

function review_del($review_id)
{
	$review = R::load("rl_review",$review_id);
	if(!$review->id)
		throw new Exception("review not found");
	$files = files($review_id);
	foreach($files as $file)
		file_del($file->id);
	R::trash($review);
}

function reviews($team_id)
{
	$reviews = array();
	try {
		$reviews = R::find("rl_review", "team_id=? ORDER BY timestamp desc",array($team_id));
	} catch(Exception $e) {
		var_dump($e->getMessage());
	}
	return $reviews;
}

function reviews_limit($team_id,$offset,$count,$access_type)
{
	if(login_id()==$team_id)
		$permissions = " ";
	else if(login_id()==0)
		$permissions = " and otherpermissions >= " . strval($access_type) . " ";
	else {
		$permissions = " and teamspermissions >= " . strval($access_type) . " ";
	}

	$reviews = array();
	try {
		$reviews = R::find("rl_review", "team_id=?".$permissions." ORDER BY timestamp desc limit ?,? ",array($team_id,$offset,$count));
	} catch(Exception $e) {
		var_dump($e->getMessage());
	}
	return $reviews;
}

function file_add($review_id,$filename)
{
	$file = R::findOne("rl_file", "review_id=? and name=?",array($review_id,$filename));
	if(is_object($file))
		throw new Exception("file already exists");
	$file = R::dispense("rl_file");
	$file->review_id = $review_id;
	$file->state = 0;
	$file->name = $filename;
	return R::store($file);
}

function file_del($file_id)
{
	$file = R::load("rl_file", $file_id);
	if(!$file->id)
		throw new Exception("file not found");
	$remarks = remarks($file_id);
	foreach($remarks as $remark)
		R::trash($remark);
	unlink($file->name);
	R::trash($file);
}

function files($review_id)
{
	$files = array();
	try {
		$files = R::find("rl_file", "review_id=?",array($review_id));
	} catch(Exception $e) {
		var_dump($e->getMessage());
	}
	return $files;
}

function remarks($file_id)
{
	$remarks = array();
	try {
		$remarks = R::find("rl_remark", "file_id=?",array($file_id));
	} catch(Exception $e) {
		var_dump($e->getMessage());
	}
	return $remarks;
}

function reviews_count($team_id,$access_type)
{
	if(login_id()==$team_id)
		$permissions = " ";
	else if(login_id()==0)
		$permissions = " and otherpermissions >= " . strval($access_type) . " ";
	else {
		$permissions = " and teamspermissions >= " . strval($access_type) . " ";
	}

	$r = R::getAll( 'select count(*) as nbr_of_reviews from rl_review where team_id = :uid ' . $permissions, array('uid'=>$team_id) );
	return intval($r[0]['nbr_of_reviews']);
}

function remarks_count($file_id)
{
	$r = R::getAll( 'select count(*) as nbr_of_remarks from rl_remark where file_id = :fid', array('fid'=>$file_id) );
	return intval($r[0]['nbr_of_remarks']);
}

function files_count($review_id)
{
	$r = R::getAll( 'select count(*) as nbr_of_files from rl_file where review_id = :rid', array('rid'=>$review_id) );
	return intval($r[0]['nbr_of_files']);
}

function files_reviewed_count($review_id)
{
	$r = R::getAll( 'select count(*) as nbr_of_files from rl_file where review_id = :rid and state = 1', array('rid'=>$review_id) );
	return intval($r[0]['nbr_of_files']);
}

function has_access2review($login_id,$review_id,$access_type=RW) {
	$review = R::load("rl_review",$review_id);
	if(!$review->id)
		return 0;
	/* my team: full access */
	if($review->team_id == $login_id)
		return 1;
	if($login_id == 0)
		return ($review->otherpermissions >= $access_type);
	else
		return ($review->teamspermissions >= $access_type);
	return 0;
}

function has_access2file($team_id,$file_id,$access_type) {
	$file = R::load("rl_file",$file_id);
	if(!$file->id)
		return 0;
	return has_access2review($team_id,$file->review_id,$access_type);
}

function has_access2file2($team_id,$file_id,$review_id,$access_type) {
	$file = R::load("rl_file",$file_id);
	if(!$file->id)
		return 0;
	if($review_id != $file->review_id)
		return 0;
	return has_access2review($team_id,$file->review_id,$access_type);
}

function has_access2remark($team_id,$remark_id,$access_type) {
	$remark = R::load("rl_remark",$remark_id);
	if(!$remark->id)
		return 0;
	return has_access2file($team_id,$remark->file_id,$access_type);
}

?>
