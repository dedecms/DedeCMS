<?php

/**
 * 附件添加
 *
 * @version   $Id: media_add.php 2 15:25 2011-6-2  $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https://weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";

//增加权限检查
if (empty($dopost)) {
    $dopost = "";
}

//上传
if ($dopost == "upload") {

    csrf_check();

    if (isset($_FILES['upfile'])) {
        ShowMsg("您没有上传任务文件，请重新上传。", "media_add.php");
        exit();
    }

    include_once DEDEINC . "/image.func.php";
    $sparr_image = array("image/pjpeg", "image/jpeg", "image/gif", "image/png", "image/x-png", "image/wbmp");
    $okdd = 0;
    $uptime = time();
    $adminid = $cuserLogin->getUserID();
    $width = $height = '';
    $files = reArrayFiles($_FILES['upfile']);

    foreach ($files as $key => $value) {
        if (is_uploaded_file($value['tmp_name']) === true) {
            $dpath = MyDate("Ymd", $uptime);
            $filesize = $value['size'];
            $upfile_type = $value['type'];
            $upfile_name = $value['name'];

            // 过滤
            if (preg_match('#\.(php|pl|cgi|asp|aspx|jsp|php5|php4|php3|shtm|shtml)[^a-zA-Z0-9]+$#i', trim($filename))) {
                ShowMsg("你指定的文件名被系统禁止！", "javascript:;");
                exit();
            }

            // 根据类型分配上传目录
            if (in_array($upfile_type, $sparr_image)) {
                $mediatype = 1;
                $savePath = $cfg_image_dir . "/" . $dpath;
            }
            else if (preg_match('#audio|media|video#i', $upfile_type) && preg_match("#\." . $cfg_mediatype . "$#i", $upfile_name)) {
                $mediatype = 3;
                $savePath = $cfg_other_medias . "/" . $dpath;
            } else if (preg_match("#\." . $cfg_softtype . "+\." . $cfg_softtype . "$#i", $upfile_name)) {
                $mediatype = 4;
                $savePath = $cfg_soft_dir . "/" . $dpath;
            } else {
                continue;
            }

            // 设置文件目录及文件名
            $filename = "{$adminid}_" . MyDate("His", $uptime) . mt_rand(100, 999) . $key;
            $ext = end(explode('.', $upfile_name));
            $filename = $filename . "." . $ext;
            $filename = $savePath . "/" . $filename;


            // 如果附件目录不存在，则创建目录
            if (!is_dir($cfg_basedir . $savePath)) {
                MkdirAll($cfg_basedir . $savePath, 777);
                CloseFtp();
            }

            $fullfilename = $cfg_basedir . $filename;

            if ($mediatype == 1) {
                @move_uploaded_file($value['tmp_name'], $fullfilename);
                $info = '';
                $data = getImagesize($fullfilename, $info);
                $width = $data[0];
                $height = $data[1];
                if (in_array($upfile_type, $cfg_photo_typenames)) {
                    //加入水印
                    WaterImg($fullfilename, 'up');
                }

            } else {
                @move_uploaded_file($value['tmp_name'], $fullfilename);
            }

            if ($key > 1) {
                $ntitle = $title . "_" . $key;
            } else {
                $ntitle = $title;
            }

            $inquery = "INSERT INTO `#@__uploads`(title,url,mediatype,width,height,playtime,filesize,uptime,mid)
            VALUES ('$ntitle','$filename','$mediatype','$width','$height','$playtime','$filesize','$uptime','$adminid'); ";
            $dsql->ExecuteNoneQuery($inquery);        
            $okdd++;
        }
    }

    ShowMsg("成功上传 {$okdd} 个文件！", "media_main.php");
    exit();

}

// 重构文件数组
function reArrayFiles(&$file_post)
{

    $file_array = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_array[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_array;
}


DedeInclude('templets/media_add.htm');
