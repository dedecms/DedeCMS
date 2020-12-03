<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
$t1 = ExecTime();
require_once(dirname(__FILE__)."/../include/inc_archives_view.php");
if(empty($startid)) $startid = 0;//起始ID号
if(empty($endid)) $endid = 0;//结束ID号
if(empty($startdd)) $startdd = 0;//结果集起始记录值
if(empty($pagesize)) $pagesize = 20;
if(empty($totalnum)) $totalnum = 0;
if(empty($typeid)) $typeid = 0;
if(empty($sss)) $sss = time();
if(empty($mkvalue)) $mkvalue = '';
//一键更新传递的参数
if(!empty($uptype)){
	if($uptype!='time') $startid = $mkvalue;
}else{
	$uptype = '';
}

header("Content-Type: text/html; charset={$cfg_ver_lang}");

$dsql = new DedeSql(false);
//获取条件
//------------------------
$gwhere = " where arcrank=0 ";
if($startid>0) $gwhere .= " And aid >= $startid ";
if($endid > $startid) $gwhere .= " And aid <= $endid ";
/*
if(!empty($onlymake)){
	$gwhere .= " and ismake=0 ";
}
*/
if($typeid!=0){
	$typeids = TypeGetSunID($typeid,$dsql,"",0,true);
  $gwhere .= " And typeid in ($typeids)";
}

if($uptype=='time'){
	 $gwhere .= " And uptime >= '$mkvalue' ";
}

//统计记录总数
//------------------------
if($totalnum==0)
{
	$row = $dsql->GetOne("Select count(*) as dd From `#@__full_search` $gwhere");
	$totalnum = $row['dd'];
}
//获取记录，并生成HTML
$nlimit = $totalnum - $startdd;
if($totalnum > $startdd+$pagesize){
	$limitSql = " limit $startdd,$pagesize";
	$nlimit = 1;
}else{
	$limitSql = " limit $startdd,{$nlimit}";
}

$tjnum = $startdd;
if($nlimit>0)
{
  $dsql->SetQuery("Select aid as ID From `#@__full_search` $gwhere $limitSql");
  $dsql->Execute();
  while($row=$dsql->GetObject())
  {
	  $tjnum++;
	  $ID = $row->ID;
	  $ac = new Archives($ID);
	  if(!$ac->IsError){
	  	$rurl = $ac->MakeHtml();
	  }else{
	  	echo "文档： $ID 错误！<br />\r\n";
	  	$rurl = '';
	  }
  }
}

$t2 = ExecTime();
$t2 = ($t2 - $t1);

//返回提示信息
if($totalnum>0) $tjlen = ceil( ($tjnum/$totalnum) * 100 );
else $tjlen=100;

$dvlen = $tjlen * 2;
$nntime = time();
$utime = $nntime - $sss;

if($utime>0){ $utime = number_format(($utime/60),2); }

$tjsta = "<div style='width:200;height:15;border:1px solid #898989;text-align:left'><div style='width:$dvlen;height:15;background-color:#829D83'></div></div>";
$tjsta .= "<br>本次用时：".number_format($t2,2)." 到达位置：".($startdd+$pagesize)."<br/>完成创建文件总数的：$tjlen %，<br> 总用时: {$utime} 分钟， 继续执行任务...";
  
if($tjnum < $totalnum)
{
	 $nurl  = "makehtml_archives_action.php?sss=$sss&endid=$endid&startid=$startid&typeid=$typeid";
	 $nurl .= "&totalnum=$totalnum&startdd=".($startdd+$pagesize)."&pagesize=$pagesize&uptype={$uptype}&mkvalue={$mkvalue}";
	 ShowMsg($tjsta,$nurl,0,100);
	 ClearAllLink();
	 exit();
}else
{
	 if($uptype==''){
		  echo "完成所有创建任务，总用时: {$utime} 分钟 。";
		  ClearAllLink();
	    exit();
	 }else{
		  ShowMsg("完成所有文档更新，现在重新优化数据！","makehtml_all.php?action=make&step=4&uptype={$uptype}&mkvalue={$mkvalue}");
		  ClearAllLink();
		  exit();
	 }
}

?>