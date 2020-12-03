<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
CheckPurview('c_Edit');
if(empty($dopost)) $dopost="";
$ID = ereg_replace("[^0-9\-]","",$ID);
/*----------------
function __Show()
-----------------*/
if($dopost=="show")
{
	$dsql = new DedeSql(false);
	$dsql->ExecuteNoneQuery("update #@__channeltype set isshow=1 where ID='$ID'");
	$dsql->Close();
	ShowMsg("操作成功！","mychannel_main.php");
	exit();
}
/*----------------
function __Hide()
-----------------*/
else if($dopost=="hide"){
	$dsql = new DedeSql(false);
	$dsql->ExecuteNoneQuery("update #@__channeltype set isshow=0 where ID='$ID'");
	$dsql->Close();
	ShowMsg("操作成功！","mychannel_main.php");
	exit();
}
/*----------------
function __SaveEdit()
-----------------*/
else if($dopost=="save"){
	$query = "
	update #@__channeltype set 
	typename = '$typename',
	addtable = '$addtable',
	addcon = '$addcon',
	mancon = '$mancon',
	editcon = '$editcon',
	listadd = '$listadd',
	issend = '$issend',
	arcsta = '$arcsta',
	sendrank = '$sendrank'
	where ID='$ID'
	";
  $dsql = new DedeSql(false);
  $trueTable = str_replace("#@__",$cfg_dbprefix,$addtable);
	if(!$dsql->IsTable($trueTable)){
		$dsql->Close();
  	ShowMsg("系统找不到你所指定的表 $trueTable ！","-1");
  	exit();
  }
	$dsql->ExecuteNoneQuery($query);
	$dsql->Close();
	ShowMsg("成功更改一个模型！","mychannel_main.php");
	exit();
}
/*----------------
function __GetTemplets()
-----------------*/
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
/*----------------
function __Delete()
-----------------*/
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
function SelectGuide(fname,chid)
{
   var posLeft = window.event.clientY-200;
   var posTop = window.event.clientX-200;
   window.open("mychannel_field_make.php?chid="+chid+"&f="+fname, "popUpImagesWin", "scrollbars=yes,resizable=no,statebar=no,width=600,height=420,left="+posLeft+", top="+posTop);
}
//删除
function DelNote(gourl){
	if(!window.confirm("你确认要删除这条记录么！")){ return false; }
	location.href=gourl;
}
-->
</script>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body topmargin="8">
<table width="98%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#98CAEF">
  <form name="form1" action="mychannel_edit.php" method="post">
    <input type='hidden' name='ID' value='<?php echo $ID?>'>
    <input type='hidden' name='dopost' value='save'>
    <input type='hidden' name='issystem' value='<?php echo $row['issystem']?>'>
    <tr> 
      <td height="20" colspan="2" background="img/tbg.gif"> <b>&nbsp;<a href="mychannel_main.php"><u>频道模型管理</u></a> 
        &gt; 更改频道模型：</b> </td>
    </tr>
    <?php 
	if($row['issystem'] == 1)
	{
	?>
    <tr> 
      <td colspan="2" bgcolor="#FFFFFF" style="color:red"> 你目前所展开的是系统模型，系统模型一般对发布程序和管理程序已经固化，更改不当将会导致使用这种内容类型的频道可能崩溃。      </td>
    </tr>
    <?php 
	}
	?>
    <tr> 
      <td width="19%" align="center" bgcolor="#FFFFFF">频道ID</td>
      <td width="81%" bgcolor="#FFFFFF"> 
        <?php echo $row['ID']?>      </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">名字标识</td>
      <td bgcolor="#FFFFFF"> 
        <?php echo $row['nid']?>      </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">频道名称</td>
      <td bgcolor="#FFFFFF"><input name="typename" type="text" id="typename" value="<?php echo $row['typename']?>"></td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">附加表</td>
      <td bgcolor="#FFFFFF"><input name="addtable" type="text" id="addtable" value="<?php echo $row['addtable']?>">
        ( #@__ 是表示数据表前缀)</td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">是否支持会员投稿：</td>
      <td bgcolor="#FFFFFF"> <input name="issend" type="radio" class="np" value="0"<?php  if($row['issend']==0) echo " checked"; ?>>
        不支持 
        <input type="radio" name="issend" class="np" value="1"<?php  if($row['issend']==1) echo " checked"; ?>>
        支持 </td>
    </tr>
    <tr>
      <td align="center" bgcolor="#FFFFFF">会员许可投稿级别：</td>
      <td bgcolor="#FFFFFF"><select name="sendrank" id="sendrank" style="width:120">
          <?php 
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
	   <input name="arcsta" class="np" type="radio" value="-1"<?php  if($row['arcsta']==-1) echo " checked";?>>
        未审核 
        <input name="arcsta" class="np" type="radio" value="0"<?php  if($row['arcsta']==0) echo " checked";?>>
        已审核（自动生成HTML） 
        <input name="arcsta" class="np" type="radio" value="1"<?php  if($row['arcsta']==1) echo " checked";?>>
        已审核（仅使用动态文档）</td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">档案发布程序</td>
      <td bgcolor="#FFFFFF"><input name="addcon" type="text" id="addcon" value="<?php echo $row['addcon']?>"></td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">档案修改程序</td>
      <td bgcolor="#FFFFFF"><input name="editcon" type="text" id="editcon" value="<?php echo $row['editcon']?>"></td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">档案管理程序</td>
      <td bgcolor="#FFFFFF"><input name="mancon" type="text" id="mancon" value="<?php echo $row['mancon']?>"></td>
    </tr>
    <tr>
      <td align="center" bgcolor="#FFFFFF">列表附加字段：</td>
      <td bgcolor="#FFFFFF"><input name="listadd" type="text" id="listadd" size="50" value="<?php echo $row['listadd']?>">
        <br>
(用&quot;,&quot;分开，可以在列表模板{dede:list}{/dede:list}中用[field:name/]调用)</td>
    </tr>
    <tr>
      <td align="center" bgcolor="#FFFFFF">附加字段配置：</td>
      <td bgcolor="#FFFFFF"><input name="fset" type="button" id="fset" value="浏览字段信息" onClick="location.href='mychannel_field.php?ID=<?php echo $ID?>'" class='nbt'></td>
    </tr>
    
    
    <tr bgcolor="#E8F8FF"> 
      <td height="28" colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="26%">&nbsp;</td>
            <td width="15%"><input name="imageField" class="np" type="image" src="img/button_ok.gif" width="60" height="22" border="0"></td>
            <td width="59%" height="50"><img src="img/button_back.gif" width="60" height="22" onClick="location='mychannel_main.php';" style="cursor:hand"></td>
          </tr>
        </table></td>
    </tr>
  </form>
</table>
<?php 
$dsql->Close();
?>
</body>
</html>