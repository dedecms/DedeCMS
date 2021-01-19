<?php
/**
 * 会员权限管理
 *
 * @version   $Id: member_rank.php 1 12:37 2010年7月20日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
CheckPurview('member_Type');
if (empty($dopost)) {
    $dopost = '';
}

//保存更改
if ($dopost == 'save') {
    if (!empty($ids)){
        foreach ($ids as $key => $value) {
            $id = ${"ID_" . $value};
            $name = ${"name_" . $value};
            $rank = ${"rank_" . $value};
            $money = ${"money_" . $value};
            $scores = ${"scores_" . $value};
            if ($rank > 0) {
                $query = "UPDATE `#@__arcrank` SET membername='$name',money='$money',rank='$rank',scores='$scores' WHERE id='$id' ";
            }
            if ($query != '') {
                $dsql->ExecuteNoneQuery($query);
            }
        }
        ShowMsg("成功更新会员等级表！", "member_rank.php");
        exit();
    }
    ShowMsg("更新失败！", "member_rank.php");
    exit();

} else if ($dopost == 'del') {
    $dsql->ExecuteNoneQuery("DELETE FROM `#@__arcrank` WHERE id='$id' AND rank<>10");
    ShowMsg("删除成功！", "member_rank.php");
    exit();
} else if ($dopost == 'add') {
    if ($rank > 0 && $name != '' && $rank > 10) {
        $inquery = "INSERT INTO `#@__arcrank`(`rank`,`membername`,`adminrank`,`money`,`scores`,`purviews`) VALUES('$rank','$name','5','$money','$scores',''); ";
        $dsql->ExecuteNoneQuery($inquery);
        ShowMsg("添加成功！", "member_rank.php");
        exit();
    }
    ShowMsg("添加失败！", "member_rank.php");
    exit();
}



$query = "SELECT * FROM `#@__arcrank` WHERE rank>0 ORDER BY rank";
$dlist = new DataListCP();
$dlist->SetTemplate(DEDEADMIN . "/templets/member_rank.htm");
$dlist->SetSource($query);
$dlist->Display();
$dlist->Close();

