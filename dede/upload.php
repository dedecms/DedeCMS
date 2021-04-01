<?php
/**
 * upload上传
 *
 * @version        $Id: upload.php 1 16:22 2010年7月20日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__) . '/config.php');
require_once(DEDEINC . '/image.func.php');

// 上传
if (empty($dopost)) {
    $res = array();

    $uploadTmp = DEDEDATA . '/uploadtmp';
    if (!is_dir($uploadTmp)) {
        MkdirAll($uploadTmp, $cfg_dir_purview);
        CloseFtp();
        if (!is_dir($uploadTmp)) {
            $res['status'] = 'fail';
            echo json_encode($res);
            exit();
        }
    }

    unlink($uploadTmp . '/' . $delete);

    $adminID = $cuserLogin->getUserID();
    $tmpName = $_FILES['file']['tmp_name'];
    $name = $_FILES['file']['name'];
    $type = $_FILES['file']['type'];
    $fileExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $fileName = $adminID . '-' . dd2char(MyDate('His', time()) . mt_rand(1000, 9999)) . '.' . $fileExt;
    $filePath = $uploadTmp . '/' . $fileName;

    if (!is_uploaded_file($tmpName)) {
        $res['status'] = 'fail';
        echo json_encode($res);
        exit();
    }

    if (!preg_match('#image/([a-z]+)#i', $type)) {
        $res['status'] = 'fail';
        echo json_encode($res);
        exit();
    }

    if (!preg_match("#{$cfg_imgtype}#i", $fileExt)) {
        $res['status'] = 'fail';
        echo json_encode($res);
        exit();
    }

    move_uploaded_file($tmpName, $filePath);

    if ($cfg_album_mark === 'Y' && ini_set('memory_limit', '512M')) {
        WaterImg($filePath, 'up');
    }

    $res['status'] = 'success';
    $res['name'] = $fileName;
    $res['remark'] = pathinfo($name, PATHINFO_FILENAME);
    echo json_encode($res);
}

// 删除
if ($dopost === 'delete') {
    $uploadTmp = DEDEDATA . '/uploadtmp';

    if (unlink($uploadTmp . '/' . $delete)) {
        echo 'success';
        exit();
    }
    echo 'fail';
}
