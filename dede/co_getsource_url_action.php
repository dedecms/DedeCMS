<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('co_PlayNote');
require_once(dirname(__FILE__)."/../include/pub_collection.php");
if(empty($islisten)) $islisten = 0;
if($nid=="") 
{
	ShowMsg("参数无效!","-1");	
	exit();
}

if(empty($glstart)) $glstart = 0;
if(empty($totalnum)) $totalnum = 0;

$gurl = "co_gather_start_action.php?islisten=$islisten&nid=$nid&startdd=$startdd&pagesize=$pagesize&threadnum=$threadnum&sptime=$sptime";

$gurlList = "co_getsource_url_action.php?islisten=$islisten&nid=$nid&startdd=$startdd&pagesize=$pagesize&threadnum=$threadnum&sptime=$sptime";

if($totalnum>0)
{
	ShowMsg("当前节点已下载网页网址，程序直接转向网页采集...",$gurl."&totalnum=$totalnum");
	exit();
}

$co = new DedeCollection();
$co->Init();
$co->LoadFromDB($nid);

$limitList = $co->GetSourceUrl($islisten,$glstart,$pagesize);

if($limitList==0)
{
	$co->dsql->SetSql("Select count(aid) as dd From #@__courl where nid='$nid'");
  $co->dsql->Execute();
  $row = $co->dsql->GetObject();
  $totalnum = $row->dd;
	$co->Close();
	ShowMsg("已获得所有待下载网址，转向网页采集...",$gurl."&totalnum=$totalnum");
	exit();
}else if($limitList>0)
{
	$co->Close();
	ShowMsg("采集列表剩余：{$limitList} 个页面，继续采集...",$gurlList."&glstart=".($glstart+$pagesize),0,100);
	exit();
}else{
	header("Content-Type: text/html; charset={$cfg_ver_lang}");
  echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset={$cfg_ver_lang}\">\r\n";
	echo "获取列表网址失败，无法完成采集！";
}

ClearAllLink();
?>