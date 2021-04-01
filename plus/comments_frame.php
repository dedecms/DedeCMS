<?php
/**
 *
 * 二级域名评论调用
 *
 * @version        $Id: comments_frame.php 1 20:43 2010年7月8日 $
 * @package        DedeCMS.Site
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC."/datalistcp.class.php");

$action = isset($action) ? trim($action) : '';
$id = empty($id)? 0 : intval(preg_replace("/[^\d]/",'', $id));

if($id < 1) exit();

$siteurl = '';
$sql ="SELECT arctype.siteurl FROM #@__archives arc LEFT JOIN #@__arctype arctype ON arctype.id=arc.typeid WHERE arc.id=$id";
$row = $dsql->GetOne($sql);
if(is_array($row)) $siteurl = $row['siteurl'];

$sql = "SELECT fb.*,mb.userid,mb.face as mface,mb.spacesta,mb.scores
    FROM `#@__feedback` fb
    LEFT JOIN `#@__member` mb ON mb.mid = fb.mid
    WHERE fb.aid=$id and fb.ischeck='1'
    ORDER BY fb.id DESC
    ";

$dlist = new DataListCP();
$dlist->pageSize = 6;
$dlist->SetParameter('id', $id);
$dlist->SetTemplet(DEDETEMPLATE.'/plus/comments_frame.htm');
$dlist->SetSource($sql);
$dlist->display();
