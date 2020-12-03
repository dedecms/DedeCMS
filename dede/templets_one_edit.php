<?php
require(dirname(__FILE__)."/config.php");
CheckPurview('temp_One');
if(empty($dopost)) $dopost = "";
if(empty($aid)) $aid = "";
$dsql = new DedeSql(false);
//////////////////////////////////////////
/*----------------------
function __saveedit();
-------------------*/
if($dopost=="saveedit")
{
  require_once(dirname(__FILE__)."/../include/inc_arcpart_view.php");
	$uptime = time();
	$body = str_replace('&quot;','\\"',$body);
	$filename = ereg_replace("^/","",$nfilename);
	//如果更改了文件名，删除旧文件
	if($oldfilename!=$filename)
	{
		$oldfilename = $cfg_basedir.$cfg_cmspath."/".$oldfilename;
		if(is_file($oldfilename)) unlink($oldfilename);
	}
	$inQuery = "
	 update #@__sgpage set
	 title='$title',
	 ismake='$ismake',
	 filename='$filename',
	 uptime='$uptime',
	 body='$body'
	 where aid='$aid';
	";
	$dsql->SetQuery($inQuery);
	if(!$dsql->ExecuteNoneQuery())
	{
		$dsql->Close();
		ShowMsg("更新页面数据时失败，请检查长相是否有问题！","-1");
	  exit();
	}
	$dsql->Close();
	$filename = $cfg_basedir.$cfg_cmspath."/".$filename;
	if($ismake==1){
	  $pv = new PartView();
    $pv->SetTemplet(stripslashes($body),"string");
    $pv->SaveToHtml($filename);
    $pv->Close();
  }
  else{
  	$fp = fopen($filename,"w") or die("创建：{$filename} 失败，可能是没有权限！");
  	fwrite($fp,stripslashes($body));
  	fclose($fp);
  }
	ShowMsg("成功更新一个页面！","templets_one.php");
	exit();
}
/*----------------------
function __delete();
-------------------*/
else if($dopost=="delete")
{
   $row = $dsql->GetOne("Select filename From #@__sgpage where aid='$aid'");
   $filename = $cfg_basedir.$cfg_cmspath."/".$row['filename'];
   $dsql->SetQuery("Delete From #@__sgpage where aid='$aid'");
   $dsql->ExecuteNoneQuery();
   $dsql->Close();
   if(is_file($filename)) unlink($filename);
   ShowMsg("成功删除一个页面！","templets_one.php");
   exit();
}
/*----------------------
function __make();
-------------------*/
else if($dopost=="make")
{
	require_once(dirname(__FILE__)."/../include/inc_arcpart_view.php");
	$dsql->SetQuery("update #@__sgpage set uptime='".time()."' where aid='$aid'");
  $dsql->ExecuteNoneQuery();
	$row = $dsql->GetOne("Select * From #@__sgpage where aid='$aid'");
	$fileurl = $cfg_cmspath."/".$row['filename'];
	$filename = $cfg_basedir.$cfg_cmspath."/".$row['filename'];
	if($row['ismake']==1)
	{
	    $pv = new PartView();
      $pv->SetTemplet($row['body'],"string");
      $pv->SaveToHtml($filename);
      $pv->Close();
   }
   else
   {  
    	$fp = fopen($filename,"w") or die("创建：{$filename} 失败，可能是没有权限！");
  	  fwrite($fp,$row['body']);
      fclose($fp);
   }
	$dsql->Close();
	ShowMsg("成功更新一个页面！",$fileurl);
	exit();
}
/*----------------------
function __makeAll();
-------------------*/
else if($dopost=="makeall")
{
	require_once(dirname(__FILE__)."/../include/inc_arcpart_view.php");
  $dsql->ExecuteNoneQuery("update #@__sgpage set uptime='".time()."'");
	$row = $dsql->Execute('meoutside',"Select * From #@__sgpage ");
	while($row = $dsql->GetArray('meoutside'))
	{
	  $fileurl = $cfg_cmspath."/".$row['filename'];
	  $filename = $cfg_basedir.$cfg_cmspath."/".$row['filename'];
	  if($row['ismake']==1)
	  {
	    $pv = new PartView();
      $pv->SetTemplet($row['body'],"string");
      $pv->SaveToHtml($filename);
     }
     else
     {  
    	  $fp = fopen($filename,"w") or die("创建：{$filename} 失败，可能是没有权限！");
  	    fwrite($fp,$row['body']);
        fclose($fp);
     }
  }
	$dsql->Close();
	ShowMsg("成功更新所有页面！","templets_one.php");
	exit();
}
$row = $dsql->GetOne("Select  * From #@__sgpage where aid='$aid'");

require_once(dirname(__FILE__)."/templets/templets_one_edit.htm");

ClearAllLink();
?>