<?php
/**
 * 友情链接类型
 *
 * @version   $Id: friendlink_type.php 1 8:48 2010年7月13日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
if (empty($dopost)) {
    $dopost = '';
}

//保存更改
if ($dopost === "save") {
    $query = "UPDATE `#@__flinktype` SET typename='$pname' WHERE id='$tid' ";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg("成功更新友情链接网站分类表!", "friendlink_type.php");
    exit();
} else if ($dopost === "del") {
    $query = "DELETE FROM `#@__flinktype` WHERE id='$id' ";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg("成功删除网站类型!", "friendlink_type.php");
    exit();
} else if ($dopost === "dels") {
    foreach (explode(",", $aids) as $key => $value) {
        $query = "DELETE FROM `#@__flinktype` WHERE id='$value' ";
        $dsql->ExecuteNoneQuery($query);
    }
    ShowMsg("成功删除网站类型!", "friendlink_type.php");
    exit();
} else if ($dopost === "add") {
    $query = "INSERT INTO `#@__flinktype`(typename) VALUES('$typename');";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg("成功创建网站类型!", "friendlink_type.php");
    exit();
}

$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN . '/templets/friendlink_type.htm');
$dlist->SetSource("Select * From #@__flinktype");
$dlist->display();
