<?php

function dbconfigfile() {
	return 'cfg/dbaccess.php';
}

function check_not_installed_yet()
{
	if(is_installed()) {
		echo "Installation already done.";
		exit(1);
	}
}

function is_installed()
{
	if(file_exists(dbconfigfile()))
		return 1;
	else
		return 0;
}

?>
