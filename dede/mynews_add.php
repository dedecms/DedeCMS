<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('plus_站内新闻发布');
if(empty($dopost)) $dopost = "";
if($dopost=="save")
{
	 $dsql = new DedeSql(false);
	 $dtime = GetMkTime($sdate);
	 $query = "
	 Insert Into #@__mynews(title,writer,senddate,body)
	 Values('$title','$writer','$dtime','$body')
	 ";
	 $dsql->SetQuery($query);
	 $dsql->ExecuteNoneQuery();
	 $dsql->Close();
	 ShowMsg("成功发布一条站内新闻！","mynews_main.php");
	 exit();
}

require_once(dirname(__FILE__)."/templets/mynews_add.htm");

ClearAllLink();
?>