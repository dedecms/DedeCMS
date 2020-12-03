<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('co_EditNote');
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if($nid=="") 
{
	ShowMsg("参数无效!","-1");	
	exit();
}
$dsql = new DedeSql(false);
$rowFirst = $dsql->GetOne("Select * from #@__conote where nid='$nid'");
$notename = $rowFirst['gathername'];
$notes = $rowFirst['noteinfo'];
$exrule = $rowFirst['typeid'];
$arcsource = $rowFirst['arcsource'];
$dsql->FreeResult();
$dtp = new DedeTagParse();
$dtp->SetNameSpace("dede","{","}");
$dtp2 = new DedeTagParse();
$dtp2->SetNameSpace("dede","{","}");
$dtp3 = new DedeTagParse();
$dtp3->SetNameSpace("dede","{","}");
$dtp->LoadString($notes);
foreach($dtp->CTags as $tid => $ctag)
{
	if($ctag->GetName()=="item")
	{
		$imgurl = $ctag->GetAtt("imgurl");
		$imgdir = $ctag->GetAtt("imgdir");
		$language = $ctag->GetAtt("language");
		$matchtype = $ctag->GetAtt("matchtype");
		$refurl = $ctag->GetAtt("refurl");
		$isref = $ctag->GetAtt("isref");
		$exptime = $ctag->GetAtt("exptime");
	}
	else if($ctag->GetName()=="list")
	{
		$sunnote = $ctag->GetInnerText();
		$dtp2->LoadString($sunnote);
		$source = $ctag->GetAtt('source');
		$sourcetype = $ctag->GetAtt('sourcetype');
		$varstart = $ctag->GetAtt('varstart');
		$varend = $ctag->GetAtt('varend');
		$urlTag = $dtp2->GetTagByName('url');
		$needTag = $dtp2->GetTagByName('need');
		$cannotTag = $dtp2->GetTagByName('cannot');
		$linkareaTag = $dtp2->GetTagByName('linkarea');
	}
	else if($ctag->GetName()=="art")
	{
		$sunnote = $ctag->GetInnerText();
		$dtp3->LoadString($sunnote);
		$sppageTag = $dtp3->GetTagByName('sppage');
  }
}

require_once(dirname(__FILE__)."/templets/co_edit.htm");

ClearAllLink();
?>