<?php
@ob_start();
@set_time_limit(3600);
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_description');
if(empty($action)) $action = '';

$tjnum = 0;
if($action=='getfields')
{
	AjaxHead();
	$dsql = new DedeSql(false);
	if(!$dsql->linkID){
		echo "<font color='red'>连接数据源的数据库失败！</font><br>";
		echo $qbutton;
		exit();
	}
	$channel = $dsql->getone("select addtable from #@__channeltype where ID=$channel");
	$channel = str_replace('#@__',$cfg_dbprefix,$channel['addtable']);
	$dsql->GetTableFields($channel);
	echo "<div style='border:1px solid #ababab;background-color:#FEFFF0;margin-top:6px;padding:3px;line-height:160%'>";
	echo "表(".$channel.")含有的字段：<br>";
	while($row = $dsql->GetFieldObject()){
		echo "<a href=\"javascript:pf('{$row->name}')\"><u>".$row->name."</u></a>\r\n";
	}
	echo "<input type='hidden' name='addtable' value='$channel' />";
	echo "</div>";
	$dsql->Close();
}elseif($action == 'fetch')
{
	$dsql = new DedeSql(false);
	if(empty($startdd)) $startdd = 0;
	if(empty($pagesize)) $pagesize = 100;
	if(empty($totalnum)) $totalnum = 0;
	if(empty($sid)) $sid = 0;
	if(empty($eid)) $eid = 0;
	$addtable = urldecode($addtable);
	$addtable = ereg_replace("[^a-zA-Z_#@]","",$addtable);
	$rpfield = ereg_replace("[^a-zA-Z_\[\]]","",$rpfield);
	//$channel = ereg_replace("[^0-9]","",$channel);
	$channel = intval($channel);
	$channelinfo = $dsql->getone("select * from #@__channeltype where ID=$channel");
	$maintable = $channelinfo['maintable'];
	require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");
	//统计记录总数
	if($totalnum==0){
		$addquery = " where channel='$channel' ";
		if($sid!=0) $addquery  .= " and ID>='$sid' ";
		if($eid!=0) $addquery .= " and ID<='$eid' ";
		$row = $dsql->GetOne("Select count(*) as dd From $maintable $addquery;");
		$totalnum = $row['dd'];
	}

	//获取记录，并分析
	if($totalnum > $startdd+$pagesize){
		$limitSql = " limit $startdd,$pagesize";
	}elseif(($totalnum-$startdd)>0){
		$limitSql = " limit $startdd,".($totalnum - $startdd);
	}else $limitSql = "";

	$tjnum = $startdd;
	if($limitSql!=""){
		$where = array();
		if($sid!=0) $where[] = "aid>='$sid'";
		if($eid!=0) $where[] = "aid<='$eid'";
		if(!empty($where)){
			$addquery = ' where '.implode(' and ', $where);
		}else{
			$addquery = '';
		}
		$fquery = "Select aid,$rpfield From $addtable $addquery $limitSql ;";

		$dsql->SetQuery($fquery);
		$dsql->Execute();
		while($row=$dsql->GetArray())
		{
			$tjnum++;
			$body = $row[$rpfield];
			$aid = $row['aid'];
			if(strlen($body) < $cfg_arcautosp_size*1024) continue;
			if(!preg_match("/#p#/iU",$body)){
				$body = SpLongBody($body,$cfg_arcautosp_size*1024,"#p#分页标题#e#");
				$body = addslashes($body);
				$dsql->ExecuteNoneQuery("Update $addtable set $rpfield='$body' where aid='$aid' ; ");
			}
		}
	}//end if limit

	//返回进度提示
	if($totalnum>0) $tjlen = ceil( ($tjnum/$totalnum) * 100 );
	else $tjlen=100;

	$dvlen = $tjlen * 2;

	$tjsta = "<div style='width:200;height:15;border:1px solid #898989;text-align:left'><div style='width:$dvlen;height:15;background-color:#829D83'></div></div>";
	$tjsta .= "<br/>完成处理文档总数的：$tjlen %，继续执行任务...";

	if($tjnum < $totalnum)
	{
		$addtable = urlencode($addtable);
		$nurl = "pagination_fetch_action.php?action=fetch&totalnum=$totalnum&startdd=".($startdd+$pagesize)."&pagesize=$pagesize&addtable={$addtable}&rpfield={$rpfield}&channel={$channel}&sid={$sid}&eid={$eid}";
		$dsql->Close();
		ShowMsg($tjsta,$nurl,0,500);
	}else{
		$dsql->executenonequery("OPTIMIZE TABLE `$addtable`");
		$dsql->Close();
		ShowMsg('完成所有任务','javascript:;');
  }
}
ClearAllLink();
?>