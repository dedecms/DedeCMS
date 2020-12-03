<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('co_NewRule');
if(empty($action)) $action = "";
$aid = ereg_replace("[^0-9]","",$aid);
if(empty($aid)){
   ShowMsg("参数无效!","-1");
   exit();
}
//----------------------------
//事件触发处理
//----------------------------
if($action=="save")
{
	$notes = "
{dede:note 
  rulename=\\'$rulename\\'
  etype=\\'$etype\\'
  tablename=\\'$tablename\\'
  autofield=\\'$autofield\\'
  synfield=\\'$synfield\\'
  channelid=\\'$channelid\\'
/}
	";
	for($i=1;$i<=50;$i++)
	{
		if( !isset(${"fieldname".$i}) ) break;
		$fieldname = ${"fieldname".$i};
		$comment = ${"comment".$i};
		$intable = ${"intable".$i};
		$source = ${"source".$i};
		$makevalue = ${"makevalue".$i};
		$notes .= "{dede:field name=\\'$fieldname\\' comment=\\'$comment\\' intable=\\'$intable\\' source=\\'$source\\'}$makevalue{/dede:field}\r\n";
	}
	$query = "
	update #@__co_exrule set 
	channelid = '$channelid',
	rulename='$rulename',
	etype='$etype',
	dtime='".time()."',
	ruleset='$notes'
	where aid='$aid'
	";
	$dsql = new DedeSql(false);
	$dsql->ExecuteNoneQuery($query);
	$dsql->Close();
	ShowMsg("成功更改一个规则!","co_export_rule.php");
	exit();
}
else if($action=="delete")
{
   if(empty($job)) $job="";
   if($job=="") //确认提示
   {
  	 require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
  	 $wintitle = "删除数据规则模型";
	   $wecome_info = "<a href='co_export_rule.php'><u>数据规则模型</u></a>::删除规则";
	   $win = new OxWindow();
	   $win->Init("co_export_rule_edit.php","js/blank.js","POST");
	   $win->AddHidden("job","yes");
	   $win->AddHidden("action",$action);
	   $win->AddHidden("aid",$aid);
	   $win->AddTitle("你确实要删除[{$aid}]这个规则？");
	   $winform = $win->GetWindow("ok");
	   $win->Display();
   }
   else if($job=="yes") //操作
   {
   	 $dsql = new DedeSql(false);
	   $dsql->ExecuteNoneQuery("Delete From #@__co_exrule where aid='$aid'");
	   $dsql->Close();
	   ShowMsg("成功删除一个规则!","co_export_rule.php");
	   exit();
   }
   exit();
}
else if($action=="export")
{
   $dsql = new DedeSql(false);
   $row = $dsql->GetOne("Select * From #@__co_exrule where aid='$aid'");
   $dsql->Close();
   require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
   $wintitle = "删除数据规则模型";
	 $wecome_info = "<a href='co_export_rule.php'><u>数据规则模型</u></a>::导出规则配置";
	 $win = new OxWindow();
	 $win->Init();
	 $win->AddTitle("以下为规则[{$aid}]的文本配置，你可以共享给你的朋友：");
	 $winform = $win->GetWindow("hand","<textarea name='cg' style='width:100%;height:300px'>".$row['ruleset']."</textarea><br/><br/>");
	 $win->Display();
   exit();
}
////////////////////////////////
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__co_exrule where aid='$aid'");
$dsql->Close();
$ruleset = $row['ruleset'];
$channelid = $row['channelid'];
$dtp = new DedeTagParse();
$dtp->LoadString($ruleset);
$noteid = 0;
if(is_array($dtp->CTags))
{
	foreach($dtp->CTags as $ctag){
		if($ctag->GetName()=='field') $noteid++;
	}
}
else
{
	ShowMsg("该规则不合法，无法进行更改!","-1");
	$dsql->Close();
	exit();
}
$noteinfos = $dtp->GetTagByName("note");

require_once(dirname(__FILE__)."/templets/co_export_rule_edit.htm");

ClearAllLink();
?>
