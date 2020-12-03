<?php 
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
   for(;$startID<=$endID;$startID++){
   	  $query = '';
   	  $tid = ${'ID_'.$startID};
   	  $pname =   ${'pname_'.$startID};
   	  $money =    ${'money_'.$startID};
   	  $num =   ${'num_'.$startID};
   	  if(isset(${'check_'.$startID})){
   	  	if($pname!=''){
   	  		$query = "update #@__moneycard_type set pname='$pname',money='$money',num='$num' where tid='$tid'";
   	  		$dsql->ExecuteNoneQuery($query);
   	  		$query = "update #@__moneycard_record set money='$money',num='$num' where ctid='$tid' ; ";
   	  		$dsql->ExecuteNoneQuery($query);
   	  	}
   	  }
   	  else{
   	  	$query = "Delete From #@__moneycard_type where tid='$tid' ";
   	  	$dsql->ExecuteNoneQuery($query);
   	  	$query = "Delete From #@__moneycard_record where ctid='$tid' And isexp<>-1 ; ";
   	  	$dsql->ExecuteNoneQuery($query);
   	  }
   }
   //增加新记录
   if(isset($check_new) && $pname_new!=''){
   	 	$query = "Insert Into #@__moneycard_type(num,pname,money) Values('{$num_new}','{$pname_new}','{$money_new}');";
		  $dsql->ExecuteNoneQuery($query);
   }
   echo "<script> alert('成功更新点卡产品分类表！'); </script>";
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>点卡产品分类</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="2" cellspacing="1" bgcolor="#98CAEF" align="center">
  <form name="form1" action="member_card_type.php" method="post">
    <input type="hidden" name="dopost" value="save">
    <tr> 
      <td height="24" colspan="4" background='img/tbg.gif'> &nbsp;<strong><a href="member_main.php"><u>会员管理</u></a> 
        > 点卡产品分类： </strong></td>
    </tr>
    <tr bgcolor="#FDFEE9"> 
      <td width="26%" height="24" align="center" valign="top">产品名称</td>
      <td width="27%" align="center" valign="top">点数</td>
      <td width="30%" align="center">价格</td>
      <td width="17%" align="center">状态</td>
    </tr>
    <?php 
	$dsql->SetQuery("Select * From #@__moneycard_type");
	$dsql->Execute();
	$k=0;
	while($row = $dsql->GetObject())
	{
	  $k++;
	?>
    <input type="hidden" name="ID_<?php echo $k?>" value="<?php echo $row->tid?>">
    <tr align="center" bgcolor="#FFFFFF"> 
      <td height="24" valign="top"> <input name="pname_<?php echo $k?>" value="<?php echo $row->pname?>" type="text" id="pname_<?php echo $k?>" style="width:90%"> 
      </td>
      <td height="24" valign="top"> <input name="num_<?php echo $k?>" value="<?php echo $row->num?>" type="text" id="num_<?php echo $k?>" style="width:80%"></td>
      <td><input name="money_<?php echo $k?>" value="<?php echo $row->money?>" type="text" id="money_<?php echo $k?>" style="width:80%">
       (元)
	   </td>
      <td>
	  <input name="check_<?php echo $k?>" type="checkbox" id="check_<?php echo $k?>" class="np" value="1" checked>
        保留
	   </td>
    </tr>
    <?php 
	}
	?>
    <input type="hidden" name="idend" value="<?php echo $k?>">
    <tr bgcolor="#F8FCF1"> 
      <td height="24" colspan="4" valign="top"><strong>新增一个会员产品类型：</strong></td>
    </tr>
    <tr height="24" align="center" bgcolor="#FFFFFF"> 
      <td valign="top"> <input name="pname_new" type="text" id="pname_new" style="width:90%"> 
      </td>
      <td valign="top"> <input name="num_new" value="100" type="text" id="num_new" style="width:80%"></td>
      <td valign="top"> <input name="money_new" type="text" id="money_new" style='width:80%' value="30">
        (元) </td>
      <td align="center" bgcolor="#FFFFFF">
	  <input name="check_new" type="checkbox" class="np" id="check_new" value="1" checked>
        新增 </td>
    </tr>
    <tr> 
      <td height="24" colspan="4" bgcolor="#F8FCF1">&nbsp;</td>
    </tr>
    <tr> 
      <td height="34" colspan="4" align="center" bgcolor="#FFFFFF"> <input name="imageField" type="image" src="img/button_ok.gif" width="60" height="22" border="0" class="np"> 
      </td>
    </tr>
  </form>
</table>
<?php 
$dsql->Close();
?>
</body>
</html>
