<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('co_AddNote');
if(empty($job)) $job="";
if($job=="")
{
     require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
     $wintitle = "导入采集规则";
	   $wecome_info = "<a href='co_main.php'><u>采集点管理</u></a>::导入采集规则";
	   $win = new OxWindow();
	   $win->Init("co_get_corule.php","js/blank.js","POST");
	   $win->AddHidden("job","yes");
	   $win->AddTitle("请在下面输入你要导入的文本配置：");
	   $win->AddMsgItem("<textarea name='notes' style='width:100%;height:300px'></textarea>");
	   $winform = $win->GetWindow("ok");
	   $win->Display();
     exit();
}
else
{
   	  CheckPurview('co_AddNote');
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
      $ctag = $dtp->GetTagByName("item");
	    $query = "
	        INSERT INTO #@__conote(typeid,gathername,language,lasttime,savetime,noteinfo) 
          VALUES('".$ctag->GetAtt('typeid')."', '".$ctag->GetAtt('name')."',
          '".$ctag->GetAtt('language')."', '0','".time()."', '".$dbnotes."');
	    ";
	    $dsql = new DedeSql(false);
	    $rs = $dsql->ExecuteNoneQuery($query);
	    $dsql->Close();
	    ShowMsg("成功导入一个规则!","co_main.php");
	    exit();
}

ClearAllLink();
?>