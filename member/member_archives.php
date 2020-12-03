<?
require_once(dirname(__FILE__)."/config_space.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/../include/inc_channel_unit_functions.php");

if(empty($keyword)) $keyword = "";
else{
	$keyword = cn_substr(trim(ereg_replace("[\|\"\r\n\t%\*\.\?\(\)\$ ;,'%-]","",stripslashes($keyword))),30);
	$keyword = addslashes($keyword);
}
if(empty($channelid)) $channelid = 0;
if(empty($mtype)) $mtype = 0;

$uid = cn_substr(trim(ereg_replace("[\|\"\r\n\t%\*\.\?\(\)\$ ;,'%-]","",stripslashes($uid))),32);
$uid = addslashes($uid);

if(empty($channelid)){
	$listName = '　§所有文档';
}
else if($channelid==1){
	$listName = "　§<a href=member_archives.php?uid=$uid' style='color:#666600'>所有文档</a>&gt;&gt;我的文章";
}
else if($channelid==2){
	$listName = "　§<a href='member_archives.php?uid=$uid' style='color:#666600'>所有文档</a>&gt;&gt;我的图集";
}

//用户信息
$dsql = new DedeSql(false);
$spaceInfos = $dsql->GetOne("Select ID,uname,spacename,spaceimage,sex,c1,c2,spaceshow,logintime From #@__member where userid='$uid'; ");
if(!is_array($spaceInfos)){
	ShowMsg("参数错误或者用户已经被删除！","-1");
	exit();
}
foreach( $spaceInfos as $k=>$v){if(ereg("[^0-9]",$k)) $$k = $v; }
$userNumID = $ID;
if($spaceimage==''){
	if($sex=='女') $spaceimage = 'img/dfgril.gif';
	else $spaceimage = 'img/dfboy.gif';
}

if(!empty($mtype)){
	$mtype = ereg_replace("[^0-9]","",$mtype);
	$rows = $dsql->GetOne("Select typename From #@__member_arctype where aid='$mtype'; ");
	$listName .= "&gt;&gt;".$rows['typename'];
}

//获取文档列表
$whereSql = " arc.memberID='$userNumID' ";
if(!empty($channelid)) $whereSql .= " And arc.channel='$channelid' ";
if(!empty($mtype)) $whereSql .= " And (arc.mtype='$mtype') ";
if($keyword!=""){
	$whereSql .= " And (arc.title like '%$keyword%') ";
}

$query = "
	Select arc.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,
	tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl
	From #@__archives arc left join #@__arctype tp on arc.typeid=tp.ID
	where $whereSql order by arc.senddate desc
";

$dlist = new DataList();
$dlist->pageSize = 10;
$dlist->SetParameter("uid",$uid);
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("mtype",$mtype);
$dlist->SetParameter("channelid",$channelid);
$dlist->SetSource($query);

include(dirname(__FILE__)."/templets/member_archives.htm");

?>