<?php 
/** 检查权限 ************************/
$gotopagerank = "admin";
require_once(dirname(__FILE__)."/config.php");

if($cuserLogin->getUserRank()==-1)
{
	showMsg("对不起,你没有权限！",-1);
	exit();
}
if(!empty($_COOKIE['GUEST_BOOK_MOVE'])) $GUEST_BOOK_MOVE = $_COOKIE['GUEST_BOOK_MOVE'];
else $GUEST_BOOK_MOVE = "index.php";
//////////////////////////////////

$dsql = new DedeSql();
$ID = ereg_replace("[^0-9]","",$ID);
if(empty($job)) $job="view";

if($job=="del")
{
	$dsql->SetQuery("Delete From #@__guestbook where ID='$ID'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功删除一条留言！",$GUEST_BOOK_MOVE);
	exit();
}
else if($job=="check")
{
	$dsql->SetQuery("update #@__guestbook set ischeck=1 where ID='$ID'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功审核一条留言！",$GUEST_BOOK_MOVE);
	exit();
}
else if($job=="editok")
{
	$remsg = trim($remsg);
	if($remsg!=""){
		$remsg = trimMsg($remsg,1);
		$remsg = cn_substr($remsg,2000);
		$msg = $msg."<br><font color=red>管理员回复：$remsg</font>";
	}
	$ID = ereg_replace("[^0-9]","",$ID);
	$dsql->SetQuery("update #@__guestbook set msg='$msg' where ID='$ID'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功更改或回复一条留言！",$GUEST_BOOK_MOVE);
	exit();
}

$dsql->SetQuery("select * from #@__guestbook where ID='$ID'"); 
$dsql->Execute();
$row = $dsql->GetObject();
?>
<html>
<head>
<title>管理留言</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel=stylesheet href="images/css.css" type="text/css">
</head>
<body topmargin="2" >
<center>
<table border="0" cellspacing="0" cellpadding="0" width='760'>
<tr>
    <td height="20"><img src="images/dedebanner.gif" width="760" height="70"></td>
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
<td width="15%"> &nbsp;<a href="#write"><b>管理留言</b></a></td>
</tr></table>
<table width="760" border="0" cellspacing="1" cellpadding="4" align="center" bgcolor="#ABD82C">
<form method="post" action="edit.php">
<input type="hidden" name="ID" value="<?php echo $ID?>">
<input type="hidden" name="job" value="editok">
<tr bgcolor="#ffffff">
  <td width="10%" align="center" nowrap><font color="#FF0000">*</font>你的姓名：</td>
  <td width="40%"><?php echo $row->uname?></td>
  <td width="9%" align="center" nowrap>OICQ号码：</td>
  <td width="41%"><?php echo $row->qq?></td>
</tr>
<tr bgcolor="#ffffff">
  <td align="center" nowrap width="10%">&nbsp;电子邮件：</td>
  <td width="40%"><?php echo $row->email?></td>
  <td width="9%" align="center" nowrap height="12">个人主页：</td>
  <td width="41%" height="12"><?php echo $row->homepage?></td>
</tr>
<tr bgcolor="#ffffff">
  <td align="center" nowrap width="10%">
  <font color="#FF0000">*</font>留言内容：<br>(1000字内)
  </td>
  <td height="2" colspan="3" align="left"><textarea name="msg" cols="80" rows="6" class="textarea"><?php echo $row->msg?></textarea></td>
  </tr>
<tr bgcolor="#ffffff">
  <td align="center" nowrap>回复留言：<br>
    (1000字内)</td>
  <td colspan="3" nowrap><textarea name="remsg" cols="80" rows="6" class="textarea"></textarea></td>
  </tr>
<tr bgcolor="#ffffff">
  <td align="center" nowrap colspan="4">
	<input maxlength="1000" type="submit" name="Submit" value=" 保 存 " class="btn">
	&nbsp;&nbsp;
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
</center>
<?php 
$dsql->Close();
?>
</body>
</html>