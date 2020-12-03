<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
SetPageRank(10);

if(!isset($sex)) $sex = "0";
if($sex=="0") $sexform = "<option value='0'>性别</option>\r\n";
else $sexform = "<option value='$sex'>$sex</option>\r\n";

if(!isset($keyword)) $keyword = "";
else $keyword = trim($keyword);

if(empty($sortkey)) $sortkey = "ID";
else $sortkey = eregi_replace("[^a-z]","",$sortkey);
if($sortkey=="ID") $sortform = "<option value='ID'>ID/注册时间</option>\r\n";
else $sortform = "<option value='logintime'>登录时间</option>\r\n";


$whereSql = "";
if($keyword!=""){
	$whereSql .= " where (userid like '%$keyword%' Or uname like '%$keyword%') ";
}
if($sex!="0"){
	if($whereSql!="") $whereSql .= " And sex='$sex' ";
	else $whereSql = " where sex='$sex' ";
}

$dsql = new DedeSql(false);
$MemberTypes = "";
$dsql->SetQuery("Select rank,membername From #@__arcrank where rank>0");
$dsql->Execute();
while($row = $dsql->GetObject()){
	$MemberTypes[$row->rank] = $row->membername;
}
$dsql->SetQuery("Select eid,name From #@__area");
$dsql->Execute();
while($row = $dsql->GetObject()){
	$Areas[$row->eid] = $row->name;
}

function GetMemberName($rank,$mt)
{
	global $MemberTypes;
	if(isset($MemberTypes[$rank])){
	   if($mt=="ut") return " 待升级：".$MemberTypes[$rank];
	   else return $MemberTypes[$rank];
  }else{
		if($mt=="ut") return "";
		else return $mt;
	}
}

function GetAreaName($e,$df)
{
	global $Areas;
	if(isset($Areas[$e])) return $Areas[$e];
	else return $df;
}

function GetUpMoney($m)
{
	if($m>0) return "申请：$m 金币";
	else return "";
}

$sql  = "select ID,userid,pwd,uname,email,sex,money,upmoney,
logintime,loginip,membertype,uptype,province,city
From #@__member $whereSql order by $sortkey desc
";

$dlist = new DataList();
$dlist->Init();
$dlist->SetParameter("sex",$sex);
$dlist->SetParameter("keyword",$keyword);
$dlist->SetSource($sql);
$dlist->SetTemplet(dirname(__FILE__)."/templets/member_main.htm");
$dlist->display();
$dlist->Close();
$dsql->Close();
?>