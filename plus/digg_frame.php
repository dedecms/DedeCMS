<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");

$action = isset($action) ? trim($action) : '';
$id = (isset($id) && is_numeric($id)) ? $id : 0;
$maintable = '#@__archives';
if($action == 'good')
{
	$dsql->ExecuteNoneQuery("Update `$maintable` set scores = scores + {$cfg_caicai_add},goodpost=goodpost+1,lastpost=".time()." where id=$id");
}
else if($action=='bad')
{
	$dsql->ExecuteNoneQuery("Update `$maintable` set scores = scores - {$cfg_caicai_sub},badpost=badpost+1,lastpost=".time()." where id=$id");
} 
$digg = '';
$row = $dsql->GetOne("Select goodpost,badpost,scores From `$maintable` where id=$id ");
if($row['goodpost']+$row['badpost'] == 0)
{
	$row['goodper'] = $row['badper'] = 0;
} 
else 
{
	$row['goodper'] = number_format($row['goodpost']/($row['goodpost']+$row['badpost']),3)*100;
	$row['badper'] = 100-$row['goodper'];
}
$digg = '<div class="diggbox digg_good" onmousemove="this.style.backgroundPosition=\'left bottom\';" onmouseout="this.style.backgroundPosition=\'left top\';" onclick="postDigg(\'good\','.$id.')">
			<div class="digg_act">顶一下</div>
			<div class="digg_num">('.$row['goodpost'].')</div>
			<div class="digg_percent">
				<div class="digg_percent_bar"><span style="width:'.$row['goodper'].'%"></span></div>
				<div class="digg_percent_num">'.$row['goodper'].'%</div>
			</div>
		</div>
		<div class="diggbox digg_bad" onmousemove="this.style.backgroundPosition=\'right bottom\';" onmouseout="this.style.backgroundPosition=\'right top\';" onclick="postDigg(\'bad\','.$id.')">
			<div class="digg_act">踩一下</div>
			<div class="digg_num">('.$row['badpost'].')</div>
			<div class="digg_percent">
				<div class="digg_percent_bar"><span style="width:'.$row['badper'].'%"></span></div>
				<div class="digg_percent_num">'.$row['badper'].'%</div>
			</div>
		</div>';
include DEDEROOT.'/templets/plus/digg_frame.htm';
