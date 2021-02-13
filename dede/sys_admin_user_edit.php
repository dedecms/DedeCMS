<?php
/**
 * 编辑系统管理员
 *
 * @version   $Id: sys_admin_user_edit.php 1 16:22 2010年7月20日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https://weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . '/config.php';
CheckPurview('sys_User');
require_once DEDEINC . '/typelink.class.php';
if (empty($dopost)) {
    $dopost = '';
}

$id = preg_replace("#[^0-9]#", '', $id);

if ($dopost == 'saveedit') {
    csrf_check();
    $pwd = trim($pwd);
    if ($pwd != '' && preg_match("#[^0-9a-zA-Z_@!\.-]#", $pwd)) {
        ShowMsg('密码不合法，请使用[0-9a-zA-Z_@!.-]内的字符！', '-1', 0, 3000);
        exit();
    }
    $safecodeok = substr(md5($cfg_cookie_encode . $randcode), 0, 24);
    if ($safecodeok != $safecode) {
        ShowMsg("请填写正确的安全验证串！", "sys_admin_user_edit.php?id={$id}&dopost=edit");
        exit();
    }
    $pwdm = '';
    if ($pwd != '') {
        $pwdm = ",pwd='" . md5($pwd) . "'";
        $pwd = ",pwd='" . substr(md5($pwd), 5, 20) . "'";
    }
    if (empty($typeids)) {
        $typeid = '';
    } else {
        $typeid = join(',', $typeids);
        if ($typeid == '0') {
            $typeid = '';
        }

    }
    if ($id != 1) {
        $query = "UPDATE `#@__admin` SET uname='$uname',usertype='$usertype',tname='$tname',email='$email',typeid='$typeid' $pwd WHERE id='$id'";
    } else {
        $query = "UPDATE `#@__admin` SET uname='$uname',tname='$tname',email='$email',typeid='$typeid' $pwd WHERE id='$id'";
    }
    $dsql->ExecuteNoneQuery($query);
    $query = "UPDATE `#@__member` SET uname='$uname',email='$email'$pwdm WHERE mid='$id'";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg("成功修改一个帐户！", "sys_admin_user.php");
    exit();
} else if ($dopost == 'delete') {
    if (empty($userok)) {
        $userok = "";
    }

    if ($userok != "yes") {
        $randcode = mt_rand(10000, 99999);
        $safecode = substr(md5($cfg_cookie_encode . $randcode), 0, 24);
        include_once DEDEINC . "/oxwindow.class.php";
        $wintitle = "删除用户";
        $wecome_info = "<a href='sys_admin_user.php'>系统帐号管理</a>::删除用户";
        $win = new OxWindow();
        $win->Init("sys_admin_user_edit.php", "js/blank.js", "POST");
        $win->AddHidden("dopost", $dopost);
        $win->AddHidden("userok", "yes");
        $win->AddHidden("randcode", $randcode);
        $win->AddHidden("safecode", $safecode);
        $win->AddHidden("id", $id);
        $win->AddTitle("系统警告！");
        $win->AddMsgItem("你确信要删除用户：$userid 吗？", "50");
        $win->AddMsgItem("安全验证串：<input name='safecode' type='text' id='safecode' size='16' style='width:200px' />&nbsp;(复制本代码： <font color='red'>$safecode</font> )", "30");
        $winform = $win->GetWindow("ok");
        $win->Display();
        exit();
    }
    $safecodeok = substr(md5($cfg_cookie_encode . $randcode), 0, 24);
    if ($safecodeok != $safecode) {
        ShowMsg("请填写正确的安全验证串！", "sys_admin_user.php");
        exit();
    }

    //不能删除id为1的创建人帐号，不能删除自己
    $rs = $dsql->ExecuteNoneQuery2("DELETE FROM `#@__admin` WHERE id='$id' AND id<>1 AND id<>'" . $cuserLogin->getUserID() . "' ");
    if ($rs > 0) {
        //更新前台用户信息
        $dsql->ExecuteNoneQuery("UPDATE `#@__member` SET matt='0' WHERE mid='$id' LIMIT 1");
        ShowMsg("成功删除一个帐户！", "sys_admin_user.php");
    } else {
        ShowMsg("不能删除id为1的创建人帐号，不能删除自己！", "sys_admin_user.php", 0, 3000);
    }
    exit();
}

//显示用户信息
$randcode = mt_rand(10000, 99999);
$safecode = substr(md5($cfg_cookie_encode . $randcode), 0, 24);
$typeOptions = '';
$row = $dsql->GetOne("SELECT * FROM `#@__admin` WHERE id='$id'");
$typeids = explode(',', $row['typeid']);



$dsql->SetQuery("SELECT reid,id,typename FROM `#@__arctype` order by topid  asc , sortrank asc");
$dsql->Execute('op');
while ($row_op = $dsql->GetArray('op')) {
    $rows[] = $row_op;
}
$typeOptions = array();
$index = array();

foreach($rows as $value) {  
    if($value["reid"] == 0) { 
        $typeOptions[$value["id"]] = $value;  
        $index[$value["id"]] =& $typeOptions[$value["id"]];
    }else {  
        $index[$value["reid"]][$value["id"]] = $value;  
        $index[$value["id"]] =& $index[$value["reid"]][$value["id"]];  
    }
}

function getswitch($data, $l, $typeids){
    foreach($data as $key=>$value){
        if(is_array($value)){
        
            $result=getswitch($value, $l,$typeids);
        }
        else{
            $result[$key]=$value;
            if (count($result) == 3){
                $l++;
                $line = "";
                for ($i=0; $i < $l-1; $i++) { 
                    $line .= "—";
                }
                echo "<option value='{$result["id"]}' ".(in_array($result['id'], $typeids) ? ' selected' : '').">{$line} {$result["typename"]}</option>\r\n";
            }
        }
    }
    return $result;
} 
make_hash();
DedeInclude('templets/sys_admin_user_edit.htm');
