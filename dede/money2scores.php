<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_List');

require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");

$query = "select * from #@__score2money_logs order by dateline desc";

$dlist = new DataList();
$dlist->Init();
$dlist->pageSize = 20;
$dlist->SetSource($query);
$log = $dlist->GetDataList();
$logs = array();
while($row = $log->GetArray('dm')) {
	$row['dbdateline'] = GetDateTimeMk($row['dateline']);
	$row['dbtype'] = $row['type'] == 'score2money' ? '积分 → 金币' : '金币 → 积分';

	$logs[] = $row;
}
$page = $dlist->GetPageList(7);
require_once(dirname(__FILE__)."/templets/money2score.htm");
ClearAllLink();
?>