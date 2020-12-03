<?
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
$dsql=new DedeSql(false);

$aid = ereg_replace("[^0-9]","",$aid);
$arow = $dsql->GetOne("Select * From #@__uploads where aid='$aid ';");
if($arow['memberid']!=$cfg_ml->M_ID){
	$dsql->Close();
	ShowMsg("你没有修改这个附件的权限！","-1");
	exit();
}
require_once(dirname(__FILE__)."/templets/space_upload_edit.htm");

?>