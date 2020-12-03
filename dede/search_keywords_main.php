<?php
require_once(dirname(__FILE__)."/config.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
if(empty($pagesize))
{
	$pagesize = 18;
}
if(empty($pageno))
{
	$pageno = 1;
}
if(empty($dopost))
{
	$dopost = '';
}
if(empty($orderby))
{
	$orderby = 'aid';
}

//重载列表
if($dopost=='getlist')
{
	AjaxHead();
	GetKeywordList($dsql,$pageno,$pagesize,$orderby);
	exit();
}

//更新字段
else if($dopost=='update')
{
	$aid = ereg_replace("[^0-9]","",$aid);
	$count = ereg_replace("[^0-9]","",$count);
	$keyword = trim($keyword);
	$spwords = trim($spwords);
	$dsql->ExecuteNoneQuery("Update `#@__search_keywords` set keyword='$keyword',spwords='$spwords',count='$count' where aid='$aid';");
	AjaxHead();
	GetKeywordList($dsql,$pageno,$pagesize,$orderby);
	exit();
}

//删除字段
else if($dopost=='del')
{
	$aid = ereg_replace("[^0-9]","",$aid);
	$dsql->ExecuteNoneQuery("Delete From `#@__search_keywords` where aid='$aid';");
	AjaxHead();
	GetKeywordList($dsql,$pageno,$pagesize,$orderby);
	exit();
}

//第一次进入这个页面
if($dopost=='')
{
	$row = $dsql->GetOne("Select count(*) as dd From `#@__search_keywords` ");
	$totalRow = $row['dd'];
	include(DEDEADMIN."/templets/search_keywords_main.htm");
}

//获得特定的关键字列表
function GetKeywordList($dsql,$pageno,$pagesize,$orderby='aid')
{
	global $cfg_phpurl;
	$start = ($pageno-1) * $pagesize;
	$printhead ="<table width='98%' border='0' cellpadding='1' cellspacing='1' bgcolor='#D1DDAA' style='margin-bottom:3px'>
    <tr align='center' bgcolor='#E9F4D5' height='24'>
      <td width='6%' height='23'><a href='#' onclick=\"ReloadPage('aid')\"><u>ID</u></a></td>
      <td width='20%'>关键字</td>
      <td width='35%'>分词结果</td>
      <td width='6%'><a href='#' onclick=\"ReloadPage('count')\"><u>频率</u></a></td>
      <td width='6%'><a href='#' onclick=\"ReloadPage('result')\"><u>结果</u></a></td>
      <td width='16%'><a href='#' onclick=\"ReloadPage('lasttime')\"><u>最后搜索时间</u></a></td>
      <td>管理</td>
    </tr>\r\n";
	echo $printhead;
	$dsql->SetQuery("Select * From #@__search_keywords order by $orderby desc limit $start,$pagesize ");
	$dsql->Execute();
	while($row = $dsql->GetArray())
	{
		$line = "
      <tr align='center' bgcolor='#FFFFFF' onMouseMove=\"javascript:this.bgColor='#FCFEDA';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">
      <td height='24'>{$row['aid']}</td>
      <td><input name='keyword' type='text' id='keyword{$row['aid']}' value='{$row['keyword']}' class='ininput'></td>
      <td><input name='spwords' type='text' id='spwords{$row['aid']}' value='{$row['spwords']}' class='ininput'></td>
      <td><input name='count' type='text' id='count{$row['aid']}' value='{$row['count']}' class='ininput'></td>
      <td><a href='{$cfg_phpurl}/search.php?kwtype=0&keyword=".urlencode($row['keyword'])."&searchtype=titlekeyword' target='_blank'><u>{$row['result']}</u></a></td>
      <td>".MyDate("Y-m-d H:i:s",$row['lasttime'])."</td>
      <td>
      <a href='#' onclick='UpdateNote({$row['aid']})'>更新</a> |
      <a href='#' onclick='DelNote({$row['aid']})'>删除</a>
      </td>
    </tr>";
		echo $line;
	}
	echo "</table>\r\n";
}

?>