<?php 
require_once(dirname(__FILE__)."/config.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$dsql = new DedeSql(false);

if(empty($pagesize)) $pagesize = 18;
if(empty($pageno)) $pageno = 1;
if(empty($dopost)) $dopost = '';
if(empty($orderby)) $orderby = 'aid';
if(empty($aid)) $aid = '0';

$aid = ereg_replace("[^0-9]","",$aid);
//重载列表
if($dopost=='getlist'){
	AjaxHead();
	GetUserList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}
//更新字段
else if($dopost=='update')
{
	$dsql->ExecuteNoneQuery("Update dedecms_users set url='$url',version='$version',rank='$rank',isok='$isok',ismember='$ismember' where aid='$aid';");
	AjaxHead();
	GetUserList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}
//删除字段
else if($dopost=='del')
{
	$dsql->ExecuteNoneQuery("Delete From dedecms_users where aid='$aid';");
	AjaxHead();
	GetUserList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}

//第一次进入这个页面
if($dopost==''){
	$row = $dsql->GetOne("Select count(*) as dd From dedecms_users");
	$totalRow = $row['dd'];
	include(dirname(__FILE__)."/templets/dedecms_user_main.htm");
  $dsql->Close();
}

//获得列表
//---------------------------------
function GetUserList($dsql,$pageno,$pagesize,$orderby='aid'){
	global $cfg_phpurl;
	$start = ($pageno-1) * $pagesize;
	$printhead ="<table width='99%' border='0' cellpadding='1' cellspacing='1' bgcolor='#333333' style='margin-bottom:3px'>
    <tr align='center' bgcolor='#E5F9FF' height='24'> 
      <td width='8%' height='23'><a href='#' onclick=\"ReloadPage('aid')\"><u>ID</u></a></td>
      <td width='30%'>网址</td>
      <td width='6%'><a href='#' onclick=\"ReloadPage('version')\"><u>版本</u></a></td>
      <td width='6%'><a href='#' onclick=\"ReloadPage('rank')\"><u>等级</u></a></td>
      <td width='6%'><a href='#' onclick=\"ReloadPage('isok')\"><u>检验</u></a></td>
      <td width='6%'><a href='#' onclick=\"ReloadPage('ismember')\"><u>会员</u></a></td>
      <td width='16%'><a href='#' onclick=\"ReloadPage('logintime')\"><u>收录时间</u></a></td>
      <td>管理</td>
    </tr>\r\n";
    echo $printhead;
    $dsql->SetQuery("Select * From dedecms_users order by $orderby desc limit $start,$pagesize ");
	  $dsql->Execute();
    while($row = $dsql->GetArray()){
    $line = "
      <tr align='center' bgcolor='#FFFFFF' onMouseMove=\"javascript:this.bgColor='#FCFEDA';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\"> 
      <td height='24'>{$row['aid']}</td>
      <td><input name='url' type='text' id='url{$row['aid']}' value='{$row['url']}' class='ininput'></td>
      <td><input name='version' type='text' id='version{$row['aid']}' value='{$row['version']}' class='ininput'></td>
      <td><input name='isok' type='text' id='isok{$row['aid']}' value='{$row['isok']}' class='ininput'></td>
      <td><input name='ismember' type='text' id='ismember{$row['aid']}' value='{$row['ismember']}' class='ininput'></td>
      <td><input name='rank' type='text' id='rank{$row['aid']}' value='{$row['rank']}' class='ininput'></td>
      <td>".strftime("%y-%m-%d %H:%M:%S",$row['logintime'])."</td>
      <td>
      <a href='/newinfo.php?feedback=".urlencode($row['url'])."' target='_blank'>浏览</a> | 
      <a href='#' onclick='UpdateNote({$row['aid']})'>更新</a> | 
      <a href='#' onclick='DelNote({$row['aid']})'>删除</a>
      </td>
    </tr>";
    echo $line;
   }
	 echo "</table>\r\n";
}


ClearAllLink();
?>

