<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_collection.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
if($aid=="") 
{
	ShowMsg("参数无效!","-1");	
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
	$co->dsql->Init(false);
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

for($i=0;$i<$dtp->GetCount();$i++)
{
	$ctag = $dtp->CTags[$i];
	if($ctag->GetName()=="field"){
		$fields[$ctag->GetAtt("name")] = $ctag->GetInnerText();
	}
}
$dtp->Clear();

$dtp->SetNameSpace("dede","{","}");
$dtp->LoadFile(dirname(__FILE__)."/templets/co_view.htm");
$dtp->display();
$dtp->Clear();

?>