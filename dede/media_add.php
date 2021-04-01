<?php
/**
 * 附件添加
 *
 * @version        $Id: media_add.php 2 15:25 2011-6-2 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");

//增加权限检查
if(empty($dopost)) $dopost = "";

//上传
if($dopost=="upload")
{
    csrf_check();
    require_once(DEDEINC."/image.func.php");
    $sparr_image = Array("image/pjpeg","image/jpeg","image/gif","image/png","image/x-png","image/wbmp");
    $sparr_flash = Array("application/xshockwaveflash");
    $okdd = 0;
    $uptime = time();
    $adminid = $cuserLogin->getUserID();
    $width = $height = '';
    
    for($i=0; $i<=40; $i++)
    {
        if(isset(${"upfile".$i}) && is_uploaded_file(${"upfile".$i}))
        {
            $filesize = ${"upfile".$i."_size"};
            $upfile_type = ${"upfile".$i."_type"};
            $upfile_name = ${"upfile".$i."_name"};
            $dpath = MyDate("ymd", $uptime);

            if($mediatype == 1) {
                if(!in_array(strtolower(pathinfo($upfile_name, PATHINFO_EXTENSION)), explode("|", $cfg_imgtype), true)) {
                    ShowMsg("您上传的文件扩展名不在许可列表 [{$cfg_imgtype}]，请更改系统配置的参数！", "-1");
                    exit();
                }

                if(!in_array($upfile_type, $sparr_image)) {
                    ShowMsg("您上传的文件存在问题，请检查文件类型！", "-1");
                    exit();
                }

                $savePath = $cfg_image_dir."/".$dpath;
            } else if($mediatype == 2) {
                if(!in_array(strtolower(pathinfo($upfile_name, PATHINFO_EXTENSION)), explode("|", "swf"), true)) {
                    ShowMsg("您上传的文件扩展名不在许可列表 [swf]", "-1");
                    exit();
                }

                if(!in_array($upfile_type, $sparr_flash)) {
                    ShowMsg("您上传的文件存在问题，请检查文件类型！", "-1");
                    exit();
                }

                $savePath = $cfg_other_medias."/".$dpath;
            } else if($mediatype == 3) {
                if(!in_array(strtolower(pathinfo($upfile_name, PATHINFO_EXTENSION)), explode("|", $cfg_mediatype), true)) {
                    ShowMsg("您上传的文件扩展名不在许可列表 [{$cfg_mediatype}]，请更改系统配置的参数！", "-1");
                    exit();
                }

                if(!preg_match('#audio|media|video#i', $upfile_type)) {
                    ShowMsg("您上传的文件存在问题，请检查文件类型！", "-1");
                    exit();
                }
                
                $savePath = $cfg_other_medias."/".$dpath;
            } else if($mediatype == 4) {
                if(!in_array(strtolower(pathinfo($upfile_name, PATHINFO_EXTENSION)), explode("|", $cfg_softtype), true)) {
                    ShowMsg("您上传的文件扩展名不在许可列表 [{$cfg_softtype}]，请更改系统配置的参数！", "-1");
                    exit();
                }

                $savePath = $cfg_soft_dir."/".$dpath;
            } else {
                continue;
            }
            $filename = "{$adminid}_".MyDate("His",$uptime).mt_rand(100,999).$i;
            $fs = explode(".",${"upfile".$i."_name"});
            $filename = $filename.".".$fs[count($fs)-1];
            $filename = $savePath."/".$filename;
            if(!is_dir($cfg_basedir.$savePath))
            {
                MkdirAll($cfg_basedir.$savePath,777);
                CloseFtp();
            }
            $fullfilename = $cfg_basedir.$filename;
            if($mediatype==1)
            {
                @move_uploaded_file(${"upfile".$i}, $fullfilename);
                $info = '';
                $data = getImagesize($fullfilename, $info);
                $width = $data[0];
                $height = $data[1];
                if(in_array($upfile_type, $cfg_photo_typenames)) WaterImg($fullfilename, 'up');
            }else
            {
                @move_uploaded_file(${"upfile".$i}, $fullfilename);
            }
            if($i>1)
            {
                $ntitle = $title."_".$i;
            }
            else
            {
                $ntitle = $title;
            }
            $inquery = "INSERT INTO `#@__uploads`(title,url,mediatype,width,height,playtime,filesize,uptime,mid)
       VALUES ('$ntitle','$filename','$mediatype','$width','$height','$playtime','$filesize','$uptime','$adminid'); ";
            $okdd++;
            $dsql->ExecuteNoneQuery($inquery);
        }
    }
    ShowMsg("成功上传 {$okdd} 个文件！","media_main.php");
    exit();
}
include DedeInclude('templets/media_add.htm');