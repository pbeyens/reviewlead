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
check_not_installed_yet();

$document_root = $_SERVER["DOCUMENT_ROOT"];
$reviewlead_path = str_replace("/install2.php","",$_SERVER["SCRIPT_FILENAME"]);
$reviewlead_url = $_SERVER['SERVER_NAME'].str_replace("/install2.php","",$_SERVER["SCRIPT_NAME"]);
?>
<html>
<head>
</head>
<body>
	<h1> Security: deny access to 'cfg/' and 'uploads/' directories </h1>
	<h2> Test </h2>
		Test your security by browsing to <a href="http://<?php echo $reviewlead_url; ?>/cfg" target="_blank">http://<?php echo $reviewlead_url; ?>/cfg</a> and <a href="http://<?php echo $reviewlead_url; ?>/uploads" target="_blank">http://<?php echo $reviewlead_url; ?>/uploads</a>. <br />
		If you see 'forbidden' access errors then both directories are secure.
	<h2> Solution </h2>
	<h3> Option 1 (best): edit Apache configuration file </h2>
		Edit the Apache configuration file (e.g. /etc/httpd/httpd.conf) and add the following statements if <?php echo $reviewlead_path;?> is your installation directory:
		<pre>
			&lt;Directory "<?php echo $reviewlead_path;?>/cfg"&gt;
				deny from all
			&lt;/Directory&gt;
			&lt;Directory "<?php echo $reviewlead_path;?>/uploads"&gt;
				deny from all
			&lt;/Directory&gt;
		</pre>
	<h3> Option 2: make sure the provided .htaccess files are used by Apache </h2>
	Reviewlead provides a .htaccess file in the 'cfg/' and 'uploads/' directories. The .htaccess files are only applied if the Apache configuration is correct (see /etc/httpd/httpd.conf). Verify the following statements for Reviewlead's installation directory (e.g. <?php echo $reviewlead_path;?>) or installation top-directory (e.g. <?php echo $document_root;?>):
		<pre>
			&lt;Directory "<?php echo $reviewlead_path;?>"&gt;
				...
				AccessFileName .htaccess # note: this is the default
				...
				AllowOverride ALL
				...
			&lt;/Directory&gt;
		</pre>

	<hr />
	<i>Proceed when your setup is secure. If you ignore this item then the software will work but you'll have serious security holes.</i>
	<form action="install3.php" method="post">
		<input type="submit" value="Proceed"></input>
	</form>
</body>
</html>
