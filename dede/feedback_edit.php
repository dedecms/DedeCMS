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

require_once(dirname(__FILE__)."/templets/feedback_edit.htm");

ClearAllLink();
?>