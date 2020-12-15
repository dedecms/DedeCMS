<?php
/**
 * 管理菜单函数
 *
 * @version        $Id: inc_menu_func.php 1 10:32 2010年7月21日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https: //weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/../config.php";
require_once DEDEINC . "/dedetag.class.php";

$headTemplet = "<ul class=\"uk-nav uk-nav-default uk-nav-parent-icon\" uk-nav>";
$headTemplet .= "<li class=\"uk-parent ~display~\"><a href=\"#\"><span data-uk-icon=\"icon: thumbnails\" class=\"uk-margin-small-right\"></span>~channelname~</a>";
$headTemplet .= " <ul class=\"uk-nav-sub\"  >\r\n";
$footTemplet = "</ul>\r\n</li>\r\n</ul>\r\n";
$itemTemplet = "<li>~link~</li>\r\n";

function GetMenus($userrank, $topos = 'main', $openitem='1')
{

    global $headTemplet, $footTemplet, $itemTemplet;

    if ($topos ==='main') {
        $openitem = (empty($openitem) ? 1 : $openitem);
        $menus = $GLOBALS['menusMain'];
    } else if ($topos === 'module') {
        $openitem = 100;
        $menus = $GLOBALS['menusMoudle'];
    }
    $dtp = new DedeTagParse();
    $dtp->SetNameSpace('m', '<', '>');
    $dtp->LoadSource($menus);
    $dtp2 = new DedeTagParse();
    $dtp2->SetNameSpace('m', '<', '>');
    $m = 0;
    foreach ($dtp->CTags as $i => $ctag) {
        if ($ctag->GetName() === 'top' && ($ctag->GetAtt('rank') === '' || TestPurview($ctag->GetAtt('rank')))) {
            if ($openitem != 999 && !preg_match("#" . $openitem . '_' . "#", $ctag->GetAtt('item')) && $openitem != 100) {
                continue;
            }

            $m++;
            echo "<!-- Item " . ($m + 1) . " Strat -->\r\n";
            $htmp = str_replace("~channelname~", $ctag->GetAtt("name"), $headTemplet);

            $htmp = str_replace('~display~', $ctag->GetAtt('display'), $htmp);

            $htmp = str_replace('~cc~', $m . '_' . $openitem, $htmp);
            echo $htmp;
            $dtp2->LoadSource($ctag->InnerText);
            foreach ($dtp2->CTags as $j => $ctag2) {
                $ischannel = trim($ctag2->GetAtt('ischannel'));
                if ($ctag2->GetName() == 'item' && ($ctag2->GetAtt('rank') == '' || TestPurview($ctag2->GetAtt('rank')))) {
                    $link = "<a class=\"chevron-icon\" href='" . $ctag2->GetAtt('link') . "' target='" . $ctag2->GetAtt('target') . "'><span width=\"12\" data-uk-icon=\"icon: triangle-right\" class=\"uk-margin-small-right\"></span>" . $ctag2->GetAtt('name') . "</a>";
                    if ($ischannel == '1') {
                        if ($ctag2->GetAtt('addalt') != '') {
                            $addalt = $ctag2->GetAtt('addalt');
                        } else {
                            $addalt = '录入新内容';
                        }

                        if ($ctag2->GetAtt('addico') !== '') {
                            $addico = $ctag2->GetAtt('addico');
                        } else {
                            $addico = 'plus-circle';
                        }

                        //an add icos , small items use att ischannel='1' addico='ico' addalt='msg' linkadd=''
                        $link = "<li class=\"uk-flex dede_menu_icon\" >
                        $link
                        <a class=\"icon\" uk-tooltip='title: ".$addalt."; pos: left' href='" . $ctag2->GetAtt('linkadd') . "' target='" . $ctag2->GetAtt('target') . "'><span data-uk-icon=\"icon: ".$addico."\" width=\"16\" class=\"uk-margin-small-right\"></span></a>
                        </li>\r\n";

                    } else {
                        $link .= "\r\n";
                    }
                    $itemtmp = str_replace('~link~', $link, $itemTemplet);
                    echo $itemtmp;
                }
            }
            echo $footTemplet;
            echo "<!-- Item " . ($m + 1) . " End -->\r\n";
        }
    }
}
//End Function
