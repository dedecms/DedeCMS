<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");

$t1 = ExecTime();

$tid = (isset($tid) && is_numeric($tid) ? $tid : 0);

$channelid = (isset($channelid) && is_numeric($channelid) ? $channelid : 0);

if($tid==0 && $channelid==0) die(" Request Error! ");

//如果指定了内容模型ID但没有指定栏目ID，那么自动获得为这个内容模型的第一个顶级栏目作为频道默认栏目
if(!empty($channelid) && empty($tid))
{
	$tinfos = $dsql->GetOne("Select tp.id,ch.issystem From `#@__arctype` tp left join `#@__channeltype` ch on ch.id=tp.channeltype where tp.channeltype='$channelid' And tp.reid=0 order by sortrank asc");
	if(!is_array($tinfos)) die(" No catalogs in the channel! ");
	$tid = $tinfos['id'];
}
else
{
	$tinfos = $dsql->GetOne("Select ch.issystem From `#@__arctype` tp left join `#@__channeltype` ch on ch.id=tp.channeltype where tp.id='$tid' ");
}

if($tinfos['issystem']==-1)
{
	$nativeplace = ( (empty($nativeplace) || !is_numeric($nativeplace)) ? 0 : $nativeplace );
	$infotype = ( (empty($infotype) || !is_numeric($infotype)) ? 0 : $infotype );
	if(!empty($keyword)) $keyword = FilterSearch($keyword);
	$cArr = array();
	if(!empty($nativeplace)) $cArr['nativeplace'] = $nativeplace;
	if(!empty($infotype)) $cArr['infotype'] = $infotype;
	if(!empty($keyword)) $cArr['keyword'] = $keyword;
	include(DEDEINC."/arc.sglistview.class.php");
	$lv = new SgListView($tid,$cArr);
}
else
{
	include(DEDEINC."/arc.listview.class.php");
	$lv = new ListView($tid);
}

if($lv->IsError)
{
	ParamError();
}

$lv->Display();

?>