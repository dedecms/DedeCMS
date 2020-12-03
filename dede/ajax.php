<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('a_Edit,a_AccEdit,a_MyEdit');

$aid = isset($aid) && is_numeric($aid) ? $aid : 0;

AjaxHead();

//编辑标题
if($action == 'show')
{
	$sql = "select title from #@__archives where id=$aid";
	$row = $dsql->getone($sql);
	echo '<input type="text" id="v_'.$aid.'" name="title" value="'.$row['title'].'" />';
	echo '<button onclick="postTitle(\''.$aid.'\')">提交</button>';
}

//编辑标题
elseif($action == 'post')
{
	$sql = "update #@__archives set title='$title' where id=$aid";
	$dsql->executenonequery($sql);
	echo '<a href="archives_do.php?aid='.$aid.'&dopost=editArchives"
	oncontextmenu="ShowMenu(event,this,'.$aid.',\''.urlencode($title).'\')">
	<u>'.$title.'</u></a>';
}


?>