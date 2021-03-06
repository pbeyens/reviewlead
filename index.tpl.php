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
$this->title = "Login";
$this->display("header.tpl.php");
?>

<body>

<div id="login">
	<img src="images/logo-352x77.png" alt="Reviewlead" />
	<form action="index.php" method="post">
		<div>
			<input type="hidden" name="action" value="action_login" />
		</div>
		<div>
			<label>Team:</label>
			<input type="text" name="teamname" />
		</div>

		<div>
			<label>Password:</label>
			<input type="password" name="password" />
		</div>

		<div>
			<label>&nbsp;</label>
			<input type="submit" value="Login" />
		</div>
	</form>
	<?php if(strlen($this->info)) echo "<div class=\"info\">".htmlentities($this->info)."</div>"; ?>
	<?php if(strlen($this->error)) echo "<div class=\"error\">Error: " . htmlentities($this->error)."</div>"; ?>
	<div class="version">v2.0.0</div>
</div>

</body>

</html>
