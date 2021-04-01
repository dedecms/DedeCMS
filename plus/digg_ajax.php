<?php
/**
 *
 * 文档digg处理ajax文件
 *
 * @version        $Id: digg_ajax.php 2 13:00 2011/11/25 $
 * @package        DedeCMS.Plus
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/../include/common.inc.php");
$action = isset($action) ? trim($action) : '';
$id = empty($id)? 0 : intval(preg_replace("/[^\d]/",'', $id));

helper('cache');

if($id < 1)
{
    exit();
}

$maintable = '#@__archives';

$prefix = 'diggCache';
$key = 'aid-'.$id;
$row = GetCache($prefix, $key);

if(!is_array($row) || $cfg_digg_update==0)
{
  $row = $dsql->GetOne("SELECT goodpost,badpost,scores FROM `$maintable` WHERE id='$id' ");
    if($cfg_digg_update == 0)
    {
		if($action == 'good')
		{
			$row['goodpost'] = $row['goodpost'] + 1;
			$dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores + {$cfg_caicai_add},goodpost=goodpost+1,lastpost=".time()." WHERE id='$id'");
		}
		else if($action=='bad')
		{
			$row['badpost'] = $row['badpost'] + 1;
			$dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores - {$cfg_caicai_sub},badpost=badpost+1,lastpost=".time()." WHERE id='$id'");
		}
		DelCache($prefix, $key);
    }
  SetCache($prefix, $key, $row, 0);
} else {
	if($action == 'good')
	{
	    $row['goodpost'] = $row['goodpost'] + 1;
	    $row['scores'] = $row['scores'] + $cfg_caicai_sub;
	    if($row['goodpost'] % $cfg_digg_update == 0)
	    {
			$add_caicai_sub = $cfg_digg_update * $cfg_caicai_sub;
		    $dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores + {$add_caicai_sub},goodpost=goodpost+{$cfg_digg_update} WHERE id='$id'");
		    DelCache($prefix, $key);
	    }
	} else if($action == 'bad')
	{
	    $row['badpost'] = $row['badpost'] + 1;
		$row['scores'] = $row['scores'] - $cfg_caicai_sub;
	    if($row['badpost'] % $cfg_digg_update == 0)
	    {
			$add_caicai_sub = $cfg_digg_update * $cfg_caicai_sub;
		    $dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores - {$add_caicai_sub},badpost=badpost+{$cfg_digg_update} WHERE id='$id'");
		    DelCache($prefix, $key);
	    }
	}
	SetCache($prefix, $key, $row, 0);
}

$digg = '';
if(!is_array($row)) exit();

if($row['goodpost'] + $row['badpost'] == 0)
{
    $row['goodper'] = $row['badper'] = 0;
}
else
{
    $row['goodper'] = number_format($row['goodpost'] / ($row['goodpost'] + $row['badpost']), 3) * 100;
    $row['badper'] = 100 - $row['goodper'];
}

if(empty($formurl)) $formurl = '';
if($formurl=='caicai')
{
    if($action == 'good') $digg = $row['goodpost'];
    if($action == 'bad') $digg  = $row['badpost'];
}
else
{
    $row['goodper'] = trim(sprintf("%4.2f", $row['goodper']));
    $row['badper'] = trim(sprintf("%4.2f", $row['badper']));
    $digg = '<div class="diggbox digg_good" onmousemove="this.style.backgroundPosition=\'left bottom\';" onmouseout="this.style.backgroundPosition=\'left top\';" onclick="postDigg(\'good\','.$id.')">
            <div class="digg_act">顶一下</div>
            <div class="digg_num">('.$row['goodpost'].')</div>
            <div class="digg_percent">
                <div class="digg_percent_bar"><span style="width:'.$row['goodper'].'%"></span></div>
                <div class="digg_percent_num">'.$row['goodper'].'%</div>
            </div>
        </div>
        <div class="diggbox digg_bad" onmousemove="this.style.backgroundPosition=\'right bottom\';" onmouseout="this.style.backgroundPosition=\'right top\';" onclick="postDigg(\'bad\','.$id.')">
            <div class="digg_act">踩一下</div>
            <div class="digg_num">('.$row['badpost'].')</div>
            <div class="digg_percent">
                <div class="digg_percent_bar"><span style="width:'.$row['badper'].'%"></span></div>
                <div class="digg_percent_num">'.$row['badper'].'%</div>
            </div>
        </div>';
}
AjaxHead();
echo $digg;
exit();
