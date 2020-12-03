<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_collection.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
isset($_COOKIE['ENV_GOBACK_URL']) ? $backurl = $_COOKIE['ENV_GOBACK_URL'] : $backurl = "co_url.php";
if(empty($action)) $action="";
if($aid=="") {
	ShowMsg("参数无效!","-1");	
	exit();
}

//保存更改
if($action=="save"){
	$dsql = new DedeSql(false);
	$result = "";
	for($i=0;$i < $endid;$i++){
		$result .= "{dede:field name=\\'".${"noteid_$i"}."\\'}".${"value_$i"}."{/dede:field}\r\n";
	}
	$dsql->ExecuteNoneQuery("Update #@__courl set result='$result' where aid='$aid'; ");
	$dsql->Close();
	ShowMsg("成功保存一条记录！",$backurl);
	exit();
}

$dsql = new DedeSql(false);
$dsql->SetSql("Select * from #@__courl where aid='$aid'");
$dsql->Execute();
$row = $dsql->GetObject();
$isdown = $row->isdown;
$nid = $row->nid;
$url = $row->url;
$dtime = $row->dtime;
$body = $row->result;
$dsql->Close();
$fields = array();
if($isdown==0)
{
	$co = new DedeCollection();
	$co->Init();
	$co->LoadFromDB($nid);
	$co->DownUrl($aid,$url);
	$co->dsql->SetSql("Select * from #@__courl where aid='$aid'");
	$co->dsql->Execute();
	$row = $co->dsql->GetObject();
	$isdown = $row->isdown;
	$nid = $row->nid;
	$url = $row->url;
	$dtime = $row->dtime;
	$body = $row->result;
	$co->Close();
}
$dtp = new DedeTagParse();
$dtp->SetNameSpace("dede","{","}");
$dtp->LoadString($body);

require_once(dirname(__FILE__)."/templets/co_view.htm");

ClearAllLink();
?>