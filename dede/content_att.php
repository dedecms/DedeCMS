<?php
/**
 * 内容属性
 *
 * @version   $Id: content_att.php 1 14:31 2010年7月12日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
CheckPurview('sys_Att');
if (empty($dopost)) {
    $dopost = '';
}

//保存更改
if ($dopost == "save") {
    if (!empty($sortid)) {
        foreach ($sortid as $key => $value) {
            $att = ${'att_' . $value};
            $attname = ${'attname_' . $value};
            $sortid = ${'sortid_' . $value};
            $query = "UPDATE `#@__arcatt` SET `attname`='$attname',`sortid`='$sortid' WHERE att='$att' ";
            $dsql->ExecuteNoneQuery($query);
        }
    }

    echo "<script> alert('成功更新自定文档义属性表！'); </script>";
}

$query = 'Select * From `#@__arcatt` order by sortid asc';
$dlist = new DataListCP();
$dlist->SetTemplate(DEDEADMIN . "/templets/content_att.htm");
$dlist->SetSource($query);
$dlist->Display();
$dlist->Close();
