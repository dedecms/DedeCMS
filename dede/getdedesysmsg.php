<?php
/**
 * 获取dede系统提示信息
 *
 * @version        $Id: getdedesysmsg.php 1 11:06 2010年7月13日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__).'/config.php');
require_once(DEDEINC.'/dedehttpdown.class.php');
AjaxHead();
$dhd = new DedeHttpDown();
$dhd->OpenUrl('http://www.dedecms.com/officialinfo.html');
$str = trim($dhd->GetHtml());
$dhd->Close();
if($cfg_soft_lang=='utf-8') $str = gb2utf8($str);
echo $str;
