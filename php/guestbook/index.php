<?
require("inc_guestbook.php");
$pagesize=10;
if(empty($page)) $page=1;
if(empty($totalresult)) $totalresult=0;
$gbook = new GuestBook($pagesize,$page,$totalresult);
?>
<html>
<head>
<title>留言簿</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel=stylesheet href="images/css.css" type="text/css">
</head>
<body topmargin="2" >
<table border="0" cellspacing="0" cellpadding="0" width='760'>
<tr>
    <td height="20" align="center"><img src="images/dedebanner.gif" width="760" height="70"></td>
  </tr>
  <tr>
    <td height="0"></td>
  </tr>
</table>
<table border='0' cellpadding='0' cellspacing='0' width='760' background='images/bottop.gif' align='center'>
<tr>
<td width="25%" height="20">&nbsp;</td>
<td width="25%" height='5'>
<td width="35%" align='right'><img src='images/quote.gif' border=0 height=16 width=16></td>
<td width="15%"> &nbsp;<a href="#write">[签写留言]</a></td>
</tr></table>
<?$gbook->printResult();?>
<table border='0' cellpadding='4' cellspacing='0' width='760' align='center'>
  <tr>
  <td align="right">
  <?$gbook->getPageList("index.php");?>&nbsp;
  </td>
  </tr>
  <tr><td></td></tr>
</table>
<a name="write"></a>
<table width="760" border="0" cellspacing="1" cellpadding="4" align="center" bgcolor="#E6D85A">
<form method="post" action="savepost.php">
<tr bgcolor="#ffffff">
  <td width="10%" align="center" nowrap><font color="#FF0000">*</font>你的姓名：</td>
  <td width="40%"><input type="text" maxlength="10" name="uname" size="46" class="input"></td>
  <td width="10%" align="center" nowrap>OICQ号码：</td>
  <td width="40%"><input maxlength="20" type="text" name="qq" size="46" class="input"></td>
</tr>
<tr bgcolor="#ffffff">
  <td align="center" nowrap width="10%">&nbsp;电子邮件：</td>
  <td width="40%"><input maxlength="80" type="text" name="email" size="46" class="input"></td>
  <td width="10%" align="center" nowrap height="12">个人主页：</td>
  <td width="40%" height="12"><input maxlength="80" type="text" name="homepage" size="46" class="input"></td>
</tr>
<tr bgcolor="#ffffff">
  <td align="center" nowrap width="10%">
  <font color="#FF0000">*</font>留言内容：<br>(1000字内)
  </td>
  <td align="left" width="40%"><textarea name="msg" cols="38" rows="8" class="textarea"></textarea></td>
  <td align="center" nowrap height="2" width="10%">选择头像：</td>
  <td nowrap height="2" width="40%">
	<input type="radio" name="img" value="01" checked><img src="images/01.gif" width="25" height="25">
	<input type="radio" name="img" value="02"><img src="images/02.gif" width="25" height="25">  
	<input type="radio" name="img" value="03"><img src="images/03.gif" width="25" height="25">  
	<input type="radio" name="img" value="04"><img src="images/04.gif" width="25" height="25">  
	<input type="radio" name="img" value="05"><img src="images/05.gif" width="25" height="25">  
	<input type="radio" name="img" value="06"><img src="images/06.gif" width="25" height="25">  
	<br><input type="radio" name="img" value="07"><img src="images/07.gif" width="25" height="25">
	<input type="radio" name="img" value="08"><img src="images/08.gif" width="25" height="25">  
	<input type="radio" name="img" value="09"><img src="images/09.gif" width="25" height="25">  
	<input type="radio" name="img" value="10"><img src="images/10.gif" width="25" height="25">  
	<input type="radio" name="img" value="11"><img src="images/11.gif" width="25" height="25">  
	<input type="radio" name="img" value="12"><img src="images/12.gif" width="25" height="25">
	<br><input type="radio" name="img" value="13"><img src="images/13.gif" width="25" height="25">
	<input type="radio" name="img" value="14"><img src="images/14.gif" width="25" height="25">
	<input type="radio" name="img" value="15"><img src="images/15.gif" width="25" height="25">
	<input type="radio" name="img" value="16"><img src="images/16.gif" width="25" height="25">
	<input type="radio" name="img" value="17"><img src="images/17.gif" width="25" height="25">
	<input type="radio" name="img" value="18"><img src="images/18.gif" width="25" height="25">
	<br><input type="radio" name="img" value="19"><img src="images/19.gif" width="25" height="25">
	<input type="radio" name="img" value="20"><img src="images/20.gif" width="25" height="25">  
	<input type="radio" name="img" value="21"><img src="images/21.gif" width="25" height="25">  
	<input type="radio" name="img" value="22"><img src="images/22.gif" width="25" height="25">  
	<input type="radio" name="img" value="23"><img src="images/23.gif" width="25" height="25">  
	<input type="radio" name="img" value="24"><img src="images/24.gif" width="25" height="25">  
  </td>
</tr>
<tr bgcolor="#ffffff">
  <td align="center" nowrap colspan="4">
	<input maxlength="1000" type="submit" name="Submit" value="提 交" class="btn">&nbsp;&nbsp;
	<input type="reset" name="Submit2" value="取 消" class="btn">
  </td>
</tr>
</form>
</table>
<table width="760" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="40" align="center"><a href="http://www.dedecms.com" target="_blank">Power by DedeCms 织梦内容管理系统</a></td>
  </tr>
</table>
</body>
</html>