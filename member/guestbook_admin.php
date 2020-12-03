<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

$dsql = new DedeSql(false);

if(empty($pagesize)) $pagesize = 5;
if(empty($pageno)) $pageno = 1;
if(empty($dopost)) $dopost = '';
if(empty($orderby)) $orderby = 'aid';

//重载列表
if($dopost=='getlist'){
	PrintAjaxHead();
	GetList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}
//删除留言
if($dopost=='del')
{
	if(!empty($aid)){
	   $aid = ereg_replace("[^0-9]","",$aid);
	   $dsql->ExecuteNoneQuery("Delete From #@__member_guestbook where aid='$aid' And mid='".$cfg_ml->M_ID."'; ");
  }else if(!empty($ids)){
  	$ids = explode(',',$ids);
  	$idsql = "";
  	foreach($ids as $aid){
  		$aid = ereg_replace("[^0-9]","",$aid);
  		if(empty($aid)) continue;
  		if($idsql=="") $idsql .= " aid='$aid' ";
  		else $idsql .= " Or aid='$aid' ";
  	}
  	if($idsql!=""){
  		$dsql->ExecuteNoneQuery("Delete From #@__member_guestbook where ($idsql) And mid='".$cfg_ml->M_ID."'; ");
  	}
  }
	PrintAjaxHead();
	GetList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}

//第一次进入这个页面
if($dopost==''){
	$row = $dsql->GetOne("Select count(*) as dd From #@__member_guestbook where mid='".$cfg_ml->M_ID."'; ");
	$totalRow = $row['dd'];
	include(dirname(__FILE__)."/templets/guestbook_admin.htm");
  $dsql->Close();
}

//获得特定的关键字列表
//---------------------------------
function GetList($dsql,$pageno,$pagesize,$orderby='aid'){
	global $cfg_phpurl,$cfg_ml;
	$start = ($pageno-1) * $pagesize;
  $dsql->SetQuery("Select * From #@__member_guestbook where mid='".$cfg_ml->M_ID."' order by $orderby desc limit $start,$pagesize ");
	$dsql->Execute();
  while($row = $dsql->GetArray()){
    $row['msg'] = ereg_replace("[ \t\r]"," ",$row['msg']);
    $row['msg'] = str_replace("  ","　",$row['msg']);
    $row['msg'] = str_replace("\n","<br>\n",$row['msg']);
    $line = "";
    $line .= "<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"3\" cellspacing=\"1\" bgcolor=\"#D9EDC0\" style=\"margin-bottom:6px\">";
    $line .= "\r\n<tr bgcolor=\"#E2EBC0\" height=\"24\"> ";
    $line .= "\r\n<td height=\"24\" colspan=\"2\" background=\"img/gbookbg.gif\">";
    $line .= "\r\n<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr> ";
    $line .= "\r\n<td width=\"85%\"> <strong>留言标题：".$row['title']."</strong></td>";
    $line .= "\r\n<td width=\"15%\" align=\"center\">";
    $line .= "\r\n<input name=\"ids\" type=\"checkbox\" id=\"ids\" value=\"".$row['aid']."\">";
    $line .= "[<a href='#' onclick='DelNote(".$row['aid'].")'>删除</a>]";
    $line .= "\r\n</td></tr></table> ";
    $line .= "\r\n</td></tr>";
    $line .= "\r\n<tr height=\"24\"> ";
    $line .= "\r\n<td width=\"31%\" bgcolor=\"#F7FEE0\">";
    $line .= "\r\n&nbsp;用户称呼：".$row['uname'];
    $line .= "\r\n</td>";
    $line .= "\r\n<td width=\"69%\" height=\"24\" bgcolor=\"FDFEF5\">";
    $line .= "时间：".strftime("%y-%m-%d %H:%M",$row['dtime'])."&nbsp;IP地址：".$row['ip']."&nbsp;";
    if(!empty($row['gid'])){
    	$line .= " <a href='index.php?uid=".$row['gid']."&action=memberinfo' target='_blank'>资料</a>
    	           <a href='index.php?uid=".$row['gid']."' target='_blank'>空间</a>
    	           <a href='index.php?uid=".$row['gid']."&action=feedback' target='_blank'>回复</a>
    	         ";
    }
    $line .= "\r\n</td></tr>";
    $line .= "\r\n<tr height=\"60\" bgcolor=\"FDFEF5\">";
    $line .= "\r\n<td valign=\"top\">";
    $line .= "\r\n<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
    $line .= "\r\n<tr><td height=\"24\">&nbsp;Email：".$row['email']."</td></tr>";
    $line .= "\r\n<tr><td height=\"24\">&nbsp;联系电话：".$row['tel']."</td></tr>";
    $line .= "\r\n<tr><td height=\"24\">&nbsp;其它：".$row['qq']."</td></tr>";
    $line .= "\r\n</table>";
    $line .= "\r\n</td>";
    $line .= "\r\n<td valign=\"top\">";
    $line .= $row['msg'];
    $line .= "\r\n</td></tr>";
    $line .= "\r\n</table>";
    echo $line;
   }
}

function PrintAjaxHead(){
	header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
	header("Content-Type: text/html; charset=gb2312");
}

?>