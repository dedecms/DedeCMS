<?php 
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost)) $dopost = "";
CheckPurview('sys_Att');
$dsql = new DedeSql(false);
//保存更改
//--------------------
if($dopost=="save")
{
   $startID = 1;
   $endID = $idend;
   for(;$startID<=$endID;$startID++)
   {
   	  $query = "";
   	  $att = ${"att_".$startID};
   	  $attname = ${"attname_".$startID};
   	  if(isset(${"check_".$startID})){
   	  	$query = "update #@__arcatt set attname='$attname' where att='$att'";
   	  }
   	  else{
   	  	$query = "Delete From #@__arcatt where att='$att'";
   	  }
   	  if($query!=""){
   	  	$dsql->SetQuery($query);
   	  	$dsql->ExecuteNoneQuery();
   	  } 
   }
   if(isset($check_new))
   {
   	 if($att_new>0 && $attname_new!=""){
   	 	 $dsql->SetQuery("Insert Into #@__arcatt(att,attname) Values('{$att_new}','{$attname_new}')");
   	   $dsql->ExecuteNoneQuery();
   	 }
   }
   echo "<script> alert('成功更新自定文档义属性表！'); </script>";
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>自定义属性管理</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#98CAEF" align="center">
  <form name="form1" action="content_att.php" method="post">
    <input type="hidden" name="dopost" value="save">
    <tr> 
      <td height="28" colspan="3" background='img/tbg.gif'>
      	<strong>文档自定义属性管理</strong>
      </td>
    </tr>
    <tr> 
      <td height="34" colspan="3" bgcolor="#FFFFFF">
    　　<b>自定义属性的意义和使用说明：</b><br>
    　　在以往的版本中，网站主页、频道封面的设计，都只能单调的用 arclist 标记把某栏目最新或按特定排序方式的文档无选择的读出来，这样做法存在很大的不足，例如，我希望在最顶部的地方显示我想要的文档，在以往的版本中是无法做到的，但使用自定义属性之后，只要给arclist 标记加上 att='ID' 的属性，然后在发布的时候对适合的文档选择专门的属性，那么使用arclist的地方就会按你的意愿显示指定的文档。<br>
　　<b>注意事项：</b>当 att='' 时，系统会调用所有文档（考虑与旧版兼容的原因），因此如果你在网页中使用了att属性，那么普通文档的arclist也应该加上att='0'的属性，防止在网页中重复出现某些文章的链接。
      </td>
    </tr>
    <tr bgcolor="#FDFEE9" align="center" > 
      <td width="20%" height="24">ID</td>
      <td width="50%">属性名称</td>
      <td width="30%">处理</td>
    </tr>
	<?php 
	$dsql->SetQuery("Select * From #@__arcatt");
	$dsql->Execute();
	$k=0;
	while($row = $dsql->GetObject())
	{
	  $k++;
	?>
	<input type="hidden" name="att_<?php echo $k?>" value="<?php echo $row->att?>">
    <tr align="center" bgcolor="#FFFFFF"> 
    <td height="24">
    	<?php echo $row->att?>
	  </td>
    <td height="24">
	  <input name="attname_<?php echo $k?>" value="<?php echo $row->attname?>"  type="text" id="attname_<?php echo $k?>" size="30">
	  </td>
      <td>
	  <input name="check_<?php echo $k?>" type="checkbox" id="check_<?php echo $k?>" class="np" value="1" checked>
       继续使用
	 </td>
    </tr>
	<?php 
	}
	?>
	<input type="hidden" name="idend" value="<?php echo $k?>">
    <tr bgcolor="#F8FCF1"> 
      <td height="24" colspan="3" valign="top"><strong>新增一个属性类型：</strong></td>
    </tr>
    <tr> 
      <td height="24" align="center" valign="top" bgcolor="#FFFFFF">
	  <input name="att_new" type="text" id="att_new" size="10">
	  </td>
      <td height="24" align="center" valign="top" bgcolor="#FFFFFF">
	  <input name="attname_new" type="text" id="attname_new" size="30">
	  </td>
      <td align="center" bgcolor="#FFFFFF">
      	<input name="check_new" type="checkbox" id="check_new" class="np" value="1" checked>
        增加新属性
     </td>
    </tr>
    <tr> 
      <td height="24" colspan="3" bgcolor="#F8FCF1">&nbsp;</td>
    </tr>
    <tr> 
      <td height="34" colspan="3" align="center" bgcolor="#CCEDFD">
      	<input name="imageField" type="image" src="img/button_ok.gif" width="60" height="22" border="0"></td>
    </tr>
  </form>
</table>
<?php 
$dsql->Close();
?>
</body>
</html>
