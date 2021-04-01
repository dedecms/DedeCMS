<?php
/**
 * 百度新闻
 *
 * @version        $Id: baidunews.php 1 14:31 2010年7月12日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");

if(empty($do))
{
    include DEDEADMIN.'/templets/baidunews.htm';
} else {
    $baidunews = "<?xml version=\"1.0\" encoding=\"".$cfg_soft_lang."\" ?>\n";
    $baidunews .= "<document>\n";
    $baidunews .= "<webSite>$cfg_webname </webSite>\n";
    $baidunews .= "<webMaster>$cfg_adminemail </webMaster>\n";
    $baidunews .= "<updatePeri>$cfg_updateperi </updatePeri>\n";

    $limit = $cfg_baidunews_limit;
    if($limit > 100 || $limit < 1)
    {
        $limit = 100;
    }

    $query = "SELECT maintable.*, addtable.body, arctype.typename
    FROM #@__archives maintable
    LEFT JOIN #@__addonarticle addtable ON addtable.aid=maintable.id
    LEFT JOIN #@__arctype arctype ON arctype.ID=maintable.typeid
    WHERE maintable.channel=1 and maintable.arcrank!=-1 ORDER BY maintable.pubdate DESC LIMIT $limit
    ";
    $dsql->SetQuery($query);
    $dsql->Execute();
    while($row = $dsql->GetArray())
    {
        $title = dede_htmlspecialchars($row['title']);
        $row1 = GetOneArchive($row['id']);
        if(strpos($row1['arcurl'],'http://') === false)
        {
            $link = ($cfg_basehost=='' ? 'http://'.$_SERVER["HTTP_HOST"].$cfg_cmspath : $cfg_basehost).$row1['arcurl'];
        }else
        {
            $link = $row1['arcurl'];
        }
        $link = dede_htmlspecialchars($link);
        $description = dede_htmlspecialchars(strip_tags($row['description']));
        $text = dede_htmlspecialchars(strip_tags($row['body']));
        $image = $row['litpic'] =='' ? '' :$row['litpic'];
        if($image != '' && strpos($image, 'http://') === false)
        {
            $image = ($cfg_basehost=='' ? 'http://'.$_SERVER["HTTP_HOST"].$cfg_cmspath : $cfg_basehost).$image;

        }
        //$headlineimg = '';
        $keywords = dede_htmlspecialchars($row['keywords']);
        $category = dede_htmlspecialchars($row['typename']);
        $author = dede_htmlspecialchars($row['writer']);
        $source = dede_htmlspecialchars($row['source']);
        $pubdate = dede_htmlspecialchars(gmdate('Y-m-d H:i',$row['pubdate'] + $cfg_cli_time * 3600));

        $baidunews .= "<item>\n";
        $baidunews .= "<title>$title </title>\n";
        $baidunews .= "<link>$link </link>\n";
        $baidunews .= "<description>$description </description>\n";
        $baidunews .= "<text>$text </text>\n";
        $baidunews .= "<image>$image </image>\n";
        //$baidunews .= "<headlineimages/>\n";
        $baidunews .= "<keywords>$keywords </keywords>\n";
        $baidunews .= "<category>$category </category>\n";
        $baidunews .= "<author>$author </author>\n";
        $baidunews .= "<source>$source </source>\n";
        $baidunews .= "<pubDate>$pubdate </pubDate>\n";
        $baidunews .= "</item>\n";
    }
    $baidunews .= "</document>\n";

    $fp = fopen(dirname(__FILE__).'/'.$filename,'w');
    fwrite($fp,$baidunews);
    fclose($fp);
    showmsg("<a href='{$filename}' target=\"_blank\">{$filename} make success</a>",'javascript:;');
}