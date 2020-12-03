<?php 
require_once(dirname(__FILE__)."/config.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$dsql = new DedeSql(false);

if(empty($pagesize)) $pagesize = 18;
if(empty($pageno)) $pageno = 1;
if(empty($dopost)) $dopost = '';
if(empty($orderby)) $orderby = 'tid';
if(empty($keyword)){
	$keyword = '';
	$addget = '';
	$addsql = '';
}else{
	$addget = '&keyword='.urlencode($keyword);
	$addsql = " where tagname like '%$keyword%' ";
}

//重载列表
if($dopost=='getlist'){
	AjaxHead();
	GetTagList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}
//更新字段
else if($dopost=='update')
{
	$tid = ereg_replace("[^0-9]","",$tid);
	$tagcc = ereg_replace("[^0-9]","",$tagcc);
	$cc = ereg_replace("[^0-9]","",$cc);
	$tagname = trim($tagname);
	$dsql->ExecuteNoneQuery("Update #@__tags set tagname='$tagname',tagcc='$tagcc',cc='$cc' where tid='$tid';");
	AjaxHead();
	GetTagList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}
//删除字段
else if($dopost=='del')
{
	$tid = ereg_replace("[^0-9]","",$tid);
	$dsql->ExecuteNoneQuery("Delete From #@__tags_archives where tid='$tid'; ");
	//$dsql->ExecuteNoneQuery("Delete From #@__tags_user where tid='$tid'; ");
	$dsql->ExecuteNoneQuery("Delete From #@__tags where tid='$tid'; ");
	AjaxHead();
	GetTagList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}

//第一次进入这个页面
if($dopost==''){
	$row = $dsql->GetOne("Select count(*) as dd From #@__tags $addsql ");
	$totalRow = $row['dd'];
	include(dirname(__FILE__)."/templets/tag_main.htm");
  $dsql->Close();
}

//获得特定的Tag列表
//---------------------------------
function GetTagList($dsql,$pageno,$pagesize,$orderby='aid'){
	global $cfg_phpurl,$addsql;
	$start = ($pageno-1) * $pagesize;
	$printhead ="<table width='99%' border='0' cellpadding='1' cellspacing='1' bgcolor='#333333' style='margin-bottom:3px'>
    <tr align='center' bgcolor='#E5F9FF' height='24'> 
      <td width='8%'><a href='#' onclick=\"ReloadPage('tid')\"><u>ID</u></a></td>
      <td width='32%'>TAG名称</td>
      <td width='10%'><a href='#' onclick=\"ReloadPage('tagcc')\"><u>使用率</u></a></td>
      <td width='10%'><a href='#' onclick=\"ReloadPage('cc')\"><u>浏览量</u></a></td>
      <td width='10%'><a href='#' onclick=\"ReloadPage('arcnum')\"><u>文档数</u></a></td>
      <td width='10%'>创建时间</td>
      <td>管理</td>
    </tr>\r\n";
    echo $printhead;
    $dsql->SetQuery("Select * From #@__tags $addsql order by $orderby desc limit $start,$pagesize ");
	  $dsql->Execute();
    while($row = $dsql->GetArray()){
    $line = "
      <tr align='center' bgcolor='#FFFFFF' onMouseMove=\"javascript:this.bgColor='#FCFEDA';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\"> 
      <td height='24'>{$row['aid']}</td>
      <td><input name='tagname' type='text' id='tagname{$row['tid']}' value='{$row['tagname']}' class='ininput'></td>
      <td><input name='tagcc' type='text' id='tagcc{$row['tagcc']}' value='{$row['tagcc']}' class='ininput'></td>
      <td><input name='cc' type='text' id='cc{$row['cc']}' value='{$row['cc']}' class='ininput'></td>
      <td> {$row['arcnum']} </td>
      <td>".strftime("%y-%m-%d",$row['stime'])."</td>
      <td>
      <a href='#' onclick='UpdateNote({$row['tid']})'>更新</a> | 
      <a href='#' onclick='DelNote({$row['tid']})'>删除</a>
      </td>
    </tr>";
    echo $line;
   }
	 echo "</table>\r\n";
}

?>

