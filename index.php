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

require_once("install_common.php");
if(!is_installed()) {
	header('Location: install.php');
	exit();
}

require_once("common.php");

function action_login()
{
	global $illegal_chars;
	list($teamname,$password) = validate_request_vars(array(
		'teamname'=>array('show'=>'Team','illegal'=>$illegal_chars),
		'password'=>array('show'=>'Password','illegal'=>$illegal_chars)
	));

	$password = md5($password);

	$team = R::findOne("rl_team", "name=? and password=?",array($teamname,$password));
	if(!is_object($team) || strcmp($team->name,$teamname))
		throw new Exception("wrong Team or Password");

	$_SESSION['team_name']=$team->name;
	$_SESSION['team_id']=$team->id;
	$_SESSION['IP']=$_SERVER['REMOTE_ADDR'];

	session_regenerate_id(true);
	if(!strcmp($teamname,"admin"))
		header('Location: admin.php');
	else
		header("Location: team.php?team=$team->name");
	exit();
}

try {
	exec_action(array("action_login"));
	if(is_logged_in()) {
		session_regenerate_id(true);
		header("Location: team.php?team=".login_name());
		exit();
	}
} catch(Exception $e) {
	$tpl->error = $e->getMessage();
}

$team = R::findOne("rl_team", "name=? and password=?",array('admin',md5('admin')));
if(is_object($team) && 0==strcmp($team->name,'admin')) {
	$tpl->error = "please change admin password";
}

$tpl->display("index.tpl.php");

?>
