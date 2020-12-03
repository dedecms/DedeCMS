<?php
$cfg_needFilter = true;
require(dirname(__FILE__)."/../include/config_base.php");
require(dirname(__FILE__)."/../include/inc_memberlogin.php");
require(dirname(__FILE__)."/../include/inc_channel_unit.php");
if(!isset($action)) $action = "";
if(!empty($artID)) $arcID = $artID;
if(!isset($arcID)) $arcID = 0;
if(!isset($feedid)) $feedid = 0;
if(!isset($arctitle)) $arctitle = '';
if(!isset($arcurl)) $arcurl = '';
if(!isset($urlindex)) $urlindex = 0;

//每页显示评论记录数
$feedbackPageSize = 15;

if($cfg_feedbackcheck=='Y') $ischeck = 0;
else $ischeck = 1;

function trimMsg($msg)
{
	global $cfg_notallowstr;
	$notallowstr = $cfg_notallowstr;
	$msg = htmlEncode($msg);
	$msg = str_replace("  ","&nbsp;&nbsp;",$msg);
	$msg = eregi_replace($notallowstr,"***",$msg);
	return $msg;
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
	ShowMsg("评论成功！","-1");
	exit();
}
else if($action=="bad")
{
	$fid = ereg_replace("[^0-9]","",$fid);
	$dsql->ExecuteNoneQuery("Update #@__feedback set bad = bad+1 where ID='$fid' ");
	$dsql->Close();
	ShowMsg("评论成功！","-1");
	exit();
}

//如果发送的网址和文档ID同时为空，禁止评论
if(empty($arcurl) && empty($arcID) && empty($urlindex))
{
	ShowMsg("无法对未知文档评论！","-1");
	$dsql->Close();
	exit();
}

$cts = array();
$cts['maintable'] = '#@__archives';
if(!empty($arcID)){
	$cts = GetChannelTable($dsql,$arcID,'arc');
}

//如果没有评论文档的索引ID，先检测
if(empty($urlindex))
{
  //读取文档信息
  if(empty($arcurl))
  {
     $arcRow = $dsql->GetOne(" Select arc.title,arc.senddate,arc.arcrank,arc.ismake,arc.money,arc.typeid,t.topID,t.typedir,t.namerule From `{$cts['maintable']}` arc  left join `#@__arctype` t on t.ID=arc.typeid where arc.ID='$arcID'; ");
     if(is_array($arcRow))
     {
	      $arctitle = addslashes($arcRow['title']);
	      $arcurl = GetFileUrl($arcID,$arcRow['typeid'],$arcRow['senddate'],$arctitle,$arcRow['ismake'],$arcRow['arcrank'],$arcRow['namerule'],$arcRow['typedir'],$arcRow['money']);
        $arcurl = addslashes($arcurl);
        $feedid = $arcRow['typeid'];
     }
  }
  //获取网址索引信息
  $row = $dsql->GetOne("Select id,title From `#@__cache_feedbackurl` where url='$arcurl' ");
  if(is_array($row))
  {
	  $urlindex = $row['id'];
	  $arctitle = $row['title'];
  }
  else
  {
	   $iquery = " INSERT INTO `#@__cache_feedbackurl`(`url`,`title`,`postnum`,`posttime`,`feedid`) VALUES ('$arcurl', '$arctitle', '0', '0', '$feedid');";
	   $rs = $dsql->ExecuteNoneQuery($iquery);
     if($rs) $urlindex = $dsql->GetLastID();
     else
     {
  	    ShowMsg("保存索引数据失败，无法评论！","javascript:;");
	      $dsql->Close();
	      exit();
     }
  }
}
//直接从索引中获取信息
else
{
	$row = $dsql->GetOne("Select id,url,title From `#@__cache_feedbackurl` where id='{$urlindex}' ");
  if(is_array($row)){
	  $urlindex = $row['id'];
	  $arctitle = $row['title'];
	  $arcurl = $row['url'];
  }else{
  	ShowMsg("获取索引数据失败无法评论！","javascript:;");
	  $dsql->Close();
	  exit();
  }
}

