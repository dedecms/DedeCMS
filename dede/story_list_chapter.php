<?php
/**
 * @version        $Id: story_list_chapter.php 1 9:02 2010年9月25日 $
 * @package        DedeCMS.Module.Book
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */

require_once(dirname(__FILE__). "/config.php");
require_once DEDEINC. '/datalistcp.class.php';
setcookie("ENV_GOBACK_URL", $dedeNowurl, time()+3600,"/");
CheckPurview('story_books');
if(!isset($action)) $action = '';

if(!isset($keyword)) $keyword = "";

if(!isset($bid)) $bid = 0;

if(!empty($bookid)) $bid = $bookid;


$addquery = " id>0 ";
$orderby = " ORDER BY id DESC ";
if($keyword!="") $addquery .= " And (bookname LIKE '%$keyword%' OR chaptername LIKE '%$keyword%') ";

if($bid!=0) $addquery .= " And bookid='$bid' ";


$query = "
   SELECT * FROM #@__story_chapter WHERE $addquery $orderby
";
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("keyword", $keyword);
$dlist->SetParameter("bid", $bid);
$dlist->SetTemplate(DEDEADMIN. '/templets/story_list_chapter.htm');
$dlist->SetSource($query);
$dlist->Display();
