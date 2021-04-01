<?php
/**
 * @version        $Id: index.php 1 2010-06-30 11:43:09 $
 * @package        DedeCMS.Site
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC."/arc.specview.class.php");
if(strlen($art_shortname) > 6) exit("art_shortname too long!");
$specfile = dirname(__FILE__)."spec_1".$art_shortname;
//如果已经编译静态列表，则直接引入第一个文件
if(file_exists($specfile))
{
    include($specfile);
    exit();
}
else
{
  $sp = new SpecView();
  $sp->Display();
}