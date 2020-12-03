<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Keyword');
empty($_COOKIE['ENV_GOBACK_URL']) ? $ENV_GOBACK_URL = "article_keywords_main.php" : $ENV_GOBACK_URL=$_COOKIE['ENV_GOBACK_URL'];
if(!isset($aids)){
	ShowMsg("你没有选择要更改的东东！",$ENV_GOBACK_URL);
	exit();
}
$dsql = new DedeSql(false);
foreach($aids as $aid)
{
	$rpurl = ${'rpurl_'.$aid};
	$rpurlold = ${'rpurlold_'.$aid};
	$keyword = ${'keyword_'.$aid};
	//删除项目
	if(!empty(${'isdel_'.$aid}))
	{
		 $query = "update #@__archives set keywords = Replace(keywords,' $keyword ',' ') where channel=1";
     $dsql->SetQuery($query);
	   $dsql->ExecuteNoneQuery();
	   $dsql->SetQuery("Delete From #@__keywords where aid='$aid'");
     $dsql->ExecuteNoneQuery();
     continue;
	}
	//禁用项目
	$staold = ${'staold_'.$aid};
	if(!empty(${'isnouse_'.$aid})) $sta = 0;
	else $sta = 1;
	if($staold!=$sta)
	{
		$query1 = "update #@__keywords set sta='$sta',rpurl='$rpurl' where aid='$aid' ";
	  $dsql->SetQuery($query1);
	  $dsql->ExecuteNoneQuery();
	  if($sta==0)
	  {
	    $query2 = "update #@__archives set keywords = Replace(keywords,' $keyword ',' ') where channel=1";
      $dsql->SetQuery($query2);
	    $dsql->ExecuteNoneQuery();
	  }
	  continue;
	}
	//更新链接网址
	if($rpurl!=$rpurlold)
	{
		$query1 = "update #@__keywords set rpurl='$rpurl' where aid='$aid' ";
	  $dsql->SetQuery($query1);
	  $dsql->ExecuteNoneQuery();
	}
}
$dsql->Close();
ShowMsg("完成指定的更改！",$ENV_GOBACK_URL);
exit();
?>