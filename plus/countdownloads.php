<?php 
//系统设置为维护状态可访问
$cfg_IsCanView = true;
$__ONLYDB = true;
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/inc_channel_unit.php");

if(empty($aid) || empty($cid)) exit;

$aid = ereg_replace("[^0-9]","",$aid);
$cid = ereg_replace("[^0-9]","",$cid);

//读取链接列表
//------------------
$dsql = new DedeSql(false);
  //读取文档基本信息
  $arctitle = "";
  $arcurl = "";
  $gquery = "Select
  addtable 
  From #@__channeltype 
  where ID='$cid'
  ";
  $channel = $dsql->GetOne($gquery);
  if(!is_array($channel)){
	  exit();
  }

$row = $dsql->GetOne("Select downloads From $channel[0] where aid='$aid'");
	echo "document.write('".$row[0]."');\r\n";

$dsql->Close();
exit();

/*-----------------------------------
如果想显示浏览次数,请增加view参数,即把下面ＪＳ调用放到文档模板适当位置 
<script src="{dede:field name='phpurl'/}/countdownloads.php?aid={dede:field name='aid'/}&cid={dede:field name='channel'/}" language="javascript"></script>
----------------------------------*/
?>