<?php   if(!defined('DEDEINC')) exit("DedeCMS Error: Request Error!");

/**
 * 动态模板memberlist标签
 *
 * @version        $Id: plus_newvisitor.php 1 13:58 2010年7月5日 $
 * @package        DedeCMS.Tpllib
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
function plus_newvisitor(&$atts,&$refObj,&$fields)
{
    global $dsql,$_vars,$cfg_memberurl;

    $attlist = "titlelen=30,infolen=200,row=6";
    FillAtts($atts,$attlist);
    FillFields($atts,$fields,$refObj);
    extract($atts, EXTR_OVERWRITE);
    $mid = $_vars['mid'];

    $query = "SELECT h.*,mb.face,mb.sex,mb.userid AS loginid,mb.uname,s.sign FROM `#@__member_vhistory` h
             LEFT JOIN `#@__member` mb ON mb.mid = h.vid
             LEFT JOIN `#@__member_space` s ON s.mid = h.vid
             WHERE  h.mid='$mid' ORDER BY h.vtime DESC LIMIT 0,$row";

    $dsql->SetQuery($query);
    $dsql->Execute("al");
    $rearr = array();
    while($row = $dsql->GetArray("al"))
    {
        $row['url'] = $cfg_memberurl."/index.php?uid=".$row['loginid'];
        if(empty($row['face']))
        {
            $row['face']=($row['sex']=='?')? $cfg_memberurl.'/templets/images/dfgirl.png' : $cfg_memberurl.'/templets/images/dfboy.png';
        }
        $rearr[] = $row;
    }
    $dsql->FreeResult("al");
    return $rearr;
}