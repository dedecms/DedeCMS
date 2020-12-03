<?php

/**
 * 二级域名评论调用
 *
 * @author cha369
 * @package dedecms
 */
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC."/datalistcp.class.php");

$action = isset($action) ? trim($action) : '';
$id = empty($id)? 0 : intval(preg_replace("/[^\d]/",'', $id));

if($id < 1)
{
	exit();
}

$siteurl = '';
$sql ="select arctype.siteurl from #@__archives arc left join #@__arctype arctype on arctype.id=arc.typeid where arc.id=$id";
$row = $dsql->GetOne($sql);
if(is_array($row))
{
	$siteurl = $row['siteurl'];
}

$sql = "SELECT fb.*,mb.userid,mb.face as mface,mb.spacesta,mb.scores
FROM `#@__feedback` fb
LEFT JOIN `#@__member` mb ON mb.mid = fb.mid
WHERE fb.aid=$id and fb.ischeck='1'
ORDER BY fb.id DESC";

$dlist = new DataListCP();
$dlist->pageSize = 6;
$dlist->SetParameter('id', $id);
$dlist->SetTemplet(DEDETEMPLATE.'/plus/comments_frame.htm');
$dlist->SetSource($sql);
$dlist->display();
