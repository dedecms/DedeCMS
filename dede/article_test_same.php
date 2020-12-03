<?php
require_once(dirname(__FILE__)."/config.php");
@set_time_limit(0);
CheckPurview('sys_ArcBatch');
if(empty($dopost)) $dopost = '';
if($dopost=='analyse')
{
	$arr = $dsql->getone("select maintable from `#@__channeltype` where id='$channelid' ");
	if(is_array($arr)) {
		$maintable = $arr['maintable'];
	} else {
		showmsg('频道id不正确，无法处理！','javascript:;');
		exit();
	}
	$dsql->SetQuery("Select count(title) as dd,title From `$maintable` where channel='$channelid' group by title order by dd desc limit 0, $pagesize");
	$dsql->Execute();
	$allarc = 0;
	include DedeInclude('templets/article_result_same.htm');
	exit();
}
//删除选中的内容（只保留一条）
else if($dopost=='delsel')
{
	require_once(dirname(__FILE__)."/../include/typelink.class.php");
	require_once(dirname(__FILE__)."/inc/inc_batchup.php");
	
	if(empty($titles))
	{
		header("Content-Type: text/html; charset={$cfg_ver_lang}");
    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset={$cfg_ver_lang}\">\r\n";
		echo "没有指定删除的文档！";
		exit();
	}
	
	$titless = split('`',$titles);

	if($channelid < -1) {
			$orderby = ($deltype=='delnew' ? " order by aid desc " : " order by aid asc ");
  }
  else {
  	  $orderby = ($deltype=='delnew' ? " order by id desc " : " order by id asc ");
  }
	
	$totalarc = 0;
	
	foreach($titless as $title)
	{
		 $title = trim($title);
		 $title = addslashes( $title=='' ? '' : urldecode($title) );
		 if($channelid < -1) {
		 	 $q1 = "Select aid as id,title From `$maintable` where channel='$channelid' and title='$title' $orderby ";
		 }
		 else {
		 	 $q1 = "Select id,title From `$maintable` where channel='$channelid' and title='$title' $orderby ";
		 }
		 $dsql->SetQuery($q1);
		 $dsql->Execute();
		 $rownum = $dsql->GetTotalRow();
		 if($rownum < 2) continue;
		 $i = 1;
		 while($row = $dsql->GetObject())
		 {
		 	  $i++;
		 	  $naid = $row->id;
		 	  $ntitle = $row->title;
		 	  if($i > $rownum) continue;
		 	  $totalarc++;
		 	  DelArc($naid, 'OFF');
		 }
	}
	$dsql->executenonequery(" OPTIMIZE TABLE `$maintable`; ");
	ShowMsg("一共删除了[{$totalarc}]篇重复的文档！","javascript:;");
	exit();
}

//向导页
$channelinfos = array();
$dsql->setquery("select id,typename,maintable,addtable from `#@__channeltype` ");
$dsql->execute();
while($row = $dsql->getarray()) $channelinfos[] = $row;

include DedeInclude('templets/article_test_same.htm');

?>