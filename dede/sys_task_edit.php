<?php
/**
 * 编辑任务
 *
 * @version        $Id: sys_task_edit.php 1 23:07 2010年7月20日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require(dirname(__FILE__)."/config.php");
CheckPurview('sys_Task');
if(empty($dopost)) $dopost = '';

if($dopost=='save')
{
    $starttime = empty($starttime) ? 0 : GetMkTime($starttime);
    $endtime = empty($endtime) ? 0 : GetMkTime($endtime);
    $runtime = $h.':'.$m;
    $query = "UPDATE `#@__sys_task`
    SET `taskname` = '$taskname',
    `dourl` = '$dourl',
    `islock` = '$nislock',
    `runtype` = '$runtype',
    `runtime` = '$runtime',
    `starttime` = '$starttime',
    `endtime` = '$endtime',
    `freq` = '$freq',
    `description` = '$description',
    `parameter` = '$parameter'
    WHERE id='$id' ";
    $rs = $dsql->ExecuteNoneQuery($query);
    if($rs) 
    {
        ShowMsg('成功修改一个任务!', 'sys_task.php');
    }
    else
    {
        ShowMsg('修改任务失败!'.$dsql->GetError(), 'javascript:;');
    }
    exit();
}

$row = $dsql->GetOne("SELECT * FROM `#@__sys_task` WHERE id='$id' ");
include DedeInclude('templets/sys_task_edit.htm');