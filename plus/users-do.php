<?php
/**
 * @version   $Id: index_do.php 1 8:24 2010年7月9日 $
 * @package   DedeCMS.Member
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/users_config.php";
if (empty($dopost)) {
    $dopost = '';
}

if (empty($fmdo)) {
    $fmdo = '';
}

/*********************
function check_email()
 *******************/
if ($fmdo == 'sendMail') {
    if (!CheckEmail($cfg_ml->fields['email'])) {
        ShowMsg('你的邮箱格式有错误！', '-1');
        exit();
    }
    if ($cfg_ml->fields['spacesta'] != -10) {
        ShowMsg('你的帐号不在邮件验证状态，本操作无效！', '-1');
        exit();
    }
    $userhash = md5($cfg_cookie_encode . '--' . $cfg_ml->fields['mid'] . '--' . $cfg_ml->fields['email']);
    $url = $cfg_basehost . (empty($cfg_cmspath) ? '/' : $cfg_cmspath) . "/plus/users_index_do.php?fmdo=checkMail&mid={$cfg_ml->fields['mid']}&userhash={$userhash}&do=1";
    $url = preg_replace("#http:\/\/#i", '', $url);
    $url = 'http://' . preg_replace("#\/\/#i", '/', $url);
    $mailtitle = "{$cfg_webname}--会员邮件验证通知";
    $mailbody = '';
    $mailbody .= "尊敬的用户[{$cfg_ml->fields['uname']}]，您好：\r\n";
    $mailbody .= "欢迎注册成为[{$cfg_webname}]的会员。\r\n";
    $mailbody .= "要通过注册，还必须进行最后一步操作，请点击或复制下面链接到地址栏访问这地址：\r\n\r\n";
    $mailbody .= "{$url}\r\n\r\n";
    $mailbody .= "Power by http://www.dedecms.com 织梦内容管理系统！\r\n";

    $headers = "From: " . $cfg_adminemail . "\r\nReply-To: " . $cfg_adminemail;
    if ($cfg_sendmail_bysmtp == 'Y' && !empty($cfg_smtp_server)) {
        $mailtype = 'TXT';
        include_once DEDEINC . '/mail.class.php';
        $smtp = new smtp($cfg_smtp_server, $cfg_smtp_port, true, $cfg_smtp_usermail, $cfg_smtp_password);
        $smtp->debug = false;
        $smtp->sendmail($cfg_ml->fields['email'], $cfg_webname, $cfg_smtp_usermail, $mailtitle, $mailbody, $mailtype);
    } else {
        @mail($cfg_ml->fields['email'], $mailtitle, $mailbody, $headers);
    }
    ShowMsg('成功发送邮件，请稍后登录你的邮箱进行接收！', '/plus');
    exit();
} else if ($fmdo == 'checkMail') {
    $mid = intval($mid);
    if (empty($mid)) {
        ShowMsg('你的效验串不合法！', '-1');
        exit();
    }
    $row = $dsql->GetOne("SELECT * FROM `#@__member` WHERE mid='{$mid}' ");
    $needUserhash = md5($cfg_cookie_encode . '--' . $mid . '--' . $row['email']);
    if ($needUserhash != $userhash) {
        ShowMsg('你的效验串不合法！', '-1');
        exit();
    }
    if ($row['spacesta'] != -10) {
        ShowMsg('你的帐号不在邮件验证状态，本操作无效！', '-1');
        exit();
    }
    $dsql->ExecuteNoneQuery("UPDATE `#@__member` SET spacesta=0 WHERE mid='{$mid}' ");
    // 清除会员缓存
    $cfg_ml->DelCache($mid);
    ShowMsg('操作成功，请重新登录系统！', 'users_login.php');
    exit();
}
/*********************
function Case_user()
 *******************/
