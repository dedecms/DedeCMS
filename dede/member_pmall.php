<?php
/**
 * 会员短消息,发布所有消息
 *
 * @version        $Id: member_pmall.php 1 11:24 2010年7月20日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Pm');
if(!isset($action)) $action = '';

if($action=="post")
{
    $floginid = 'admin';
    $fromid = 0;
    $toid = 0;
    $tologinid = 0;
    $sendtime = time();
    $writetime = time();
    $subject = cn_substrR(HtmlReplace($subject),70);
    $message = cn_substrR(HtmlReplace($message),1000);
    if(!isset($subject)||empty($subject))
    {
        ShowMsg('短信标题不能为空!','-1');
        exit();
    } else if (!isset($message) || empty($message))
    {
        ShowMsg('请填写短信内容!','-1');
        exit();
    }
    
    #api{{
    if(defined('UC_API') && @include_once DEDEROOT.'/uc_client/client.php')
    {
        uc_pm_send(0,'',$subject,$message);
        ShowMsg('短信已成功发送','-1'); exit;
    }
    #/aip}}
    
    $rs = $dsql->ExecuteNoneQuery("INSERT INTO #@__member_pms(floginid,fromid,toid,tologinid,folder,hasview,subject,sendtime,writetime,message,isadmin) VALUES('$floginid','$fromid','$toid','$tologinid','outbox','0','$subject','$sendtime','$writetime','$message','1');");
    ShowMsg('短信已成功发送','-1');
    exit();
}
require_once(DEDEADMIN."/templets/member_pmall.htm");