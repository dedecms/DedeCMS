<?php
/**
 * 搜索关键词管理
 *
 * @version   $Id: search_keywords_main.php 1 15:46 2010年7月20日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
setcookie("ENV_GOBACK_URL", $dedeNowurl, time() + 3600, "/");

if (empty($pagesize)) {
    $pagesize = 30;
}

if (empty($pageno)) {
    $pageno = 1;
}

if (empty($dopost)) {
    $dopost = '';
}

if (empty($orderby)) {
    $orderby = 'aid';
}


//更新字段
if ($dopost === 'update') {
    $aid = preg_replace("#[^0-9]#", "", $aid);
    $count = preg_replace("#[^0-9]#", "", $count);
    $keyword = trim($keyword);
    $spwords = trim($spwords);
    $dsql->ExecuteNoneQuery("UPDATE `#@__search_keywords` SET keyword='$keyword',spwords='$spwords',count='$count' WHERE aid='$aid';");
    ShowMsg("更新成功！<br />", $ENV_GOBACK_URL);
    exit();
}
//删除字段
else if ($dopost === 'del') {
    $aid = preg_replace("#[^0-9]#", "", $aid);
    $dsql->ExecuteNoneQuery("DELETE FROM `#@__search_keywords` WHERE aid='$aid';");
    ShowMsg("删除成功！<br />", $ENV_GOBACK_URL);
    exit();
}
//批量删除字段
else if ($dopost === 'delall') {
    foreach (explode(",", $aids) as $aid) {
        $dsql->ExecuteNoneQuery("DELETE FROM `#@__search_keywords` WHERE aid='$aid';");
    }
    ShowMsg("删除成功！", $ENV_GOBACK_URL);
    exit();
}
//第一次进入这个页面
if ($dopost === '' || $dopost === 'getlist') {
    $query = "SELECT * FROM #@__search_keywords ORDER BY 'aid' ";
    $dlist = new DataListCP();
    $dlist->SetTemplet(DEDEADMIN . "/templets/search_keywords_main.htm");
    $dlist->SetSource($query);
    $dlist->display();
}

