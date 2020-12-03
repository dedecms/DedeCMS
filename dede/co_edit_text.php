<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('co_EditNote');
if(empty($job)) $job="";
if($job=="")
{
     require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
     $wintitle = "更改采集规则";
	   $wecome_info = "<a href='co_main.php'><u>采集点管理</u></a>::更改采集规则";
	   $win = new OxWindow();
	   $win->Init("co_edit_text.php","js/blank.js","POST");
	   $win->AddHidden("job","yes");
	   $win->AddHidden("nid",$nid);
	   $win->AddTitle("文本配置专家更改模式：[<a href='co_edit.php?nid={$nid}'>使用可视化修改模式</a>]");
	   $dsql = new DedeSql(false);
	   $row = $dsql->GetOne("Select * From #@__conote where nid='$nid' ");
	   $dsql->Close();
	   $win->AddMsgItem("<textarea name='notes' style='width:100%;height:500px' rows='20'>{$row['noteinfo']}</textarea>");
	   $winform = $win->GetWindow("ok");
	   $win->Display();
     exit();
}
else
{
   	  CheckPurview('co_EditNote');
   	  require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
   	  $dtp = new DedeTagParse();
   	  $dbnotes = $notes;
   	  $notes = stripslashes($notes);
      $dtp->LoadString($notes);
   	  if(!is_array($dtp->CTags)){
	      ShowMsg("该规则不合法，无法保存!","-1");
	      $dsql->Close();
	      exit();
      }
      $ctag = $dtp->GetTagByName("item");
	    $query = "
	      Update #@__conote 
	        set typeid='".$ctag->GetAtt('typeid')."',
	        gathername='".$ctag->GetAtt('name')."',
	        language='".$ctag->GetAtt('language')."',
	        lasttime=0,
	        savetime='".time()."',
	        noteinfo='".$dbnotes."'
	      where nid = $nid;
	    ";
	    $dsql = new DedeSql(false);
	    $rs = $dsql->ExecuteNoneQuery($query);
	    $dsql->Close();
	    ShowMsg("成功保存规则!","co_main.php");
	    exit();
}

ClearAllLink();
?>