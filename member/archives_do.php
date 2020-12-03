<?php 
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost)) $dopost = "";
/*-----------------
function delStow()
删除收藏
------------------*/
if($dopost=="delStow")
{
	CheckRank(0,0);
	if(!empty($_COOKIE['ENV_GOBACK_URL'])) $ENV_GOBACK_URL = $_COOKIE['ENV_GOBACK_URL'];
	else $ENV_GOBACK_URL = "artlist.php";
	$aid = ereg_replace("[^0-9]","",$aid);
	$dsql = new DedeSql(false);
	$dsql->SetQuery("Delete From #@__memberstow where aid='$aid' And uid='".$cfg_ml->M_ID."'; ");
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
	require_once(dirname(__FILE__)."/inc/inc_batchup.php");
	
	if(!empty($_COOKIE['ENV_GOBACK_URL'])) $ENV_GOBACK_URL = $_COOKIE['ENV_GOBACK_URL'];
	else $ENV_GOBACK_URL = 'content_list.php?channelid=';
	$aid = ereg_replace("[^0-9]","",$aid);
	
	$dsql = new DedeSql(false);
	
	$equery = "Select #@__archives.ID,#@__archives.arcrank,#@__archives.channel,#@__channeltype.arcsta
 	from #@__archives left join #@__channeltype on #@__channeltype.ID=#@__archives.channel
	where #@__archives.memberID='".$cfg_ml->M_ID."' And #@__archives.ID='$aid'";
	
	$row = $dsql->GetOne($equery);
	if(!is_array($row)){
		$dsql->Close();
	  ShowMsg("你没有权限删除这篇文档！","-1");
	  exit();
	}else if($row['arcrank']>=0 && $row['arcsta']==-1){
		$dsql->Close();
	  ShowMsg("这篇文档已被管理员处理过，你不能再删除它！","-1");
	  exit();
	}
	
	$channelid = $row['channel'];
	
	//删除文档
	DelArc($aid);
	
	//更新用户记录
	if($channelid==1) $dsql->ExecuteNoneQuery("Update #@__member set c1=c1-1 where ID='".$cfg_ml->M_ID."';");
	else if($channelid==2) $dsql->ExecuteNoneQuery("Update #@__member set c2=c2-1 where ID='".$cfg_ml->M_ID."';");
	else $dsql->ExecuteNoneQuery("Update #@__member set c3=c3-1 where ID='".$cfg_ml->M_ID."';");
	
	$dsql->Close();
	
	if($ENV_GOBACK_URL=='content_list.php?channelid=') $ENV_GOBACK_URL = $ENV_GOBACK_URL.$channelid;
	ShowMsg("成功删除一篇文档！",$ENV_GOBACK_URL);
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
/*--------------
function DelUploads()
删除上传的附件
----------------*/
else if($dopost=="delUploads")
{
	CheckRank(0,0);
	if(empty($ids)) $ids = "";
	if(empty($aid)) $aid = "";
	$dsql = new DedeSql(false);
	$tj = 0;
	if($ids==""){
	  $aid = ereg_replace("[^0-9]","",$aid);
	  $arow = $dsql->GetOne("Select url,memberid From #@__uploads where aid='$aid'; ");
	  if(is_array($arow) && $arow['memberid']==$cfg_ml->M_ID){
	      $row = $dsql->GetOne("Select count(*) as dd From #@__uploads where url='".$arow['url']."'; ");
	      $dsql->ExecuteNoneQuery("Delete From #@__uploads where aid='$aid'; ");
	      if($row['dd']==1){
	      	  if(file_exists($cfg_basedir.$arow['url']) && is_file($cfg_basedir.$arow['url']))
	      	  { @unlink($cfg_basedir.$arow['url']);}
	      }
	  }
	  $tj++;
  }
  else{
  	$ids = explode(',',$ids);
  	foreach($ids as $aid){
  		$aid = ereg_replace("[^0-9]","",$aid);
	    $arow = $dsql->GetOne("Select url,memberid From #@__uploads where aid='$aid'; ");
	    if(is_array($arow) && $arow['memberid']==$cfg_ml->M_ID){
	      $row = $dsql->GetOne("Select count(*) as dd From #@__uploads where url='".$arow['url']."'; ");
	      $dsql->ExecuteNoneQuery("Delete From #@__uploads where aid='$aid'; ");
	      $tj++;
	      if($row['dd']==1){
	      	  if(file_exists($cfg_basedir.$arow['url']) && is_file($cfg_basedir.$arow['url']))
	      	  { @unlink($cfg_basedir.$arow['url']); }
	      }
	    }
  	}
  }
  ShowMsg("成功删除 $tj 个附件！",$ENV_GOBACK_URL);
  $dsql->Close();
	exit();
}
/*--------------
function editUpload()
修改上传的附件
----------------*/
else if($dopost=="editUpload")
{
	CheckRank(0,0);
	$svali = GetCkVdValue();
  if(strtolower($vdcode)!=$svali || $svali==""){
  	 ShowMsg("验证码错误！","-1");
  	 exit();
  }
  $aid = ereg_replace("[^0-9]","",$aid);
  $title = addslashes(ereg_replace($cfg_egstr,"",stripslashes($title)));
  $dsql = new DedeSql(false);
	$arow = $dsql->GetOne("Select url,memberid,mediatype From #@__uploads where aid='$aid'; ");
	if(is_array($arow) && $arow['memberid']==$cfg_ml->M_ID)
	{
	    //重新上传了文件
	    if(is_uploaded_file($addonfile))
	    {
	    	if($addonfile_size > $cfg_mb_upload_size*1024){
	    		  @unlink(is_uploaded_file($addonfile));
	    		  $dsql->Close();
		        ShowMsg("你上传的附件太大，不允许保存！","-1");
		        exit();
	    	}
	    	if(eregi("^text",$addonfile_type)){
		        $dsql->Close();
		        ShowMsg("不允许上传文本类型附件!","-1");
		        exit();
	      }
	    	$fsize = $addonfile_size;
	    	$sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png");
	    	//图片附件
	    	if(in_array($addonfile_type,$sparr))
	    	{
	    		  @move_uploaded_file($addonfile,$cfg_basedir.$arow['url']);
	    		  $info = ""; $datas[0] = 0; $datas[1] = 0;
	          $datas = GetImageSize($cfg_basedir.$arow['url'],$info);
	          if($datas[0]>0) $addquery = " width='".$datas[0]."',height='".$datas[1]."',filesize='$fsize' ";
		        else $addquery = " filesize='$fsize' ";
	          $upquery = "Update #@__uploads set title='$title',mediatype='1', $addquery where aid='$aid'; ";
	          $dsql->ExecuteNoneQuery($upquery);
	          $dsql->Close();
		        ShowMsg("成功更新一个图片！","space_upload_edit.php?aid=".$aid."&dopost=edit".time());
		        exit();
	    	//普通附件
	    	}else{
	    		  if($cfg_mb_upload=='否'){
	      	     $dsql->Close();
		           ShowMsg("系统不允许会员上传非图片附件!","-1");
		           exit();
	          }
	    		  if(!CheckAddonType($uploadfile_name)){
		           $dsql->Close();
		           ShowMsg("你所上传的文件类型被禁止，系统只允许上传<br>".$cfg_mb_mediatype." 类型附件和图片！","-1");
		           exit();
	          }else{
	          	 @move_uploaded_file($addonfile,$cfg_basedir.$arow['url']);
	             $upquery = "Update #@__uploads set title='$title',filesize='$fsize' where aid='$aid'; ";
	             $dsql->ExecuteNoneQuery($upquery);
	             $dsql->Close();
	             ShowMsg("成功更新一个附件！","space_upload_edit.php?aid=".$aid."&dopost=edit".time());
		           exit();
	          }
	    	}
	    //没上传附件
	    }else{
	      $upquery = "Update #@__uploads set title='$title' where aid='$aid'; ";
	      $dsql->ExecuteNoneQuery($upquery);
	      $dsql->Close();
	      ShowMsg("成功更新附件信息！","space_upload_edit.php?aid=".$aid."&dopost=edit".time());
		    exit();
	    }
	}else{
		$dsql->Close();
		ShowMsg("你没权限更改让此附件！","-1");
		exit();
	}
	
}
?>