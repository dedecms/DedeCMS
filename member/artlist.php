<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist.php");
CheckRank(0,0);
setcookie("ENV_GOBACK_URL",GetCurUrl(),time()+3600,"/");

function GetArcSta($sta)
{
	if($sta > -1) return "已审核";
	else return "未审核";
}

function GetArcMan($sta,$aid)
{
	if($sta == -1) return "<a href='artedit.php?dopost=editArc&aid=$aid'>[更改]</a>&nbsp;&nbsp;<a href='archives_do.php?dopost=delArc&aid=$aid'>[删除]</a>";
	else return "<a href='archives_do.php?dopost=viewArchives&aid=$aid' target='_blank'>[查看]</a>";
}

$sql = "
Select 
#@__archives.ID,#@__archives.title,#@__arctype.typename,
#@__archives.arcrank,#@__archives.senddate 
From
#@__archives left join #@__arctype on #@__arctype.ID=#@__archives.typeid 
where
#@__archives.memberID='".$cfg_ml->M_ID."' order by senddate desc
";

$dlist = new DataList();
$dlist->Init();
$dlist->SetSource($sql);
$dlist->SetTemplet(dirname(__FILE__)."/templets/artlist.htm");
$dlist->display();
$dlist->Close();
?>