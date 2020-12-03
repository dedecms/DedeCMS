<?
require(dirname(__FILE__)."/../include/config_base.php");
require(dirname(__FILE__)."/../include/inc_channel_unit.php");
if(!isset($action)) $action = "";
if(!empty($artID)) $arcID = $artID;
if(!isset($arcID)) $arcID = "";
$arcID = ereg_replace("[^0-9]","",$arcID);
if(empty($arcID)){
	  ShowMsg("文档ID不能为空!","-1");
	  exit();
}
//////////////////////////////////////////////
if($action=="")
{
  $dsql = new DedeSql(false);
  //读取文档信息
  $arctitle = "";
  $arcurl = "";
  $arcRow = $dsql->GetOne("Select #@__archives.title,#@__archives.senddate,#@__archives.arcrank,#@__archives.ismake,#@__archives.money,#@__archives.typeid,#@__arctype.typedir,#@__arctype.namerule From #@__archives  left join #@__arctype on #@__arctype.ID=#@__archives.typeid where #@__archives.ID='$arcID'");
  if(is_array($arcRow)){
	  $arctitle = $arcRow['title'];
	  $arcurl = GetFileUrl($arcID,$arcRow['typeid'],$arcRow['senddate'],$arctitle,$arcRow['ismake'],$arcRow['arcrank'],$arcRow['namerule'],$arcRow['typedir'],$arcRow['money']);
  }
  else{
	  $dsql->Close();
	  ShowMsg("无法把未知文档推荐给好友!","-1");
	  exit();
  }
  $dsql->Close();
}
//发送推荐信息
//-----------------------------------
else if($action=="send")
{
	if(!eregi("(.*)@(.*)\.(.*)",$email)){
	  echo "<script>alert('Email不正确!');history.go(-1);</script>";
	  exit();
  }
  $mailbody = "";
  $msg = ereg_replace("[><]","",$msg);
  $mailtitle = "你的好友给你推荐了一篇文章";
  $mailbody .= "$msg \r\n\r\n";
  $mailbody .= "Power by http://www.dedecms.com 织梦内容管理系统！";
  if(eregi("(.*)@(.*)\.(.*)",$email)){
	  $headers = "From: ".$cfg_adminemail."\r\nReply-To: ".$cfg_adminemail;
    @mail($email, $mailtitle, $mailbody, $headers);
  }
  ShowMsg("成功推荐一篇文章!",$arcurl);
  exit();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>推荐好友</title>
<link href="../base.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style2 {
	color: #CC0000;
	font-size: 11pt;
}
-->
</style>
</head>
<body>
<table width="650" border="0" align="center" cellspacing="2">
<tr> 
<td><img src="img/recommend.gif" width="320" height="46"></td>
</tr>
<tr> 
<td bgcolor="#CCCC99" height="6"></td>
</tr>
<tr> 
<td height="28">
<span class="style2">&nbsp;文章名称：<a href="<? echo $arcurl ?>"><? echo $arctitle ?></a></span>
</td>
</tr>
<tr> 
<td bgcolor="#DFEAE4">&nbsp;我要把它发送给我的好友：</td>
</tr>
<tr> 
<td> <table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr> 
<td height="3"></td>
</tr>
<tr> 
<td height="100" align="center" valign="top">
<form name="form1" method="post" action="recommend.php">
<input type="hidden" name="arcurl" value="<? echo $arcurl ?>">
<input type="hidden" name="action" value="send">
<input type="hidden" name="arcID" value="<?=$arcID?>">
<table width="98%" border="0" cellspacing="0" cellpadding="0">
<tr> 
<td width="19%" height="30">你好友的Email：</td>
<td width="81%">
	<input name="email" type="text" id="email"> 
</td>
</tr>
<tr> 
<td height="30">你的留言：</td>
<td>&nbsp;</td>
</tr>
<tr align="center"> 
<td height="61" colspan="2">
<textarea name="msg" cols="72" rows="6" id="msg" style="width:98%">
你好，我在 [<?=$cfg_webname?>] 发现了一个很好的东东：
你不妨去看看吧！
文档的名称是：<?=$arctitle?>
网址是：<?=$cfg_basehost.$arcurl?>
</textarea>
</td>
</tr>
<tr> 
<td height="50">&nbsp;</td>
<td><input type="submit" name="Submit" value=" 发 送 "></td>
</tr>
</table>
</form>
</td>
</tr>
<tr> 
<td height="3"></td>
</tr>
</table></td>
</tr>
<tr> 
<td bgcolor="#CCCC99" height="6"></td>
</tr>
<tr> 
<td align="center">
<?=$cfg_powerby?>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
