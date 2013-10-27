<?php

require_once("common.php");
require_once("db.php");

if(login_id()!=1 || strcmp(login_name(),"admin"))
	redirect2index();

function action_change_password()
{
	global $tpl,$illegal_chars;
	list($newpassword,$newpassword_confirm) = validate_request_vars(array(
		'new_password'=>array('show'=>'New password','illegal'=>$illegal_chars),
		'new_password_confirm'=>array('show'=>'Confirm','illegal'=>$illegal_chars)
	)); 
	team_change_password($_SESSION['team_id'],$newpassword,$newpassword_confirm);
	$tpl->info = "password changed";
}

function action_add_team()
{
	global $tpl,$illegal_chars;
	list($teamname,$teampassword) = validate_request_vars(array(
		'new_team'=>array('show'=>'New team','illegal'=>$illegal_chars),
		'team_password'=>array('show'=>'Password','illegal'=>$illegal_chars)
	));
	team_add($teamname,$teampassword);
	$tpl->info = "team '$teamname' added";
}

function action_del_team()
{
	global $tpl;
	list($team_id) = validate_request_vars(array(
		'team_id'=>array('is_int'=>1)
	));
	team_del($team_id);
	$tpl->info = "team deleted";
}

function action_change_team_password()
{
	global $tpl,$illegal_chars;
	list($team_id,$newteampassword) = validate_request_vars(array(
		'team_id'=>array('is_int'=>1),
		'new_team_password'=>array('show'=>'New team password','illegal'=>$illegal_chars)
	));
	team_change_password($team_id,$newteampassword,$newteampassword);
	$tpl->info = "team password changed";
}

try {
	exec_action(array("action_change_password","action_add_team","action_del_team","action_change_team_password"));
} catch(Exception $e) {
	$tpl->error = $e->getMessage();
}

$team = R::findOne("rl_team", "name=? and password=?",array('admin',md5('admin')));
if(is_object($team) && 0==strcmp($team->name,'admin')) {
	if(strlen($tpl->error)) $tpl->error .= ", ";
	$tpl->error .= "please change admin password";
}

$tpl->teams = teams();
$tpl->name = $_SESSION['team_name'];
$tpl->display("admin.tpl.php");

?>
