<?php
/**
 * @version   $Id: account.php 1 8:38 2010年7月9日 $
 * @package   DedeCMS.Member
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */

require_once dirname(__FILE__) . '/../include/common.inc.php';
if ($cfg_mb_open == 'N') {
    exit();
}
require_once dirname(__FILE__) . "/users_config.php";
$gourl = RemoveXSS($gourl);
$cfg_ml = new MemberLogin();

// 用户状态
if ($dopost === 'status') {
    $tpl = DEDETEMPLATE . '/plus/'."users-status-notlogged.htm";
    if ($cfg_ml->IsLogin()) {
        $tpl = DEDETEMPLATE . '/plus/'."users-status-logged.htm";
    }  
    $dlist = new DataListCP();
    $dlist->SetTemplate($tpl);
    $dlist->Display();
} else if ($dopost === 'cart' && $cfg_ml->IsLogin()) {
    include_once DEDEINC . "/shopcar.class.php";
    $cart = new MemberShops();
    if ($cart->cartCount() > 0) {
        echo '<span class="uk-badge">'.$cart->cartCount().'</span>';
    }
} else if ($dopost === 'orders' && $cfg_ml->IsLogin() ) {
    $row = $dsql->GetOne("SELECT COUNT(userid) FROM #@__shops_orders WHERE userid='" . $cfg_ml->M_ID."'");
    echo ' ('.$row['COUNT(userid)'].')';
}
