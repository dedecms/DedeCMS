<?php 
require_once(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $cfg_softname." ".$cfg_version?></title>
</head>
	<frameset rows="76,*" cols="*" frameborder="no" border="0" framespacing="0" >
		<frame src="index_top.php" name="topFrame" id="topFrame" scrolling="no" noresize>
		<frameset cols="176,*" name="bodyFrame" id="bodyFrame" frameborder="no" border="0" framespacing="0"  >
			<frame src="index_menu.php?c=9" name="menu" id="menu" scrolling="auto" noresize>
			<frame src="index_body.php" name="main" id="main" scrolling="auto" noresize>
		</frameset>
	</frameset>
	<noframes>
		<body>你的浏览器不支持框架！</body>
	</noframes>
</html>
