<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_imgbt.php");
CheckRank(0,0);

if($cfg_mb_album=='否'){
	ShowMsg("对不起，系统禁用了图集的功能，因此无法使用！","-1");
	exit();
}

require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");

$aid = ereg_replace("[^0-9]","",$aid);
$channelid="1";
$dsql = new DedeSql(false);
//读取归档信息
//------------------------------
$arcQuery = "Select 
#@__archives.*,#@__addonimages.*
From #@__archives
left join #@__addonimages on #@__addonimages.aid=#@__archives.ID
where #@__archives.ID='$aid' And #@__archives.memberID='".$cfg_ml->M_ID."'";
$dsql->SetQuery($arcQuery);
$row = $dsql->GetOne($arcQuery);

if(!is_array($row)){
	$dsql->Close();
	ShowMsg("读取图集信息出错!","-1");
	exit();
}

$arow = $dsql->GetOne(" Select typename From #@__arctype where ID='".$row['typeid']."'; ");

require_once(dirname(__FILE__)."/templets/album_edit.htm");

?>