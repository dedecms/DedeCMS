<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
$t1 = ExecTime();
require_once(dirname(__FILE__)."/../include/inc_archives_view.php");
if(empty($startid)) $startid = 1;//起始ID号
if(empty($endid)) $endid = 0;//结束ID号
if(empty($startdd)) $startdd = 0;//结果集起始记录值
if(empty($pagesize)) $pagesize = 20;
if(empty($totalnum)) $totalnum = 0;
if(empty($typeid)) $typeid = 0;
if(empty($seltime)) $seltime = 0;
if(empty($stime)) $stime = "";
if(empty($etime)) $etime = "";
if(empty($sss)) $sss = time();

$dsql = new DedeSql(false);
//获取条件
//------------------------
$gwhere = " where ID>=$startid And arcrank=0 ";
if($endid > $startid) $gwhere .= " And ID<= $endid ";
$idsql = "";
if($typeid!=0){
	$idArrary = TypeGetSunTypes($typeid,$dsql,0);
	if(is_array($idArrary))
	{
	  foreach($idArrary as $tid){
		  if($idsql=="") $idsql .= " typeid=$tid ";
		  else $idsql .= " or typeid=$tid ";
	  }
	  $idsql = " And (".$idsql.")";
  }
  $idsql = $gwhere.$idsql;
}
if($idsql=="") $idsql = $gwhere;
if($seltime==1){
	 $t1 = GetMkTime($stime);
	 $t2 = GetMkTime($etime);
	 $idsql .= " And (senddate >= $t1 And senddate <= $t2) ";
}
//统计记录总数
//------------------------
if($totalnum==0)
{
	$row = $dsql->GetOne("Select count(*) as dd From #@__archives $idsql");
	$totalnum = $row['dd'];
}
//获取记录，并生成HTML
if($totalnum > $startdd+$pagesize) $limitSql = " limit $startdd,$pagesize";
else $limitSql = " limit $startdd,".($totalnum - $startdd);
$tjnum = $startdd;
$dsql->SetQuery("Select ID From #@__archives $idsql $limitSql");
$dsql->Execute();
while($row=$dsql->GetObject())
{
	$tjnum++;
	$ID = $row->ID;
	$ac = new Archives($ID);
	$rurl = $ac->MakeHtml();
	$ac->Close();
}

$t2 = ExecTime();
$t2 = ($t2 - $t1);

//返回提示信息
if($totalnum>0) $tjlen = ceil( ($tjnum/$totalnum) * 100 );
else $tjlen=100;
$dvlen = $tjlen * 2;
$nntime = time();
$utime = $nntime - $sss;
if($utime>0){
	$utime = number_format(($utime/60),2);
}
$tjsta = "<div style='width:200;height:15;border:1px solid #898989;text-align:left'><div style='width:$dvlen;height:15;background-color:#829D83'></div></div>";
$tjsta .= "<br>本次用时：".number_format($t2,2)." 到达位置：".($startdd+$pagesize)."<br/>完成创建文件总数的：$tjlen %，<br> 总用时: {$utime} 分钟， 继续执行任务...";

if($tjnum < $totalnum)
{
	$nurl  = "makehtml_archives_action.php?sss=$sss&endid=$endid&startid=$startid&typeid=$typeid";
	$nurl .= "&totalnum=$totalnum&startdd=".($startdd+$pagesize)."&pagesize=$pagesize";
	$nurl .= "&seltime=$seltime&stime=".urlencode($stime)."&etime=".urlencode($etime);
	$dsql->Close();
	ShowMsg($tjsta,$nurl,0,100);
	exit();
}
else
{
	$dsql->Close();
	echo "完成所有创建任务，总用时: {$utime} 分钟 。";
	exit();
}

?>