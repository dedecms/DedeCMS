<?php
/**
 * 生成Tag
 *
 * @version        $Id: makehtml_taglist.php 1 11:17 2020年8月19日 $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
$tid = isset($tid)? $tid : 0;
include DedeInclude('templets/makehtml_taglist.htm');

?>