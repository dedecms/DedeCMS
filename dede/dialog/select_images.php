<?php
/**
 * 图片选择框
 *
 * @version   $Id: select_images.php 1 9:43 2010年7月8日 $
 * @package   DedeCMS.Dialog
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
require DEDEDATA . '/mark/inc_photowatermark_config.php';

if (empty($activepath)) {
    $activepath = '';
}
if (empty($imgstick)) {
    $imgstick = '';
}
$noeditor = isset($noeditor) ? $noeditor : '';
$activepath = str_replace('.', '', $activepath);
$activepath = preg_replace("#\/{1,}#", '/', $activepath);
if (strlen($activepath) < strlen($cfg_medias_dir)) {
    $activepath = $cfg_medias_dir;
}
$inpath = $cfg_basedir . $activepath;
$activeurl = '..' . $activepath;

if (empty($f)) {
    $f = 'form1.picname';
}
$f = RemoveXSS($f);
if (empty($v)) {
    $v = 'picview';
}
if (empty($comeback)) {
    $comeback = '';
}
$addparm = '';
if (!empty($CKEditor)) {
    $addparm = '&CKEditor=' . $CKEditor;
    $f = $CKEditor;
}
if (!empty($CKEditorFuncNum)) {
    $addparm .= '&CKEditorFuncNum=' . $CKEditorFuncNum;
}

if (!empty($noeditor)) {
    $addparm .= '&noeditor=yes';
}

$tpl = new DedeTemplate();
$tpl->LoadTemplate(DEDEADMIN . "/templets/dialog/select_images.htm");
$tpl->Display();
