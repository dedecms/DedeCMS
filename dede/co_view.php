<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/dedecollection.class.php");
$backurl = isset($_COOKIE['ENV_GOBACK_URL']) ? $_COOKIE['ENV_GOBACK_URL'] : "co_url.php";
if(empty($action))
{
	$action='';
}
if($aid=='')
{
	ShowMsg('参数无效!','-1');
	exit();
}

//保存更改
if($action=="save")
{
	$result = '';
	for($i=0;$i < $endid;$i++)
	{
		$result .= "{dede:field name=\\'".${"noteid_$i"}."\\'}".${"value_$i"}."{/dede:field}\r\n";
	}
	$dsql->ExecuteNoneQuery("Update `#@__co_htmls` set result='$result' where aid='$aid'; ");
	ShowMsg("成功保存一条记录！",$backurl);
	exit();
}
$dsql->SetSql("Select * from `#@__co_htmls` where aid='$aid'");
$dsql->Execute();
$row = $dsql->GetObject();
$isdown = $row->isdown;
$nid = $row->nid;
$url = $row->url;
$dtime = $row->dtime;
$body = $row->result;
$litpic = $row->litpic;
$fields = array();
if($isdown==0)
{
	$co = new DedeCollection();
	$co->LoadNote($nid);
	$co->DownUrl($aid,$url,$litpic);
	$co->dsql->SetSql("Select * from `#@__co_htmls` where aid='$aid'");
	$co->dsql->Execute();
	$row = $co->dsql->GetObject();
	$isdown = $row->isdown;
	$nid = $row->nid;
	$url = $row->url;
	$dtime = $row->dtime;
	$body = $row->result;
	$litpic = $row->litpic;
}
$dtp = new DedeTagParse();
$dtp->SetNameSpace("dede","{","}");
$dtp->LoadString($body);
include DedeInclude('templets/co_view.htm');

?>