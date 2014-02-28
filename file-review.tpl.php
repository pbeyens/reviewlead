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
$this->title = get_filename($this->file->name);
$this->display("header2.tpl.php");
?>

<body onload="onload()">
<div id="header">
	<pre><span id="loading" style="position:absolute; left:30px; top:123px;"><i>loading...</i></span></pre>
	<div id="logo"><img src='images/pencil.png' alt="" /></div>
	<div class="menuitem"><a href="logout.php"><img src='images/logout.png' title="<?php if(!strcmp($this->login,"")) echo "Login"; else echo "Logout"; ?>" alt="logout" /></a></div>
	<div class="menuitem"><a href="team.php?team=<?php echo $this->team; ?>"><img src='images/home.png' title="Home" alt="team" /></a></div>
	<div class="menuitem"><a href="review.php?team=<?php echo $this->team; ?>&amp;review_id=<?php echo $this->review->id; ?>"><img src='images/back.png' title="Review" alt="review" /></a></div>
	<?php
		if(!strcmp($this->login,"")) echo "<div class=\"menuitem\"><i>You are not logged in.</i></div>";
		else if(strcmp($this->login,$this->team)) echo "<div class=\"menuitem\"><i>You are logged in as <a href=\"team.php?team=$this->login\">team " . htmlentities($this->login) . "</a>.</i></div>";
	?>

	<h1><?php echo get_filename($this->file->name) . " [".review_short_description($this->review->description)."]"; ?></h1>
	<div id="fileremark">
	<form action="file-review.php" method="post">
		<div><label>File comment</label></div>
		<div><textarea id="fileremark-content" name="file_remark" cols="" rows=""><?php echo htmlentities($this->file->remark); ?></textarea></div>
		<?php
		if(has_access2review(login_id(),$this->review->id,RW)) {
		?>
		<div><input id="fileremark-update" type="button" value="update" onclick="update_file_remark();" /></div>
		<?php
		}
		?>
	</form>
	</div>
	<div style="clear:both; display:block;"></div>

	<?php if(strlen($this->error)) echo "<p class=\"error\">Error: ".htmlentities($this->error)."</p>"; ?>
	<?php if(strlen($this->info)) echo "<p class=\"info\">Success: ".htmlentities($this->info)."</p>"; ?>
</div>

