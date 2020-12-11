<?php
/**
 * @version        $Id: edit_face.php 1 8:38 2010年7月9日 $
 * @package        DedeCMS.Member
 * @copyright      Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
CheckRank(0, 0);
$menutype = 'config';
if (!isset($dopost)) {
    $dopost = '';
}
if (!isset($backurl)) {
    $backurl = 'edit_face.php';
}
if ($dopost == 'save') {
    // 校验CSRF
    CheckCSRF();
    $face = HtmlReplace($faceurl, -1);

    $query = "UPDATE `#@__member` SET `face` = '$face' WHERE mid='{$cfg_ml->M_ID}' ";
    $dsql->ExecuteNoneQuery($query);
    // 清除缓存
    $cfg_ml->DelCache($cfg_ml->M_ID);
    ShowMsg('成功更新头像信息！', $backurl);
    exit();
}
$face = $cfg_ml->fields['face'];
include DEDEMEMBER . "/templets/edit_face.htm";
exit();
