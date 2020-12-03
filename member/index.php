<?php
require_once(dirname(__FILE__)."/config_space.php");
if(empty($uid)) $uid = "";
if(empty($id)) $id = "";
if(empty($action)) $action = "";
if(!empty($_SERVER["QUERY_STRING"])){
	$nquery = $_SERVER["QUERY_STRING"];
	if(ereg('/$',$nquery) && !ereg('=',$nquery)) $uid = ereg_replace('/$','',$nquery);
}

//会员管理中心主页面
/*--------------------
 function ShowDefaultControl();
------------------*/
if(empty($action) && empty($uid) && empty($id))
{
  require_once(dirname(__FILE__)."/config_index.php");
  $dsql  = new DedeSql(false);
  $isbookmodule = 0;
  $isaskmodule = 0;
  $isgroupmodule = 0;
  if($dsql->IsTable("{$cfg_dbprefix}story_books")) $isbookmodule = 1;
  if($dsql->IsTable("{$cfg_dbprefix}ask")) $isaskmodule = 1;
  if($dsql->IsTable("{$cfg_dbprefix}groups")) $isgroupmodule = 1;
  $temp = new PubmemberContent();
 
  if($isgroupmodule==1){
    //热门帖子
    $n = 7;//7条记录
    $topics 	 	 = $temp->SetQuery("SELECT subject,digest,replies,gid,tid FROM `#@__group_threads` WHERE closed=0 ORDER BY replies DESC LIMIT 0,$n");  
    //圈子排行
    $n = 5;//5条记录
    $topgroups 	 = $temp->SetQuery("SELECT groupid,groupname FROM `#@__groups` WHERE ishidden=0 ORDER BY threads DESC LIMIT 0,$n");
  }
  
  if($isaskmodule==1){
    //最新问答
    $n = 7;//7条记录
    $newask 	 	 = $temp->SetQuery("SELECT id,title,status FROM `#@__ask` ORDER BY dateline DESC LIMIT 0,$n");
  }
  
  if($isbookmodule==1){
    //小说
    $n = 7;//7条记录
    $books = $temp->SetQuery("SELECT id,bookname FROM `#@__story_books` WHERE ischeck>'0' ORDER BY pubdate DESC LIMIT 0,$n");
  }

  $n = 5;//5条记录
  $topmember 	 = $temp->SetQuery("SELECT userid,uname FROM `#@__member` ORDER BY scores DESC LIMIT 0,$n");
  //新加入企业
  $n = 5;//5条记录
  $newcompanys = $temp->SetQuery("SELECT comname,userid FROM `#@__member_cominfo` AS c LEFT JOIN #@__member AS m ON c.id=m.ID WHERE m.type='1' ORDER BY m.jointime DESC LIMIT 0,$n");
  //热门文章
  $n = 7;//7条记录
  $hotarchives = $temp->SetQuery("SELECT aid as ID,typeid,title FROM `#@__full_search` WHERE mid>'0' AND arcrank>'-1' ORDER BY click DESC LIMIT 0,$n");
  //最新文章
  $n = 7;//7条记录
  $newarchives = $temp->SetQuery("SELECT aid as ID,typeid,title FROM `#@__full_search` WHERE mid>'0' AND arcrank>'-1' ORDER BY aid DESC LIMIT 0,$n");
  //图集
  $n = 5;//5条记录
  $pics = $temp->SetQuery("SELECT aid as ID,typeid,title,litpic FROM `#@__full_search` WHERE mid>'0' AND arcrank>'-1' AND channelid='2' ORDER BY aid DESC LIMIT 0,$n");
  //分类信息
  $n = 7;//7条记录
  $infos = $temp->SetQuery("SELECT ID,typeid,title FROM `#@__infos` WHERE memberid>'0' AND arcrank>'-1' ORDER BY ID DESC LIMIT 0,$n");
  
  $temp->Close();
  require_once(dirname(__FILE__)."/templets/index.htm");
  exit();
}
/*----------------------
//个人会员空间相关
function __Space()
-----------------------*/
//这里允许用id或uid指向空间
if(empty($id))
{
	if(!TestStringSafe($uid)){
			ShowMsg("用户ID不合法！","-1");
			exit();
	}
	$fieldname = 'userid';
	$fieldvalue = $uid;
}else{
	$fieldname = 'ID';
	$fieldvalue = $id;
}
if(!TestStringSafe($uid)){
	ShowMsg("用户ID不合法！","-1");
	exit();
}
//查看个人用户档案
/*--------------------
 function ViewPersionInfo();
------------------*/
if($action=="memberinfo")
{
	require_once(dirname(__FILE__)."/config.php");
	CheckRank(0,0);
	$notarchives = "yes";
	$dsql = new DedeSql(false);
	$spaceInfos = $dsql->GetOne("Select * From #@__member where `$fieldname`='{$fieldvalue}'; ");
	$perInfos = $dsql->GetOne("Select * From #@__member_perinfo where id='{$spaceInfos['ID']}'; ");

	if(!is_array($spaceInfos)){
		$dsql->Close();
		ShowMsg("参数错误或用户已经被删除！","-1");
		exit();
	}
	$spaceInfos = array_merge($spaceInfos, $perInfos);
	//积分头衔
	$scores = $spaceInfos['scores'];
	$honors = @explode("#|",Gethonor($scores));
	$honor = $honors[0];
	$space_star = $honors[1];
	foreach( $spaceInfos as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
	foreach( $perInfos as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
	$pwd = '';
	$userNumID = $ID;
	if($spaceimage==''){
		if($sex=='女') $spaceimage = 'images/space_nophoto.gif';
		else $spaceimage = 'images/space_nophoto.gif';
	}

	require_once(dirname(__FILE__)."/templets/space/member_info.htm");
}
//给用户留言
/*------------------
function GuestBookSend()
------------------*/
else if($action=="feedback"){
	require_once(dirname(__FILE__)."/../include/inc_memberlogin.php");
  $cfg_ml = new MemberLogin();
	$notarchives = "yes";
	$dsql = new DedeSql(false);
	$spaceInfos = $dsql->GetOne("Select ID,uname,spacename,spaceimage,news,sex,c1,c2,spaceshow,logintime,scores From #@__member where `$fieldname`='{$fieldvalue}'; ");
	if(!is_array($spaceInfos)){
		$dsql->Close();
		ShowMsg("参数错误或用户已经被删除！","-1");
		exit();
	}
	//积分头衔
	$scores = $spaceInfos['scores'];
	$honors = @explode("#|",Gethonor($scores));
	$honor = $honors[0];
	$space_star = $honors[1];
	foreach( $spaceInfos as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
	$userNumID = $ID;
	if($spaceimage==''){
		if($sex=='女') $spaceimage = 'images/space_nophoto.gif';
		else $spaceimage = 'images/space_nophoto.gif';
	}
	require_once(dirname(__FILE__)."/templets/space/member_guestbook_form.htm");
}
//会员空间主页面
/*------------------
function ShowSpaceIndex()
------------------*/
elseif($action=="")
{
		require_once(dirname(__FILE__)."/../include/inc_channel_unit_functions.php");
		$notarchives = "yes";
		$dsql = new DedeSql(false);
		$spaceInfos = $dsql->GetOne("Select ID, userid, `type`, uname,c1,c2,spaceshow,logintime,spacename,spaceimage,news,scores From #@__member where `$fieldname`='{$fieldvalue}'; ");
		if(!is_array($spaceInfos)){
			 $dsql->Close();
			 ShowMsg("参数错误或用户已经被删除！","javascript:;");
			 exit();
		}
		//积分头衔
		$scores = $spaceInfos['scores'];
		$honors = @explode("#|",Gethonor($scores));
		$honor = $honors[0];
		$space_star = $honors[1];
		if($spaceInfos['type'] == 0)
		{
			 $perInfo = $dsql->GetOne("select sex from #@__member_perinfo where id='{$spaceInfos['ID']}' ");
			 foreach( $spaceInfos as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
			 foreach( $perInfo as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
			 $userNumID = $ID;
			 $spacename = $spaceInfos['spacename'];
			 if($spaceimage=='')
			 {
				  if($sex=='女') $spaceimage = 'images/space_nophoto.gif';
				  else $spaceimage = 'images/space_nophoto.gif';
			 }
			 $uid = $spaceInfos['userid'];
			 require_once(dirname(__FILE__)."/templets/space/member_index.htm");
		}
		else
		{
			 $uid = $spaceInfos['userid'];
			 $cominfo = $dsql->GetOne("select * from #@__member_cominfo where id='{$spaceInfos['ID']}' ");
			 require_once(dirname(__FILE__)."/company.php");
		}
}
//会员建立的圈子
/*------------------
function ShowSpaceGroups()
------------------*/
elseif($action=="group"){
	require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
	require_once(dirname(__FILE__)."/../include/inc_functions.php");
	$dsql = new DedeSql(false);
	$spaceInfos = $dsql->GetOne("Select ID, userid, `type`, uname,c1,c2,spaceshow,logintime,spacename,spaceimage,news,scores From #@__member where `$fieldname`='{$fieldvalue}'; ");
	if(!is_array($spaceInfos)){
		$dsql->Close();
		ShowMsg("参数错误或用户已经被删除！","javascript:;");
		 exit();
	}
	//积分头衔
	$scores = $spaceInfos['scores'];
	$honors = @explode("#|",Gethonor($scores));
	$honor = $honors[0];
	$space_star = $honors[1];
	$uid = $spaceInfos['userid'];
	$perInfo = $dsql->GetOne("select sex from #@__member_perinfo where id='{$spaceInfos['ID']}' ");
	foreach( $spaceInfos as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
	foreach( $perInfo as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
	$userNumID = $spaceInfos['ID'];
	$spacename = $spaceInfos['spacename'];
	if($spaceimage=='')
	{
		if($sex=='女') $spaceimage = 'images/space_nophoto.gif';
		else $spaceimage = 'images/space_nophoto.gif';
	}
	$sql = "SELECT * FROM #@__groups WHERE ishidden='0' AND uid='{$userNumID}'  ORDER BY threads DESC,stime DESC";
	$dlist = new DataList();
	$dlist->pageSize = 5;
	$dlist->SetParameter("id",$id);
	$dlist->SetParameter("uid",$uid);
	$dlist->SetParameter("action",$action);
	$dlist->SetSource($sql);
	require_once(dirname(__FILE__)."/templets/space/member_group.htm");
	$dlist->Close();
}
//会员主题页面
/*------------------
function ShowSpaceThreads()
------------------*/
elseif($action=="threads"){
	require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
	require_once(dirname(__FILE__)."/../include/inc_functions.php");
	$dsql = new DedeSql(false);
	$spaceInfos = $dsql->GetOne("Select ID, userid, `type`, uname,c1,c2,spaceshow,logintime,spacename,spaceimage,news,scores From #@__member where `$fieldname`='{$fieldvalue}'; ");
	if(!is_array($spaceInfos)){
		$dsql->Close();
		ShowMsg("参数错误或用户已经被删除！","javascript:;");
		 exit();
	}
	//积分头衔
	$scores = $spaceInfos['scores'];
	$honors = @explode("#|",Gethonor($scores));
	$honor = $honors[0];
	$space_star = $honors[1];
	$uid = $spaceInfos['userid'];
	$perInfo = $dsql->GetOne("select sex from #@__member_perinfo where id='{$spaceInfos['ID']}' ");
	foreach( $spaceInfos as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
	foreach( $perInfo as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
	$userNumID = $spaceInfos['ID'];
	$spacename = $spaceInfos['spacename'];
	if($spaceimage=='')
	{
		if($sex=='女') $spaceimage = 'images/space_nophoto.gif';
		else $spaceimage = 'images/space_nophoto.gif';
	}
	$sql = "SELECT subject,digest,replies,gid,tid,views,lastposter,lastpost,closed FROM #@__group_threads WHERE closed=0 AND authorid='{$userNumID}' ORDER BY lastpost DESC";
	$dlist = new DataList();
	$dlist->pageSize = 20;
	$dlist->SetParameter("id",$id);
	$dlist->SetParameter("uid",$uid);
	$dlist->SetParameter("action",$action);
	$dlist->SetSource($sql);
	require_once(dirname(__FILE__)."/templets/space/member_threads.htm");
	$dlist->Close();
}elseif($action=="ask"){
	$timestamp = time();
	require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
	$dsql = new DedeSql(false);
	$spaceInfos = $dsql->GetOne("Select ID, userid, `type`, uname,c1,c2,spaceshow,logintime,spacename,spaceimage,news,scores From #@__member where `$fieldname`='{$fieldvalue}'; ");
	if(!is_array($spaceInfos)){
		$dsql->Close();
		ShowMsg("参数错误或用户已经被删除！","javascript:;");
		 exit();
	}
	//积分头衔
	$scores = $spaceInfos['scores'];
	$honors = @explode("#|",Gethonor($scores));
	$honor = $honors[0];
	$space_star = $honors[1];
	$uid = $spaceInfos['userid'];
	$perInfo = $dsql->GetOne("select sex from #@__member_perinfo where id='{$spaceInfos['ID']}' ");
	foreach( $spaceInfos as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
	foreach( $perInfo as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
	$userNumID = $spaceInfos['ID'];
	$spacename = $spaceInfos['spacename'];
	if($spaceimage=='')
	{
		if($sex=='女') $spaceimage = 'images/space_nophoto.gif';
		else $spaceimage = 'images/space_nophoto.gif';
	}
	$sql = "SELECT id, tid, tidname, tid2, tid2name, title, reward, dateline, expiredtime, solvetime, status, replies FROM #@__ask WHERE uid='{$userNumID}' ORDER BY dateline DESC";
	$dlist = new DataList();
	$dlist->pageSize = 20;
	$dlist->SetParameter("id",$id);
	$dlist->SetParameter("uid",$uid);
	$dlist->SetParameter("action",$action);
	$dlist->SetSource($sql);
	require_once(dirname(__FILE__)."/templets/space/member_asks.htm");
	$dlist->Close();
}elseif($action=="info"){
	require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
	$dsql = new DedeSql(false);
	$spaceInfos = $dsql->GetOne("Select ID, userid, `type`, uname,c1,c2,spaceshow,logintime,spacename,spaceimage,news,scores From #@__member where `$fieldname`='{$fieldvalue}'; ");
	if(!is_array($spaceInfos)){
		$dsql->Close();
		ShowMsg("参数错误或用户已经被删除！","javascript:;");
		 exit();
	}
	//积分头衔
	$scores = $spaceInfos['scores'];
	$honors = @explode("#|",Gethonor($scores));
	$honor = $honors[0];
	$space_star = $honors[1];
	$uid = $spaceInfos['userid'];
	$perInfo = $dsql->GetOne("select sex from #@__member_perinfo where id='{$spaceInfos['ID']}' ");
	foreach( $spaceInfos as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
	foreach( $perInfo as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
	$userNumID = $spaceInfos['ID'];
	$spacename = $spaceInfos['spacename'];
	if($spaceimage=='')
	{
		if($sex=='女') $spaceimage = 'images/space_nophoto.gif';
		else $spaceimage = 'images/space_nophoto.gif';
	}
	$sql = "SELECT ID, typeid, title, senddate, endtime FROM #@__infos WHERE memberID='{$userNumID}' ORDER BY senddate DESC";
	$dlist = new DataList();
	$dlist->pageSize = 20;
	$dlist->SetParameter("id",$id);
	$dlist->SetParameter("uid",$uid);
	$dlist->SetParameter("action",$action);
	$dlist->SetSource($sql);
	require_once(dirname(__FILE__)."/templets/space/member_infos.htm");
	$dlist->Close();
}
?>