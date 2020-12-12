<?php

/**
 * 生成Tag操作
 *
 * @version        $Id: makehtml_taglist_action.php 1 11:17 2020年8月19日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https: //weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
CheckPurview('sys_MakeHtml');
require_once DEDEINC . "/arc.taglist.class.php";

if (empty($pageno)) {
    $pageno = 0;
}

if (empty($mkpage)) {
    $mkpage = 1;
}

if (empty($upall)) {
    $upall = 0;
}
// 是否更新全部 0为更新单个 1为更新全部
if (empty($ctagid)) {
    $ctagid = 0;
}
// 当前处理的tagid
if (empty($maxpagesize)) {
    $maxpagesize = 50;
}

$tagid = isset($tagid) ? intval($tagid) : 0;

if ($tagid > 0) {
    $upall = 0; // 更新单个模式
    $ctagid = $tagid;
} else {
    $upall = 1; // 更新全部模式
}
$allfinish = false; // 是否全部完成

$dd = $dsql->GetOne("SELECT ROUND(AVG(total)) as tt FROM `#@__tagindex`"); // 取一个平均

if ($upall == 1 && $ctagid == 0) {
    $rr = $dsql->GetOne("SELECT * FROM `#@__tagindex` WHERE mktime <> uptime AND total > {$dd['tt']} LIMIT 1");
    if (!empty($rr) && count($rr) > 0) {
        $ctagid = $rr['id'];
    } else {
        $allfinish = true;
    }
}

if ($ctagid == 0 && $allfinish) {
    $reurl = '../a/tags/';
    ShowMsg("完成TAG更新！<a href='$reurl' target='_blank'>浏览TAG首页</a>", "javascript:;");
    exit;
}

$tag = $dsql->GetOne("SELECT * FROM `#@__tagindex` WHERE id='$ctagid' LIMIT 0,1;");

MkdirAll($cfg_basedir . "/a/tags", $cfg_dir_purview);

if (is_array($tag) && count($tag) > 0) {
    $dlist = new TagList($tag['tag'], 'taglist.htm');
    $dlist->CountRecord();
    $ntotalpage = $dlist->TotalPage;

    if ($ntotalpage <= $maxpagesize) {
        $dlist->MakeHtml('', '');
        $finishType = true; // 生成一个TAG完成
    } else {
        $reurl = $dlist->MakeHtml($mkpage, $maxpagesize);
        $finishType = false;
        $mkpage = $mkpage + $maxpagesize;
        if ($mkpage >= ($ntotalpage + 1)) {
            $finishType = true;
        }

    }

    $nextpage = $pageno + 1;
    $onefinish = $nextpage >= $ntotalpage && $finishType;
    if (($upall == 0 && $onefinish) || ($upall == 1 && $allfinish && $onefinish)) {
        $dlist = new TagList('', 'tag.htm');
        $dlist->MakeHtml(1, 10);
        $reurl = '../a/tags/';
        if ($upall == 1) {
            ShowMsg("完成TAG更新！<a href='$reurl' target='_blank'>浏览TAG首页</a>", "javascript:;");
        } else {
            $query = "UPDATE `#@__tagindex` SET mktime=uptime WHERE id='$ctagid' ";
            $dsql->ExecuteNoneQuery($query);

            $reurl .= GetPinyin($tag['tag']);
            ShowMsg("完成TAG更新：[" . $tag['tag'] . "]！<a href='$reurl' target='_blank'>浏览TAG首页</a>", "javascript:;");
        }
        exit();
    } else {
        if ($finishType) {
            // 完成了一个跳到下一个
            if ($upall == 1) {
                $query = "UPDATE `#@__tagindex` SET mktime=uptime WHERE id='$ctagid' ";
                $dsql->ExecuteNoneQuery($query);
                $ctagid = 0;
                $nextpage = 0;
            }
            $gourl = "makehtml_taglist_action.php?maxpagesize=$maxpagesize&tagid=$tagid&pageno=$nextpage&upall=$upall&ctagid=$ctagid";
            ShowMsg("成功生成TAG：[" . $tag['tag'] . "]，继续进行操作！", $gourl, 0, 100);
            exit();
        } else {
            // 继续当前这个
            $gourl = "makehtml_taglist_action.php?mkpage=$mkpage&maxpagesize=$maxpagesize&tagid=$tagid&pageno=$pageno&upall=$upall&ctagid=$ctagid";
            ShowMsg("成功生成TAG：[" . $tag['tag'] . "]，继续进行操作...", $gourl, 0, 100);
            exit();
        }
    }
}
