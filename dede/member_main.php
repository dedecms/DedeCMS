<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_List');
require_once(dirname(__FILE__)."/../include/pub_datalist.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

if(!isset($sex)) $sex = "0";
if($sex=="0") $sexform = "<option value='0'>性别</option>\r\n";
else $sexform = "<option value='$sex'>$sex</option>\r\n";

if(!isset($keyword)) $keyword = "";
else $keyword = trim($keyword);

if(empty($sortkey)) $sortkey = "ID";
else $sortkey = eregi_replace("[^a-z]","",$sortkey);
if($sortkey=="ID") $sortform = "<option value='ID'>ID/注册时间</option>\r\n";
else if($sortkey=="spaceshow") $sortform = "<option value='spaceshow'>空间访问量</option>\r\n";
else if($sortkey=="pageshow") $sortform = "<option value='pageshow'>文档总点击量</option>\r\n";
else $sortform = "<option value='logintime'>登录时间</option>\r\n";


$dsql = new DedeSql(false);
if($sex=="0") $whereSql = " where sex like '%%' ";
else $whereSql = " where sex like '$sex' ";

if($keyword!=""){
	$whereSql .= "  And (userid like '%$keyword%' Or uname like '%$keyword%') ";
}

$attform = "";
if(!empty($att)){
	if($att=="ad"){
		$attform = "<option value='ad'>被推荐会员</option>\r\n";
		$whereSql .= "  And matt=1 ";
  }else if($att=="uptype"){
  	$attform = "<option value='uptype'>待升级会员</option>\r\n";
  	$whereSql .= "  And uptype>0 ";
	}else if($att=="upmoney"){
		$attform = "<option value='upmoney'>待充充值会员</option>\r\n";
		$whereSql .= "  And upmoney>0 ";
	}else if($att=="mb"){
		$attform = "<option value='mb'>非普通会员</option>\r\n";
		$whereSql .= "  And membertype>10 ";
	}
}


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
	   if($mt=="ut") return " <font color='red'>待升级：".$MemberTypes[$rank]."</font>";
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
	if($m>0) return " <font color='red'>申请：$m 金币</font> ";
	else return "";
}

function GetMAtt($m){
	if($m<1) return "";
	else return "<img src='img/adminuserico.gif' width='16' height='15'><font color='red'>(荐)</font>";
}

$sql  = "select ID,userid,pwd,uname,email,sex,money,upmoney,c1,c2,c3,matt,
logintime,loginip,membertype,uptype,province,city,spaceshow,pageshow
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