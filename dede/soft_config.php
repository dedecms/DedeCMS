<?php
/**
 * 软件配置
 *
 * @version        $Id: soft_config.php 1 16:09 2010年7月20日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_SoftConfig');
if(empty($dopost)) $dopost = '';

//保存
if($dopost=="save")
{
    if($dfrank>0 || $dfywboy>0) $gotojump = 1;
    $query = "UPDATE `#@__softconfig` SET
           `downtype` = '$downtype' ,
           `gotojump` ='$gotojump' ,
           `ismoresite` = '$ismoresite',
           `islocal` = '$islocal',
           `sites` = '$sites',
           `moresitedo` = '$moresitedo',
           `dfrank` = '$dfrank',
           `dfywboy` = '$dfywboy',
           `argrange` = '$argrange',
           downmsg = '$downmsg' ";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg('成功保存参数！', 'soft_config.php');
    exit();
}
//读取参数
$row = $dsql->GetOne("SELECT * FROM `#@__softconfig` ");
if(!is_array($row))
{
    $dsql->ExecuteNoneQuery("INSERT INTO `#@__softconfig`(`downtype`,`ismoresite`,`islocal`,`gotojump`,`sites`,`downmsg`,`moresitedo`,`dfrank`,`dfywboy`, `argrange`)
    VALUES ('1', '0','1', '0', '' ,'$downmsg','1', '0', '0', '0'); ");
    $row['downtype']   = 1;
    $row['ismoresite'] = 0;
    $row['islocal']    = 1;
    $row['gotojump']   = 0;
    $row['sites']      = '';
    $row['moresitedo']      = '1';
    $row['dfrank']      = '0';
    $row['dfywboy']      = '0';
    $row['downmsg']    = '';
    $row['argrange'] = 0;
}
include DedeInclude('templets/soft_config.htm');
exit();