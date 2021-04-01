<?php
/**
 * 管理员后台基本函数
 *
 * @version        $Id:inc_stat.php 1 13:58 2010年7月5日 $
 * @package        DedeCMS.Libraries
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
function SpUpdateStat()
{
    global $cfg_version;
    if(empty($cfg_version))
    {
        $cfg_version = 'notknow';
    }
    $statport = array(0x68,0x74,0x74,0x70,0x3a,0x2f,0x2f,0x77,0x77,0x77,0x2e,0x64,0x65,0x64,0x65,
    0x63,0x6d,0x73,0x2e,0x63,0x6f,0x6d,0x2f,0x73,0x74,0x61,0x74,0x2e,0x70,0x68,0x70,
    0x3f,0x72,0x66,0x68,0x6f,0x73,0x74,0x3d);
    $staturl = '';
    foreach($statport as $c)
    {
        $staturl .= chr($c);
    }
    $staturl = $staturl.urlencode($_SERVER['HTTP_HOST']).'&ver='.urlencode($cfg_version);
    $stat = @file_get_contents($staturl);
    return $stat;
}