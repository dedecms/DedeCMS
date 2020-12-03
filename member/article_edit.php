<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
require_once(dirname(__FILE__)."/../include/inc_imgbt.php");
CheckRank(0,0);

$aid = ereg_replace("[^0-9]","",$aid);
$channelid="1";
$dsql = new DedeSql(false);
//读取归档信息
//------------------------------
$arcQuery = "Select 
#@__archives.*,#@__addonarticle.body
From #@__archives
left join #@__addonarticle on #@__addonarticle.aid=#@__archives.ID
where #@__archives.ID='$aid' And #@__archives.memberID='".$cfg_ml->M_ID."'";
$dsql->SetQuery($arcQuery);
$row = $dsql->GetOne($arcQuery);
if(!is_array($row)){
	$dsql->Close();
	ShowMsg("读取文章信息出错!","-1");
	exit();
}

$cInfos = $dsql->GetOne("Select arcsta From #@__channeltype  where ID='1'; ");	

if($row['arcrank']>=0 && $cInfos['arcsta']==-1){
	$dsql->Close();
	ShowMsg("对不起，这篇文章已经被管理员锁定，你不能再更改!","-1");
	exit();
}

$arow = $dsql->GetOne(" Select typename From #@__arctype where ID='".$row['typeid']."'; ");

require_once(dirname(__FILE__)."/templets/article_edit.htm");

?>