<?php 
require_once(dirname(__FILE__)."/config.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$dsql = new DedeSql(false);

if(empty($pagesize)) $pagesize = 18;
if(empty($pageno)) $pageno = 1;
if(empty($dopost)) $dopost = '';
if(empty($orderby)) $orderby = 'aid';
if(empty($keyword)){
	$keyword = '';
	$addget = '';
	$addsql = '';
}else{
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
//更新字段
else if($dopost=='update')
{
	$aid = ereg_replace("[^0-9]","",$aid);
	$count = ereg_replace("[^0-9]","",$count);
	$istag = ereg_replace("[^0-9]","",$istag);
	$keyword = trim($keyword);
	$spwords = trim($spwords);
	$dsql->ExecuteNoneQuery("Update #@__search_keywords set keyword='$keyword',spwords='$spwords',count='$count',istag='$istag' where aid='$aid';");
	AjaxHead();
	GetKeywordList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}
//删除字段
else if($dopost=='del')
{
	$aid = ereg_replace("[^0-9]","",$aid);
	$dsql->ExecuteNoneQuery("Delete From #@__search_keywords where aid='$aid';");
	AjaxHead();
	GetKeywordList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}

//第一次进入这个页面
if($dopost==''){
	$row = $dsql->GetOne("Select count(*) as dd From #@__search_keywords $addsql ");
	$totalRow = $row['dd'];
	include(dirname(__FILE__)."/templets/search_keywords_main.htm");
  $dsql->Close();
}

//获得特定的关键字列表
//---------------------------------
function GetKeywordList($dsql,$pageno,$pagesize,$orderby='aid'){
	global $cfg_phpurl,$addsql;
	$start = ($pageno-1) * $pagesize;
	$printhead ="<table width='99%' border='0' cellpadding='1' cellspacing='1' bgcolor='#333333' style='margin-bottom:3px'>
    <tr align='center' bgcolor='#E5F9FF' height='24'> 
      <td width='6%' height='23'><a href='#' onclick=\"ReloadPage('aid')\"><u>ID</u></a></td>
      <td width='20%'>关键字</td>
      <td width='25%'>分词结果</td>
      <td width='6%'><a href='#' onclick=\"ReloadPage('count')\"><u>频率</u></a></td>
      <td width='6%'><a href='#' onclick=\"ReloadPage('result')\"><u>结果</u></a></td>
      <td width='10%'><a href='#' onclick=\"ReloadPage('istag')\"><u>是否Tag</u></a></td>
      <td width='16%'><a href='#' onclick=\"ReloadPage('lasttime')\"><u>最后搜索时间</u></a></td>
      <td>管理</td>
    </tr>\r\n";
    echo $printhead;
    $dsql->SetQuery("Select * From #@__search_keywords $addsql order by $orderby desc limit $start,$pagesize ");
	  $dsql->Execute();
    while($row = $dsql->GetArray()){
    if($row['istag']){ 
       $atag = "<input type='radio' class='np' name='istag{$row['aid']}' id='istag{$row['aid']}1' value='1' checked>是 <input type='radio' class='np' name='istag{$row['aid']}' id='istag{$row['aid']}0' value='2'>否";
    }else{
       $atag = "<input type='radio' class='np' name='istag{$row['aid']}' id='istag{$row['aid']}1' value='1'>是 <input type='radio' class='np' name='istag{$row['aid']}' id='istag{$row['aid']}0' value='2' checked>否";
    }
    $line = "
      <tr align='center' bgcolor='#FFFFFF' onMouseMove=\"javascript:this.bgColor='#FCFEDA';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\"> 
      <td height='24'>{$row['aid']}</td>
      <td><input name='keyword' type='text' id='keyword{$row['aid']}' value='{$row['keyword']}' class='ininput'></td>
      <td><input name='spwords' type='text' id='spwords{$row['aid']}' value='{$row['spwords']}' class='ininput'></td>
      <td><input name='count' type='text' id='count{$row['aid']}' value='{$row['count']}' class='ininput'></td>
      <td><a href='{$cfg_phpurl}/search.php?kwtype=0&keyword=".urlencode($row['keyword'])."&searchtype=titlekeyword' target='_blank'><u>{$row['result']}</u></a></td>
      <td> $atag </td>
      <td>".strftime("%y-%m-%d %H:%M:%S",$row['lasttime'])."</td>
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

