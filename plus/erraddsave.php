<?php
/**
 *
 * 错误提交
 *
 * @version        $Id: erraddsave.php 1 15:38 2010年7月8日 $
 * @package        DedeCMS.Site
 * @copyright      Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/../include/common.inc.php";
require_once DEDEINC . '/memberlogin.class.php';

$dopost = isset($dopost) ? $dopost : "";
$aid = isset($aid) ? intval($aid) : 0;
if (empty($aid)) {
    die(" Request Error! ");
}
if ($dopost == "saveedit") {
    $cfg_ml = new MemberLogin();
    $title = HtmlReplace($title);
    $format = isset($format) ? $format : "";
    $type = isset($type) && is_numeric($type) ? $type : 0;
    $mid = isset($cfg_ml->M_ID) ? $cfg_ml->M_ID : 0;
    $err = trimMsg(cn_substr(RemoveXSS($err), 2000), 1);
    $oktxt = trimMsg(cn_substr(RemoveXSS($erradd), 2000), 1);
    $time = time();
    $query = "INSERT INTO `#@__erradd`(aid,mid,title,type,errtxt,oktxt,sendtime)
                  VALUES ('$aid','$mid','$title','$type','$err','$oktxt','$time'); ";
    $dsql->ExecuteNoneQuery($query);
    if (!empty($format)) {
        echo json_encode(array(
            "code" => 200,
            "data" => "ok",
        ));
    } else {
        ShowMsg("谢谢您对本网站的支持，我们会尽快处理您的建议！", "javascript:window.close();");
    }

    exit();
} else {
    die(" Request undefined ");
}
