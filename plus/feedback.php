<?
require(dirname(__FILE__)."/../include/config_base.php");
require(dirname(__FILE__)."/../include/inc_memberlogin.php");
require(dirname(__FILE__)."/../include/inc_channel_unit.php");
if(!isset($action)) $action = "";
//默认的情况下评论需审核才显示，
//如果你想直接显示请把下面值改为 1 
$ischeck = 0;

if(!empty($artID)) $arcID = $artID;
if(!isset($arcID)) $arcID = "";
$arcID = ereg_replace("[^0-9]","",$arcID);
if(empty($arcID)){
	  ShowMsg("文档ID不能为空!","-1");
	  exit();
}
$ml = new MemberLogin();
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
	 ShowMsg("无法对未知文档发表评论!","-1");
	 exit();
}
//查看评论
//-----------------------------------
if($action==""||$action=="show")
{
	require_once(dirname(__FILE__)."/../include/pub_datalist.php");
  $querystring = "select * from #@__feedback where aid='$arcID' and ischeck='1' order by dtime desc";
  $dlist = new DataList();
  $dlist->Init();
  $dlist->SetParameter("arcID",$arcID);
  $dlist->SetParameter("action","show");
  $dlist->SetSource($querystring);
  $dlist->SetTemplet($cfg_basedir.$cfg_templets_dir."/plus/feedback_templet.htm");
  $dlist->display();
  $dlist->Close();
  $dsql->Close();
}
//发表评论
//------------------------------------
/*
function __send()
*/
else if($action=="send")
{
  if(empty($notuser)) $notuser=0;
  if($notuser==1) //匿名发表评论
  {
	  if(empty($username)) $username = "guest";
  }
  else if($ml->M_ID > 0) //已登录的用户
  {
	  $username = $ml->M_UserName;
  }
  else //用户身份验证
  {
	  $username = ereg_replace("[ '\"\*\?\%]","",$username);
	  $pwd = ereg_replace("[ '\"\*\?\%]","",$pwd);
 	  $rs = $ml->CheckUser($username,$pwd);
    if($rs==1) {
   	  $dsql->SetQuery("update #@__member set logintime='".time()."',loginip='".GetIP()."' where ID='".$cfg_ml->M_ID."'");
   	  $dsql->ExecuteNoneQuery();
   	  $username = $ml->M_UserName;
    }
    else{
  	  ShowMsg("验证用户失败，请重新输入你的用户名和密码！","-1");
  	  exit();
    }
  }
  $arcID = ereg_replace("[^0-9]","",$arcID);
  $msg = cn_substr(trim($msg),1000);
  $msg = str_replace("<","&lt;",$msg);
  $msg = str_replace(">","&gt;",$msg);
  $msg = str_replace("  ","&nbsp;&nbsp;",$msg);
  $msg = str_replace("\r\n","<br>\n",$msg);
  $msg = trim($msg);
  $ip = GetIP();
  $dtime = time();
  //保存评论内容
  if($msg!="")
  {
	  $inquery = "
	  Insert Into #@__feedback(aid,username,arctitle,ip,msg,ischeck,dtime) 
	  values('$arcID','$username','$arctitle','$ip','$msg','$ischeck','$dtime')
	  ";
	  $dsql->SetQuery($inquery);
	  $dsql->ExecuteNoneQuery();
  }
  $dsql->Close();
  if($ischeck==0) ShowMsg("成功发表评论，但需审核后才会显示你的评论!","feedback.php?arcID=$arcID");
  if($ischeck==1) ShowMsg("成功发表评论，现在转到评论页面!","feedback.php?arcID=$arcID");
  exit();
}
?>