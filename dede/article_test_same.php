<?php
require_once(dirname(__FILE__)."/config.php");
@set_time_limit(0);
CheckPurview('sys_ArcBatch');
if(empty($dopost)) $dopost = '';

$dsql = new DedeSql(false);

if($dopost=='analyse'){
	$channelid = intval($channelid);
	$maintable = $dsql->getone("select maintable from #@__channeltype where ID=$channelid");
	if(is_array($maintable)){
		$maintable = $maintable['maintable'];
	}else{
		$dsql->close();
		showmsg('频道id不正确，请重新选择频道','javascript:;');
		exit();
	}

	$dsql->SetQuery("Select count(title) as dd,title From $maintable where channel=$channelid group by title order by dd desc limit 0,$pagesize");
	$dsql->Execute();
	$allarc = 0;
	require_once(dirname(__FILE__)."/templets/article_result_same.htm");
	$dsql->Close();
	exit();
}else if($dopost=='delsel'){
	require_once(dirname(__FILE__)."/../include/inc_typelink.php");
	require_once(dirname(__FILE__)."/inc/inc_batchup.php");
	if(empty($titles)){
		$dsql->close();
		header("Content-Type: text/html; charset={$cfg_ver_lang}");
    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset={$cfg_ver_lang}\">\r\n";
		echo "没有指定删除的文档！";
		exit();
	}
	$titless = split('`',$titles);

	if($deltype=='delnew') $orderby = " order by ID desc ";
	else $orderby = " order by ID asc ";
	$totalarc = 0;
	foreach($titless as $title){
		 $title = trim($title);
		 if($title=='') $q1 = "Select ID,title From $maintable where channel='$channelid' and title='' $orderby ";
		 else{
		 	  $title = addslashes(urldecode($title));
		 	  $q1 = "Select ID,title From $maintable where channel='$channelid' and title='$title' $orderby ";
		 }
		 $dsql->SetQuery($q1);
		 $dsql->Execute();
		 $rownum = $dsql->GetTotalRow();
		 if($rownum<2) continue;
		 $i = 1;
		 while($row = $dsql->GetObject()){
		 	 $i++;
		 	 $naid = $row->ID;
		 	 $ntitle = $row->title;
		 	 if($i > $rownum){ continue; }
		 	 $totalarc++;
		 	 DelArc($naid);
		 }
	}
	$dsql->executenonequery("OPTIMIZE TABLE `$maintable`");
	$dsql->Close();
	ShowMsg("一共删除了[{$totalarc}]篇重复的文档！","javascript:;");
	exit();
}

$channelinfos = array();
$dsql->setquery("select ID,typename,maintable,addtable from #@__channeltype");
$dsql->execute();
while($row = $dsql->getarray())
{
	$channelinfos[] = $row;
}
require_once(dirname(__FILE__)."/templets/article_test_same.htm");
ClearAllLink();
?>