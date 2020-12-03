<?
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

if(empty($pagesize)) $pagesize = 8;
if(empty($pageno)) $pageno = 1;
if(empty($dopost)) $dopost = '';

$dsql = new DedeSql(false);

//用户查询条件
if(empty($keyword)) $keyword = '';
else{
	$keyword = cn_substr(trim(ereg_replace("[\|\"\r\n\t%\*\.\?\(\)\$ ;,'%-]","",stripslashes($keyword))),30);
	$keyword = addslashes($keyword);
}

if(empty($sex)) $sex = '';
else{ if($sex!='男' && $sex!='女') $sex = ''; }

if(!empty($age1) && !empty($age2)){
	$age1 = GetAlabNum($age1); 
	$age2 = GetAlabNum($age2);
}else{
	$age1 = 0; $age2=0;
}

if(empty($province)) $province = 0;
else $province = GetAlabNum($province);

if(empty($city)) $city = 0;
else $city = GetAlabNum($city);

if(empty($pic)) $pic = 0;

$addQuery = " where 1 ";
if(!empty($sex)) $addQuery .= " And sex='$sex' ";
if(!empty($age1) && $age2>$age1){
	$addQuery .= " And ((YEAR(NOW()) - YEAR(birthday)>=$age1) And (YEAR(NOW()) - YEAR(birthday)<=$age2)) ";
}
if(!empty($province)) $addQuery .= " And province='$province' ";

if(!empty($city)) $addQuery .= " And city='$city' ";

if($pic==1) $addQuery .= " And spaceimage<>'' ";

if(strlen($keyword)>=2) $addQuery .= " And CONCAT(uname,myinfo,spacename) like '%$keyword%' ";

//统计记录总数
$row = $dsql->GetOne("Select count(*) as dd From #@__member $addQuery ");
$totalRow = $row['dd'];

//重载列表
if($dopost=='getlist'){
	$dsql = new DedeSql(false);
	PrintAjaxHead();
	GetList($dsql,$pageno,$pagesize);
	$dsql->Close();
	exit();
}

//第一次进入这个页面
if($dopost==''){
	include(dirname(__FILE__)."/templets/search_member.htm");
  exit();
}

//获得特定的关键字列表
//---------------------------------
function GetList($dsql,$pageno,$pagesize){
	global $cfg_phpurl,$addQuery;
	$start = ($pageno-1) * $pagesize;
  $query = "
    Select ID,userid,uname,sex,(YEAR(NOW()) - YEAR(birthday)) as age,province,city,
    spacename,spaceimage,myinfo,logintime 
    From #@__member $addQuery order by logintime desc limit $start,$pagesize
  ";
  $dsql->SetQuery($query);
	$dsql->Execute();
  while($row = $dsql->GetArray()){
      if($row['age']>100 || $row['age']<12) $age = ' 不详 ';
      else $age = $row['age'];
      $msg = str_replace("\n","<br />",html2text($row['myinfo']));
      $area = '';
      if($row['province']>0){ $area .= GetProvince($row['province'],$dsql); }
      if($row['city']>0){ $area .= " &gt; ".GetProvince($row['city'],$dsql); }
      if($area=='') $area = ' 不详 ';
      
      if($row['spaceimage']!='') $imagsrc = $row['spaceimage'];
      else $imagsrc = "img/dfpic.gif";
      
      $line = "";
      $line .= "<table width='100%' border='0' cellpadding=\"1\" cellspacing=\"1\" bgcolor=\"#FFFFFF\" class=\"mbb\">\r\n";
      $line .= "<tr> \r\n";
      $line .= "<td width='18%' rowspan=\"3\" align=\"center\" valign=\"top\">\r\n";
      $line .= "<table width=\"100\" height=\"75\" border='0' cellpadding='1' cellspacing='1' bgcolor=\"#E6EAE3\">\r\n";
      $line .= "<tr> \r\n";
      $line .= "<td bgcolor=\"#FFFFFF\"><a href='index.php?uid=".$row['userid']."' target='_blank'><img src=\"".$imagsrc."\" width=\"100\" border=\"0\" height=\"75\" alt=\"".$row['spacename']."\"></a></td>\r\n";
      $line .= "</tr>\r\n";
      $line .= "</table>\r\n";
      $line .= "</td>\r\n";
      $line .= "<td width=\"15%\" height=\"22\" align=\"center\" bgcolor=\"#E8F1C9\">用户昵称：</td>\r\n";
      $line .= "<td width=\"15%\" bgcolor=\"#F3FADE\"> ".$row['uname']." </td>\r\n";
      $line .= "<td width=\"28%\" bgcolor=\"#F3FADE\">最后登录：".strftime('%y-%m-%d %H:%M',$row['logintime'])."</td>\r\n";
      $line .= "<td width=\"24%\" align=\"center\" bgcolor=\"#F3FADE\">\r\n";
      $line .= " [<a href='index.php?uid=".$row['userid']."' target='_blank'>空间</a>] [<a href='index.php?uid=".$row['userid']."&action=memberinfo' target='_blank'>资料</a>] [<a href='index.php?uid=".$row['userid']."&action=feedback' target='_blank'>留言</a>] ";
      $line .= "</td>\r\n</tr>\r\n";
      $line .= "<tr> \r\n";
      $line .= "<td height=\"24\" colspan=\"4\" class=\"mbline\">&nbsp;性别：".$row['sex']." 年龄：".$age." 地区：".$area."</td>\r\n";
      $line .= "</tr>\r\n";
      $line .= "<tr> \r\n";
      $line .= "<td colspan=\"4\" valign=\"top\" height=\"31\">&nbsp;个人说明：".$msg."</td>\r\n";
      $line .= "</tr>\r\n";
      $line .= "</table>\r\n";
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