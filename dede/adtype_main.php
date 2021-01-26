<?php
/**
 * 广告类型
 *
 * @version   $Id: adtype_main.php 1 8:48 2010年7月13日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https://weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
if (empty($dopost)) {
    $dopost = '';
}

if ($dopost === "save") {
    $query = "UPDATE `#@__myadtype` SET typename='$pname' WHERE id='$tid' ";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg("成功更新广告分类列表！!", "adtype_main.php");
    exit();
} else if ($dopost === "del") {
    $query = "DELETE FROM `#@__myadtype` WHERE id='$id' ";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg("成功删除广告分类!", "adtype_main.php");
    exit();
} else if ($dopost === "dels") {
    foreach (explode(",", $aids) as $key => $value) {
        $query = "DELETE FROM `#@__myadtype` WHERE id='$value' ";
        $dsql->ExecuteNoneQuery($query);
    }
    ShowMsg("成功删除广告分类!", "adtype_main.php");
    exit();
} else if ($dopost === "add") {
    $query = "INSERT INTO `#@__myadtype`(typename) VALUES('$typename');";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg("成功创建广告分类!", "adtype_main.php");
    exit();
}

$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN . '/templets/adtype_main.htm');
$dlist->SetSource("Select * From #@__myadtype");
$dlist->display();

