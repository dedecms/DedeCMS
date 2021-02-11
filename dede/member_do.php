<?php
/**
 * 用户管理操作
 *
 * @version   $Id: member_do.php 1 13:47 2010年7月19日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https://weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
require_once DEDEINC . "/oxwindow.class.php";
if (empty($dopost)) {
    $dopost = '';
}

if (empty($fmdo)) {
    $fmdo = '';
}

$ENV_GOBACK_URL = isset($_COOKIE['ENV_GOBACK_URL']) ? 'member_main.php' : '';

/*----------------
function __DelMember()
删除用户
----------------*/
if ($dopost == "delmember") {
    CheckPurview('member_Del');
    if ($fmdo == 'yes') {
        $id = preg_replace("#[^0-9]#", '', $id);
        $safecodeok = substr(md5($cfg_cookie_encode . $randcode), 0, 24);
        if ($safecodeok != $safecode) {
            ShowMsg("请填写正确的安全验证串！", "member_do.php?id={$id}&dopost=delmember");
            exit();
        }
        if (!empty($id)) {
            //删除用户信息
            $row = $dsql->GetOne("SELECT * FROM `#@__member` WHERE mid='$id' LIMIT 1 ");
            $rs = 0;
            if ($row['matt'] == 10) {
                $nrow = $dsql->GetOne("SELECT * FROM `#@__admin` WHERE id='$id' LIMIT 1 ");
                //已经删除关连的管理员帐号
                if (!is_array($nrow)) {
                    $rs = $dsql->ExecuteNoneQuery2("DELETE FROM `#@__member` WHERE mid='$id' LIMIT 1");
                }

            } else {
                $rs = $dsql->ExecuteNoneQuery2("DELETE FROM `#@__member` WHERE mid='$id' LIMIT 1");
            }
            if ($rs > 0) {
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_tj` WHERE mid='$id' LIMIT 1");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_space` WHERE mid='$id' LIMIT 1");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_company` WHERE mid='$id' LIMIT 1");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_person` WHERE mid='$id' LIMIT 1");

                //删除用户相关数据
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_stow` WHERE mid='$id' ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_flink` WHERE mid='$id' ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_guestbook` WHERE mid='$id' ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_operation` WHERE mid='$id' ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_pms` WHERE toid='$id' Or fromid='$id' ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_friends` WHERE mid='$id' Or fid='$id' ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_vhistory` WHERE mid='$id' Or vid='$id' ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__feedback` WHERE mid='$id' ");
                $dsql->ExecuteNoneQuery("UPDATE `#@__archives` SET mid='0' WHERE mid='$id'");

            } else {
                ShowMsg("无法删除此用户，如果这个用户是<b>[管理员]</b>，<br />必须先删除这个<b>[管理员]</b>才能删除此帐号！", $ENV_GOBACK_URL, 0, 5000);
                exit();
            }
        }
        ShowMsg("成功删除一个用户！", $ENV_GOBACK_URL);
        exit();
    }
    $randcode = mt_rand(10000, 99999);
    $safecode = substr(md5($cfg_cookie_encode . $randcode), 0, 24);
    $wintitle = "用户管理-删除用户";
    $wecome_info = "<a href='" . $ENV_GOBACK_URL . "'>用户管理</a>::删除用户";
    $win = new OxWindow();
    $win->Init("member_do.php", "js/blank.js", "POST");
    $win->AddHidden("fmdo", "yes");
    $win->AddHidden("dopost", $dopost);
    $win->AddHidden("id", $id);
    $win->AddHidden("randcode", $randcode);
    $win->AddHidden("safecode", $safecode);
    $win->AddTitle("你确实要删除(ID:" . $id . ")这个用户?");
    $win->AddMsgItem("安全验证串：<input name='safecode' type='text' id='safecode' size='16' style='width:200px' />&nbsp;(复制本代码： <font color='red'>$safecode</font> )", "30");
    $winform = $win->GetWindow("ok");
    $win->Display();
} else if ($dopost == "delmembers") {
    CheckPurview('member_Del');
    if ($fmdo == 'yes') {
        $safecodeok = substr(md5($cfg_cookie_encode . $randcode), 0, 24);
        if ($safecodeok != $safecode) {
            ShowMsg("请填写正确的安全验证串！", "member_do.php?id={$id}&dopost=delmembers");
            exit();
        }
        if (!empty($id)) {
            //删除用户信息

            $rs = $dsql->ExecuteNoneQuery2("DELETE FROM `#@__member` WHERE mid IN (" . str_replace("`", ",", $id) . ") And matt<>10 ");
            if ($rs > 0) {
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_tj` WHERE mid IN (" . str_replace("`", ",", $id) . ") ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_space` WHERE mid IN (" . str_replace("`", ",", $id) . ") ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_company` WHERE mid IN (" . str_replace("`", ",", $id) . ") ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_person` WHERE mid IN (" . str_replace("`", ",", $id) . ") ");

                //删除用户相关数据
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_stow` WHERE mid IN (" . str_replace("`", ",", $id) . ") ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_flink` WHERE mid IN (" . str_replace("`", ",", $id) . ") ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_guestbook` WHERE mid IN (" . str_replace("`", ",", $id) . ") ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_operation` WHERE mid IN (" . str_replace("`", ",", $id) . ") ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_pms` WHERE toid IN (" . str_replace("`", ",", $id) . ") Or fromid IN (" . str_replace("`", ",", $id) . ") ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_friends` WHERE mid IN (" . str_replace("`", ",", $id) . ") Or fid IN (" . str_replace("`", ",", $id) . ") ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__member_vhistory` WHERE mid IN (" . str_replace("`", ",", $id) . ") Or vid IN (" . str_replace("`", ",", $id) . ") ");
                $dsql->ExecuteNoneQuery("DELETE FROM `#@__feedback` WHERE mid IN (" . str_replace("`", ",", $id) . ") ");
                $dsql->ExecuteNoneQuery("UPDATE `#@__archives` SET mid='0' WHERE mid IN (" . str_replace("`", ",", $id) . ")");
            } else {
                ShowMsg("无法删除此用户，如果这个用户是管理员关连的ID，<br />必须先删除这个管理员才能删除此帐号！", $ENV_GOBACK_URL, 0, 3000);
                exit();
            }
        }
        ShowMsg("成功删除这些用户！", $ENV_GOBACK_URL);
        exit();
    }
    $randcode = mt_rand(10000, 99999);
    $safecode = substr(md5($cfg_cookie_encode . $randcode), 0, 24);
    $wintitle = "用户管理-删除用户";
    $wecome_info = "<a href='" . $ENV_GOBACK_URL . "'>用户管理</a>::删除用户";
    $win = new OxWindow();
    $win->Init("member_do.php", "js/blank.js", "POST");
    $win->AddHidden("fmdo", "yes");
    $win->AddHidden("dopost", $dopost);
    $win->AddHidden("id", $id);
    $win->AddHidden("randcode", $randcode);
    $win->AddHidden("safecode", $safecode);
    $win->AddTitle("你确实要删除(ID:" . $id . ")这个用户?");
    $win->AddMsgItem(" 安全验证串：<input name='safecode' type='text' id='safecode' size='16' style='width:200px' /> (复制本代码： <font color='red'>$safecode</font>)", "30");
    $winform = $win->GetWindow("ok");
    $win->Display();
}
/*----------------
function __Recommend()
推荐用户
----------------*/
else if ($dopost == "recommend") {
    CheckPurview('member_Edit');
    $id = preg_replace("#[^0-9]#", "", $id);
    if ($matt == 0) {
        $dsql->ExecuteNoneQuery("UPDATE `#@__member` SET matt=1 WHERE mid='$id' AND matt<>10 LIMIT 1");
        ShowMsg("成功设置一个用户推荐！", $ENV_GOBACK_URL);
        exit();
    } else {
        $dsql->ExecuteNoneQuery("UPDATE `#@__member` SET matt=0 WHERE mid='$id' AND matt<>10 LIMIT 1");
        ShowMsg("成功取消一个用户推荐！", $ENV_GOBACK_URL);
        exit();
    }
}
/*----------------
function __EditUser()
修改用户
----------------*/
else if ($dopost == 'edituser') {
    CheckPurview('member_Edit');
    if (!isset($_POST['id'])) {
        exit("DedeCMS Error: Request Error!");
    }

    $pwdsql = empty($pwd) ? '' : ",pwd='" . md5($pwd) . "'";
    if (empty($sex)) {
        $sex = '男';
    }

    $uptime = GetMkTime($uptime);

    if ($matt == 10 && $oldmatt != 10) {
        ShowMsg("对不起，为安全起见，不支持直接把前台用户转为管理的操作！", "-1");
        exit();
    }
    $query = "UPDATE `#@__member` SET
            email = '$email',
            uname = '$uname',
            sex = '$sex',
            matt = '$matt',
            money = '$money',
            scores = '$scores',
            rank = '$rank',
            spacesta='$spacesta',
            uptime='$uptime',
            exptime='$exptime'
            $pwdsql
            WHERE mid='$id' AND matt<>10 ";
    $rs = $dsql->ExecuteNoneQuery2($query);
    if ($rs == 0) {
        $query = "UPDATE `#@__member` SET
            email = '$email',
            uname = '$uname',
            sex = '$sex',
            money = '$money',
            scores = '$scores',
            rank = '$rank',
            spacesta='$spacesta',
            uptime='$uptime',
            exptime='$exptime'
            $pwdsql
            WHERE mid='$id' ";
        $rs = $dsql->ExecuteNoneQuery2($query);
    }



    ShowMsg('成功修改用户资料！', 'member_view.php?id=' . $id);
    exit();
}
/*--------------
function __LoginCP()
登录用户的控制面板
----------*/
else if ($dopost == "memberlogin") {
    CheckPurview('member_Edit');
    PutCookie('DedeUserID', $id, 1800);
    PutCookie('DedeLoginTime', time(), 1800);
    if (empty($jumpurl)) {
        header("location:../member/index.php");
    } else {
        header("location:$jumpurl");
    }

} else if ($dopost == "deoperations") {
    $nid = preg_replace('#[^0-9,]#', '', preg_replace('#`#', ',', $nid));
    $nid = explode(',', $nid);
    if (is_array($nid)) {
        foreach ($nid as $var) {
            $query = "DELETE FROM `#@__member_operation` WHERE aid = '$var'";
            $dsql->ExecuteNoneQuery($query);
        }
        ShowMsg("删除成功！", "member_operations.php");
        exit();
    }
} else if ($dopost == "upoperations") {
    $nid = preg_replace('#[^0-9,]#', '', preg_replace('#`#', ',', $nid));
    $nid = explode(',', $nid);
    if (is_array($nid)) {
        foreach ($nid as $var) {
            $query = "UPDATE `#@__member_operation` SET sta = '1' WHERE aid = '$var'";
            $dsql->ExecuteNoneQuery($query);
            ShowMsg("设置成功！", "member_operations.php");
            exit();
        }
    }
} else if ($dopost == "okoperations") {
    $nid = preg_replace('#[^0-9,]#', '', preg_replace('#`#', ',', $nid));
    $nid = explode(',', $nid);
    if (is_array($nid)) {
        foreach ($nid as $var) {
            $query = "UPDATE `#@__member_operation` SET sta = '2' WHERE aid = '$var'";
            $dsql->ExecuteNoneQuery($query);
            ShowMsg("设置成功！", "member_operations.php");
            exit();
        }
    }
}
