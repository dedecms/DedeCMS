<?php
/**
 * 管理一个模板
 *
 * @version        $Id: templets_one.php 1 23:07 2010年7月20日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('temp_One');
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

$addquery = '';
$keyword = (!isset($keyword) ? '' : $keyword);
$likeid = (!isset($likeid) ? '' : $likeid);
$addq = $likeid!='' ? " AND likeid LIKE '$likeid' " : '';
$sql = "SELECT aid,title,ismake,uptime,filename,likeid FROM `#@__sgpage` WHERE title LIKE '%$keyword%' $addq ORDER BY aid DESC";
$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN."/templets/templets_one.htm");
$dlist->SetSource($sql);
$dlist->display();

function GetIsMake($im)
{
    return $im==1 ? '需编译' : '不编译';
}