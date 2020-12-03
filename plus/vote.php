<?php 
require(dirname(__FILE__)."/../include/inc_vote.php");
if(empty($dopost)) $dopost = "";
if(empty($aid)) $aid="";
$aid = ereg_replace("[^0-9]","",$aid);
if($aid=="")
{
	ShowMsg("没指定投票项目的ID！","-1");
	exit();
}
$vo = new DedeVote($aid);
$rsmsg = "";
if($dopost=="send")
{
  if(!empty($voteitem)){
  	$rsmsg = "<br>&nbsp;你方才的投票状态：".$vo->SaveVote($voteitem)."<br>";
  }
}
$vo->Close(); //这个操作仅关闭了数据库 $vo是还可以用的 

             
$voname = $vo->VoteInfos['votename'];
$totalcount = $vo->VoteInfos['totalcount'];
$starttime = GetDateMk($vo->VoteInfos['starttime']);
$endtime = GetDateMk($vo->VoteInfos['endtime']);
$votelist = $vo->GetVoteResult(); 

//显示模板(简单PHP文件)
include_once($cfg_basedir.$cfg_templets_dir."/plus/vote.htm"); 

?>