<?php
/**
 * 数据库备份/还原
 *
 * @version   $Id: sys_data.php 1 17:19 2010年7月20日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
CheckPurview('sys_Data');
if (empty($dopost)) {
    $dopost = '';
}


if ($dopost == "viewinfo") //查看表结构
{
    echo "[<a href='#' onclick='javascript:HideObj(\"_mydatainfo\")'><u>关闭</u></a>]\r\n<xmp>";
    if (empty($tablename)) {
        echo "没有指定表名！";
    } else {
        $dsql->SetQuery("SHOW CREATE TABLE " . $dsql->dbName . "." . $tablename);
        $dsql->Execute('me');
        $row2 = $dsql->GetArray('me', MYSQLI_BOTH);
        $ctinfo = $row2[1];
        echo trim($ctinfo);
    }
    echo '</xmp>';
    exit();
} else if ($dopost == "opimize") //优化表
{
    echo "[<a href='#' onclick='javascript:HideObj(\"_mydatainfo\")'><u>关闭</u></a>]\r\n<xmp>";
    if (empty($tablename)) {
        echo "没有指定表名！";
    } else {
        $rs = $dsql->ExecuteNoneQuery("OPTIMIZE TABLE `$tablename` ");
        if ($rs) {
            echo "执行优化表： $tablename  OK！";
        } else {
            echo "执行优化表： $tablename  失败，原因是：" . $dsql->GetError();
        }
    }
    echo '</xmp>';
    exit();
} else if ($dopost == "repair") //修复表
{
    echo "[<a href='#' onclick='javascript:HideObj(\"_mydatainfo\")'><u>关闭</u></a>]\r\n<xmp>";
    if (empty($tablename)) {
        echo "没有指定表名！";
    } else {
        $rs = $dsql->ExecuteNoneQuery("REPAIR TABLE `$tablename` ");
        if ($rs) {
            echo "修复表： $tablename  OK！";
        } else {
            echo "修复表： $tablename  失败，原因是：" . $dsql->GetError();
        }
    }
    echo '</xmp>';
    exit();
}

//获取系统存在的表信息
$otherTables = array();
$dedeSysTables = array();
$channelTables = array();
$dsql->SetQuery("SELECT addtable FROM `#@__channeltype` ");
$dsql->Execute();
while ($row = $dsql->GetObject()) {
    $channelTables[] = $row->addtable;
}
$dsql->SetQuery("SHOW TABLE STATUS");
$dsql->Execute('t');
while ($row = $dsql->GetArray('t', MYSQLI_BOTH)) {
    if (preg_match("#^{$cfg_dbprefix}#", $row[0]) || in_array($row[0], $channelTables)) {
        $dedeSysTables[] = $row;
    } else {
        $otherTables[] = $row[0];
    }
}

$mysql_version = $dsql->GetVersion();
$dlist = new DataListCP();
$dlist->SetParameter("dedeSysTables", $dedeSysTables);
$dlist->SetTemplet(DEDEADMIN . "/templets/sys_data.htm");
$dlist->display();


