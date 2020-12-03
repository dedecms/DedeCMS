<?php
require_once(dirname(__FILE__)."/config.php");

if(empty($do)){

	include './templets/baidunews.htm';
}else{
	$baidunews = "<?xml version=\"1.0\" encoding=\"gb18030\" ?>\n";
	$baidunews .= "<document>\n";
	$baidunews .= "<webSite>$cfg_webname </webSite>\n";
	$baidunews .= "<webMaster>$cfg_adminemail </webMaster>\n";
	$baidunews .= "<updatePeri>$cfg_updateperi </updatePeri>\n";

	$limit = $cfg_baidunews_limit;
	if($limit > 100 || $limit < 1) $limit = 100;

	$query = "select maintable.*, addtable.body, arctype.typename, arc.writer,arc.source
	from #@__full_search maintable
	left join #@__addonarticle addtable on addtable.aid=maintable.aid
	left join #@__arctype arctype on arctype.ID=maintable.typeid
	left join #@__archives arc on arc.ID=maintable.aid
	where maintable.channelid=1 order by maintable.uptime desc limit $limit
	";
	$dsql->SetQuery($query);
	$dsql->execute();
	while($row = $dsql->getarray()){
		$title = htmlspecialchars($row['title']);
		
		if(strpos($row['url'],'http://') === false){
		$link = $cfg_basehost.$row['url'];
		}else{
		$link = $row['url'];
		}
		$link = htmlspecialchars($link);
		
		$description = htmlspecialchars($row['addinfos']);
		$text = htmlspecialchars($row['body']);
		$image = '';
		$headlineimg = '';
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
		$baidunews .= "<headlineImg />\n";
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
	showmsg("<a href='{$filename}' target=\"_blank\">{$filename}生成成功</a>",'javascript:;');
}