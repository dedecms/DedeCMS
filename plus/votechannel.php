<?php 
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
if(empty($action)) $action = "";
if(empty($aid)){
	ShowMsg("必须指定文档ID!","-1");
	exit();
}
if($action==""){
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=">
<title>投票确认</title>
<link rel="stylesheet" type="text/css" href="base.css">
</head>
<body leftmargin="8" topmargin='8'>
<table width="300px" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#A5D0F1" style="BORDER-COLLAPSE: collapse">
  <tr> 
    <td width="100%" height="4"></td>
  </tr>
  <tr> 
    <td width="100%" height="20">
<form name='myform' action='votechannel.php'>
<input type='hidden' name='aid' value='<?=$aid?>'>
<input type='hidden' name='action' value='ok'>
<table width='100%'  border='0' cellpadding='3' cellspacing='1' bgcolor='#A5D0F1'>
<tr bgcolor='#D2EFFD'>
<td colspan='2' background='img/wbg.gif' align='center'><font color='#666600'><b>投票确认</b></font></td>
</tr>
<tr bgcolor='#FFFFFF'>
<td colspan='2'  height='100'><table width="100%" border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td width="28%" align="center">验证码：</td>
    <td width="32%" align="center"><input type='text' name='validate' size='10'></td>
    <td width="40%"><img src='../include/vdimgckBig.php'></td>
  </tr>
</table></td>
</tr>
<tr>
<td colspan='2' bgcolor='#D2EFFD'>
<table width='270' border='0' align="center" cellpadding='0' cellspacing='0'>
<tr align='center'>
<td>
  <input type="submit" name="Submit" value="确认投票" class="nbt">
</td>
</tr>
</table>
</td>
</tr></table>
</form>
    </td>
  </tr>
  <tr> 
    <td width="100%" height="2" valign="top"></td>
  </tr>
</table>
<p align="center">

<br>
<br>
</p>
</body>
</html>
<?php
exit();
}else{
  @session_start();
  if(!isset($_SESSION['dd_mkvt'])){
  	session_register('dd_mkvt');
  	$_SESSION['dd_mkvt'] = '';
  }
  if(empty($validate)) $validate="";
  else $validate = strtolower($validate);
  $svali = GetCkVdValue();
  if($validate=="" || $validate!=$svali){
	   ShowMsg("验证码不正确!","-1");
	   exit();
  }
  if($_SESSION['dd_mkvt']==$aid){
  	 ShowMsg("请不要重复投票!","-1");
	   exit();
  }
  $_SESSION['dd_mkvt'] = $aid;
  $_SESSION['dd_ckstr'] = '';
  $aid = intval($aid);
  $dsql = new DedeSql(false);
  $dsql->ExecuteNoneQuery("Update #@__addonvote set votecount = votecount+1 where aid='$aid'; ");
  $dsql->Close();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=">
<title>投票成功</title>
<link rel="stylesheet" type="text/css" href="base.css">
</head>
<body leftmargin="8" topmargin='8'>
<table width="300px" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#A5D0F1" style="BORDER-COLLAPSE: collapse">
  <tr> 
    <td width="100%" height="4"></td>
  </tr>
  <tr> 
    <td width="100%" height="20">
<form name='myform' action='votechannel.php'>
<input type='hidden' name='aid' valie='3'>
<table width='100%'  border='0' cellpadding='3' cellspacing='1' bgcolor='#A5D0F1'>
<tr bgcolor='#D2EFFD'>
<td colspan='2' background='img/wbg.gif'><font color='#666600'><b>投票成功</b></font></td>
</tr>
<tr bgcolor='#FFFFFF'>
<td colspan='2'  height='80' align='center'>
投票成功 [<a href='javascript:window.close();'>关闭窗口</a>]
</td>
</tr>
</table>
</form>
    </td>
  </tr>
  <tr> 
    <td width="100%" height="2" valign="top"></td>
  </tr>
</table>
<p align="center">

<br>
<br>
</p>
</body>
</html>

<?php
  exit();
}
?>