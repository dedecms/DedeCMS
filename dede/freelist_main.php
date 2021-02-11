<?php
/**
 * 自由列表管理
 *
 * @version   $Id: freelist_main.php 1 8:48 2010年7月13日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https://weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
CheckPurview('c_FreeList');
require_once DEDEINC . '/channelunit.func.php';
setcookie("ENV_GOBACK_URL", $dedeNowurl, time() + 3600, "/");

if (empty($pagesize)) {
    $pagesize = 18;
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

if (empty($keyword)) {
    $keyword = '';
    $addget = '';
    $addsql = '';
} else {
    $addget = '&keyword=' . urlencode($keyword);
    $addsql = " where title like '%$keyword%' ";
}



//删除字段
if ($dopost == 'del') {
    $aid = preg_replace("#[^0-9]#", "", $aid);
    $dsql->ExecuteNoneQuery("DELETE FROM #@__freelist WHERE aid='$aid'; ");
    $sql = "Select aid,title,templet,click,edtime,namerule,listdir,defaultpage,nodefault From #@__freelist $addsql order by $orderby desc  ";
    $dlist = new DataListCP();
    $dlist->SetTemplet(DEDEADMIN . "/templets/freelist_main.htm");
    $dlist->SetSource($sql);
    $dlist->display();
}

//第一次进入这个页面
if ($dopost == '') {
    $sql = "Select aid,title,templet,click,edtime,namerule,listdir,defaultpage,nodefault From #@__freelist $addsql order by $orderby desc  ";
    $dlist = new DataListCP();
    $dlist->SetTemplet(DEDEADMIN . "/templets/freelist_main.htm");
    $dlist->SetSource($sql);
    $dlist->display();
}
