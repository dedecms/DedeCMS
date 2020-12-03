<?php
require(dirname(__FILE__)."/../include/common.inc.php");
require(DEDEINC."/dedevote.class.php");
if(empty($dopost)) $dopost = '';

$aid = (isset($aid) && is_numeric($aid)) ? $aid : 0;
if($aid==0) die(" Request Error! ");

if($aid==0)
{
	ShowMsg("没指定投票项目的ID！","-1");
	exit();
}
$vo = new DedeVote($aid);
$rsmsg = '';

if($dopost=='send')
{
  if(!empty($voteitem))
  {
  	$rsmsg = "<br />&nbsp;你方才的投票状态：".$vo->SaveVote($voteitem)."<br />";
  }
  else
  {
  	$rsmsg = "<br />&nbsp;你刚才没选择任何投票项目！<br />";
  }
}

$voname = $vo->VoteInfos['votename'];
$totalcount = $vo->VoteInfos['totalcount'];
$starttime = GetDateMk($vo->VoteInfos['starttime']);
$endtime = GetDateMk($vo->VoteInfos['endtime']);
$votelist = $vo->GetVoteResult("98%",30,"30%");

//显示模板(简单PHP文件)
include(DEDETEMPLATE.'/plus/vote.htm');

?>