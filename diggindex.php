<?php
require_once(dirname(__FILE__)."/include/config_base.php");
$aid = intval($aid);
if(empty($aid)) exit();
$dsql = new DedeSql(false);
$tbs = GetChannelTable($dsql,$aid,'arc');
$dsql->ExecuteNoneQuery("Update `#@__full_search` set digg=digg+1,diggtime=".time()." where aid='$aid' ");
$dsql->ExecuteNoneQuery("Update `{$tbs['maintable']}` set digg=digg+1,diggtime=".time()." where ID='$aid' ");
$row = $dsql->GetOne("Select digg From `{$tbs['maintable']}` where ID='$aid' ");
$dsql->Close();
header("Pragma:no-cache");
header("Cache-Control:no-cache");
header("Expires:0");
header("Content-Type: text/html; charset=utf-8");
?>
<span><?php echo $row['digg']; ?></span>
<a href='javascript:alert("顶过了哦！");' class="digvisited"><!--顶一下--></a>