//查看评论
/*--------------------------
function _ShowFeedback()
----------------------------*/
if($action==""||$action=="show")
{

	require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");

	$row = $dsql->GetOne("Select AVG(rank) as dd From #@__feedback where urlindex = '$urlindex' Or aid='$arcID' ");
	$agvrank = $row['dd'];

  $dlist = new DataList();
  $dlist->Init();
  $dlist->pageSize = $feedbackPageSize;

  //最近热门评论
  $feedback_hot = "";
	$nearTime = 30;  //最近评论的文章的发布日期(表示多少天前)
	$minTime = mytime() - (3600 * 24 * $nearTime);

	if($feedid==0) $hotquery = "Select id,title From `#@__cache_feedbackurl` where  posttime>$minTime order by postnum desc limit 0,10";
	else $hotquery = "Select id,title From `#@__cache_feedbackurl` where posttime>$minTime And feedid='$feedid' order by postnum desc limit 0,10";

  $dlist->dsql->Execute("hotq",$hotquery);
  while($myrow = $dlist->dsql->GetArray("hotq")){
  	$feedback_hot .= "<div class='nndiv'>·<a href='feedback.php?urlindex={$myrow['id']}'>{$myrow['title']}</a></div>\r\n";
  }
  $dlist->dsql->FreeResult("hotq");

  //评论内容列表
  if(empty($arcID)) $wq = " urlindex = '$urlindex' ";
  else $wq = " aid='$arcID' ";
  $querystring = "select * from `#@__feedback` where $wq and ischeck='1' order by dtime desc";
  $dlist->SetParameter("arcID",$arcID);
  $dlist->SetParameter("urlindex",$urlindex);
  $dlist->SetParameter("feedid",$feedid);
  $dlist->SetParameter("action","show");
  $dlist->SetSource($querystring);
  require_once($cfg_basedir.$cfg_templets_dir."/plus/feedback_templet.htm");
  $dlist->Close();
  $dsql->Close();
}
//发表评论
/*-----------------------------
function __send()
------------------------------*/
else if($action=="send")
{
  //是否加验证码重确认
  if(!isset($isconfirm)) $isconfirm = '';
  if($cfg_feedback_ck=='Y' && empty($isconfirm)){
  	require_once($cfg_basedir.$cfg_templets_dir."/plus/feedback_confirm.htm");
  	$dsql->Close();
  	exit();
  }
  //检查验证码
  if($cfg_feedback_ck=='Y'){
  	if(empty($validate)) $validate=="";
    else $validate = strtolower($validate);
    $svali = GetCkVdValue();
    if(strtolower($validate)!=$svali || $svali=="")
    {
       $dsql->Close();
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
   	  $dsql->Close();
   	  ShowMsg("用户名或密码不合法！","-1",0,2000);
  	  exit();
    }
 	  $row = $dsql->GetOne("Select ID,pwd From `#@__member` where userid='$username' ");
 	  $isok = false;
 		if(is_array($row)){
 			$pwd = GetEncodePwd($pwd);
 			if($pwd == $row['pwd']) $isok = true;
 	  }
    if(!$isok) {
  	  $dsql->Close();
  	  ShowMsg("验证用户失败，请重新输入你的用户名和密码！","-1");
  	  exit();
    }
  }
  $msg = cn_substr(trimMsg($msg),1000);
  $ip = GetIP();
  $dtime = mytime();
  if(empty($face)) $face = '0';
  //保存评论内容
  if($msg!="")
  {
	  if(empty($rank)) $rank = '0';
	  $inquery = "
	    Insert Into `#@__feedback`(aid,username,arctitle,urlindex,url,ip,msg,ischeck,dtime,rank,face)
	    values('$arcID','$username','$arctitle','$urlindex','$arcurl','$ip','$msg','$ischeck','$dtime','$rank','$face')
	  ";
	  
	  $dsql->ExecuteNoneQuery($inquery);
	  
	  $row = $dsql->GetOne("Select count(*) as dd From `#@__feedback` where urlindex='$urlindex' Or aid='$arcID' ");
	  if(!empty($arcID))
	  {
	    $dsql->ExecuteNoneQuery("Update `{$cts['maintable']}` set postnum='".$row['dd']."',lastpost='".mytime()."' where ID='$arcID'");
      //更新文档
      if($cfg_feedback_make=='Y')
      {
    	  require(dirname(__FILE__)."/../include/inc_archives_view.php");
  	    $arc = new Archives($arcID);
        $arc->MakeHtml();
      }
    }
    $dsql->ExecuteNoneQuery("Update `#@__cache_feedbackurl` set postnum='".$row['dd']."',posttime='".mytime()."' where id='$urlindex' ");
  }
  $dsql->Close();
  if($ischeck==0) ShowMsg("成功发表评论，但需审核后才会显示你的评论!","feedback.php?arcID=$arcID&urlindex=$urlindex");
  if($ischeck==1) ShowMsg("成功发表评论，现在转到评论页面!","feedback.php?arcID=$arcID&urlindex=$urlindex");
  exit();
}
?>