<?php
/**
 * 文档日志详情
 *
 * @version        $Id: archives_log_detail.php 1 8:48 2010年7月13日 $
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

$archives_id = intval($archives_id);
if($archives_id > 0) {
    $where .= " AND `#@__archives_log_detail`.`archives_id` = '{$archives_id}' ";
}

$sql = "SELECT `#@__archives_log_detail`.*, `#@__admin`.`userid` FROM `#@__archives_log_detail`
LEFT JOIN `#@__admin` ON `#@__admin`.`id` = `#@__archives_log_detail`.`admin_id`
WHERE 1=1 $where ORDER BY `#@__archives_log_detail`.`id` ASC";

$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetTemplate(DEDEADMIN."/templets/archives_log_detail.htm");
$dlist->SetSource($sql);
$dlist->Display();