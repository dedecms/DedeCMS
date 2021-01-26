<?php
/**
 * SQL命令执行器
 *
 * @version   $Id: sys_sql_query.php 1 22:28 2010年7月20日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https://weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require dirname(__FILE__) . "/config.php";
CheckPurview('sys_Data');
require_once dirname(__FILE__) . "/../include/oxwindow.class.php";
if (empty($dopost)) {
    $dopost = "";
}


//优化表
if ($dopost == "opimize") {
    csrf_check();
    $msg = null;
    if (empty($tablename)) {
        $msg .="没有指定表名！";
    } else {
        $rs = $dsql->ExecuteNoneQuery("OPTIMIZE TABLE `$tablename` ");
        if ($rs) {
            $msg .="执行优化表： $tablename  OK！";
        } else {
            $msg .="执行优化表： $tablename  失败，原因是：" . $dsql->GetError();
        }

    }
    $msg .= "<br /><br /><a href='javascript:history.go(-1);'>确定并返回</a>";
    ShowMsg($msg, "javascript:;");
    exit();
}
//优化全部表
else if ($dopost == "opimizeAll") {
    csrf_check();
    $dsql->SetQuery("SHOW TABLES");
    $dsql->Execute('t');
    $c = $t = $f = 0;
    while ($row = $dsql->GetArray('t', MYSQLI_BOTH)) {
        $c++;
        $rs = $dsql->ExecuteNoneQuery("OPTIMIZE TABLE `{$row[0]}` ");
        if ($rs) {
            $t++;
        } else {
            $f++;
        }
    }
    $msg = "共优化表{$c}个, ";
    $msg .= "其中{$t}个成功, ";
    $msg .= "{$f}个失败。<br />";
    $msg .= "<br /><a href='javascript:history.go(-1);'>确定并返回</a>";
    ShowMsg($msg, "javascript:;");
    exit();
}
//修复表
else if ($dopost == "repair") {
    csrf_check();
    $msg = null;
    if (empty($tablename)) {
        $msg .="没有指定表名！";
    } else {
        $rs = $dsql->ExecuteNoneQuery("REPAIR TABLE `$tablename` ");
        if ($rs) {
            $msg .= "修复表： $tablename  OK！";
        } else {
            $msg .= "修复表： $tablename  失败，原因是：" . $dsql->GetError();
        }
    }

    $msg .= "<br /><br /><a href='javascript:history.go(-1);'>确定并返回</a>";
    ShowMsg($msg, "javascript:;");
    exit();
}
//修复全部表
else if ($dopost == "repairAll") {
    csrf_check();
    $dsql->SetQuery("Show Tables");
    $dsql->Execute('t');
    $c = $t = $f = 0;

    while ($row = $dsql->GetArray('t', MYSQLI_BOTH)) {
        $c++;
        $rs = $dsql->ExecuteNoneQuery("REPAIR TABLE `{$row[0]}` ");
        if ($rs) {
            $t++;
        } else {
            $f++;
        }
    }

    $msg = "共修复表{$c}个, ";
    $msg .= "其中{$t}个成功, ";
    $msg .= "{$f}个失败。<br />";
    $msg .= "<br /><a href='javascript:history.go(-1);'>确定并返回</a>";
    ShowMsg($msg, "javascript:;");
    exit();
}

