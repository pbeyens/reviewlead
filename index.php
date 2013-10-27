<?php

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
