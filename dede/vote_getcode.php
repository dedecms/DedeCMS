<?php
/**
 * 获取投票代码
 *
 * @version        $Id: vote_getcode.php 1 23:54 2010年7月20日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/dedevote.class.php");
$aid = isset($aid) && is_numeric($aid) ? $aid : 0;
include DedeInclude('templets/vote_getcode.htm');