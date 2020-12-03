<?
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
	   $win->AddTitle("文本配置专家更改模式：");
	   $dsql = new DedeSql(false);
	   $row = $dsql->GetOne("Select * From #@__ where nid='$nid' ");
	   $dsql->Close();
	   $win->AddMsgItem("<textarea name='notes' style='width:100%;height:500'>$notestring</textarea>");
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
          '".$ctag->GetAtt('language')."', '0','".mytime()."', '".$dbnotes."');
	    ";
	    $dsql = new DedeSql(false);
	    $rs = $dsql->ExecuteNoneQuery($query);
	    $dsql->Close();
	    ShowMsg("成功导入一个规则!","co_main.php");
	    exit();
}
?>