<div style="clear:both; float:left; display:block; width:75%">
		
	<pre class="prettyprint" style="font-size: 100%; line-height: 1; display: block;"><?php echo $this->fcontent; ?></pre>

	<script type="text/javascript" src="js/google-code-prettify/prettify.js"></script>
	<script type='text/javascript' src='js/jquery-1.7.1.min.js' charset='utf-8'></script>
	<script type='text/javascript' src='js/jquery-ui-1.8.16.custom.min.js' charset='utf-8'></script>
	<script type="text/javascript" src="js/jquery.qtip-1.0.0-rc3.min.js"></script>
	<script type="text/javascript" src="js/jquery.json-2.3.min.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		var result;
		$(function() {
			$( "#selectable" ).selectable({filter: 'li'},{
				stop: function() {
					<?php
					if(has_access2review(login_id(),$this->review->id,RW)) {
					?>
					result = $( "#select-result" ).empty();
					var first = true;
					$( ".ui-selected", this ).each(function() {
						index = $( "#selectable li" ).index( this );
						if(index >= 0) {
							if(first)
								result.append( index );
							else
								result.append( "-" + (index) );
							first = false;
						}
					});
					$("#selected").qtip('destroy');
					$("#selected").replaceWith($("#selected").contents());
					remove_qtip_and_bg("new");
					$(".ui-selected:first").wrapInner("<div id=qtip_new></div>");
					var button_new = "<input type=\"button\" value=\"New comment\" onclick=\"new_remark();\" />";
					var del_new = "<img src=\"images/delete.png\" style='float: right;' onclick=\"remove_new_qtip();\" alt=\"client-side del\" />";
					$("#qtip_new").qtip({
						content: button_new+del_new,
						show: {delay: 0, when: false, ready: true},
						hide: false,
						position: {
							corner: { target: 'rightMiddle',tooltip: 'leftMiddle'}
						},
						style: { name: 'dark', tip: 'leftMiddle' }});
					}
					<?php
					}
					?>
					});
		});
		//]]>
		</script>
		<script type="text/javascript">
			//<![CDATA[
			var styles = new Array("blue","green","red","dark","cream","light");

			var remarks = new Array();
			var cur_remark = -1;

			function remarks_arr_length() {
				return remarks.length;
			}
			function remark_compare(r1,r2){
				if(r1.first_line < r2.first_line) return -1;
				else if(r1.first_line > r2.first_line) return 1;
				else {
					if(r1.last_line < r2.last_line) return -1;
					else if(r1.last_line > r2.last_line) return 1;
				}
				return 0;
			}
			function remarks_arr_sort() {
				remarks.sort(remark_compare);
			}
			function remarks_arr_add(remark,do_sort) {
				remarks.push(remark);
				if(do_sort) remarks_arr_sort();
			}
			function remarks_arr_get_by_index(index) {
				if(remarks.length == 0 || index >= remarks.length) return -1;
				return remarks[index];
			}
			function remarks_arr_get_by_id(id,update) {
				for(var i=0;i<remarks.length;++i) {
					if(remarks[i].id == id) {
						if(update) cur_remark = i;
						return remarks[i];
					}
				}
				return -1;
			}
			function remarks_arr_del(id) {
				for(var i=0;i<remarks.length;++i) {
					if(remarks[i].id == id) {
						remark = remarks.splice(i,1);
						delete remark;
						if(cur_remark >= remarks.length) {
							if(remarks.length) cur_remark = 0;
							else cur_remark = -1;
						}
						return;
					}
				}
			}
			function remarks_arr_get_cur_remark() {
				if(cur_remark<0 || cur_remark >= remarks.length) return -1;
				return remarks[cur_remark];
			}
			function remarks_arr_get_next_remark() {
				if(remarks.length == 0) return -1;
				if(cur_remark < 0 || cur_remark >= (remarks.length-1)) return remarks[0];
				else return remarks[cur_remark+1];
			}
			function remarks_arr_get_prev_remark() {
				if(remarks.length == 0) return -1;
				if(cur_remark <= 0) return remarks[remarks.length-1];
				else return remarks[cur_remark-1];
			}


			function update_toolbar() {
				$("#toolbar_remark_position").empty().html((cur_remark+1)+"/"+remarks_arr_length());
			}
			function show_prev_remark() {
				var remark = remarks_arr_get_prev_remark();
				if(remark == -1) return;
				add_focus(remark.id);
				if((obj = document.getElementById("qtip_"+remark.id)) && obj != null){
					window.scrollTo(0, obj.offsetTop-100);
				}
			}
			function show_next_remark() {
				var remark = remarks_arr_get_next_remark();
				if(remark == -1) return;
				add_focus(remark.id);
				if((obj = document.getElementById("qtip_"+remark.id)) && obj != null){
					window.scrollTo(0, obj.offsetTop-100);
				}
			}
			function new_remark() {
				$("#loading").show();
				var t = $("#select-result").text();
				var split = t.split("-");

				$.post('file-review-ajax.php', {"action": "srv_new_remark","file_id":$.toJSON(<?php echo $this->file->id; ?>),"first_line":$.toJSON(parseInt(split[0])),"last_line":$.toJSON(parseInt(split[split.length-1]))}, function(res){
					var response = $.evalJSON(res);
					//alert(JSON.stringify(response));
					if(response[0]=="ok") {
						remove_new_qtip();
						show_remarks(response[1]);
					}
					else alert(response[1]);
					$("#loading").hide();
				});
			}
			function to_remark(id,first_line,last_line,style,content) {
				var remark = {
					id: id,
					first_line: first_line,
					last_line: last_line,
					style: style,
					content: content,
					to_class: function () { return this.style+"_"+this.id; }
				}
				return remark;
			}
			function remove_focus() {
				for(var i=0;i<styles.length;++i) {
					$("li.focus_"+styles[i]+"_one").removeClass("focus_"+styles[i]+"_one");
					$("li.focus_"+styles[i]+"_first").removeClass("focus_"+styles[i]+"_first");
					$("li.focus_"+styles[i]+"_last").removeClass("focus_"+styles[i]+"_last");
					$("li.focus_"+styles[i]+"_middle").removeClass("focus_"+styles[i]+"_middle");
				}
			}
			function add_focus(id) {
				var remark = remarks_arr_get_by_id(id,1);
				remove_focus();
				if(remark.first_line == remark.last_line)
					$("li#L"+remark.first_line).addClass("focus_"+remark.style+"_one");
				else {
					$("li#L"+remark.first_line).addClass("focus_"+remark.style+"_first");
					$("li#L"+remark.first_line).nextUntil("#L"+remark.last_line).addClass("focus_"+remark.style+"_middle");
					$("li#L"+remark.last_line).addClass("focus_"+remark.style+"_last");
				}
				add_background(remark.id);
				$("#qtip_"+id).qtip('focus');
				$("#qtip_"+id).qtip('api').updatePosition();
				update_toolbar();
			}
			function remove_class(first_line,last_line,cssclass) {
				if(first_line == last_line)
					$("li#L"+first_line).removeClass(cssclass);
				else {
					$("li#L"+first_line).removeClass(cssclass);
					$("li#L"+first_line).nextUntil("#L"+last_line).removeClass(cssclass);
					$("li#L"+last_line).removeClass(cssclass);
				}
			}
			function add_class(first_line,last_line,cssclass) {
				if(first_line == last_line)
					$("li#L"+first_line).addClass(cssclass);
				else {
					$("li#L"+first_line).addClass(cssclass);
					$("li#L"+first_line).nextUntil("#L"+last_line).addClass(cssclass);
					$("li#L"+last_line).addClass(cssclass);
				}
			}
			function remove_background(first_line,last_line) {
				//alert("rm:"+first_line+"-"+last_line);
				for(var i=0;i<styles.length;++i)
					remove_class(first_line,last_line,styles[i]);
			}
			function add_background(id) {
				var remark = remarks_arr_get_by_id(id,0);
				//alert(JSON.stringify(remark));
				remove_background(remark.first_line,remark.last_line);
				//alert("add:"+remark.first_line+"-"+remark.last_line);
				add_class(remark.first_line,remark.last_line,remark.style);
			}
			function update_background(first_line,last_line) {
				for(var i=0;i<remarks_arr_length();++i) {
					var remark = remarks_arr_get_by_index(i);
					if((remark.first_line>=first_line) && (remark.first_line<=last_line) ||
						(remark.last_line>=first_line) && (remark.last_line<=last_line) ||
						(remark.first_line<=first_line) && (remark.last_line>=last_line) ||
						(first_line<=remark.first_line) && (last_line>=remark.last_line))
						add_background(remark.id);
				}
			}
			function get_style_select_option(current_style, option_style) {
				var o = "<option value="+option_style;
				if(current_style == option_style) o += " selected>";
				else o += ">";
				o += option_style + "</option>";
				return o;
			}
			function add_qtip(id) {
				var remark = remarks_arr_get_by_id(id,0);
				var front = "<img src='images/front.png' style='float: left; margin: 0 5px 2px 0;' onclick=\"add_focus("+remark.id+");\" alt='move to front' />";
				var del_client = "<img src='images/delete.png' style='float: right;' onclick=\"del_remark_client("+remark.id+");\" alt='client-side del' />";

				<?php
				if(has_access2review(login_id(),$this->review->id,RW)) {
				?>
				var style_select = "<select style='float: left; margin: 0 5px 0 0;' id='style_select_qtip_"+remark.id+"' onchange=\"change_style("+remark.id+");\">";
				style_select += get_style_select_option(remark.style,"blue");
				style_select += get_style_select_option(remark.style,"green");
				style_select += get_style_select_option(remark.style,"red");
				style_select += get_style_select_option(remark.style,"dark");
				style_select += get_style_select_option(remark.style,"cream");
				style_select += get_style_select_option(remark.style,"light");
				style_select += "</select>";

				var edit = "<img src='images/edit.png' style='float: left;' alt='edit' onclick=\"edit_remark("+remark.id+");\" alt='edit'/>";
				var del = "<img src='images/delete_red.png' style='float: right;' onclick=\"del_remark("+remark.id+");\" alt='delete' />";
				<?php
				} else echo "var style_select=\"actions disabled (ro mode)\"; var edit=\"\"; var del=\"\";";
				?>
				$("li#L"+remark.first_line).wrapInner("<div id=qtip_"+remark.id+"></div>")
					$("div#qtip_"+remark.id).qtip({
						content: front+style_select+edit+del+del_client+"<div id=\"edit_"+remark.id+"\"><textarea class='qtipcomment' readonly=\"readonly\">"+remark.content+"</textarea></div>",
							show: {delay: 0, when: false, ready: true},
							hide: false,
							adjust: {scroll: true, resize: true},
							position: {
								corner: { target: 'rightMiddle',tooltip: 'leftMiddle'}
							},
							style: { width: { min: 260}, name: remark.style, tip: 'leftMiddle' }});
			}
			function add_remark(remark,do_sort) {
				remarks_arr_add(remark,do_sort);
				add_background(remark.id);
				add_qtip(remark.id);
				add_focus(remark.id);
			}
			function remove_new_qtip() {
				$("#selected").qtip('destroy');
				$("#selected").replaceWith($("#selected").contents());
				$(".ui-selected").removeClass("ui-selected");
				$("#select-result").empty();
				remove_qtip_and_bg("new");
			}
			function remove_qtip_and_bg(id) {
				$("#qtip_"+id).qtip('destroy');
				$("#qtip_"+id).replaceWith($("#qtip_"+id).contents());
				if(id != "new") {
					var remark = remarks_arr_get_by_id(id,0);
					var first_line = remark.first_line;
					var last_line = remark.last_line;
					remove_background(remark.first_line,remark.last_line);
					remarks_arr_del(id);
					remove_focus();
					update_background(first_line,last_line);
					var remark = remarks_arr_get_cur_remark();
					if(remark != -1)
						add_focus(remark.id);
				}
				update_toolbar();
			}
			function change_content(id) {
				$("#loading").show();
				var new_content = $("#textarea_"+id).val();
				$.post('file-review-ajax.php', {"action": "srv_change_content","id":$.toJSON(id),"content":$.toJSON(new_content)}, function(res){
					var response = $.evalJSON(res);
					if(response[0]=="ok") {
						remove_qtip_and_bg(id);
						show_remarks(id);
					}
					else alert(response[1]);
					$("#loading").hide();
				});
			}
			function cancel_edit(id) {
				var remark = remarks_arr_get_by_id(id,0);
				$("#edit_"+remark.id).empty().html("<textarea class='qtipcomment' readonly=\"readonly\" onclick=\"edit_remark("+remark.id+");\">"+remark.content+"</textarea>");
				$("#qtip_"+id).qtip('api').updatePosition();
			}
			function edit_remark(id) {
				var remark = remarks_arr_get_by_id(id,0);
				$("#edit_"+remark.id).empty().html(" \
					<form> \
					<textarea id=\"textarea_"+remark.id+"\" class='qtipcomment'>"+remark.content+"</textarea> \
					<input type=\"button\" value=\"submit\" onclick=\"change_content("+remark.id+");\" /> \
					<input type=\"button\" value=\"cancel\" onclick=\"cancel_edit("+remark.id+");\" /> \
					</form>");
				add_focus(remark.id);
				$("#textarea_"+remark.id).focus();
			}
			function del_remark_client(id) {
				remove_qtip_and_bg(id);
			}
			function del_remark(id) {
				$("#loading").show();
				$.post('file-review-ajax.php', {"action": "srv_del_remark","file_id":$.toJSON(<?php echo $this->file->id; ?>),"id":$.toJSON(id)}, function(res){
					var response = $.evalJSON(res);
					if(response[0]=="ok")
						remove_qtip_and_bg(id);
					else alert(response[1]);
					$("#loading").hide();
				});
			}
			function change_style(id) {
				$("#loading").show();
				var new_style = $("#style_select_qtip_"+id+" option:selected").val();
				$.post('file-review-ajax.php', {"action": "srv_change_style","style":$.toJSON(new_style),"id":$.toJSON(id)}, function(res){
					var response = $.evalJSON(res);
					if(response[0]=="ok") {
						remove_qtip_and_bg(id);
						show_remarks(id);
					}
					else alert(response[1]);
					$("#loading").hide();
				});
			}
			function show_remarks(id){
				$("#loading").show();
				$.post('file-review-ajax.php', {"action": "srv_get_remarks","file_id":$.toJSON(<?php echo $this->file->id ?>),"id":$.toJSON(id)}, function(res){
					var response = $.evalJSON(res);
					for(var i=0;i < response.length; ++i) {
						var remark = to_remark(response[i][0],
							parseInt(response[i][1]),
							parseInt(response[i][2]),
							response[i][3],
							response[i][4]);
						var do_sort = 1;
						if(id==-1) do_sort = 0;
						add_remark(remark,do_sort);
					}
					if(id == -1) {
						remarks_arr_sort();
						var remark = remarks_arr_get_by_index(0);
						if(remark != -1)
							add_focus(remark.id);
					}
					update_toolbar();
					$("#loading").hide();
				});
			}
			function onload() {
				$("#loading").show();
				prettyPrint();
				$("#loading").hide();
				show_remarks(-1);
			}

			function update_file_remark() {
				$("#fileremark-update").val("saving");
				$.post('file-review-ajax.php', {"file_id":$.toJSON(<?php echo $this->file->id; ?>),"action":"srv_change_file_remark","content":$.toJSON($("#fileremark-content").val())}, function(res){
					var response = $.evalJSON(res);
					if(response[0]=="ok") {
					}
					else {
						alert(response[1]);
					}
				});
				$("#fileremark-update").val("update (saved)");
			}
		//]]>
		</script>
</div>
<?php $this->display("footer.tpl.php"); ?>

<p>
<br /><br />
</p>

<div id="toolbar">
<table>
	<tr>
		<td></td>
		<td style="width:30px;"><img src="images/prev.png" onclick="show_prev_remark();" alt="prev" /></td>
		<td style="width:10%;" id="toolbar_remark_position">0/0</td>
		<td style="width:30px;"><img src="images/next.png" onclick="show_next_remark();" alt="next" /></td>
		<td></td>
	</tr>
</table>
</div>

<pre><span id="select-result" style="visibility:hidden;"></span></pre>
</body>
</html>

