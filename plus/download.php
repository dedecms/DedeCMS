<?php 
//系统设置为维护状态后仍可访问
$cfg_IsCanView = true;
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/inc_channel_unit.php");
if(!isset($open)) $open = 0;
if(!isset($aid)) $aid = "";
$aid = ereg_replace("[^0-9]","",$aid);
//读取链接列表
//------------------
$dsql = new DedeSql(false);
  //读取文档基本信息
  $arctitle = "";
  $arcurl = "";
  $gquery = "Select
  #@__archives.title,#@__archives.senddate,#@__archives.arcrank,
  #@__archives.ismake,#@__archives.typeid,#@__archives.channel,#@__archives.money,
  #@__arctype.typedir,#@__arctype.namerule 
  From #@__archives 
  left join #@__arctype on #@__arctype.ID=#@__archives.typeid 
  where #@__archives.ID='$aid'
  ";
  $arcRow = $dsql->GetOne($gquery);
  if(is_array($arcRow)){
	  $arctitle = $arcRow['title'];
	  $arcurl = GetFileUrl($aid,$arcRow['typeid'],$arcRow['senddate'],$arctitle,$arcRow['ismake'],$arcRow['arcrank'],$arcRow['namerule'],$arcRow['typedir'],$arcRow['money']);
  }else{
	  $dsql->Close();
	  ShowMsg("无法获取未知文档的信息!","-1");
	  exit();
  }
	$cu = new ChannelUnit($arcRow['channel'],$aid);
	if(!is_array($cu->ChannelFields)) {
		$cu->Close();
		$dsql->Close();
	  ShowMsg("获取文档链接信息失败！","-1");
	  exit();
	}


if($open==0)
{

	$vname = "";
	foreach($cu->ChannelFields as $k=>$v){
		if($v['type']=="softlinks"){ $vname=$k; break; }
	}
	if(!is_array($cu->ChannelFields)) {
		$cu->Close();
		$dsql->Close();
	  ShowMsg("获取文档链接信息失败！","-1");
	  exit();
	}
	$row = $dsql->GetOne("Select $vname From ".$cu->ChannelInfos['addtable']." where aid='$aid'");
	$downlinks = $cu->GetAddLinks($row[$vname],$aid,$cid);
	$dsql->Close();
	$cu->Close();
	require_once($cfg_basedir.$cfg_templets_dir."/plus/download_links_templet.htm");
	exit();
}
//提供软件给用户下载
//------------------------
else if($open==1){
	$query = "update {$cu->ChannelInfos['addtable']} set downloads=downloads+1 where aid='$aid'";
	$dsql->setQuery($query);
	$dsql->executenonequery();
	$dsql->Close();
	$cu->Close();
	$link = base64_decode($link);
	echo "<script language='javascript'>location=\"$link\";</script>";
	exit();
}
?>