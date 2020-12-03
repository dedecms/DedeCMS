<?
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost)) $dopost = "";
/*------------------
function AddArchives()
发表投稿
-------------------*/
if($dopost=="addArc")
{
   session_start();
   CheckRank(0,0);
 	 if( empty($_SESSION["s_validate"]) ) $svali = "";
   else $svali = $_SESSION["s_validate"];
   if(strtolower($vdcode)!=$svali && $svali!=""){
  	 ShowMsg("验证码错误！","-1");
  	 exit();
   }
   $pubdate = time();
   $sortrank = $pubdate;
   $senddate = $pubdate;
   if($typeid==""||$typeid==0){
	   ShowMsg('档案主栏目必须选择！','-1');
	   exit();
   }
   $title = cn_substr($title,60);
   $writer =  cn_substr($writer,30);
   $source = cn_substr($source,50);
   $description = cn_substr($description,250);
   $keywords = ereg_replace(","," ",$keywords);
   $keywords = cn_substr(trim($keywords),50)." ";
   $arcrank = -1;
   $dsql = new DedeSql(false);
   $row = $dsql->GetOne("Select issend,channeltype,ispart From #@__arctype where ID='$typeid'");
   if(!is_array($row)){
   	 $dsql->Close();
   	 ShowMsg('你选择的栏目不允许投稿！','-1');
	   exit();
   }
   else if($row['issend']!=1 || $row['channeltype']!=1){
   	 $dsql->Close();
   	 ShowMsg('你选择的栏目不允许投稿！','-1');
	   exit();
   }
   //----------------------------------
   $inQuery = "INSERT INTO #@__archives(
   typeid,typeid2,sortrank,iscommend,ismake,channel,
   arcrank,click,title,color,writer,source,litpic,
   pubdate,senddate,adminID,memberID,description,keywords) 
   VALUES ('$typeid','0','$sortrank','0','0','1',
   '$arcrank','0','$title','','$writer','$source','',
   '$pubdate','$senddate','0','".$cfg_ml->M_ID."','$description','$keywords');";
   $dsql->SetQuery($inQuery);
   if(!$dsql->ExecuteNoneQuery()){
	   $dsql->Close();
	   ShowMsg("把数据保存到数据库时出错，请联系管理员！","-1");
	   exit();
   }
   $arcID = $dsql->GetLastID();
   $dsql->SetQuery("INSERT INTO #@__addonarticle(aid,typeid,body) Values('$arcID','$typeid','$body')");
   if(!$dsql->ExecuteNoneQuery()){
	    $dsql->SetQuery("Delete From #@__archives where ID='$arcID'");
	    $dsql->ExecuteNoneQuery();
	    $dsql->Close();
	    ShowMsg("把数据保存到数据库附时出错，请联系管理员！","-1");
	    exit();
   }
   $dsql->Close();
   ShowMsg("成功发布一篇文章！","artlist.php");
	 exit();
}
/*-----------------
function delStow()
删除收藏
------------------*/
else if($dopost=="delStow")
{
	CheckRank(0,0);
	if(!empty($_COOKIE['ENV_GOBACK_URL'])) $ENV_GOBACK_URL = $_COOKIE['ENV_GOBACK_URL'];
	else $ENV_GOBACK_URL = "artlist.php";
	$aid = ereg_replace("[^0-9]","",$aid);
	$dsql = new DedeSql(false);
	$dsql->SetQuery("Delete From #@__memberstow where aid='$aid' And uid='".$cfg_ml->M_ID."'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功删除一条收藏记录！",$ENV_GOBACK_URL);
	exit();
}
/*--------------------
function delArchives()
删除文章
--------------------*/
else if($dopost=="delArc")
{
	CheckRank(0,0);
	if(!empty($_COOKIE['ENV_GOBACK_URL'])) $ENV_GOBACK_URL = $_COOKIE['ENV_GOBACK_URL'];
	else $ENV_GOBACK_URL = "artlist.php";
	$aid = ereg_replace("[^0-9]","",$aid);
	$dsql = new DedeSql(false);
	$row = $dsql->GetOne("Select arcrank From #@__archives where memberID='".$cfg_ml->M_ID."' And ID='$aid'");
	if(!is_array($row))
	{
		$dsql->Close();
	  ShowMsg("你没有权限删除这篇文章！","-1");
	  exit();
	}
	else if($row['arcrank']!=-1)
	{
		$dsql->Close();
	  ShowMsg("这篇文章已被审核，你没权限删除！","-1");
	  exit();
	}
	$dsql->SetQuery("Delete From #@__archives where ID='$aid'");
	$dsql->ExecuteNoneQuery();
	$dsql->SetQuery("Delete From #@__addonarticle where aid='$aid'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功删除一篇文章！",$ENV_GOBACK_URL);
	exit();
}
/*-----------------
function viewArchives()
查看文章
------------------*/
else if($dopost=="viewArchives")
{
	CheckRank(0,0);
	$aid = ereg_replace("[^0-9]","",$aid);
	header("location:".$cfg_plus_dir."/view.php?aid=".$aid);
}
/*-----------------
function editArchives()
更改文章
------------------*/
else if($dopost=="editArc")
{
   session_start();
   CheckRank(0,0);
 	 if( empty($_SESSION["s_validate"]) ) $svali = "";
   else $svali = $_SESSION["s_validate"];
   if(strtolower($vdcode)!=$svali && $svali!=""){
  	 ShowMsg("验证码错误！","-1");
  	 exit();
   }
   if(!empty($_COOKIE['ENV_GOBACK_URL'])) $ENV_GOBACK_URL = $_COOKIE['ENV_GOBACK_URL'];
	 else $ENV_GOBACK_URL = "artlist.php";
   $ID = ereg_replace("[^0-9]","",$ID);
   if($typeid==""||$typeid==0)
   {
	   ShowMsg('档案主栏目必须选择！','-1');
	   exit();
   }
   
   $title = cn_substr($title,60);
   $writer =  cn_substr($writer,30);
   $source = cn_substr($source,50);
   $description = cn_substr($description,250);
   $keywords = ereg_replace(","," ",$keywords);
   $keywords = cn_substr(trim($keywords),50)." ";
   $dsql = new DedeSql(false);
   $row = $dsql->GetOne("Select issend,channeltype,ispart From #@__arctype where ID='$typeid'");
   if(!is_array($row)){
   	 $dsql->Close();
   	 ShowMsg('你选择的栏目不允许投稿！','-1');
	   exit();
   }
   else if($row['issend']!=1 || $row['channeltype']!=1){
   	 $dsql->Close();
   	 ShowMsg('你选择的栏目不允许投稿！','-1');
	   exit();
   }
	 $row = $dsql->GetOne("Select arcrank From #@__archives where memberID='".$cfg_ml->M_ID."' And ID='$ID'");
	 if(!is_array($row)){
		  $dsql->Close();
	    ShowMsg("你没权限更改这篇文章！","-1");
	    exit();
	  }
	  else if($row['arcrank']!=-1){
		  $dsql->Close();
	    ShowMsg("这篇文章已被审核，你没权限更改！","-1");
	    exit();
    }
   //----------------------------------
   $inQuery = "update #@__archives
   set typeid='$typeid',
   title='$title',
   writer='$writer',
   source='$source',
   description = '$description',
   keywords = '$keywords'
   where ID='$ID' And memberID='".$cfg_ml->M_ID."'
   ";
   $inQuery2 = "update #@__addonarticle set body='$body' where ID='$ID' And memberID='".$cfg_ml->M_ID."'";
   $dsql->SetQuery($inQuery);
   $dsql->ExecuteNoneQuery();
   $dsql->SetQuery($inQuery2);
   $dsql->ExecuteNoneQuery();
   $dsql->Close();
   ShowMsg("成功更改一篇文章！",$ENV_GOBACK_URL);
	 exit();
}
?>