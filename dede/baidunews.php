<?php
require_once(dirname(__FILE__)."/config.php");

if(empty($do))
{
	include DEDEADMIN.'/templets/baidunews.htm';
}else
{
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

	$query = "select maintable.*, addtable.body, arctype.typename
	from #@__archives maintable
	left join #@__addonarticle addtable on addtable.aid=maintable.id
	left join #@__arctype arctype on arctype.ID=maintable.typeid
	where maintable.channel=1 and maintable.arcrank!=-1 order by maintable.pubdate desc limit $limit
	";
	$dsql->SetQuery($query);
	$dsql->Execute();
	while($row = $dsql->GetArray())
	{
		$title = htmlspecialchars($row['title']);
		$row1 = GetOneArchive($row['id']);
		if(strpos($row1['arcurl'],'http://') === false)
		{
			$link = ($cfg_basehost=='' ? 'http://'.$_SERVER["HTTP_HOST"].$cfg_cmspath : $cfg_basehost).$row1['arcurl'];
		}else
		{
			$link = $row1['arcurl'];
		}
		$link = htmlspecialchars($link);
		$description = htmlspecialchars(strip_tags($row['description']));
		$text = htmlspecialchars(strip_tags($row['body']));
		$image = $row['litpic'] =='' ? '' :$row['litpic'];
		if($image != '' && strpos($image, 'http://') === false)
		{
			$image = ($cfg_basehost=='' ? 'http://'.$_SERVER["HTTP_HOST"].$cfg_cmspath : $cfg_basehost).$image;

		}
		//$headlineimg = '';
		$keywords = htmlspecialchars($row['keywords']);
		$category = htmlspecialchars($row['typename']);
		$author = htmlspecialchars($row['writer']);
		$source = htmlspecialchars($row['source']);
		$pubdate = htmlspecialchars(gmdate('Y-m-d H:i',$row['pubdate'] + $cfg_cli_time * 3600));

		$baidunews .= "<item>\n";
		$baidunews .= "<title>$title </title>\n";
		$baidunews .= "<link>$link </link>\n";
		$baidunews .= "<description>$description </description>\n";
		$baidunews .= "<text>$text </text>\n";
		$baidunews .= "<image>$image </image>\n";
		//$baidunews .= "<headlineImg />\n";
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

?>