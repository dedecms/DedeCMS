<?php
/**
 *
 * 友情链接
 *
 * @version        $Id: flink.php 1 15:38 2010年7月8日 $
 * @package        DedeCMS.Site
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/../include/common.inc.php");
if(empty($dopost)) $dopost = '';


if($dopost=='save')
{
    $validate = isset($validate) ? strtolower(trim($validate)) : '';
    $svali = GetCkVdValue();
    if($validate=='' || $validate!=$svali)
    {
        ShowMsg('验证码不正确!','-1');
        exit();
    }
    $msg = dede_htmlspecialchars($msg);
    $email = dede_htmlspecialchars($email);
    $webname = dede_htmlspecialchars($webname);
    $url = dede_htmlspecialchars($url);
    $logo = dede_htmlspecialchars($logo);
    $typeid = intval($typeid);
    $dtime = time();
    $query = "INSERT INTO `#@__flink`(sortrank,url,webname,logo,msg,email,typeid,dtime,ischeck)
                    VALUES('50','$url','$webname','$logo','$msg','$email','$typeid','$dtime','0')";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg('成功增加一个链接，但需要审核后才能显示!','-1',1);
}

//显示模板(简单PHP文件)
include_once(DEDETEMPLATE.'/plus/flink-list.htm');