<?php

/**
 * 附件添加
 *
 * @version        $Id: media_add.php 2 15:25 2011-6-2  $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__) . "/config.php");

//增加权限检查
if (empty($dopost)) $dopost = "";

//上传
if ($dopost == "upload") {
    csrf_check();
    require_once(DEDEINC . "/image.func.php");
    $sparr_image = array("image/pjpeg", "image/jpeg", "image/gif", "image/png", "image/x-png", "image/wbmp");
    $sparr_flash = array("application/xshockwaveflash");
    $okdd = 0;
    $uptime = time();
    $adminid = $cuserLogin->getUserID();
    $width = $height = '';

    for ($i = 0; $i <= 40; $i++) {
        if (isset(${"upfile" . $i}) && is_uploaded_file(${"upfile" . $i})) {
            $filesize = ${"upfile" . $i . "_size"};
            $upfile_type = ${"upfile" . $i . "_type"};
            $upfile_name = ${"upfile" . $i . "_name"};
            $dpath = MyDate("ymd", $uptime);

            if (in_array($upfile_type, $sparr_image)) {
                $mediatype = 1;
                $savePath = $cfg_image_dir . "/" . $dpath;
            } else if (in_array($upfile_type, $sparr_flash)) {
                $mediatype = 2;
                $savePath = $cfg_other_medias . "/" . $dpath;
            }
            // 2011-6-2 修复附件无法上传的错误(by:tianya)
            else if (preg_match('#audio|media|video#i', $upfile_type) && preg_match("#\." . $cfg_mediatype . "$#i", $upfile_name)) {
                $mediatype = 3;
                $savePath = $cfg_other_medias . "/" . $dpath;
            } else if (preg_match("#\." . $cfg_softtype . "+\." . $cfg_softtype . "$#i", $upfile_name)) {
                $mediatype = 4;
                $savePath = $cfg_soft_dir . "/" . $dpath;
            } else {
                continue;
            }
            $filename = "{$adminid}_" . MyDate("His", $uptime) . mt_rand(100, 999) . $i;
            $fs = explode(".", ${"upfile" . $i . "_name"});
            $filename = $filename . "." . $fs[count($fs) - 1];
            $filename = $savePath . "/" . $filename;
            if (!is_dir($cfg_basedir . $savePath)) {
                MkdirAll($cfg_basedir . $savePath, 777);
                CloseFtp();
            }
            /*
			dedecms后台文件任意上传漏洞
			漏洞描述：dedecms早期版本后台存在大量的富文本编辑器，该控件提供了一些文件上传接口，同时dedecms对上传文件的后缀类型未进行严格的限制，这导致了黑客可以上传WEBSHELL，获取网站后台权限。
			*/
            if (preg_match('#\.(php|pl|cgi|asp|aspx|jsp|php5|php4|php3|shtm|shtml)[^a-zA-Z0-9]+$#i', trim($filename))) {
                ShowMsg("你指定的文件名被系统禁止！", "javascript:;");
                exit();
            }
            $fullfilename = $cfg_basedir . $filename;
            if ($mediatype == 1) {
                @move_uploaded_file(${"upfile" . $i}, $fullfilename);
                $info = '';
                $data = getImagesize($fullfilename, $info);
                $width = $data[0];
                $height = $data[1];
                if (in_array($upfile_type, $cfg_photo_typenames)) WaterImg($fullfilename, 'up');
            } else {
                @move_uploaded_file(${"upfile" . $i}, $fullfilename);
            }
            if ($i > 1) {
                $ntitle = $title . "_" . $i;
            } else {
                $ntitle = $title;
            }
            $inquery = "INSERT INTO `#@__uploads`(title,url,mediatype,width,height,playtime,filesize,uptime,mid)
       VALUES ('$ntitle','$filename','$mediatype','$width','$height','$playtime','$filesize','$uptime','$adminid'); ";
            $okdd++;
            $dsql->ExecuteNoneQuery($inquery);
        }
    }
    ShowMsg("成功上传 {$okdd} 个文件！", "media_main.php");
    exit();
}
include DedeInclude('templets/media_add.htm');
