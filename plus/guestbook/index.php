<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../../include/pub_datalist_dm.php");
setcookie("GUEST_BOOK_MOVE",GetCurUrl(),time()+3600,"/");

if($gotopagerank=="admin"){
	$userrank = $cuserLogin->getUserRank();
}
else{ $userrank = -1; }

function GetIsCheck($ischeck,$id)
{
	if($ischeck==0) return "<br><a href='edit.php?job=check&ID=$id' style='color:red'>[…Û∫À]</a>";
	else return "";
}


if($userrank>0) $sql = "select * from #@__guestbook order by ID desc";
else $sql = "select * from #@__guestbook where ischeck=1 order by ID desc";

$dlist = new DataList();
$dlist->Init();
$dlist->pageSize = 10;
$dlist->SetParameter("gotopagerank",$gotopagerank);
$dlist->SetSource($sql);
require_once($cfg_basedir.$cfg_templets_dir."/plus/guestbook.htm");
$dlist->Close();
?>