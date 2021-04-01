<?php
/**
 * 采集规则编辑-专家更改模式
 *
 * @version        $Id: co_edit_text.php 1 14:31 2010年7月12日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require(dirname(__FILE__)."/config.php");
CheckPurview('co_EditNote');
if(empty($job)) $job='';

if($job=='')
{
    require_once(DEDEINC."/oxwindow.class.php");
    $wintitle = "更改采集规则";
    $wecome_info = "<a href='co_main.php'><u>采集点管理</u></a>::更改采集规则 - 专家更改模式";
    $win = new OxWindow();
    $win->Init("co_edit_text.php", "js/blank.js", "POST");
    $win->AddHidden("job", "yes");
    $win->AddHidden("nid", $nid);
    $row = $dsql->GetOne("SELECT * FROM `#@__co_note` WHERE nid='$nid' ");
    $win->AddTitle("索引与基本信息配置：");
    $win->AddMsgItem("<textarea name='listconfig' style='width:100%;height:200px'>{$row['listconfig']}</textarea>");
    $win->AddTitle("字段配置：");
    $win->AddMsgItem("<textarea name='itemconfig' style='width:100%;height:300px'>{$row['itemconfig']}</textarea>");
    $winform = $win->GetWindow("ok");
    $win->Display();
    exit();
}
else
{
    CheckPurview('co_EditNote');
    $query = "UPDATE `#@__co_note` SET listconfig='$listconfig',itemconfig='$itemconfig' WHERE nid='$nid' ";
    $rs = $dsql->ExecuteNoneQuery($query);
    ShowMsg("成功修改一个规则!","co_main.php");
    exit();
}