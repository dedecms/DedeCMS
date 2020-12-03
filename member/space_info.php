<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

$dsql=new DedeSql();

$row = $dsql->GetOne("select spacename,spaceimage,news,mybb from #@__member where ID='".$cfg_ml->M_ID."'");

if(!is_array($row)){
	$row['spacename'] = '';
	$row['spaceimage'] = '';
	$row['mybb'] = '';
	$row['news'] = '';
}

if($row['spaceimage']==''){
	$row['spaceimage'] = 'img/pview.gif';
}

foreach($row as $key=>$value) if(ereg('[^0-9]',$key)) $$key = $value;

require_once(dirname(__FILE__)."/templets/space_info.htm");

?>