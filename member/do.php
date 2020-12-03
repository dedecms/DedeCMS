<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/com_config.php");

require_once(dirname(__FILE__)."/../include/inc_type_tree_member.php");
if($action =='add')
{
	 if(!isset($dopost)) $dopost = '';
   if(!isset($c)) $c = 0;
   $opall = (!isset($opall) ? false : true);
   $issend = (!isset($issend) ? 1 : $issend);
   $channelid = (!isset($channelid) ? 0 : $channelid);
	//载入子栏目
	if($dopost=='GetSunListsTree'){
		header("Pragma:no-cache\r\n");
		header("Cache-Control:no-cache\r\n");
		header("Expires:0\r\n");
		header("Content-Type: text/html; charset=utf-8");
		PutCookie('lastCidTree',$cid,3600*24,"/");
		$tu = new TypeTreeMember();
		$tu->dsql = new DedeSql(false);
		$tu->LogicListAllSunType($cid,'|--',$opall,$issend,$channelid);
		$tu->Close();
		exit();
	}
	require_once(dirname(__FILE__)."/templets/company/infotype.htm");
	exit();
}elseif($action == 'edit')
{
	require_once(dirname(__FILE__)."/infoedit.php");
  exit();
}elseif($action == 'list')
{
	require_once(dirname(__FILE__).'/content_list.php');
	exit();
}elseif($action == 'delete')
{
	require_once(dirname(__FILE__)."/inc/inc_batchup.php");

	if(!empty($_COOKIE['ENV_GOBACK_URL'])) $ENV_GOBACK_URL = $_COOKIE['ENV_GOBACK_URL'];
	else $ENV_GOBACK_URL = 'do.php?action=list&channelid=';
	$aid = intval($aid);

	$equery = "Select aid,uptime,arcrank,channelid From `#@__full_search` where aid='$aid' And mid='{$cfg_ml->M_ID}'";
	$row = $dsql->GetOne($equery);
	$exday = 3600 * 24 * $cfg_locked_day;
  $ntime = mytime();
	if(!is_array($row)){
		$dsql->Close();
	  ShowMsg("你没有权限删除这篇文档！","-1");
	  exit();
	}else if($row['arcrank']>=0 && $row['uptime']-$ntime > $exday){
		$dsql->Close();
	  ShowMsg("这篇文档已被锁定，你不能再删除它！","-1");
	  exit();
	}
	$channelid = $row['channelid'];

	//删除文档
	DelArc($aid);

	//更新用户记录
	if($channelid==1) $dsql->ExecuteNoneQuery("Update #@__member set c1=c1-1,scores=scores-{$cfg_send_score} where ID='".$cfg_ml->M_ID."';");
	else if($channelid==2) $dsql->ExecuteNoneQuery("Update #@__member set c2=c2-1,scores=scores-{$cfg_send_score} where ID='".$cfg_ml->M_ID."';");
	else $dsql->ExecuteNoneQuery("Update #@__member set c3=c3-1,scores=scores-{$cfg_send_score} where ID='".$cfg_ml->M_ID."';");

	$dsql->Close();

	if($ENV_GOBACK_URL=='content_list.php?channelid=') $ENV_GOBACK_URL = $ENV_GOBACK_URL.$channelid;
	ShowMsg("成功删除一篇文档！",$ENV_GOBACK_URL);
	exit();
}
?>