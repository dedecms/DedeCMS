<?php
/**
 * 文档日志导出
 *
 * @version        $Id: archives_log_export.php 1 8:48 2010年7月13日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview("sys_Log");

$filename = "文档日志导出___" . date("Y_m_d__H_i_s") . ".csv";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . $filename);
$fp = fopen("php://output", "a");

$head = array("文章ID", "文章标题", "文章内容", "审核意见", "操作类型", "文档状态", "发布人", "访问IP", "时间");
foreach ($head as $key => $val) {
    $head[$key] = iconv("UTF-8", "GBK//IGNORE", $val);
}
fputcsv($fp, $head);

$dsql->SetQuery("SELECT `#@__archives_log_detail`.*, `#@__arcrank`.`membername`, `#@__admin`.`userid` FROM `#@__archives_log_detail`
LEFT JOIN `#@__arcrank` ON `#@__arcrank`.`rank` = `#@__archives_log_detail`.`arcrank`
LEFT JOIN `#@__admin` ON `#@__admin`.`id` = `#@__archives_log_detail`.`admin_id`
ORDER BY `archives_id` DESC, `time` ASC");
$dsql->Execute();

while ($row = $dsql->GetObject()) {
    $membername = $row->membername;
    if ($membername == "") {
        $membername = "已删除";
    }
    $time = date("Y-m-d H:i:s", $row->time);

    $data = array();
    $data[] = iconv("UTF-8", "GBK//IGNORE", $row->archives_id);
    $data[] = iconv("UTF-8", "GBK//IGNORE", $row->title);
    $data[] = iconv("UTF-8", "GBK//IGNORE", $row->body);
    $data[] = iconv("UTF-8", "GBK//IGNORE", $row->remark);
    $data[] = iconv("UTF-8", "GBK//IGNORE", $row->type);
    $data[] = iconv("UTF-8", "GBK//IGNORE", $membername);
    $data[] = iconv("UTF-8", "GBK//IGNORE", $row->userid);
    $data[] = iconv("UTF-8", "GBK//IGNORE", $row->ip);
    $data[] = iconv("UTF-8", "GBK//IGNORE", $time);
    fputcsv($fp, $data);

    $i++;
    if ($i == 5000) {
        ob_flush();
        flush();
        $i = 0;
    }
}

exit();