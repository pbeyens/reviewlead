<?php

//error_reporting(E_ALL & E_STRICT);
error_reporting(0);
ini_set('display_errors','Off');
ini_set('session.use_only_cookies',1);
ini_set('session.use_trans_ip',0);

ini_set('file_uploads','1');
ini_set('upload_max_filesize','500K');

function redirect2index()
{
	session_destroy();
	header('Location: index.php');
	exit();
}

function exec_action($options)
{
	global $illegal_chars;
	if(!isset($_REQUEST['action']))
		return FALSE;
	list($action) = validate_request_vars(array(
		'action'=>array('options'=>$options,'illegal'=>$illegal_chars)
	));
	return call_user_func($action);
}

function validate_request_vars($key_info) {
	$values = array();
	foreach(array_keys($key_info) as $key) {
		if(!isset($key_info[$key]['show'])) $keyshow = $key;
		else $keyshow = $key_info[$key]['show'];
		if(!isset($key_info[$key]['is_int'])) $is_int = 0;
		else $is_int = $key_info[$key]['is_int'];

		if(!isset($_REQUEST[$key]))
			throw new Exception("key '$key' not found");
		$val = trim($_REQUEST[$key]);
		if(get_magic_quotes_gpc() == 1)
			$val = stripslashes($val);
		if(!is_string($val))
			throw new Exception("value of '$keyshow' not a string");

		if($is_int == 1) {
			if(strcmp($val,strval(intval($val))))
				throw new Exception("value of '$keyshow' must be an integer");
			$ival = intval($val);
			if(isset($key_info[$key]['min']) && $ival < $key_info[$key]['min'])
				throw new Exception("value of '$keyshow' too small");
			if(isset($key_info[$key]['max']) && $ival > $key_info[$key]['max'])
				throw new Exception("value of '$keyshow' too big");
			if(isset($key_info[$key]['options']) && !in_array($ival,$key_info[$key]['options'],true))
				throw new Exception("value of '$keyshow' not a valid option");
			$values[] = $ival;
		}
		else {
			if(!isset($key_info[$key]['min'])) $valmin = 1;
			else $valmin = $key_info[$key]['min'];
			if(!isset($key_info[$key]['max'])) $valmax = 30;
			else $valmax = $key_info[$key]['max'];
			if(!isset($key_info[$key]['illegal'])) $illegal = array();
			else $illegal = $key_info[$key]['illegal'];

			if(isset($key_info[$key]['options']) && !in_array($val,$key_info[$key]['options'],true))
				throw new Exception("value of '$keyshow' not a valid option");

			if(strlen($val)>$valmax || strlen($val)<$valmin)
				throw new Exception("value of '$keyshow' too short or too long");
			foreach($illegal as $c)
				if(strpos($val,$c) !== FALSE)
					throw new Exception("value of '$keyshow' contains an illegal character");
			$values[] = $val;
		}
	}
	return $values;
}

function get_filename($f)
{
	$b = basename($f);
	sscanf($b, "%d_%d_%[^$]s",$user_id,$review_id,$filename);
	$filename = substr($filename,0,-3);
	return htmlentities($filename);
}

function is_logged_in() {
	if(!isset($_SESSION['team_id']) || !isset($_SESSION['team_name']) || !strcmp($_SESSION['team_name'],'admin'))
		return 0;
	else
		return 1;
}

function login_id() {
	if(!isset($_SESSION['team_id']) || !isset($_SESSION['team_name']))
		return 0;
	else
		return $_SESSION['team_id'];
}

function login_name() {
	if(!isset($_SESSION['team_id']) || !isset($_SESSION['team_name']))
		return "";
	else
		return $_SESSION['team_name'];
}

function review_short_description($description)
{
	if(!strlen($description))
		return "--";
	$d = preg_split('/\n/',$description);
	$max_len = 45;
	if(count($d) == 1) {
		if(strlen($d[0]) > $max_len) $descr = substr($d[0],0,$max_len-3) . "...";
		else $descr = $d[0];
	}
	else if (count($d) > 1) {
		if(strlen($d[0]) > $max_len) $descr = substr($d[0],0,$max_len-3) . "...";
		else $descr = $d[0] . "...";
	}
	return $descr;
}

/* sessions */
session_start(); 
if(isset($_SESSION['IP']) && isset($_SERVER['REMOTE_ADDR']) && $_SESSION['IP']!=$_SERVER['REMOTE_ADDR']) {
	redirect2index();
}

/* redbean */
require("rb.php");
require_once("install_common.php");
require_once(dbconfigfile());

try {
	$dbname = dbname();
	R::setup("mysql:host=localhost;dbname=$dbname",dbuser(),dbpassword());
	R::freeze();
}
catch(Exception $e) {
	print("<br />Unable to connect to database: ");
	var_dump($e->getMessage());
	exit(1);
}

/* savant */
require_once 'Savant3.php';
$tpl = new Savant3();
$tpl->info = "";
$tpl->error = "";

$illegal_chars = array(' ',';',',','\'','"');

/* permissions */
define(RO,4);
define(RW,6);

?>
