<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
CheckPurview('c_Edit');
if(empty($dopost)) $dopost="";
$isdefault = (empty($isdefault) ? '0' : $isdefault);
$ID = (empty($ID) ? '' : intval($ID));
/*----------------
function __Show()
-----------------*/
if($dopost=="show")
{
	$dsql = new DedeSql(-100);
	$dsql->ExecuteNoneQuery("update #@__channeltype set isshow=1 where ID='$ID'");
	$dsql->Close();
	ShowMsg("操作成功！","mychannel_main.php");
	exit();
}
/*----------------
function __Hide()
-----------------*/
else if($dopost=="hide")
{
	$dsql = new DedeSql(-100);
	$dsql->ExecuteNoneQuery("update #@__channeltype set isshow=0 where ID='$ID'");
	$dsql->Close();
	ShowMsg("操作成功！","mychannel_main.php");
	exit();
}
/*----------------
function __SaveEdit()
-----------------*/
else if($dopost=="save")
{
	$query = "
	update `#@__channeltype` set 
	typename = '$typename',
	addtable = '$addtable',
	addcon = '$addcon',
	mancon = '$mancon',
	editcon = '$editcon',
	useraddcon = '$useraddcon',
	usermancon = '$usermancon',
	usereditcon = '$usereditcon',
	issend = '$issend',
	arcsta = '$arcsta',
	sendrank = '$sendrank',
	sendmember = '$sendmember',
	issystem = '$issystem',
	isdefault = '$isdefault'
	where ID='$ID'
	";
  $dsql = new DedeSql(-100);
  $trueTable = str_replace("#@__",$cfg_dbprefix,$addtable);
	if(!$dsql->IsTable($trueTable)){
		$dsql->Close();
  	ShowMsg("系统找不到你所指定的表 $trueTable ！","-1");
  	exit();
  }
	$dsql->ExecuteNoneQuery($query);
	if($isdefault>0){
		$dsql->ExecuteNoneQuery("update `#@__channeltype` set isdefault=0 where ID<>'$ID' ");
	}
	$dsql->Close();
	ShowMsg("成功更改一个模型！","mychannel_main.php");
	exit();
}
/*----------------
function __Copy()
-----------------*/
else if($dopost=="copy")
{
	if($ID==-1){
		ShowMsg("专题模型不支持复制！","-1");
		exit();
	}
	$dsql = new DedeSql(-100);
	$row = $dsql->GetOne("Select * From #@__channeltype where ID='$ID'");
	if($row['ID']>-1){
	  $nrow = $dsql->GetOne("Select ID From #@__channeltype order by ID desc limit 0,1 ");
    $newid = $nrow['ID']+1;
    if($newid<10) $newid = $newid+10;
    $idname = $newid;
  }else
  {
  	$nrow = $dsql->GetOne("Select ID From #@__channeltype order by ID asc limit 0,1 ");
    $newid = $nrow['ID']-1;
    if($newid<-10) $newid = $newid-10;
    $idname = 'w'.($newid * -1);
  }
  $row = $dsql->GetOne("Select * From #@__channeltype where ID='$ID'");
  $dsql->Close();
  if($row['maintable']=='') $row['maintable'] = '#@__archives';
  $wintitle = "频道管理-模型复制";
	$wecome_info = "<a href='mychannel_main.php'>频道管理</a> - 模型复制";
	  $win = new OxWindow();
	  $win->Init("mychannel_edit.php","js/blank.js","post");
	  $win->AddTitle("被复制频道： [<font color='red'>".$row['typename']."</font>]");
	  $win->AddHidden("cid",$ID);
	  $win->AddHidden("dopost",'savecopy');
	  $msg = "
<table width='460' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='170' height='24' align='center'>新频道ID：</td>
    <td width='230'><input name='newid' type='text' id='newid' size='6' value='{$newid}' /></td>
  </tr>
  <tr>
    <td height='24' align='center'>新频道名称：</td>
    <td><input name='newtypename' type='text' id='newtypename' value='{$row['typename']}{$idname}' style='width:250px' /></td>
  </tr>
  <tr>
    <td height='24' align='center'>新频道标识：</td>
    <td><input name='newnid' type='text' id='newnid' value='{$row['nid']}{$idname}' style='width:250px' /></td>
  </tr>
  <tr>
    <td height='24' align='center'>新索引表：</td>
    <td><input name='newmaintable' type='text' id='newmaintable' value='{$row['maintable']}{$idname}' style='width:250px' /></td>
  </tr>
  <tr>
    <td height='24' align='center'>新附加表：</td>
    <td><input name='newaddtable' type='text' id='newaddtable' value='{$row['addtable']}{$idname}' style='width:250px' /></td>
  </tr>
  <tr>
    <td height='24' align='center'>复制模板：</td>
    <td>
    <input name='copytemplet' type='radio' id='copytemplet' value='1' class='np' checked='checked' /> 复制
    &nbsp;
    <input name='copytemplet' type='radio' id='copytemplet' class='np' value='0' /> 不复制
    </td>
  </tr>
</table>  
	  ";
	  $win->AddMsgItem("<div style='padding:20px;line-height:300%'>$msg</div>");
	  $winform = $win->GetWindow("ok","");
	  $win->Display();
	  exit();
}
/*----------------
function __SaveCopy()
-----------------*/
else if($dopost=="savecopy")
{
	$dsql = new DedeSql(-100);
  $row = $dsql->GetOne("Select * From #@__channeltype where ID='$cid' ", MYSQL_ASSOC);
  foreach($row as $k=>$v) ${strtolower($k)} = addslashes($v);
  $inquery = " INSERT INTO `#@__channeltype`(`ID` , `nid` , `typename` , `maintable` , `addtable` , `addcon` ,
                `mancon` , `editcon` , `useraddcon` , `usermancon` , `usereditcon` , `fieldset` , `listadd` ,
                 `issystem` , `isshow` , `issend` , `arcsta` , `sendrank` , `sendmember`) 
              VALUES('$newid' , '$newnid' , '$newtypename' , '$newmaintable' , '$newaddtable' , '$addcon' ,
               '$mancon' , '$editcon' , '$useraddcon' , '$usermancon' , '$usereditcon' , '$fieldset' , '$listadd' ,
               '0' , '$isshow' , '$issend' , '$arcsta' , '$sendrank' , '$sendmember');
  ";
  $mysql_version = $dsql->GetVersion();
  $mysql_versions = explode(".",trim($mysql_version));
  $mysql_version = $mysql_versions[0].".".$mysql_versions[1];
  $narrs1 = array('maintalbe'=>$newmaintable,'addtalbe'=>$newaddtable);
  $narrs2 = array('maintalbe'=>$maintable,'addtalbe'=>$addtable);
  foreach($narrs1 as $f=>$fn)
  {
    if(!$dsql->IsTable($fn))
    {
        $tb = $narrs2[$f];
        $dsql->SetQuery("SHOW CREATE TABLE {$dsql->dbName}.{$tb}");
	      $dsql->Execute();
		 	  $row = $dsql->GetArray();
		 	  /*
		 	  //根据数据库版本备份表结构，由于主表取消了自动递增字段，因此可以省略此选项
		 	  $eng1 = "ENGINE=MyISAM DEFAULT CHARSET=".$cfg_db_language;
		 	  $eng2 = "ENGINE=MyISAM AUTO_INCREMENT=([0-9]{1,}) DEFAULT CHARSET=".$cfg_db_language;
		 	  if($datatype==4.0 && $mysql_version > 4.0){
		 	      $tableStruct = eregi_replace($eng1,"TYPE=MyISAM",$row[1]);
		 	      $tableStruct = eregi_replace($eng2,"TYPE=MyISAM",$row[1]);
		 	  }
		 	  else if($datatype==4.1 && $mysql_version < 4.1){
		 	      $tableStruct = eregi_replace("TYPE=MyISAM",$eng1,$row[1]);
		 	  }else{
		 	  */
		 	  $tableStruct = $row[1];
		 	  $tb = str_replace('#@__',$cfg_dbprefix,$tb);
		 	  $tableStruct = preg_replace("/CREATE TABLE `$tb`/iU","CREATE TABLE `$fn`",$tableStruct);
		 	  $dsql->ExecuteNoneQuery($tableStruct);
		}
  }
  if($copytemplet==1){
    $tmpletdir = $cfg_basedir.$cfg_templets_dir.'/'.$cfg_df_style;
    @copy("{$tmpletdir}/article_{$nid}.htm","{$tmpletdir}/article_{$newnid}.htm");
    @copy("{$tmpletdir}/list_{$nid}.htm","{$tmpletdir}/list_{$newnid}.htm");
    @copy("{$tmpletdir}/index_{$nid}.htm","{$tmpletdir}/index_{$newnid}.htm");
  }
  $rs = $dsql->ExecuteNoneQuery($inquery);
  if($rs)
  {
  	 ShowMsg("成功复制模型，现转到详细参数页... ","mychannel_edit.php?ID={$newid}&dopost=edit");
  	 $dsql->Close();
     exit();
  }
  else
  {
  	 $errv = $dsql->GetError();
  	 ShowMsg("系统出错，请把错误代码发送到官方论坛，以检查原因！<br /> 错误代码：mychannel_edit.php?dopost=savecopy $errv","javascript:;");
  	 $dsql->Close();
     exit();
  }
}
/*----------------
function __GetTemplets()
-----------------*/
else if($dopost=="gettemplets")
{
  $dsql = new DedeSql(-100);
  $row = $dsql->GetOne("Select * From #@__channeltype where ID='$ID'");
  $dsql->Close();
  $wintitle = "频道管理-查看模板";
	$wecome_info = "<a href='mychannel_main.php'>频道管理</a>:查看模板";
	  $win = new OxWindow();
	  $win->Init("","js/blank.js","");
	  $win->AddTitle("频道：（".$row['typename']."）默认模板文件说明：");
	  $msg = "
	    文档模板：{$cfg_templets_dir}/{$cfg_df_style}/article_{$row['nid']}.htm
	     <a href='file_manage_view.php?fmdo=edit&filename=article_{$row['nid']}.htm&activepath=".urlencode("{$cfg_templets_dir}/{$cfg_df_style}")."'>[修改]</a><br/>
	    列表模板：{$cfg_templets_dir}/{$cfg_df_style}/list_{$row['nid']}.htm 
	    <a href='file_manage_view.php?fmdo=edit&filename=list_{$row['nid']}.htm&activepath=".urlencode("{$cfg_templets_dir}/{$cfg_df_style}")."'>[修改]</a>
	    <br/>
	    频道封面模板：{$cfg_templets_dir}/{$cfg_df_style}/index_{$row['nid']}.htm
	    <a href='file_manage_view.php?fmdo=edit&filename=index_{$row['nid']}.htm&activepath=".urlencode("{$cfg_templets_dir}/{$cfg_df_style}")."'>[修改]</a>
	  ";
	  $win->AddMsgItem("<div style='padding:20px;line-height:300%'>$msg</div>");
	  $winform = $win->GetWindow("hand","");
	  $win->Display();
	  exit();
}
/*----------------
function __Delete()
-----------------*/
else if($dopost=="delete")
{
	@set_time_limit(0);
	CheckPurview('c_Del');
	$dsql = new DedeSql(-100);
  $row = $dsql->GetOne("Select * From #@__channeltype where ID='$ID'");
  if($row['issystem'] == 1)
  {
  	$dsql->Close();
  	ShowMsg("系统模型不允许删除！","mychannel_main.php");
	  exit();
  }
  
  if(empty($job)) $job="";
  
  if($job=="") //确认提示
  {
  	$dsql->Close();
  	$wintitle = "频道管理-删除模型";
	  $wecome_info = "<a href='mychannel_main.php'>频道管理</a>::删除模型";
	  $win = new OxWindow();
	  $win->Init("mychannel_edit.php","js/blank.js","POST");
	  $win->AddHidden("job","yes");
	  $win->AddHidden("dopost",$dopost);
	  $win->AddHidden("ID",$ID);
	  $win->AddTitle("你确实要删除 (".$row['typename'].") 这个频道？");
	  $winform = $win->GetWindow("ok");
	  $win->Display();
	  exit();
  }
  else if($job=="yes") //操作
  {
    require_once(dirname(__FILE__)."/../include/inc_typeunit_admin.php");
    $dsql = new DedeSql(false);
    $myrow = $dsql->GetOne("Select maintable,addtable From `#@__channeltype` where ID='$ID'",MYSQL_ASSOC);
    if(!is_array($myrow)){
    	$dsql->Close();
    	ShowMsg("你所指定的频道信息不存在!","-1");
    	exit();
    }
		if($myrow['maintable']=='') $myrow['maintable'] = '#@__archives';
		
		//检查频道的表是否独占数据表
		$maintable = str_replace($cfg_dbprefix,'',str_replace('#@__',$cfg_dbprefix,$myrow['maintable']));
		$addtable = str_replace($cfg_dbprefix,'',str_replace('#@__',$cfg_dbprefix,$myrow['addtable']));
		
		$row = $dsql->GetOne("Select count(ID) as dd From `#@__channeltype` where  maintable like '{$cfg_dbprefix}{$maintable}' Or maintable like CONCAT('#','@','__','$maintable') ; ");
		$isExclusive1 = ($row['dd']>1 ? 0 : 1 );
		$row = $dsql->GetOne("Select count(ID) as dd From `#@__channeltype` where  addtable like '{$cfg_dbprefix}{$addtable}' Or addtable like CONCAT('#','@','__','$addtable') ; ");
		$isExclusive2 = ($row['dd']>1 ? 0 : 1 );
		
		//获取与频道关连的所有栏目ID
		$tids = '';
		$dsql->Execute('qm',"Select ID From `#@__arctype` where channeltype='$ID'");
		while($row = $dsql->GetArray('qm')){
			$tids .= ($tids=='' ? $row['ID'] : ','.$row['ID']);
		}
		
		//删除主表
		if($isExclusive1==1) $dsql->ExecuteNoneQuery("DROP TABLE IF EXISTS `{$cfg_dbprefix}{$maintable}`;");
		else
		{
			if($tids!=''){
      	$dsql->ExecuteNoneQuery("Delete From `{$myrow['maintable']}` where typeid in($tids); ");
        $dsql->ExecuteNoneQuery("update `{$myrow['maintable']}` set typeid2=0 where typeid2 in ($tids); ");
      }
		} 
		
		//删除附加表
		if($isExclusive2==1) $dsql->ExecuteNoneQuery("DROP TABLE IF EXISTS `{$cfg_dbprefix}{$addtable}`;");
		else
		{
		   if($tids!='' && $myrow['addtable']!=''){
		   	  $dsql->ExecuteNoneQuery("Delete From `{$myrow['addtable']}` where typeid in ($tids); ");
		   }
		}
		   
	  //删除其它数据
	  if($tids!='')
    {
			 $dsql->ExecuteNoneQuery("Delete From `#@__spec` where typeid in ($tids); ");
			 $dsql->ExecuteNoneQuery("Delete From `#@__feedback` where typeid in ($tids); ");
		   $dsql->ExecuteNoneQuery("Delete From `#@__arctype` where ID in ($tids); ");
		   $dsql->ExecuteNoneQuery("Delete From `#@__full_search` where typeid in ($tids); ");
		}
		//删除频道配置信息
    $dsql->ExecuteNoneQuery("Delete From `#@__channeltype` where ID='$ID'");
	  //更新栏目缓存
	  UpDateCatCache($dsql);
	  
	  $dsql->Close();
	  ShowMsg("成功删除一个模型！","mychannel_main.php");
	  exit();
  }
}
$dsql = new DedeSql(-100);
$row = $dsql->GetOne("Select * From #@__channeltype where ID='$ID'");

require_once(dirname(__FILE__)."/templets/mychannel_edit.htm");

ClearAllLink();
?>