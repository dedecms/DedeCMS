<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

$svali = GetCkVdValue();
if(strtolower($vdcode)!=$svali || $svali==""){
  ShowMsg("验证码错误！","-1");
  exit();
}
if($cfg_mb_sendall=='否'){
	ShowMsg("对不起，系统禁用了自定义模型投稿，因此无法使用此功能！","-1");
	exit();
}

require_once(dirname(__FILE__)."/inc/inc_archives_all.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($isjump)) $isjump = 0;
if(!isset($isbold)) $isbold = 0;
if(!isset($isrm)) $isrm = 0;
if(!isset($ddisfirst)) $ddisfirst = 0;
if(!isset($ddisremote)) $ddisremote = 0;
$typeid = ereg_replace("[^0-9]","",$typeid);
$channelid = ereg_replace("[^0-9]","",$channelid);

if($typeid==0){
	ShowMsg("请指定文档隶属的栏目！","-1");
	exit();
}

if(!CheckChannel($typeid,$channelid)){
	ShowMsg("你所选择的栏目与当前模型不相符，或不支持投稿，请选择白色的选项！","-1");
	exit();
}

$dsql = new DedeSql(false);

$cInfos = $dsql->GetOne("Select * From #@__channeltype  where ID='$channelid'; ");	
if($cInfos['issystem']!=0 || $cInfos['issend']!=1){
	$dsql->Close();
	ShowMsg("你指定的频道参数的错误！","-1");
	exit();
}

if($cInfos['sendrank'] > $cfg_ml->M_Type){
	$row = $dsql->GetOne("Select membername From #@__arcrank where rank='".$cInfos['sendrank']."' ");
	$dsql->Close();
	ShowMsg("对不起，需要[".$row['membername']."]才能在这个频道发布文档！","-1","0",5000);
	exit();
}

if($cInfos['arcsta']==0){
	$ismake = 0;
	$arcrank = 0;
}
else if($cInfos['arcsta']==1){
	$ismake = -1;
	$arcrank = 0;
}
else{
	$ismake = 0;
	$arcrank = -1;
}

//对保存的内容进行处理
//--------------------------------
$typeid2 = 0;
$pubdate = mytime();
$senddate = $pubdate;
$sortrank = $pubdate;
$shorttitle = '';
$color =  '';
$money = 0;
$arcatt = 0;
$pagestyle = 2;

$title = ClearHtml($title);
$writer =  cn_substr(trim(ClearHtml($writer)),30);
$source = '';
$description = cn_substr(trim(ClearHtml($description)),250);
if($keywords!=""){
	$keywords = ereg_replace("[,;]"," ",trim(ClearHtml($keywords)));
	$keywords = trim(cn_substr($keywords,60))." ";
}
$userip = GetIP();
//处理上传的缩略图
if(!empty($litpic)) $litpic = GetUpImage('litpic',true,true);
else $litpic = "";
$adminID = 0;
$memberID = $cfg_ml->M_ID;

//加入主档案表
//----------------------------------
$inQuery = "INSERT INTO #@__archives(
typeid,typeid2,sortrank,iscommend,ismake,channel,
arcrank,click,money,title,shorttitle,color,writer,source,litpic,
pubdate,senddate,arcatt,adminID,memberID,description,keywords,mtype,userip) 
VALUES ('$typeid','$typeid2','$sortrank','$iscommend','$ismake','$channelid',
'$arcrank','0','$money','$title','$shorttitle','$color','$writer','$source','$litpic',
'$pubdate','$senddate','$arcatt','$adminID','$memberID','$description','$keywords','0','$userip');";
$dsql->SetQuery($inQuery);
if(!$dsql->ExecuteNoneQuery()){
	$dsql->Close();
	ShowMsg("把数据保存到数据库archives表时出错，请检查！","-1");
	exit();
}
$arcID = $dsql->GetLastID();

//----------------------------------
//分析处理附加表数据
//----------------------------------
$dtp = new DedeTagParse();
$dtp->SetNameSpace("field","<",">");
$dtp->LoadSource($cInfos['fieldset']);
$dede_addonfields = "";
if(is_array($dtp->CTags)){
    foreach($dtp->CTags as $tid=>$ctag){
        if($dede_addonfields=="") $dede_addonfields = $ctag->GetName().",".$ctag->GetAtt('type');
        else $dede_addonfields .= ";".$ctag->GetName().",".$ctag->GetAtt('type');
    }
}
$dede_addtablename = $cInfos['addtable'];
//------------------------------------------------
$addonfields = explode(";",$dede_addonfields);
$inadd_f = "";
$inadd_v = "";
$autoDescription = false;
foreach($addonfields as $v)
{
	if($v=="") continue;
	$vs = explode(",",$v);
	//HTML文本特殊处理
	if($vs[1]=="htmltext"||$vs[1]=="textdata")
	{
		${$vs[0]} = stripslashes(${$vs[0]});
    //获得文章body里的外部资源
    ${$vs[0]} = eregi_replace("<(iframe|script)","",${$vs[0]});
    //自动摘要
    if($description==""){
    	$description = cn_substr(html2text(${$vs[0]}),$cfg_auot_description);
	    $description = trim(preg_replace("/#p#|#e#/","",$description));
	    $description = addslashes($description);
	    $autoDescription = true;
    }
    ${$vs[0]} = addslashes(${$vs[0]});
    ${$vs[0]} = GetFieldValue(${$vs[0]},$vs[1],$arcID);
	}else{
		${$vs[0]} = GetFieldValue(${$vs[0]},$vs[1],$arcID);
	}
	$inadd_f .= ",".$vs[0];
	$inadd_v .= ",'".${$vs[0]}."'";
}

if($autoDescription){
	$dsql->ExecuteNoneQuery("update #@__archives set description='$description' where ID='$arcID';");
}

if($dede_addtablename!="" && $addonfields!="")
{
   $dsql->SetQuery("INSERT INTO ".$dede_addtablename."(aid,typeid".$inadd_f.") Values('$arcID','$typeid'".$inadd_v.")");
   if(!$dsql->ExecuteNoneQuery()){
	   $dsql->SetQuery("Delete From #@__archives where ID='$arcID'");
	   $dsql->ExecuteNoneQuery();
	   $dsql->Close();
	   ShowMsg("把数据保存到数据库附加表 ".$dede_addtablename." 时出错，请检查原因！","-1");
	   exit();
  }
}

$dsql->ExecuteNoneQuery("Update #@__member set c3=c3+1 where ID='".$cfg_ml->M_ID."';");
$dsql->Close();

$artUrl = MakeArt($arcID);

//---------------------------------
//返回成功信息
//----------------------------------

$msg = "
请选择你的后续操作：
<a href='archives_add.php?channelid=$channelid&cid=$typeid'><u>继续发布信息</u></a>
&nbsp;&nbsp;
<a href='archives_edit.php?aid=".$arcID."'><u>更改信息</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览信息</u></a>
&nbsp;&nbsp;
<a href='content_list.php?channelid=$channelid'><u>已发布信息管理</u></a>
&nbsp;&nbsp;
<a href='index.php'><u>会员主页</u></a>
";

$wintitle = "成功发布一则信息！";
$wecome_info = "文档管理::发布文档";
$win = new OxWindow();
$win->AddTitle("成功发布一则信息：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>