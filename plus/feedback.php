<?
$needFilter = true;
require(dirname(__FILE__)."/../include/config_base.php");
require(dirname(__FILE__)."/../include/inc_memberlogin.php");
require(dirname(__FILE__)."/../include/inc_channel_unit.php");
if(!isset($action)) $action = "";
if(!empty($artID)) $arcID = $artID;
if(!isset($arcID)) $arcID = "";

//每页显示评论记录数
$feedbackPageSize = 15;

if($cfg_feedbackcheck=='是') $ischeck = 0;
else $ischeck = 1;

function trimMsg($msg)
{
	global $cfg_notallowstr;
	$notallowstr = $cfg_notallowstr;
	$msg = htmlspecialchars(trim($msg));
	$msg = nl2br($msg);
	$msg = str_replace("  ","&nbsp;&nbsp;",$msg);
	$msg = eregi_replace($notallowstr,"***",$msg);
	return $msg;
}

$arcID = ereg_replace("[^0-9]","",$arcID);
if(empty($arcID)){
	  ShowMsg("文档ID不能为空!","-1");
	  exit();
}

$ml = new MemberLogin();

$dsql = new DedeSql(false);

//扔鸡蛋鲜花
//-----------------------------------
if($action=="good")
{
	$fid = ereg_replace("[^0-9]","",$fid);
	$dsql->ExecuteNoneQuery("Update #@__feedback set good = good+1 where ID='$fid' ");
	$dsql->Close();
	ShowMsg("成功发出一束鲜花！","feedback.php?arcID=$arcID");
	exit();
}
else if($action=="bad")
{
	$fid = ereg_replace("[^0-9]","",$fid);
	$dsql->ExecuteNoneQuery("Update #@__feedback set bad = bad+1 where ID='$fid' ");
	$dsql->Close();
	ShowMsg("成功扔出一只鸡蛋！","feedback.php?arcID=$arcID");
	exit();
}

//读取文档信息
$arctitle = "";
$arcurl = "";
$topID = 0;
$arcRow = $dsql->GetOne("Select #@__archives.title,#@__archives.senddate,#@__archives.arcrank,#@__archives.ismake,#@__archives.money,#@__archives.typeid,#@__arctype.topID,#@__arctype.typedir,#@__arctype.namerule From #@__archives  left join #@__arctype on #@__arctype.ID=#@__archives.typeid where #@__archives.ID='$arcID'");
if(is_array($arcRow)){
	$arctitle = $arcRow['title'];
	$topID = $arcRow['topID'];
	$arcurl = GetFileUrl($arcID,$arcRow['typeid'],$arcRow['senddate'],$arctitle,$arcRow['ismake'],$arcRow['arcrank'],$arcRow['namerule'],$arcRow['typedir'],$arcRow['money']);
}
else{
	 $dsql->Close();
	 ShowMsg("无法对未知文档发表评论!","-1");
	 exit();
}

//查看评论
/*
function _ShowFeedback()
*/
//-----------------------------------
if($action==""||$action=="show")
{
	
	require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
	
	$row = $dsql->GetOne("Select AVG(rank) as dd From #@__feedback where aid='$arcID' ");
	$agvrank = $row['dd'];
	
  $dlist = new DataList();
  $dlist->Init();
  $dlist->pageSize = $feedbackPageSize;
  
  //最近热门评论
  $feedback_hot = "";
	$nearTime = 60;  //最近评论的文章的发布日期(表示多少天前)
	$minTime = mytime() - (3600 * 24 * $nearTime);
	
	if($topID==0) $hotquery = "Select ID,title From #@__archives where ID<>'$arcID' And senddate>$minTime order by postnum desc limit 0,10";
	else $hotquery = "Select ID,title From #@__archives where ID<>'$arcID' And senddate>$minTime And typeid=$topID order by postnum desc limit 0,10";
  
  $dlist->dsql->Execute("hotq",$hotquery);
  while($myrow = $dlist->dsql->GetArray("hotq")){
  	$feedback_hot .= "<div class='nndiv'>·<a href='feedback.php?arcID={$myrow['ID']}'>{$myrow['title']}</a></div>\r\n"; 
  }
  $dlist->dsql->FreeResult("hotq");
  
  //评论内容列表
  $querystring = "select * from #@__feedback where aid='$arcID' and ischeck='1' order by dtime desc";
  $dlist->SetParameter("arcID",$arcID);
  $dlist->SetParameter("action","show");
  $dlist->SetSource($querystring);
  require_once($cfg_basedir.$cfg_templets_dir."/plus/feedback_templet.htm");
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
  //是否加验证码重确认
  if(empty($isconfirm)) $isconfirm = "";
  if($isconfirm!="yes" && $cfg_feedback_ck=="是"){
  	require_once($cfg_basedir.$cfg_templets_dir."/plus/feedback_confirm.htm");
  	exit();
  }
  //检查验证码
  if($cfg_feedback_ck=="是"){
  	if(empty($validate)) $validate=="";
    else $validate = strtolower($validate);
    $svali = GetCkVdValue();
    if(strtolower($validate)!=$svali || $svali==""){
       ShowMsg("验证码错误！","-1");
       exit();
    }
  }
  //其它检查
  if(empty($notuser)) $notuser=0;
  if($notuser==1){ //匿名发表评论
	  if(empty($username)) $username = "guest";
  }
  else if($ml->M_ID > 0){ //已登录的用户
	  $username = $ml->M_UserName;
  }
  else{
  	//用户身份验证，考虑到整合的原因，验证后不支持保存用户的登录信息
	  if(!TestStringSafe($username)||!TestStringSafe($pwd)){
   	  ShowMsg("用户名或密码不合法！","-1",0,2000);
  	  exit();
    }
 	  $row = $dsql->GetOne("Select ID,pwd From #@__member where userid='$username' ");
 	  $isok = false;
 		if(is_array($row)){
 			$pwd = GetEncodePwd($pwd);
 			if($pwd == $row['pwd']) $isok = true;
 	  }
    if(!$isok) {
  	  ShowMsg("验证用户失败，请重新输入你的用户名和密码！","-1");
  	  exit();
    }
  }
  $msg = cn_substr(trimMsg($msg),1000);
  $ip = GetIP();
  $dtime = mytime();
  //保存评论内容
  if($msg!="")
  {
	  if(empty($rank)) $rank = '0';
	  $inquery = "
	  Insert Into #@__feedback(aid,username,arctitle,ip,msg,ischeck,dtime,rank) 
	  values('$arcID','$username','$arctitle','$ip','$msg','$ischeck','$dtime','$rank')
	  ";
	  $dsql->ExecuteNoneQuery($inquery);
	  $row = $dsql->GetOne("Select count(*) as dd From #@__feedback where aid='$arcID' ");
	  $dsql->ExecuteNoneQuery("Update #@__archives set postnum='".$row['dd']."',lastpost='".mytime()."' where ID='$arcID'");
    //更新文档
    if($cfg_feedback_make=='是'){
    	require(dirname(__FILE__)."/../include/inc_archives_view.php");
  	  $arc = new Archives($arcID);
      $arc->MakeHtml();
      $arc->Close();
    }
  }
  $dsql->Close();
  if($ischeck==0) ShowMsg("成功发表评论，但需审核后才会显示你的评论!","feedback.php?arcID=$arcID");
  if($ischeck==1) ShowMsg("成功发表评论，现在转到评论页面!","feedback.php?arcID=$arcID");
  exit();
}
?>