<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/inc_unit.php");
CheckRank(0,0);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>投稿</title>
<link href="base.css" rel="stylesheet" type="text/css">	
<script language="javascript">
<!--
function checkSubmit()
{
if(document.form1.title.value=="")
{
	alert("文章标题不能为空！");
	document.form1.title.focus();
	return false;
}
if(document.form1.typeid.value==0){
	alert("文章类别不能为空！");
	return false;
}
if(document.form1.vdcode.value=="")
{
 document.form1.vdcode.focus();
 alert("验证码不能为空！");
 return false;
}
}
-->
</script>
</head>
<body leftmargin="0" topmargin="0">
<table width="760" border="0" align="center" cellpadding="0" cellspacing="0" style="border-bottom:1px solid #666666">
<tr bgcolor="#FFFFFF"> 
<td height="50" colspan="2"><img src="img/member.gif" width="320" height="46"></td>
</tr>
<tr> 
<td width="168" bordercolor="#FFFFFF" bgcolor="#808DB5">&nbsp;</td>
<td width="575" align="right"> 
<?=$cfg_member_menu?>
</td>
</tr>
</table>
<table width="760" border="0" align="center" cellpadding="0" cellspacing="0">
<tr> 
<td height="6"></td>
</tr>
<tr> 
<td height="21" valign="bottom" background="img/tbg.gif">
<font color="#333333"> <strong>　&nbsp;投稿：</strong></font></td>
</tr>
<tr> 
<td height="183" valign="top"> <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#B0C693">
<form name="form1" action="archives_do.php" method="post" onSubmit="return checkSubmit();">
<input type="hidden" name="dopost" value="addArc">
<tr> 
<td width="16%" height="28" align="center" bgcolor="#FFFFFF">文章标题：</td>
<td width="84%" bgcolor="#FFFFFF"> <input name="title" type="text" id="title" size="30" class="nb"></td>
</tr>
<tr> 
<td height="28" align="center" bgcolor="#F7FDF0">文章出处：</td>
<td bgcolor="#F7FDF0"> <input name="source" type="text" id="source" size="30" class="nb"></td>
</tr>
<tr> 
<td height="28" align="center" bgcolor="#FFFFFF">文章作者：</td>
<td bgcolor="#FFFFFF"> <input name="writer" type="text" id="writer" size="30" class="nb" value="<?=$cfg_ml->M_UserName?>"></td>
</tr>
<tr> 
<td height="28" align="center" bgcolor="#F7FDF0">所属类目：</td>
<td bgcolor="#F7FDF0"> <select name="typeid" id="typeid" style="width:200">
<option value='0'>--请选择分类--</option>
<?
$dsql = new DedeSql(false);
GetOptionArray(0,$dsql);
$dsql->Close();
?>
</select>
</td>
</tr>
<tr> 
<td height="70" align="center" bgcolor="#FFFFFF">文章摘要：</td>
<td bgcolor="#FFFFFF"> <textarea name="description" id="description" style="width:500;height:60"></textarea></td>
</tr>
<tr bgcolor="#F7FDF0"> 
<td height="28" align="center">验证码：</td>
<td height="25"> 
<table width="200" border="0" cellspacing="0" cellpadding="0">
<tr> 
<td width="84"><input name="vdcode" type="text" id="vdcode" size="10"></td>
<td width="116"><img src='../include/validateimg.php' width='50' height='20'></td>
</tr>
</table></td>
</tr>
<tr bgcolor="#FFFFFF"> 
<td height="70" align="center">关键词：</td>
<td> <textarea name="keywords" cols="30" rows="3" id="keywords"></textarea>
(用空格分开，用于关联相近内容的文章)</td>
</tr>
<tr bgcolor="#F7FDF0"> 
<td height="24" align="center">文章内容：</td>
<td bgcolor="#F7FDF0">&nbsp;</td>
</tr>
<tr> 
<td height="250" colspan="2" align="center" bgcolor="#FFFFFF"> 
<?
	GetEditor("body","",350,"Member");
?>
</td>
</tr>
<tr bgcolor="#F7FDF0"> 
<td height="45" colspan="2" align="center">
<input name="imageField" type="image" src="img/button_save.gif" width="60" height="22" border="0"> 
&nbsp;&nbsp;&nbsp;
<img src="img/button_reset.gif" width="60" height="22" style="cursor:hand" onClick="location.reload();"> 
</td>
</tr>
</form>
</table></td>
</tr>
<tr> 
<td height="10"></td>
</tr>
</table>
<p align='center'>
<?
echo $cfg_powerby
?>
</p>
</body>
</html>
