<?php
/**
 * 文档日志管理
 *
 * @version        $Id: archives_log_list.php 1 8:48 2010年7月13日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Log');
require_once(DEDEINC."/datalistcp.class.php");
require_once(DEDEINC."/common.func.php");
require_once(DEDEADMIN."/inc/inc_list_functions.php");
$sql = $where = "";

$sql = "SELECT * FROM `#@__archives_log_list` ORDER BY `id` DESC";

$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetTemplate(DEDEADMIN."/templets/archives_log_list.htm");
$dlist->SetSource($sql);
$dlist->Display();