<?php if (!defined('DEDEINC')) {exit('Request Error!');
}

/**
* 
* 
 * @version   $Id: edit.inc.php 1 10:06 2010-11-10  $
 * @package   DedeCMS.Site
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 
*/

if (!empty($_COOKIE['GUEST_BOOK_POS'])) {
    $GUEST_BOOK_POS = $_COOKIE['GUEST_BOOK_POS'];

} else {
    $GUEST_BOOK_POS = "guestbook.php";

}

$id = intval($id);
if (empty($job)) {
    $job = 'view';

}

if ($job == 'del' && $g_isadmin) {
    $dsql->ExecuteNoneQuery("DELETE FROM `#@__guestbook` WHERE id='$id' ");
    ShowMsg("成功删除一条留言！", $GUEST_BOOK_POS);
    exit();

} else if ($job == 'check' && $g_isadmin) {
    $dsql->ExecuteNoneQuery("UPDATE `#@__guestbook` SET ischeck=1 WHERE id='$id' ");
    ShowMsg("成功审核一条留言！", $GUEST_BOOK_POS);
    exit();

} else if ($job == 'editok') {
    $remsg = trim($remsg);
    if ($remsg != '') {
        //管理员回复不过滤HTML
        if ($g_isadmin) {
            $msg = "<div class=\\'rebox\\'>" . $msg . "</div>\n" . $remsg;
            //$remsg <br><font color=red>管理员回复：</font>
        
        } else {
            $row = $dsql->GetOne("SELECT msg From `#@__guestbook` WHERE id='$id' ");
            $oldmsg = "<div class=\\'rebox\\'>" . addslashes($row['msg']) . "</div>\n";
            $remsg = trimMsg(cn_substrR($remsg, 1024), 1);
            $msg = $oldmsg . $remsg;
        
        }
    
    } else {
        if (!$g_isadmin) {
            ShowMsg("无权提交修改当前留言！", $GUEST_BOOK_POS);
            exit();
        
        }
    
    }
    $msg = HtmlReplace($msg, -1);
    /*
    漏洞描述：dedecms留言板注入漏洞。
     */
    $msg = addslashes($msg);
    $dsql->ExecuteNoneQuery("UPDATE `#@__guestbook` SET `msg`='$msg', `posttime`='" . time() . "' WHERE id='$id' ");
    ShowMsg("成功更改或回复一条留言！", $GUEST_BOOK_POS);
    exit();

}

if ($g_isadmin) {
    $row = $dsql->GetOne("SELECT * FROM `#@__guestbook` WHERE id='$id'");
    $dlist = new DataListCP();
    $dlist->SetTemplate(DEDETEMPLATE . '/plus/guestbook-admin.htm');
    $dlist->Display();

} else {
    $row = $dsql->GetOne("SELECT id,title FROM `#@__guestbook` WHERE id='$id'");
    $dlist = new DataListCP();
    $dlist->SetTemplate(DEDETEMPLATE . '/plus/guestbook-user.htm');
    $dlist->Display();

}
