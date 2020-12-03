<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_ArcBatch');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/inc/inc_batchup.php");
@set_time_limit(0);

$t1 = $t2 = 0;
$dsql = new DedeSql(false);

if(empty($startid))	$startid = 0;
if(empty($endid)) $endid = 0;
if(empty($seltime)){
	$seltime = 0;
}else{
	$seltime = 1;
	$t1 = GetMkTime($starttime);
	$t2 = GetMkTime($endtime);
}
if(empty($typeid)) $typeid = 0;
if(empty($newtypeid)) $newtypeid = 0;
if(empty($keyword)) $keyword = '';
$keyword = trim($keyword);

$start = $startid;
$startid = abs(intval($startid));
$endid = abs(intval($endid));
$typeid = intval($typeid);
$newtypeid = intval($newtypeid);

$gwhere = array();
if($startid > 0 ) $gwhere[] = "ID>=$startid";
if($endid > 0) $gwhere[] = "ID<=$endid";
if($typeid != 0){
	$idArray = TypeGetSunID($typeid,$dsql,'',0,true);
	$gwhere[] = "typeid in ($idArray)";
}

if($action == 'check' || $action == 'del'){
	$where = implode(' and ',$gwhere);
	if($where != ''){
		$where = 'where '.$where;
	}

	$channelids = 0;
	$full_search_where = str_replace('ID','aid',$where);
	$query = "select DISTINCT channelid from `#@__full_search` $full_search_where";
	$dsql->setquery($query);
	$dsql->execute();
	while($row = $dsql->getarray())
	{
		$channelids .= ','.$row['channelid'];
	}

	if($seltime == 1){
		if($t1 > 0) $gwhere[] = "senddate>$t1";
		if($t2 > $t1) $gwhere[] = "senddate<$t2";
	}
	if($action == 'check'){
		$gwhere[] = "arcrank='-1'";
	}

	$where = implode(' and ',$gwhere);
	if($where != ''){
		$where = ' where '.$where;
	}

	$query = "select ID,maintable,addtable from #@__channeltype where ID in ($channelids);";
	$dsql->setquery($query);
	$dsql->execute('channel');
	$minid = array();
	$minid = 0;
	$nums = 0;
	while( $row = $dsql->getarray('channel'))
	{
/*
function check()
*/
		if($action == 'check')
		{
			$rs = $dsql->getone("select ID from ".$row['maintable'].$where." order by ID asc limit 1");
			if(is_array($rs)){
				if($minid != 0){
					$minid = min($rs['ID'], $minid);
				}else{
					$minid = $rs['ID'];
				}
			}else{
				showmsg('未找到符合条件的内容','javascript:;');
				exit();
			}
			$query = "update ".$row['maintable']." set arcrank=0 $where";
			$dsql->setquery($query);
			$nums += $dsql->executenonequery2();
			$query = "OPTIMIZE TABLE ".$row['maintable'];
			$dsql->executenonequery($query);
			$dsql->close();
/*
function delete()
*/
		}elseif($action == 'del')
		{
			if($where == ''){
				ShowMsg('该操作必须指定条件！','javascript:;');
				exit();
			}
			$query ="Select ID From ".$row['maintable'].$where;

			$dsql->SetQuery($query);
			$dsql->Execute('x');
			$tdd = 0;

			while($row = $dsql->GetObject('x')){ if(DelArc($row->ID)) $tdd++; }
			$query = "OPTIMIZE TABLE ".$row['maintable'];
			$dsql->executenonequery($query);
			$query = "OPTIMIZE TABLE ".$row['addtable'];
			$dsql->executenonequery($query);
			$dsql->Close();
			ShowMsg("成功删除 $tdd 条记录！","javascript:;");
			exit();
		}
	}// while end
	if($nums < 1){
			showmsg('未找到符合条件的记录', 'javascript:;');
			exit();
		}
	$msg = "共审核 $nums 条记录<br>";
	if($minid > 0){
		$jumpurl  = "makehtml_archives_action.php?endid=$endid&startid=$minid";
		$jumpurl .= "&typeid=$typeid&pagesize=20&seltime=$seltime";
		$jumpurl .= "&stime=".urlencode($starttime)."&etime=".urlencode($endtime);
		$msg .= '<a href='.$jumpurl.'>点击此处开始更新html</a>';
	}
	showmsg($msg,'javascript:;');
	exit();
/*
function move()
*/
}elseif($action == 'move')
{
	if($keyword != ''){
		$gwhere[] = "title like %".$keyword."%";
	}

	$where = implode(' and ',$gwhere);
	if($where != ''){
		$where = 'where '.$where;
	}

	if(empty($typeid) || empty($newtypeid)){
		ShowMsg('该操作必须指定栏目！','javascript:;');
		exit();
	}
	$typeold = $dsql->GetOne("Select * From #@__arctype where ID='$typeid'; ");
	$typenew = $dsql->GetOne("Select * From #@__arctype where ID='$newtypeid'; ");

	if(!is_array($typenew) || !is_array($typeold)){
		$dsql->Close();
		ShowMsg("无法检测栏目信息，不能完成操作！","javascript:;");
		exit();
	}
	if($typenew['ispart']!=0){
		$dsql->Close();
		ShowMsg("你不能把数据移动到非最终列表的栏目！","javascript:;");
		exit();
	}
	if($typenew['channeltype'] != $typeold['channeltype']){
		$dsql->Close();
		ShowMsg("不能把数据移动到内容类型不同的栏目！","javascript:;");
		exit();
	}

	$nrow = $dsql->GetOne("Select addtable,maintable From #@__channeltype where ID='{$typenew['channeltype']}' ");
	$addtable = $nrow['addtable'];
	$maintable = $nrow['maintable'];
	if(empty($maintable)) $maintable = '#@__archives';

	$dsql->SetQuery("Select ID From `$maintable` $where");
	$dsql->Execute('m');
	$tdd = 0;
	while($row = $dsql->GetObject('m')){
	 	 $rs = $dsql->ExecuteNoneQuery("Update `$maintable` set typeid='$newtypeid' where ID='{$row->ID}' ");
	 	 if($rs) $rs = $dsql->ExecuteNoneQuery("Update `$addtable` set typeid='$newtypeid' where aid='{$row->ID}' ");
	   if($rs) $tdd++;
	}

	$query = "OPTIMIZE TABLE ".$maintable;
	$dsql->executenonequery($query);
	$query = "OPTIMIZE TABLE ".$addtable;
	$dsql->executenonequery($query);
	$dsql->Close();
	if($tdd>0)
	{
		$jumpurl  = "makehtml_archives_action.php?endid=$endid&startid=$startid";
		$jumpurl .= "&typeid=$newtypeid&pagesize=20&seltime=$seltime";
		$jumpurl .= "&stime=".urlencode($starttime)."&etime=".urlencode($endtime);
		ShowMsg("成功移动 $tdd 条记录，准备重新生成HTML...",$jumpurl);
		exit();
	}else{
		ShowMsg("完成操作，没移动任何数据...","javascript:;");
		exit();
	}
/*
function makehtml()
*/
}elseif($action == 'makehtml')
{
	$jumpurl  = "makehtml_archives_action.php?endid=$endid&startid=$startid";
	$jumpurl .= "&typeid=$typeid&pagesize=20&seltime=$seltime";
	$jumpurl .= "&stime=".urlencode($starttime)."&etime=".urlencode($endtime);
	header("Location: $jumpurl");
	exit();
}
ClearAllLink();
?>