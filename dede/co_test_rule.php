<?php
/**
 * 采集规则测试
 *
 * @version        $Id: co_test_rule.php 1 17:13 2010年7月12日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/dedecollection.class.php");
$nid = intval($nid);
$co = new DedeCollection();
$co->LoadNote($nid);
include DedeInclude('templets/co_test_rule.htm');
exit();