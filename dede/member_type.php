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
   	  $aid = ${'ID_'.$startID};
   	  $pname =   ${'pname_'.$startID};
   	  $rank =    ${'rank_'.$startID};
   	  $money =   ${'money_'.$startID};
   	  $exptime = ${'exptime_'.$startID};
   	  if(isset(${'check_'.$startID})){
   	  	if($pname!='') $query = "update #@__member_type set pname='$pname',money='$money',rank='$rank',exptime='$exptime' where aid='$aid'";
   	  }
   	  else{
   	  	$query = "Delete From #@__member_type where aid='$aid' ";
   	  }
   	  if($query!=''){
   	  	$dsql->ExecuteNoneQuery($query);
   	  } 
   }
   //增加新记录
   if(isset($check_new) && $pname_new!=''){
   	 	$query = "Insert Into #@__member_type(rank,pname,money,exptime) Values('{$rank_new}','{$pname_new}','{$money_new}','{$exptime_new}');";
   	  $dsql->ExecuteNoneQuery($query);
   }
   echo "<script> alert('成功更新会员产品分类表！'); </script>";
}
$arcranks = array();
$dsql->SetQuery("Select * From #@__arcrank where rank>10 ");
$dsql->Execute();
while($row=$dsql->GetArray()){ $arcranks[$row['rank']] = $row['membername']; }
$times = array();
$dsql->SetQuery("Select * From #@__member_time order by mday asc ");
$dsql->Execute();
while($row=$dsql->GetArray()){ $times[$row['mday']] = $row['tname']; }
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>会员类型管理</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="2" cellspacing="1" bgcolor="#98CAEF" align="center">
  <form name="form1" action="member_type.php" method="post">
    <input type="hidden" name="dopost" value="save">
    <tr> 
      <td height="24" colspan="5" background='img/tbg.gif'><table width="98%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="30%"> <strong><a href="co_main.php"><u>会员管理</u></a> &gt; 
              会员产品分类：</strong> </td>
            <td align="right"> <input type="button" name="ss1" value="会员级别管理" style="width:90px;margin-right:6px" onclick="location='member_rank.php';" class='nbt'> 
              <input type="button" name="ss2" value="会员业务记录" style="width:90px" onclick="location='member_operations.php';" class='nbt'> 
            </td>
          </tr>
        </table> </td>
    </tr>
    <tr bgcolor="#FDFEE9"> 
      <td width="22%" height="24" align="center" valign="top">产品名称</td>
      <td width="23%" align="center" valign="top">会员级别</td>
      <td width="21%" align="center">产品价格</td>
      <td width="18%" align="center">会员期限(天)</td>
      <td width="16%" align="center">状态</td>
    </tr>
    <?php 
	$dsql->SetQuery("Select * From #@__member_type");
	$dsql->Execute();
	$k=0;
	while($row = $dsql->GetObject())
	{
	  $k++;
	?>
    <input type="hidden" name="ID_<?php echo $k?>" value="<?php echo $row->aid?>">
    <tr align="center" bgcolor="#FFFFFF"> 
      <td height="24" valign="top"> <input name="pname_<?php echo $k?>" value="<?php echo $row->pname?>" type="text" id="pname_<?php echo $k?>" style="width:90%"> 
      </td>
      <td height="24" valign="top"> <select name='rank_<?php echo $k?>' id='rank_<?php echo $k?>' style='width:90%'>
          <?php 
	   	foreach($arcranks as $kkk=>$vvv){
	   		if($row->rank==$kkk) echo "    <option value='{$kkk}' selected>{$vvv}</option>\r\n";
	   		else echo "    <option value='{$kkk}'>{$vvv}</option>\r\n";
	   	} 
	   	?>
        </select> </td>
      <td> <input name="money_<?php echo $k?>" value="<?php echo $row->money?>" type="text" id="money_<?php echo $k?>" style="width:80%">
        (元) </td>
      <td> <select name='exptime_<?php echo $k?>' id='exptime_<?php echo $k?>' style='width:90%'>
          <?php 
	   	foreach($times as $kkk=>$vvv){
	   		if($row->exptime==$kkk) echo "    <option value='{$kkk}' selected>{$vvv}</option>\r\n";
	   		else echo "    <option value='{$kkk}'>{$vvv}</option>\r\n";
	   	} 
	   	?>
        </select> </td>
      <td> <input name="check_<?php echo $k?>" type="checkbox" id="check_<?php echo $k?>" class="np" value="1" checked>
        保留 </td>
    </tr>
    <?php 
	}
	?>
    <input type="hidden" name="idend" value="<?php echo $k?>">
    <tr bgcolor="#F8FCF1"> 
      <td height="24" colspan="5" valign="top"><strong>新增一个会员产品类型：</strong></td>
    </tr>
    <tr height="24" align="center" bgcolor="#FFFFFF"> 
      <td valign="top"> <input name="pname_new" type="text" id="pname_new" style="width:90%"> 
      </td>
      <td valign="top"> <select name='rank_new' id='rank_new' style='width:90%'>
          <?php 
	   	foreach($arcranks as $kkk=>$vvv){
	   		echo "    <option value='{$kkk}'>{$vvv}</option>\r\n";
	   	} 
	   	?>
        </select> </td>
      <td valign="top"> <input name="money_new" type="text" id="money_new" style='width:80%' value="100">
        (元) </td>
      <td valign="top"> <select name='exptime_new' id='exptime_new' style='width:90%'>
          <?php 
	   	foreach($times as $kkk=>$vvv){
	   		echo "    <option value='{$kkk}'>{$vvv}</option>\r\n";
	   	} 
	   	?>
        </select> </td>
      <td align="center" bgcolor="#FFFFFF"> <input name="check_new" type="checkbox" class="np" id="check_new" value="1" checked>
        新增 </td>
    </tr>
    <tr> 
      <td height="24" colspan="5" bgcolor="#F8FCF1">&nbsp;</td>
    </tr>
    <tr> 
      <td height="34" colspan="5" align="center" bgcolor="#FFFFFF"> <input name="imageField" type="image" src="img/button_ok.gif" width="60" height="22" border="0" class="np"> 
      </td>
    </tr>
  </form>
</table>
<?php 
$dsql->Close();
?>
</body>
</html>
