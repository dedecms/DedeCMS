<?php
/**
 * @version   $Id: login.php 1 8:38 2010年7月9日 $
 * @package   DedeCMS.Member
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/users_config.php";
$gourl = RemoveXSS($gourl);
if ($cfg_ml->IsLogin()) {
    ShowMsg('你已经登陆系统，无需重新注册！', $cfg_cmsurl.'/');
    exit();
}
$dlist = new DataListCP();
$dlist->SetTemplate(DEDETEMPLATE . '/plus/users-login.htm');
$dlist->Display();
