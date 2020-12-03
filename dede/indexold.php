<?
require("config.php");
if($cuserLogin->getUserType()==10) $menu = "admin_menu.php";
else if($cuserLogin->getUserType()==5) $menu = "user_menu.php";
else $menu = "thuser_menu.php";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>织梦内容管理系统V2.1完美版</title>
<style type="text/css">
#changeBar{
	cursor: hand;
	border-left: 1px solid #0C6960;
	border-right: 1px solid #333333;
}
#strChar{
	font-size:6pt;
	font-family:webdings;
	color:#DDDDDD;
	background-color:#777777;
	border-right: 1px solid #DDDDDD;
}
</style>
</head>
<body style='MARGIN: 0px' bgColor='#ffffff' leftMargin='0' topMargin='0' scroll='no'>
<script language="JavaScript">
function showMenu()
{
	if(document.all.strChar.innerText=="3")
	{
		document.all.strChar.innerText="4";
		document.all.menuFrame.style.display="none";
	}	
	else
	{
		document.all.strChar.innerText="3";
		document.all.menuFrame.style.display="";
	}
}
</script>
<table id="mainTable" width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="110" valign="top" id="menuFrame"> 
      <iframe width="110" height="100%" src="menu/<?=$menu?>" scrolling="no" marginwidth="0" frameborder="0"></iframe>
	</td>
    <td width="8" align="right" background="img/allbg.gif" id="changeBar" onClick="showMenu();"><span id="strChar">3</span></td>
    <td valign="top" id="mainFrame">
	<iframe width="100%" height="100%" src="blank.php?menu=open" name="main" marginwidth="2" frameborder="0"></iframe></td>
  </tr>
</table>
</body>
</html>
