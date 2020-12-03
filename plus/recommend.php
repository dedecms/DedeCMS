<?php 
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

//显示模板(简单PHP文件)
include_once($cfg_basedir.$cfg_templets_dir."/plus/recommend.htm");

?>
