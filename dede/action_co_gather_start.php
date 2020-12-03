<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_collection.php");
if($totalnum==0)
{
	ShowMsg("种子网址为零！","",1);
	exit();
}
$dsql = new DedeSql(false);
//多线程模式初次设置
if(!isset($threadnum)) $threadnum = 0;
if(!isset($oldstart)) $oldstart = $startdd;
if(!isset($thethr)) $thethr = 0;
if($threadnum>0)
{
	$step = ceil($totalnum / $threadnum);
	$j = 0;
	for($i=1;$i<=$totalnum;$i++)
	{
		if($i%$step==0)
		{
			$j++;
			$sdd = ($i-$step);
			$surl = "action_co_gather_start.php?thethr=$j&sptime=$sptime&nid=$nid&oldstart=$sdd&startdd=$sdd&totalnum=".($step * $j)."&pagesize=$pagesize";
			echo "<iframe scrolling='no' name='thredfrm$j' frameborder='0' width='100%' height='200' src='$surl'></iframe>\r\n";
		}
	}
	if($totalnum % $threadnum != 0)
	{
		
		$sdd = $j*$step;
		$k = $j+1;
		$surl = "action_co_gather_start.php?thethr=$k&sptime=$sptime&nid=$nid&oldstart=$sdd&startdd=$sdd&totalnum=$totalnum&pagesize=$pagesize";
		echo "<iframe scrolling='no' name='thredfrm$j' frameborder='0' width='100%' height='200' src='$surl'></iframe>\r\n";
	}
	exit();
}
if($totalnum > $startdd+$pagesize) $limitSql = " limit $startdd,$pagesize";
else $limitSql = " limit $startdd,".($totalnum - $startdd);
$co = new DedeCollection();
$co->Init();
$co->LoadFromDB($nid);
$co->dsql->SetQuery("Update #@__conote set lasttime='".time()."' where nid=$nid");
$co->dsql->ExecuteNoneQuery();
$co->dsql->SetQuery("Select aid,url From #@__courl where nid=$nid $limitSql");
$co->dsql->Execute(99);
$tjnum = $startdd;
while($row = $co->dsql->GetObject(99))
{
	$co->DownUrl($row->aid,$row->url);
	$tjnum++;
	if($sptime>0) sleep($sptime);
}
$co->Close();
$tjlen = ceil( (($tjnum-$oldstart)/($totalnum-$oldstart)) * 100 );
$dvlen = $tjlen * 2;
$tjsta = "<div style='width:200;height:15;border:1px solid #898989;text-align:left'><div style='width:$dvlen;height:15;background-color:#829D83'></div></div>";
$tjsta .= "<br/>完成线程 $thethr 的：$tjlen %，继续执行任务...";
if($tjnum < $totalnum)
{
	ShowMsg($tjsta,"action_co_gather_start.php?thethr=$thethr&sptime=$sptime&nid=$nid&oldstart=$oldstart&totalnum=$totalnum&startdd=".($startdd+$pagesize)."&pagesize=$pagesize");
	exit();
}
else
{
	ShowMsg("完成当前下载任务！","javascript:;");
	exit();
}
?>