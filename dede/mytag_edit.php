<?php
/**
 * 自定义标记修改
 *
 * @version   $Id: mytag_edit.php 1 15:37 2010年7月20日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https://weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require dirname(__FILE__) . "/config.php";
CheckPurview('temp_Other');
require_once DEDEINC . "/typelink.class.php";

if (empty($dopost)) {
    $dopost = '';
}

$aid = intval($aid);
$ENV_GOBACK_URL = empty($_COOKIE['ENV_GOBACK_URL']) ? 'mytag_main.php' : $_COOKIE['ENV_GOBACK_URL'];

if ($dopost == 'delete') {
    csrf_check();
    $dsql->ExecuteNoneQuery("DELETE FROM #@__mytag WHERE aid='$aid'");
    ShowMsg("成功删除一个自定义标记！", $ENV_GOBACK_URL);
    exit();
} else if ($dopost == "saveedit") {
    csrf_check();
    $starttime = GetMkTime($starttime);
    $endtime = GetMkTime($endtime);
    $query = "UPDATE `#@__mytag`
     SET
     typeid='$typeid',
     timeset='$timeset',
     starttime='$starttime',
     endtime='$endtime',
     normbody='$normbody',
     expbody='$expbody'
     WHERE aid='$aid' ";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg("成功修改一个自定义标记！", $ENV_GOBACK_URL);
    exit();
} else if ($dopost == "getjs") {
    include_once DEDEINC . "/oxwindow.class.php";
    $jscode = "<script src='{$cfg_phpurl}/mytag_js.php?aid=$aid' language='javascript'></script>";
    $showhtml = "<pre><code>\r\n".htmlspecialchars($jscode)."\r\n\r\n</code></pre>";
    $showhtml .= "<div class=\"uk-margin\"><h4 class='uk-h4'>预览：</h4></div><iframe name='testfrm' frameborder='0' src='mytag_edit.php?aid={$aid}&dopost=testjs' id='testfrm' width='100%' height='250'></iframe>";
    $wintitle = "宏标记定义-获取JS";
    $wecome_info = "<ul class=\"uk-breadcrumb\"><li><a href='mytag_main.php'>宏标记定义</a></li><li><span>获取JS</span></li></ul>";
    $win = new OxWindow();
    $win->Init();
    $win->AddTitle('以下为选定宏标记的JS调用代码：');
    $winform = $win->GetWindow('hand', $showhtml);
    $win->Display();
    exit();
} else if ($dopost == "testjs") {
    $tpl = new DedeTemplate();
    $tpl->LoadTemplate(DEDEADMIN . "/templets/mytag_js.htm");
    $tpl->Display();
    exit();
}
$row = $dsql->GetOne("SELECT * FROM `#@__mytag` WHERE aid='$aid'");
DedeInclude('templets/mytag_edit.htm');
