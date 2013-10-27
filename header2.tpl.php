<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="css/reviewlead.css" />
	<link href="js/google-code-prettify/prettify.css" type="text/css" rel="stylesheet" />
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
	<style type="text/css">
		/* #feedback { font-size: 1.4em; } */
		#selectable .ui-selecting { background: #FECA40; }
		#selectable .ui-selected { background: #F39814; color: white; }
		#selectable { list-style-type: none; margin: 0px 0px 0px 0px; padding: 0; }

		textarea.qtipcomment { border-style: none; height: 100px; width: 230px; min-width: 230px; max-width: 230px }

		.blue { background-color: #E5F6FE; }
		.green { background-color: #CDE6AC; }
		.red { background-color: #F79992; }
		.dark { background-color: #505050; }
		.cream { background-color: #FBF7AA; }
		.light { background-color: white; }

		li.focus_green_first { border-style:solid solid none solid; border-width: 3; border-color: #A9DB66; }
		li.focus_green_middle { border-style:none solid none solid; border-width: 3; border-color: #A9DB66; }
		li.focus_green_last { border-style:none solid solid solid; border-width: 3; border-color: #A9DB66; }
		li.focus_green_one { border-style: solid; border-width: 3; border-color: #A9DB66; }

		li.focus_blue_first { border-style:solid solid none solid; border-width: 3; border-color: #ADD9ED; }
		li.focus_blue_middle { border-style:none solid none solid; border-width: 3; border-color: #ADD9ED; }
		li.focus_blue_last { border-style:none solid solid solid; border-width: 3; border-color: #ADD9ED; }
		li.focus_blue_one { border-style: solid; border-width: 3; border-color: #ADD9ED; }

		li.focus_red_first { border-style:solid solid none solid; border-width: 3; border-color: #CE6F6F; }
		li.focus_red_middle { border-style:none solid none solid; border-width: 3; border-color: #CE6F6F; }
		li.focus_red_last { border-style:none solid solid solid; border-width: 3; border-color: #CE6F6F; }
		li.focus_red_one { border-style: solid; border-width: 3; border-color: #CE6F6F; }

		li.focus_dark_first { border-style:solid solid none solid; border-width: 3; border-color: #303030; }
		li.focus_dark_middle { border-style:none solid none solid; border-width: 3; border-color: #303030; }
		li.focus_dark_last { border-style:none solid solid solid; border-width: 3; border-color: #303030; }
		li.focus_dark_one { border-style: solid; border-width: 3; border-color: #303030; }

		li.focus_cream_first { border-style:solid solid none solid; border-width: 3; border-color: #F9E98E; }
		li.focus_cream_middle { border-style:none solid none solid; border-width: 3; border-color: #F9E98E; }
		li.focus_cream_last { border-style:none solid solid solid; border-width: 3; border-color: #F9E98E; }
		li.focus_cream_one { border-style: solid; border-width: 3; border-color: #F9E98E; }

		li.focus_light_first { border-style:solid solid none solid; border-width: 3; border-color: #E2E2E2; }
		li.focus_light_middle { border-style:none solid none solid; border-width: 3; border-color: #E2E2E2; }
		li.focus_light_last { border-style:none solid solid solid; border-width: 3; border-color: #E2E2E2; }
		li.focus_light_one { border-style: solid; border-width: 3; border-color: #E2E2E2; }
	</style>
	<title><?php echo $this->title; ?></title>
</head>