else if ($fmdo == 'user') {

    //检查用户名是否存在
    if ($dopost == "checkuser") {
        AjaxHead();
        $msg = '';
        $uid = trim($uid);
        if ($cktype == 0) {
            $msgtitle = '用户笔名';
        } else {
            $msgtitle = '用户名';
        }
        if ($cktype != 0 || $cfg_mb_wnameone == 'N') {
            $msg = CheckUserID($uid, $msgtitle);
        } else {
            $msg = CheckUserID($uid, $msgtitle, false);
        }
        if ($msg == 'ok') {
            $msg = "<font color='#4E7504'><b>√{$msgtitle}可以使用</b></font>";
        } else {
            $msg = "<font color='red'><b>×{$msg}</b></font>";
        }
        echo $msg;
        exit();
    }

    //检查email是否存在
    else if ($dopost == "checkmail") {
        AjaxHead();

        if ($cfg_md_mailtest == 'N') {
            $msg = "<font color='#4E7504'><b>√可以使用</b></font>";
        } else {
            if (!CheckEmail($email)) {
                $msg = "<font color='#4E7504'><b>×Email格式有误</b></font>";
            } else {
                $row = $dsql->GetOne("SELECT mid FROM `#@__member` WHERE email LIKE '$email' LIMIT 1");
                if (!is_array($row)) {
                    $msg = "<font color='#4E7504'><b>√可以使用</b></font>";
                } else {
                    $msg = "<font color='red'><b>×Email已经被另一个帐号占用！</b></font>";
                }
            }
        }
        echo $msg;
        exit();
    }

    //引入注册页面
    else if ($dopost == "regnew") {
        $step = empty($step) ? 1 : intval(preg_replace("/[^\d]/", '', $step));
        include_once dirname(__FILE__) . "/users-reg-new.php";


        exit();
    }

}

/*********************
function login()
 *******************/
else if ($fmdo == 'login') {
    //用户登录
    if ($dopost == "login") {
        if (!isset($vdcode)) {
            $vdcode = '';
        }
        $svali = GetCkVdValue();
        if (preg_match("/2/", $safe_gdopen)) {
            if (strtolower($vdcode) != $svali || $svali == '') {
                ResetVdValue();
                ShowMsg('验证码错误！', 'users_login.php');
                exit();
            }

        }
        if (CheckUserID($userid, '', false) != 'ok') {
            ResetVdValue();
            ShowMsg("你输入的用户名 {$userid} 不合法！", "users_login.php");
            exit();
        }
        if ($pwd == '') {
            ResetVdValue();
            ShowMsg("密码不能为空！", "-1", 0, 2000);
            exit();
        }

        //检查帐号
        $rs = $cfg_ml->CheckUser($userid, $pwd);

        if ($rs == 0) {
            ResetVdValue();
            ShowMsg("用户名不存在！", "users_login.php", 0, 2000);
            exit();
        } else if ($rs == -1) {
            ResetVdValue();
            ShowMsg("密码错误！", "users_login.php", 0, 2000);
            exit();
        } else if ($rs == -2) {
            ResetVdValue();
            ShowMsg("管理员帐号不允许从前台登录！", $cfg_cmsurl."/", 0, 2000);
            exit();
        } else {
            // 清除会员缓存
            $cfg_ml->DelCache($cfg_ml->M_ID);
            if (empty($gourl) || preg_match("#action|_do#i", $gourl)) {
                ShowMsg("成功登录，5秒钟后转向系统主页...", $cfg_cmsurl."/", 0, 2000);
            } else {
                $gourl = str_replace('^', '&', $gourl);
                ShowMsg("成功登录，现在转向指定页面...", $gourl, 0, 2000);
            }
            exit();
        }
    }

    //退出登录
    else if ($dopost == "exit") {
        $cfg_ml->ExitCookie();
        ShowMsg("成功退出登录！", $cfg_cmsurl."/", 0, 2000);
        exit();
    }
}
/*********************
function moodmsg()
 *******************/
else if ($fmdo == 'moodmsg') {
    //用户登录
    if ($dopost == "sendmsg") {
        if (!empty($content)) {
            $ip = GetIP();
            $dtime = time();
            $ischeck = ($cfg_mb_msgischeck == 'Y') ? 0 : 1;
            if ($cfg_soft_lang == 'gb2312') {
                $content = utf82gb(nl2br($content));
            }
            $content = cn_substrR(HtmlReplace($content, 1), 360);
            //对表情进行解析
            $content = addslashes(preg_replace("/\[face:(\d{1,2})\]/is", "<img src='" . $cfg_memberurl . "/templets/images/smiley/\\1.gif' style='cursor: pointer; position: relative;'>", $content));
            $content = RemoveXSS($content);
            $inquery = "INSERT INTO `#@__member_msg`(`mid`,`userid`,`ip`,`ischeck`,`dtime`, `msg`)
                   VALUES ('{$cfg_ml->M_ID}','{$cfg_ml->M_LoginID}','$ip','$ischeck','$dtime', '$content'); ";
            $rs = $dsql->ExecuteNoneQuery($inquery);
            if (!$rs) {
                $output['type'] = 'error';
                $output['data'] = '更新失败,请重试.';
                exit();
            }
            $output['type'] = 'success';
            if ($cfg_soft_lang == 'gb2312') {
                $content = utf82gb(nl2br($content));
            }
            $output['data'] = stripslashes($content);
            exit(json_encode($output));
        }
    }
} else {
    ShowMsg("本页面禁止返回!", $cfg_cmsurl."/");
}
