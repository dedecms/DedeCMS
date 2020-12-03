<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('a_New,a_AccNew');
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");
require_once(dirname(__FILE__)."/inc/inc_archives_all.php");

if(!isset($iscommend)) $iscommend = 0;
if(!isset($isjump)) $isjump = 0;
if(!isset($isbold)) $isbold = 0;
if(!isset($autokey)) $autokey = 0;
if(!isset($remote)) $remote = 0;
if(!isset($dellink)) $dellink = 0;

if($typeid==0){
	ShowMsg("请指定文档的栏目！","-1");
	exit();
}
if(empty($channelid)){
	ShowMsg("文档为非指定的类型，请检查你发布内容的表单是否合法！","-1");
	exit();
}
if(!CheckChannel($typeid,$channelid) || !CheckChannel($typeid2,$channelid)){
	ShowMsg("你所选择的栏目与当前模型不相符，请选择白色的选项！","-1");
	exit();
}
if(!TestPurview('a_New')) {
	CheckCatalog($typeid,"对不起，你没有操作栏目 {$typeid} 的权限！");
	if($typeid2!=0) CheckCatalog($typeid2,"对不起，你没有操作栏目 {$typeid2} 的权限！");
}
//对保存的内容进行处理
//--------------------------------
$iscommend = $iscommend + $isbold;

$pubdate = GetMkTime($pubdate);
$senddate = mytime();
$sortrank = AddDay($senddate,$sortup);

if($ishtml==0) $ismake = -1;
else $ismake = 0;

$shorttitle = cn_substr($shorttitle,36);
$color =  cn_substr($color,10);
$writer =  cn_substr($writer,30);
$source = cn_substr($source,50);
$description = cn_substr($description,250);
$keywords = cn_substr($keywords,60);
if(!TestPurview('a_Check,a_AccCheck,a_MyCheck')){ $arcrank = -1; }

//处理上传的缩略图
if(empty($ddisremote)) $ddisremote = 0;
$litpic = GetDDImage('litpic',$picname,$ddisremote);

if($keywords!="") $keywords = trim(cn_substr($keywords,56))." ";
$adminID = $cuserLogin->getUserID();

//加入数据库的SQL语句
//----------------------------------
$inQuery = "INSERT INTO #@__archives(
typeid,typeid2,sortrank,iscommend,ismake,channel,
arcrank,click,money,title,shorttitle,color,writer,source,litpic,
pubdate,senddate,arcatt,adminID,memberID,description,keywords) 
VALUES ('$typeid','$typeid2','$sortrank','$iscommend','$ismake','$channelid',
'$arcrank','0','$money','$title','$shorttitle','$color','$writer','$source','$litpic',
'$pubdate','$senddate','$arcatt','$adminID','0','$description','$keywords');";

$dsql = new DedeSql();
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
if(empty($dede_addtablename)) $dede_addtablename = "";
if(empty($dede_addonfields)) $dede_addonfields = "";
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
    if($isUrlOpen && $remote==1){
	    ${$vs[0]} = GetCurContent(${$vs[0]});
    }
    //去除内容中的站外链接
    if($dellink==1){
	    ${$vs[0]} = str_replace($cfg_basehost,'#basehost#',${$vs[0]});
	    ${$vs[0]} = preg_replace("/(<a[ \t\r\n]{1,}href=[\"']{0,}http:\/\/[^\/]([^>]*)>)|(<\/a>)/isU","",${$vs[0]});
      ${$vs[0]} = str_replace('#basehost#',$cfg_basehost,${$vs[0]});
    }
    //自动摘要
    if($description=="" && $cfg_auot_description>0){
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

$dsql->Close();

//生成HTML
//---------------------------------

$artUrl = MakeArt($arcID,true);
if($artUrl=="") $artUrl = $cfg_plus_dir."/view.php?aid=$arcID";

//---------------------------------
//返回成功信息
//----------------------------------

$msg = "
　　请选择你的后续操作：
<a href='archives_add.php?cid=$typeid'><u>继续发布文档</u></a>
&nbsp;&nbsp;
<a href='archives_do.php?aid={$arcID}&dopost=editArchives'><u>更改文档</u></a>
&nbsp;&nbsp;
<a href='$artUrl' target='_blank'><u>预览文档</u></a>
&nbsp;&nbsp;
<a href='catalog_do.php?cid=$typeid&dopost=listArchives'><u>已发布文档管理</u></a>
&nbsp;&nbsp;
<a href='catalog_main.php'><u>网站栏目管理</u></a>
";

$wintitle = "成功发布文档！";
$wecome_info = "文章管理::发布文档";
$win = new OxWindow();
$win->AddTitle("成功发布文档：");
$win->AddMsgItem($msg);
$winform = $win->GetWindow("hand","&nbsp;",false);
$win->Display();
?>