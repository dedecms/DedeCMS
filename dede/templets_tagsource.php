<?php
/**
 * 文件管理器
 *
 * @version   $Id: templets_tagsource.php 1 23:44 2010年7月20日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . '/config.php';
CheckPurview('plus_文件管理器');

$libdir = DEDEINC . '/taglib';
$helpdir = DEDEINC . '/taglib/help';

//获取默认文件说明信息
function GetHelpInfo($tagname)
{
    global $helpdir;
    $helpfile = $helpdir . '/' . $tagname . '.txt';
    if (!file_exists($helpfile)) {
        return '该标签没帮助信息';
    }
    $fp = fopen($helpfile, 'r');
    $helpinfo = fgets($fp, 64);
    fclose($fp);
    return $helpinfo;
}


$filearray = array();
// 判断是否为目录
if (is_dir($libdir)) {
    // 打开目录句柄
    if ($dh = opendir($libdir)) {
        // 获取目录下内容
        while (($file = readdir($dh)) !== false){
            if(preg_match("#\.php#", $file)) {

                $filetime = filemtime($libdir.'/'.$file);
                $_file['filename'] = $file;
                $_file['filetime'] = MyDate("Y-m-d H:i", $filetime);
                $_file['fileinfo'] = GetHelpInfo(str_replace('.lib.php', '', $file));
                $filearray[] = $_file;
            }
        }
         // 关闭目录句柄
        closedir($dh);
    }
}


$dlist = new DataListCP();
$dlist->SetParameter("files", $filearray);
$dlist->SetTemplet(DEDEADMIN . "/templets/templets_tagsource.htm");
$dlist->display();
