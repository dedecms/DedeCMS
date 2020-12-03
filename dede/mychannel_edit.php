<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
CheckPurview('c_Edit');
if(empty($dopost)) $dopost="";
$ID = ereg_replace("[^0-9\-]","",$ID);
if($dopost=="show")
{
	$dsql = new DedeSql(false);
	$dsql->SetQuery("update #@__channeltype set isshow=1 where ID='$ID'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("操作成功！","mychannel_main.php");
	exit();
}
else if($dopost=="hide")
{
	$dsql = new DedeSql(false);
	$dsql->SetQuery("update #@__channeltype set isshow=0 where ID='$ID'");
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("操作成功！","mychannel_main.php");
	exit();
}
else if($dopost=="save")
{
	
	$query = "
	update #@__channeltype set 
	typename = '$typename',
	addtable = '$addtable',
	addcon = '$addcon',
	mancon = '$mancon',
	editcon = '$editcon',
	fieldset = '$fieldset',
	listadd = '$listadd',
	issend = '$issend',
	arcsta = '$arcsta',
	sendrank = '$sendrank'
	where ID='$ID'
	";
	
	$dtp = new DedeTagParse();
	$dtp->SetNameSpace("field","<",">");
  $dtp->LoadSource(stripslashes($fieldset));
  if(!is_array($dtp->CTags)){
  	$dsql->Close();
  	ShowMsg("配置参数无效！","-1");
  	exit();
  }
  
  $dsql = new DedeSql(false);
  $trueTable = str_replace("#@__",$cfg_dbprefix,$addtable);
	if(!$dsql->IsTable($trueTable)){
		$dsql->Close();
  	ShowMsg("系统找不到你所指定的表 $trueTable ，请手工创建这个表！","-1");
  	exit();
  }
	$dsql->SetQuery($query);
	$dsql->ExecuteNoneQuery();
	
if($issystem!=1){ ////对非系统模型，检测数据库里是否存在某字段，如果没有就增加，差异则修改
	$fields = Array();
	$rs = mysql_query("show fields from $trueTable",$dsql->linkID);
	while($row = mysql_fetch_array($rs)){
		$fields[$row['Field']] = $row['Type'];
	}
  foreach($dtp->CTags as $tid=>$ctag)
  {
  	 $fieldname = $ctag->GetName();
     $dfvalue = $ctag->GetAtt('default');
     $isnull = $ctag->GetAtt('isnull');
     $dtype = $ctag->GetAtt('type');
     $mxlen = $ctag->GetAtt('maxlength');
     if($dtype=="int"||$dtype=="datetime")
     {
    		if($dfvalue=="" || ereg("[^0-9]",$dfvalue)){ $dfvalue = 0; }
    		if($isnull=="true") $tabsql = " `$fieldname` int(11) default NULL;";
    		else $tabsql = " `$fieldname` int(11) NOT NULL default '$dfvalue';";
    		$buideType = "int(11)";
     }else if($dtype=="float"){
    		if($isnull=="true") $tabsql = " `$fieldname` float default NULL;";
    		else $tabsql = " `$fieldname` float NOT NULL default '$dfvalue';";
    		$buideType = "float";
     }else if($dtype=="img"||$dtype=="media"||$dtype=="addon"){
    		if($mxlen=="") $mxlen = 200;
    		if($mxlen > 255) $mxlen = 50;
    		$tabsql = " `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue';";
    		$buideType = "varchar($mxlen)";
     }else if($dtype=="multitext"||$dtype=="htmltext"){
    		if($isnull=="true") $tabsql = " `$fieldname` text NOT NULL;";
    		else $tabsql = " `$fieldname` text;";
    		$buideType = "text";
     }else if($dtype=="textdata"){
    		$tabsql = " `$fieldname` varchar(100) NOT NULL default '';";
    		$buideType = "varchar(100)";
     }else{
    		if($mxlen=="") $mxlen = 50;
    		if($mxlen > 255) $mxlen = 250;
    		$tabsql = " `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue';";
    		$buideType = "varchar($mxlen)";
     }
     if(isset($fields[$fieldname]) && $fields[$fieldname]!=$buideType){
     	  $tabsql = "ALTER TABLE `$trueTable` CHANGE `$fieldname` ".$tabsql;
     }else if(!isset($fields[$fieldname])){
     	$tabsql = "ALTER TABLE `$trueTable` ADD ".$tabsql;
     }else{
     	 $tabsql = "";
     }
     if($tabsql!=""){
     	 $dsql->ExecuteNoneQuery($tabsql);
     	 $tabsql = "";
     }
  }
}//对非系统模型，修改字段
	$dsql->Close();
	ShowMsg("成功更改一个模型！","mychannel_main.php");
	exit();
}
else if($dopost=="gettemplets"){
	require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
  $dsql = new DedeSql(false);
  $row = $dsql->GetOne("Select * From #@__channeltype where ID='$ID'");
  $dsql->Close();
  $wintitle = "频道管理-查看模板";
	$wecome_info = "<a href='mychannel_main.php'>频道管理</a>::查看模板";
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
else if($dopost=="delete")
{
	CheckPurview('c_Del');
	$dsql = new DedeSql(false);
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
  	require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
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
    $ut = new TypeUnit();
    $dsql->SetQuery("Select ID From #@__arctype where reID='0' And channeltype='$ID'");
    $dsql->Execute();
    $ids = "";
    while($row = $dsql->GetObject()){
  	  $ut->DelType($row->ID,"yes");
    }
    $dsql->SetQuery("Delete From #@__channeltype where ID='$ID'");
    $dsql->ExecuteNoneQuery();
    $dsql->Close();
	  $ut->Close();
	  ShowMsg("成功删除一个模型！","mychannel_main.php");
	  exit();
 }
 
}
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__channeltype where ID='$ID'");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>更改频道模型</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<script language="javascript">
<!--
function SelectGuide(fname)
{
   var posLeft = window.event.clientY-200;
   var posTop = window.event.clientX-200;
   window.open("mychannel_field_make.php?f="+fname, "popUpImagesWin", "scrollbars=yes,resizable=no,statebar=no,width=600,height=420,left="+posLeft+", top="+posTop);
}
-->
</script>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body topmargin="8">
<table width="98%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <form name="form1" action="mychannel_edit.php" method="post">
    <input type='hidden' name='ID' value='<?=$ID?>'>
    <input type='hidden' name='dopost' value='save'>
    <input type='hidden' name='issystem' value='<?=$row['issystem']?>'>
    <tr> 
      <td height="20" colspan="2" background="img/tbg.gif"> <b>&nbsp;<a href="mychannel_main.php"><u>频道模型管理</u></a> 
        &gt; 更改频道模型：</b> </td>
    </tr>
    <?
	if($row['issystem'] == 1)
	{
	?>
    <tr> 
      <td colspan="2" bgcolor="#FFFFFF" style="color:red"> 你目前所展开的是系统模型，系统模型一般对发布程序和管理程序已经固化，如果你胡乱更改系统模型将会导致使用这种内容类型的频道可能崩溃。 
      </td>
    </tr>
    <?
	}
	?>
    <tr> 
      <td width="19%" align="center" bgcolor="#FFFFFF">频道ID</td>
      <td width="81%" bgcolor="#FFFFFF"> 
        <?=$row['ID']?>
      </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">名字标识</td>
      <td bgcolor="#FFFFFF"> 
        <?=$row['nid']?>
      </td>
    </tr>
    <!--tr> 
      <td align="center" bgcolor="#FFFFFF">是否允许投稿</td>
      <td bgcolor="#FFFFFF"> 
        
      </td>
    </tr-->
    <tr> 
      <td align="center" bgcolor="#FFFFFF">频道名称</td>
      <td bgcolor="#FFFFFF"><input name="typename" type="text" id="typename" value="<?=$row['typename']?>"></td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">附加表</td>
      <td bgcolor="#FFFFFF"><input name="addtable" type="text" id="addtable" value="<?=$row['addtable']?>">
        ( #@__ 是表示数据表前缀)</td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">是否支持会员投稿：</td>
      <td bgcolor="#FFFFFF"> <input name="issend" type="radio" class="np" value="0"<? if($row['issend']==0) echo " checked"; ?>>
        不支持 
        <input type="radio" name="issend" class="np" value="1"<? if($row['issend']==1) echo " checked"; ?>>
        支持 </td>
    </tr>
    <tr>
      <td align="center" bgcolor="#FFFFFF">会员许可投稿级别：</td>
      <td bgcolor="#FFFFFF"><select name="sendrank" id="sendrank" style="width:120">
          <?
              $urank = $cuserLogin->getUserRank();
              $dsql->SetQuery("Select * from #@__arcrank where adminrank<='$urank' And rank>=10");
              $dsql->Execute();
              while($row2 = $dsql->GetObject())
              {
              	if($row2->rank==$row['sendrank']) echo "     <option value='".$row2->rank."' selected>".$row2->membername."</option>\r\n";
				else echo "     <option value='".$row2->rank."'>".$row2->membername."</option>\r\n";
              }
          ?>
        </select></td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">会员稿件默认状态：</td>
      <td bgcolor="#FFFFFF">
	   <input name="arcsta" class="np" type="radio" value="-1"<? if($row['arcsta']==-1) echo " checked";?>>
        未审核 
        <input name="arcsta" class="np" type="radio" value="0"<? if($row['arcsta']==0) echo " checked";?>>
        已审核（自动生成HTML） 
        <input name="arcsta" class="np" type="radio" value="1"<? if($row['arcsta']==1) echo " checked";?>>
        已审核（仅使用动态文档）</td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">档案发布程序</td>
      <td bgcolor="#FFFFFF"><input name="addcon" type="text" id="addcon" value="<?=$row['addcon']?>"></td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">档案修改程序</td>
      <td bgcolor="#FFFFFF"><input name="editcon" type="text" id="editcon" value="<?=$row['editcon']?>"></td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">档案管理程序</td>
      <td bgcolor="#FFFFFF"><input name="mancon" type="text" id="mancon" value="<?=$row['mancon']?>"></td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">列表附加字段：</td>
      <td bgcolor="#FFFFFF"><input name="listadd" type="text" id="listadd" size="50" value="<?=$row['listadd']?>"> 
        <br>
        (用&quot;,&quot;分开，可以在列表模板{dede:list}{/dede:list}中用[field:name/]调用)</td>
    </tr>
    <tr> 
      <td height="24" align="center" bgcolor="#FFFFFF">附加字段配置：</td>
      <td rowspan="2" bgcolor="#FFFFFF"><textarea name="fieldset"  style="width:600" rows="12" id="fieldset"><?=$row['fieldset']?></textarea></td>
    </tr>
    <tr> 
      <td height="110" align="center" valign="top" bgcolor="#FFFFFF"> <input name="fset" type="button" id="fset" value="字段添加向导" onClick="SelectGuide('form1.fieldset')"> 
        <br> <br> <a href="help_addtable.php" target="_blank"><u>模型附加字段定义参考</u></a></td>
    </tr>
    <tr bgcolor="#F9FDF0"> 
      <td height="28" colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="26%">&nbsp;</td>
            <td width="15%"><input name="imageField" class="np" type="image" src="img/button_ok.gif" width="60" height="22" border="0"></td>
            <td width="59%"><img src="img/button_back.gif" width="60" height="22" onClick="location='mychannel_main.php';" style="cursor:hand"></td>
          </tr>
        </table></td>
    </tr>
  </form>
</table>
<?
$dsql->Close();
?>
</body>
</html>