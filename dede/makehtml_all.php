<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_channel_unit_functions.php");
$action = (empty($action) ? '' : $action);

//优化数据
function OptimizeData($dsql)
{
	global $cfg_dbprefix;
	$tptables = array("{$cfg_dbprefix}full_search","{$cfg_dbprefix}cache_tagindex","{$cfg_dbprefix}cache_value");
	$dsql->SetQuery("Select maintable,addtable From `#@__channeltype` ");
	$dsql->Execute();
	while($row = $dsql->GetObject()){
		$maintable = str_replace('#@__',$cfg_dbprefix,$row->maintable);
		$addtable = str_replace('#@__',$cfg_dbprefix,$row->addtable);
		if($maintable!='' && !in_array($maintable,$tptables)) $tptables[] = $maintable;
		if($addtable!='' && !in_array($addtable,$tptables)) $tptables[] = $addtable;
	}
	$tptable = '';
	foreach($tptables as $t){
		$tptable .= ($tptable=='' ? "`{$t}`" : ",`{$t}`" );
	}
	$dsql->ExecuteNoneQuery(" OPTIMIZE TABLE $tptable; ");
}


if($action==''){
  require_once(dirname(__FILE__)."/templets/makehtml_all.htm");
  ClearAllLink();
  exit();
}
/*-----------
function _0_mskeStart()
-----------*/
else if($action=='make')
{
	//step = 1 更新主页、step = 2 更新内容、step = 3 更新栏目
	if(empty($step)) $step = 1;
//更新主页
/*-------------------------
function _1_MakeHomePage()
-------------------*/
if($step==1)
{
	include_once(DEDEADMIN."/../include/inc_arcpart_view.php");
	$starttime = GetMkTime($starttime);
	$mkvalue = ($uptype=='time' ? $starttime : $startid);
	$pv = new PartView();
  $row = $pv->dsql->GetOne("Select * From #@__homepageset");
  $templet = str_replace("{style}",$cfg_df_style,$row['templet']);
  $homeFile = dirname(__FILE__)."/".$row['position'];
	$homeFile = str_replace("\\","/",$homeFile);
	$homeFile = str_replace("//","/",$homeFile);
	$fp = fopen($homeFile,"w") or die("主页文件：{$homeFile} 没有写权限！");
	fclose($fp);
	$pv->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$templet);
	$pv->SaveToHtml($homeFile);
	$pv->Close();
	ShowMsg("更新主页成功，现在开始更新文档页！","makehtml_all.php?action=make&step=2&uptype={$uptype}&mkvalue={$mkvalue}");
	ClearAllLink();
  exit();
}
//更新文档前优化数据
/*-------------------
function _2_OptimizeData1()
---------------------*/
else if($step==2)
{
	$dsql = new DedeSql(false);
	OptimizeData($dsql);
	ClearAllLink();
	ShowMsg("完成数据优化，现在开始更新文档页！","makehtml_all.php?action=make&step=3&uptype={$uptype}&mkvalue={$mkvalue}");
  exit();
}
//更新文档
/*-------------------
function _3_MakeArchives()
---------------------*/
else if($step==3)
{
	include_once(dirname(__FILE__)."/makehtml_archives_action.php");
	ClearAllLink();
	exit();
}
//更新栏目
/*-------------------
function _4_MakeCatalog()
--------------------*/
else if($step==4)
{
	$dsql = new DedeSql(false);
	$mkvalue = intval($mkvalue);
	$typeids = array();
	$adminID = $cuserLogin->getUserID();
	$mkcachefile = DEDEADMIN."/../data/mkall_cache_{$adminID}.php";
	if($mkvalue<=0)
	{
		$dsql->SetQuery("Select ID From `#@__arctype` ");
		$dsql->Execute();
		while($row = $dsql->GetArray()) $typeids[] = $row['ID'];
	}else
	{
		if($uptype=='time') $query = "Select typeid From `#@__full_search` where uptime>='{$mkvalue}' group by typeid";
		else $query = "Select typeid From `#@__full_search` where aid>='{$mkvalue}' group by typeid";
		$dsql->SetQuery($query);
		$dsql->Execute();
		while($row = $dsql->GetArray()){
			if(!isset($typeids[$row['typeid']])) $typeids[$row['typeid']] = 1;
		}
		foreach($typeids as $v){
			$vs = SpGetTopIDS($v);
			foreach($vs as $vv){ if(!isset($typeids[$vv])) $typeids[$row[$vv]] = 1; }
		}
	}
	$fp = fopen($mkcachefile,'w') or die("无法写入缓存文件：{$mkcachefile} 所以无法更新栏目！");
	if(count($typeids)>0)
	{
		fwrite($fp,"<"."?php\r\n");
		$i = -1;
		foreach($typeids as $k=>$t){
			if($k!=''){ $i++; fwrite($fp,"\$idArray[$i]={$k};\r\n"); }
		}
		fwrite($fp,"?".">");
		fclose($fp);
		ClearAllLink();
	  ShowMsg("完成栏目缓存处理，现转向更新栏目！","makehtml_list_action.php?gotype=mkall");
    exit();
	}else{
		fclose($fp);
		ClearAllLink();
		ShowMsg("没有可更新的栏目，现在作最后数据优化！","makehtml_all.php?action=make&step=10");
		exit();
	}
}
//成功状态
/*-------------------
function _10_MakeAllOK()
--------------------*/
else if($step==10)
{
	$adminID = $cuserLogin->getUserID();
	$mkcachefile = DEDEADMIN."/../data/mkall_cache_{$adminID}.php";
	@unlink($mkcachefile);
	$dsql = new DedeSql(false);
	OptimizeData($dsql);
	ClearAllLink();
	ShowMsg("完成所有文件的更新！","javascript:;");
	exit();
}//make step
	
} //action=='make'
ClearAllLink();
exit();
?>