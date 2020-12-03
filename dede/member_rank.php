<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Type');
if(empty($dopost)) $dopost = "";
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
   	  $ID = ${"ID_".$startID};
   	  $name = ${"name_".$startID};
   	  $rank = ${"rank_".$startID};
   	  $money = ${"money_".$startID};
   	  if(isset(${"check_".$startID})){
   	  	if($rank>0) $query = "update #@__arcrank set membername='$name',money='$money',rank='$rank' where ID='$ID'";
   	  }
   	  else{
   	  	$query = "Delete From #@__arcrank where ID='$ID' And rank<>10";
   	  }
   	  
   	  if($query!=""){
   	  	$dsql->SetQuery($query);
   	  	$dsql->ExecuteNoneQuery();
   	  } 
   }
   if(isset($check_new))
   {
   	 if($rank_new>0 && $name_new!="" && $money_new!=""){
   	 	 $dsql->SetQuery("Insert Into #@__arcrank(rank,membername,adminrank,money) Values('$rank_new','$name_new','5','$money_new')");
   	   $dsql->ExecuteNoneQuery();
   	 }
   }
   echo "<script> alert('成功更新会员等级表！'); </script>";
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>会员权限管理</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <form name="form1" action="member_rank.php" method="post">
    <input type="hidden" name="dopost" value="save">
    <tr> 
      <td height="28" colspan="4" background='img/tbg.gif'><strong><a href="co_main.php"><u>会员管理</u></a> 
        &gt; 会员权限管理：</strong></td>
    </tr>
    <tr bgcolor="#F8FCF1"> 
      <td height="24" colspan="4"><strong>会员名称和级别值：（注册会员这个级别不能删除，否则会员系统将会无法正常使用某些功能）</strong></td>
    </tr>
    <tr bgcolor="#FDFEE9"> 
      <td width="25%" height="24" align="center" valign="top">名称</td>
      <td width="27%" align="center" valign="top">级别值</td>
      <td width="24%" align="center">默认拥有金币</td>
      <td width="24%" align="center">状态</td>
    </tr>
	<?
	$dsql->SetQuery("Select * From #@__arcrank where rank>0 order by rank");
	$dsql->Execute();
	$k=0;
	while($row = $dsql->GetObject())
	{
	  $k++;
	?>
	<input type="hidden" name="ID_<?=$k?>" value="<?=$row->ID?>">
    <tr align="center" bgcolor="#FFFFFF"> 
      <td height="24" valign="top">
	  <input name="name_<?=$k?>" value="<?=$row->membername?>" type="text" id="name_<?=$k?>" size="20">
	  </td>
      <td height="24" valign="top">
	  <input name="rank_<?=$k?>" value="<?=$row->rank?>"  type="text" id="rank_<?=$k?>" size="20">
	  </td>
      <td>
	  <input name="money_<?=$k?>"  value="<?=$row->money?>"  type="text" id="money_<?=$k?>" size="20">
	  </td>
      <td>
	  <input name="check_<?=$k?>" type="checkbox" id="check_<?=$k?>" class="np" value="1" checked>
       继续使用
	 </td>
    </tr>
	<?
	}
	?>
	<input type="hidden" name="idend" value="<?=$k?>">
    <tr bgcolor="#F8FCF1"> 
      <td height="24" colspan="4" valign="top"><strong>新增一个类别：</strong></td>
    </tr>
    <tr> 
      <td height="24" align="center" valign="top" bgcolor="#FFFFFF">
	  <input name="name_new" type="text" id="name_new" size="20">
	  </td>
      <td height="24" align="center" valign="top" bgcolor="#FFFFFF">
	  <input name="rank_new" type="text" id="rank_new" size="20">
	  </td>
      <td align="center" bgcolor="#FFFFFF">
      <input name="money_new" type="text" id="money_new" size="20">
      </td>
      <td align="center" bgcolor="#FFFFFF"><input name="check_new" type="checkbox" id="check_new" class="np" value="1">
        增加新等级</td>
    </tr>
    <tr> 
      <td height="24" colspan="4" bgcolor="#F8FCF1">&nbsp;</td>
    </tr>
    <tr> 
      <td height="34" colspan="4" align="center" bgcolor="#FFFFFF">
      	<input name="imageField" type="image" src="img/button_ok.gif" width="60" height="22" border="0" class="np">
      </td>
    </tr>
  </form>
</table>
<?
$dsql->Close();
?>
</body>
</html>
