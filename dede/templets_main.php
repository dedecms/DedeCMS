<?php
/**
 * 模板文件管理
 *
 * @version        $Id: templets_main.php 1 23:07 2010年7月20日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__).'/config.php');
CheckPurview('plus_文件管理器');

if(empty($acdir)) $acdir = $cfg_df_style;
$templetdir = $cfg_basedir.$cfg_templets_dir;
$templetdird = $templetdir.'/'.$acdir;
$templeturld = $cfg_templeturl.'/'.$acdir;

if(preg_match("#\.#", $acdir))
{
    ShowMsg('Not Allow dir '.$acdir.'!', '-1');
    exit();
}

//获取默认文件说明信息
function GetInfoArray($filename)
{
    $arrs = array();
    $dlist = file($filename);
    foreach($dlist as $d)
    {
        $d = trim($d);
        if($d!='')
        {
            list($dname, $info) = explode(',', $d);
            $arrs[$dname] = $info;
        }
    }
    return $arrs;
}

$dirlists  = GetInfoArray($templetdir.'/templet-dirlist.inc');
$filelists = GetInfoArray($templetdir.'/templet-filelist.inc');
$pluslists = GetInfoArray($templetdir.'/templet-pluslist.inc');
$fileinfos = ($acdir=='plus' ? $pluslists : $filelists);

include DedeInclude('templets/templets_default.htm');