<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('co_NewRule');
if(empty($action)) $action = "";
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
	Insert Into #@__co_exrule(channelid,rulename,etype,dtime,ruleset)
	Values('$channelid','$rulename','$etype','".time()."','$notes')
	";
	$dsql = new DedeSql(false);
	$dsql->ExecuteNoneQuery($query);
	$dsql->Close();
	ShowMsg("成功增加一个规则!","co_export_rule.php");
	exit();
}
else if($action=="hand")
{
	 if(empty($job)) $job="";
	 if($job=="")
	 {
     require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
     $wintitle = "数据导入规则";
	   $wecome_info = "<a href='co_export_rule.php'><u>数据导入规则</u></a>::导入文本配置";
	   $win = new OxWindow();
	   $win->Init("co_export_rule_add.php","js/blank.js","POST");
	   $win->AddHidden("job","yes");
	   $win->AddHidden("action",$action);
	   $win->AddTitle("请在下面输入你要导入的文本配置：");
	   $win->AddMsgItem("<textarea name='notes' style='width:100%;height:300'></textarea>");
	   $winform = $win->GetWindow("ok");
	   $win->Display();
     exit();
   }
   else
   {
   	  require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
   	  $dtp = new DedeTagParse();
   	  $dbnotes = $notes;
   	  $notes = stripslashes($notes);
      $dtp->LoadString($notes);
   	  if(!is_array($dtp->CTags))
      {
	      ShowMsg("该规则不合法，无法保存!","-1");
	      $dsql->Close();
	      exit();
      }
      $noteinfos = $dtp->GetTagByName("note");
	    $query = "
	        Insert Into #@__co_exrule(channelid,rulename,etype,dtime,ruleset)
	        Values('".$noteinfos->GetAtt('channelid')."','".$noteinfos->GetAtt('rulename')."','".$noteinfos->GetAtt('etype')."','".time()."','$dbnotes')
	    ";
	    $dsql = new DedeSql(false);
	    $dsql->ExecuteNoneQuery($query);
	    $dsql->Close();
	    ShowMsg("成功导入一个规则!","co_export_rule.php");
	    exit();
   }
}


require_once(dirname(__FILE__)."/templets/co_export_rule_add.htm");

ClearAllLink();
?>
