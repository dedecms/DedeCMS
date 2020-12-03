<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Feedback');
$ID = ereg_replace("[^0-9]","",$ID);

if(empty($dopost)) $dopost = "";
if(empty($_COOKIE['ENV_GOBACK_URL'])) $ENV_GOBACK_URL="feedback_main.php";
else $ENV_GOBACK_URL = $_COOKIE['ENV_GOBACK_URL'];

$dsql = new DedeSql(false);

if($dopost=="edit")
{
   $msg = cn_substr($msg,1500);
   $adminmsg = trim($adminmsg);
   if($adminmsg!="")
   {
	  $adminmsg = cn_substr($adminmsg,1500);
	  $adminmsg = str_replace("<","&lt;",$adminmsg);
	  $adminmsg = str_replace(">","&gt;",$adminmsg);
	  $adminmsg = str_replace("  ","&nbsp;&nbsp;",$adminmsg);
	  $adminmsg = str_replace("\r\n","<br/>\n",$adminmsg);
	  $msg = $msg."<br/>\n"."<font color=red>管理员回复： $adminmsg</font>\n";
   }
   $query = "update #@__feedback set username='$username',msg='$msg',ischeck=1 where ID=$ID";
   $dsql->SetQuery($query);
   $dsql->ExecuteNoneQuery();
   $dsql->Close();
   ShowMsg("成功回复一则留言！",$ENV_GOBACK_URL);
   exit();
}

$query = "select * from #@__feedback where ID=$ID";
$dsql->SetQuery($query);
$dsql->Execute();
$row = $dsql->GetObject();
$dsql->Close();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>编辑评论</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<link href="base.css" rel="stylesheet" type="text/css">
</head>

<body>
&nbsp;
<table width="98%"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#98CAEF">
  <tr>
    <td width="100%" height="24" colspan="2" background="img/tbg.gif"><strong><a href="<?php echo $ENV_GOBACK_URL?>"><u>评论管理</u></a>&gt;&gt;编辑评论：</strong></td>
  </tr>
  <tr>
    <td height="187" colspan="2" align="center" bgcolor="#FFFFFF">
	<form name="form1" method="post" action="feedback_edit.php">
	<input type="hidden" name="dopost" value="edit">
	<input type="hidden" name="ID" value="<?php echo $row->ID?>">
        <table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ADA896">
          <tr bgcolor="#FFFFFF"> 
            <td width="21%" height="24">评论所属文章：</td>
            <td width="79%"> 
              <?php echo $row->arctitle?>
            </td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">评论人：</td>
            <td> 
              <input name="username" type="text" id="username" size="20" value="<?php echo $row->username?>"> 
            </td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">评论发布时间：</td>
            <td> 
              <?php echo GetDateTimeMK($row->dtime)?>
            </td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">IP地址：</td>
            <td> 
              <?php echo $row->ip?>
            </td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">评论内容：</td>
            <td>更改的评论内容HTML代码不会被屏蔽，可用HTML语法编辑。</td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="62" align="center">&nbsp; </td>
            <td height="62"> 
              <textarea name="msg" cols="60" rows="5" id="textarea"><?php echo $row->msg?></textarea></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24">管理员回复：</td>
            <td>回复内容的HTML代码会被屏蔽。</td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24" align="center">&nbsp; </td>
            <td height="24"> 
              <textarea name="adminmsg" cols="60" rows="5" id="textarea2"></textarea></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="40" colspan="2" align="center"> 
              <input type="submit" name="Submit" value="保存更改">
              　 
              <input type="button" name="Submit2" value="不理返回" onClick="location='<?php echo $ENV_GOBACK_URL?>';" class='nbt'></td>
          </tr>
        </table>
	  </form>
	  </td>
  </tr>
</table>
</body>
</html>