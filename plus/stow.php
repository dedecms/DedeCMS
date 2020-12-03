<?php
require(dirname(__FILE__)."/../include/config_base.php");
require(dirname(__FILE__)."/../include/inc_memberlogin.php");
require(dirname(__FILE__)."/../include/inc_channel_unit.php");
if(!empty($artID)) $arcID = $artID;
if(!isset($arcID)) $arcID = "";
$arcID = ereg_replace("[^0-9]","",$arcID);
if(empty($arcID)){
	ShowMsg("文档ID不能为空!","-1");
	exit();
}
$ml = new MemberLogin();
if($ml->M_ID==0){
	ShowMsg("只有会员才允许这项操作！","-1");
	exit();
}
$dsql = new DedeSql(false);

if(empty($type))
{
	//读取文档信息
	$arctitle = "";
	$arcurl = "";
	$tableinfo = $dsql->getone("select c.maintable from `#@__full_search` i
		left join #@__channeltype c on c.ID=i.channelid where i.aid=$arcID");
	$maintable = $tableinfo['maintable'];
	$arcRow = $dsql->GetOne("Select title From $maintable where ID='$arcID'");
	if(is_array($arcRow)){
		$arctitle = $arcRow['title'];
	}
	else{
		$dsql->Close();
		ShowMsg("无法收藏未知文档!","-1");
		exit();
	}

	$tmp = $dsql->getone("select aid from #@__memberstow where arcid='$arcID' and uid={$ml->M_ID} && url='' ");
	if(is_array($tmp)){
		showmsg('您已经收藏过该网页', '-1');
		exit();
	}
	$addtime = time();
	$dsql->SetQuery("INSERT INTO #@__memberstow(uid,arcid,title,url,addtime)
		VALUES ('".$ml->M_ID."','$arcID','$arctitle','','$addtime');");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功收藏一篇文档！",'-1');
	exit();
}elseif($type == 'book'){
	//读取文档信息
	$arctitle = "";
	$arcurl = "";
	$arcRow = $dsql->GetOne("Select bookname From #@__story_books where id='$arcID'");
	if(is_array($arcRow)){
		$arctitle = $arcRow['bookname'];
		$arcurl = $cfg_cmspath.'/book/book.php?id='.$arcID;
	}else{
		$dsql->Close();
		ShowMsg("无法收藏未知文档!","-1");
		exit();
	}
	$tmp = $dsql->getone("select aid from #@__memberstow
		where arcid='$arcID' and uid={$ml->M_ID} &&  url='$arcurl'");
	if(is_array($tmp)){
		showmsg('您已经收藏过该网页', '-1');
		exit();
	}
	$addtime = time();
	$dsql->SetQuery("INSERT INTO #@__memberstow(uid,arcid,title,addtime,url) VALUES ('".$ml->M_ID."','$arcID','$arctitle','$addtime','$arcurl');");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功收藏一篇文档！",'-1');
	exit();
}
?>