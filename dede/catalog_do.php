<?php
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost))
{
	ShowMsg("对不起，请指定栏目参数！","catalog_main.php");
	exit();
}
$cid = empty($cid) ? 0 : intval($cid);
$channelid = empty($channelid) ? 0 : intval($channelid);

/*--------------------------
//增加文档
function addArchives();
---------------------------*/
if($dopost=="addArchives")
{
	//默认文章调用发布表单
	if(empty($cid) && empty($channelid))
	{
		header("location:article_add.php");
		exit();
	}
	if(!empty($channelid))
	{
		//根据模型调用发布表单
		$row = $dsql->GetOne("Select addcon from #@__channeltype where id='$channelid'");
	}
	else
	{
		//根据栏目调用发布表单
		$row = $dsql->GetOne("Select ch.addcon from `#@__arctype` tp left join `#@__channeltype` ch on ch.id=tp.channeltype where tp.id='$cid' ");
	}
	$gurl = $row["addcon"];
	if($gurl=="")
	{
		ShowMsg("对不起，你指的栏目可能有误！","catalog_main.php");
		exit();
	}

	//跳转并传递参数
	header("location:{$gurl}?channelid={$channelid}&cid={$cid}");
	exit();
}

/*--------------------------
//管理文档
function listArchives();
---------------------------*/
else if($dopost=="listArchives")
{
	if(!empty($gurl))
	{
		if(empty($arcrank))
		{
			$arcrank = '';
		}
		$gurl = str_replace('..','',$gurl);
		header("location:{$gurl}?arcrank={$arcrank}&cid={$cid}");
		exit();
	}
	if($cid>0)
	{
		$row = $dsql->GetOne("Select #@__arctype.typename,#@__channeltype.typename as channelname,#@__channeltype.id,#@__channeltype.mancon from #@__arctype left join #@__channeltype on #@__channeltype.id=#@__arctype.channeltype where #@__arctype.id='$cid'");
		$gurl = $row["mancon"];
		$channelid = $row["id"];
		$typename = $row["typename"];
		$channelname = $row["channelname"];
		if($gurl=="")
		{
			ShowMsg("对不起，你指的栏目可能有误！","catalog_main.php");
			exit();
		}
	}
	else if($channelid>0)
	{

		$row = $dsql->GetOne("Select typename,id,mancon from #@__channeltype where id='$channelid'");
		$gurl = $row["mancon"];
		$channelid = $row["id"];
		$typename = "";
		$channelname = $row["typename"];
	}
	
	if(empty($gurl))
	{
		$gurl = 'content_list.php';
	}
	header("location:{$gurl}?channelid={$channelid}&cid={$cid}");
	exit();
}

/*--------------------------
//浏览通用模板目录
function viewTempletDir();
---------------------------*/
else if($dopost=="viewTemplet")
{
	header("location:tpl.php?path=/".$cfg_df_style);
	exit();
}

/*--------------------------
//留言簿管理
function GoGuestBook();
---------------------------*/
else if($dopost=="guestbook")
{
	header("location:{$cfg_phpurl}/guestbook.php?gotopagerank=admin");
	exit();
}

/*------------------------
浏览单个页面的栏目
function ViewSgPage()
------------------------*/
else if($dopost=="viewSgPage")
{
	require_once(DEDEINC."/arc.listview.class.php");
	$lv = new ListView($cid);
	$pageurl = $lv->MakeHtml();
	ShowMsg("更新缓冲，请稍后...",$pageurl);
	exit();
}

/*------------------------
更改栏目排列顺序
function upRank()
------------------------*/
else if($dopost=="upRank")
{
	//检查权限许可
	CheckPurview('t_Edit,t_AccEdit');

	//检查栏目操作许可
	CheckCatalog($cid,"你无权更改本栏目！");
	$row = $dsql->GetOne("Select reid,sortrank From #@__arctype where id='$cid'");
	$reid = $row['reid'];
	$sortrank = $row['sortrank'];
	$row = $dsql->GetOne("Select sortrank From #@__arctype where sortrank<=$sortrank And reid=$reid order by sortrank desc ");
	if(is_array($row))
	{
		$sortrank = $row['sortrank']-1;
		$dsql->ExecuteNoneQuery("update #@__arctype set sortrank='$sortrank' where id='$cid'");
	}
	ShowMsg("操作成功，返回目录...","catalog_main.php");
	exit();
}
else if($dopost=="upRankAll")
{
	//检查权限许可
	CheckPurview('t_Edit');
	$row = $dsql->GetOne("Select id From #@__arctype order by id desc");
	if(is_array($row))
	{
		$maxID = $row['id'];
		for($i=1;$i<=$maxID;$i++)
		{
			if(isset(${'sortrank'.$i}))
			{
				$dsql->ExecuteNoneQuery("Update #@__arctype set sortrank='".(${'sortrank'.$i})."' where id='{$i}';");
			}
		}
	}
	ShowMsg("操作成功，正在返回...","catalog_main.php");
	exit();
}

/*--------------------------
//更新栏目缓存
function UpCatlogCache();
---------------------------*/
else if($dopost=="upcatcache")
{
	UpDateCatCache();
	$sql = " TRUNCATE TABLE `#@__arctiny`";
	$dsql->executenonequery($sql);
	
	//导入普通模型微数据
	$sql = "insert into `#@__arctiny`(id, typeid, typeid2, arcrank, channel, senddate, sortrank, mid)  
	        Select id, typeid, typeid2, arcrank, channel, senddate, sortrank, mid from `#@__archives` ";
	$dsql->executenonequery($sql);
	
	//导入单表模型微数据
	$dsql->SetQuery("Select id,addtable From `#@__channeltype` where id < -1 ");
	$dsql->Execute();
	$doarray = array();
	while($row = $dsql->GetArray())
	{
		$tb = str_replace('#@__', $cfg_dbprefix, $row['addtable']);
		if(empty($tb) || isset($doarray[$tb]) )
		{
			continue;
		}
		else
		{
		
			$sql = "insert into `#@__arctiny`(id, typeid, typeid2, arcrank, channel, senddate, sortrank, mid)  
			        Select aid, typeid, 0, arcrank, channel, senddate, 0, mid from `$tb` ";
			$rs = $dsql->executenonequery($sql); 
			$doarray[$tb]  = 1;
		}
	}
	
	ShowMsg("操作成功，正在返回...","catalog_main.php");
	exit();
}

/*---------------------
获取JS文件
function GetJs
----------------------*/
else if($dopost=="GetJs")
{
	header("location:makehtml_js.php");
	exit();
}

/*-----------
获得子类的内容
function GetSunListsMenu();
-----------*/
else if($dopost=="GetSunListsMenu")
{
	$userChannel = $cuserLogin->getUserChannel();
	require_once(DEDEINC."/typeunit.class.menu.php");
	AjaxHead();
	PutCookie('lastCidMenu',$cid,3600*24,"/");
	$tu = new TypeUnit($userChannel);
	$tu->LogicListAllSunType($cid,"　");
}

/*-----------
获得子类的内容
function GetSunLists();
-----------*/
else if($dopost=="GetSunLists")
{
	require_once(DEDEINC."/typeunit.class.admin.php");
	AjaxHead();
	PutCookie('lastCid',$cid,3600*24,"/");
	$tu = new TypeUnit();
	$tu->dsql = $dsql;
	echo "    <table width='100%' border='0' cellspacing='0' cellpadding='0'>\r\n";
	$tu->LogicListAllSunType($cid,"　");
	echo "    </table>\r\n";
	$tu->Close();
}

?>