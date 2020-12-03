<?php
require_once(dirname(__FILE__)."/config.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$dsql = new DedeSql(false);

if(empty($pagesize)) $pagesize = 20;
if(empty($pageno)) $pageno = 1;
if(empty($dopost)) $dopost = '';
if(empty($orderby)) $orderby = 'aid';
if(empty($showtag)) $showtag = 0;
if(empty($showkw)) $showkw = 0;
if(!isset($action)) $action = '';


if($action == 'delete'){
	if(is_array($kids)){
		$kids = implode(',', $kids);
		$query = "delete from #@__search_keywords where aid in ($kids)";
		if($dsql->executenonequery($query)){
			showmsg('批量删除关键词成功', 'search_keywords_main.php');
			exit;
		}else{
			showmsg('批量删除关键词失败', 'search_keywords_main.php');
			exit;
		}
	}
}

//初处化处理
$addget = '';
$addsql = '';
if(empty($keyword)){
	$keyword = '';
}
if(!empty($type)){
	$addget = '&keyword='.urlencode($keyword);
	$addsql = " where CONCAT(keyword,spwords) like '%$keyword%' ";
}
//重载列表
if($dopost=='getlist'){
	AjaxHead();
	GetKeywordList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}
elseif($dopost == 'add'){
	$count = ereg_replace("[^0-9]","",$count);
	$keyword = trim($keyword);
	$spwords = trim($spwords);
	$timestamp = time();
	$dsql->executeNoneQuery("insert into #@__search_keywords(keyword, spwords, count, lasttime)
	values('$keyword', '$spwords','$count', '$timestamp');
	");
	AjaxHead();
	GetKeywordList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
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
	$dsql->Close();
	exit();
}
//删除字段
else if($dopost=='del')
{
	$aid = ereg_replace("[^0-9]","",$aid);
	$dsql->ExecuteNoneQuery("Delete From `#@__search_keywords` where aid='$aid';");
	AjaxHead();
	GetKeywordList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}

//第一次进入这个页面
if($dopost==''){
	$row = $dsql->GetOne("Select count(*) as dd From `#@__search_keywords` $addsql ");
	$totalRow = $row['dd'];
	include(dirname(__FILE__)."/templets/search_keywords_main.htm");
  $dsql->Close();
}

//获得特定的关键字列表
//---------------------------------
function GetKeywordList(&$dsql,$pageno,$pagesize,$orderby='aid'){
	global $cfg_phpurl,$addsql;
	$start = ($pageno-1) * $pagesize;
	$printhead ="<table width='96%' border='0' cellpadding='1' cellspacing='1' bgcolor='#E2F5BC' style='margin-bottom:3px' align='center'>
    <tr align='center' bgcolor='' height='24'>
      <td width='6%' height='28'><a href='#' onclick=\"ReloadPage('aid')\"><u>ID</u></a></td>
      <td width='20%'>关键字</td>
      <td width='25%'>分词结果</td>
      <td width='6%'><a href='#' onclick=\"ReloadPage('count')\"><u>频率</u></a></td>
      <td width='6%'><a href='#' onclick=\"ReloadPage('result')\"><u>结果</u></a></td>
      <td width='6%'>选择</td>
      <td width='20%'><a href='#' onclick=\"ReloadPage('lasttime')\"><u>最后搜索时间</u></a></td>
      <td>管理</td>
    </tr>
    ";
    echo $printhead;
    //echo "Select * From #@__search_keywords $addsql order by $orderby desc limit $start,$pagesize ";
    $dsql->SetQuery("Select * From #@__search_keywords $addsql order by $orderby desc limit $start,$pagesize ");
	  $dsql->Execute();
	  $i = 0;
    while($row = $dsql->GetArray())
    {
      $i++;
      $nurl = "{$cfg_phpurl}/search.php?kwtype=0&keyword=".urlencode($row['keyword'])."&searchtype=titlekeyword";
    $line = "
      <tr align='center' bgcolor='#FFFFFF' onMouseMove=\"javascript:this.bgColor='#FCFEDA';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">
      <td height='28'>{$row['aid']}</td>
      <td><input name='keyword' type='text' id='keyword{$row['aid']}' value='{$row['keyword']}' class='ininput'></td>
      <td><input name='spwords' type='text' id='spwords{$row['aid']}' value='{$row['spwords']}' class='ininput'></td>
      <td><input name='count' type='text' id='count{$row['aid']}' value='{$row['count']}' class='ininput'></td>
      <td><a href='$nurl' target='_blank'><u>{$row['result']}</u></a></td>
      <td><input type='checkbox' id='kids{$i}' name='kids[]' class='np' value='{$row['aid']}' /></td>
      <td>".strftime("%y-%m-%d %H:%M",$row['lasttime'])."</td>
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

