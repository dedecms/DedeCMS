<?
require_once(dirname(__FILE__)."/config.php");
SetPageRank(10);
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
	$dsql = new DedeSql(false);
	$query = "
	update #@__channeltype set 
	typename = '$typename',
	addtable = '$addtable',
	addcon = '$addcon',
	mancon = '$mancon',
	editcon = '$editcon',
	fieldset = '$fieldset',
	listadd = '$listadd'
	where ID='$ID'
	";
	$dsql->SetQuery($query);
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功更改一个模型！","mychannel_main.php");
	exit();
}
else if($dopost=="delete")
{
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
    require_once(dirname(__FILE__)."/../include/inc_typeunit2.php");
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
$dsql->Close();
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
   window.open("mychannel_field_make.php?f="+fname, "popUpImagesWin", "scrollbars=yes,resizable=no,statebar=no,width=600,height=403,left="+posLeft+", top="+posTop);
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
    <tr> 
      <td height="20" colspan="2" background="img/tbg.gif"> <b>&nbsp;<a href="mychannel_main.php"><u>频道模型管理</u></a> 
        &gt; 更改频道模型：</b> </td>
    </tr>
    <?
	if($row['issystem'] == 1)
	{
	?>
	<tr> 
      <td colspan="2" bgcolor="#FFFFFF" style="color:red">
	  你目前所展开的是系统模型，系统模型一般对发布程序和管理程序已经固化，如果你胡乱更改系统模型将会导致使用这种内容类型的频道可能崩溃。
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
      <td height="110" align="center" valign="top" bgcolor="#FFFFFF"> 
        <input name="fset" type="button" id="fset" value="字段添加向导" onClick="SelectGuide('form1.fieldset')"> 
        <br> <br>
        <a href="help_addtable.php" target="_blank"><u>模型附加字段定义参考</u></a></td>
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
</body>
</html>