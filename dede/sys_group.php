<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Group');
if(empty($dopost)) $dopost = "";
$dsql = new DedeSql(false);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>系统用户组管理</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#98CAEF" align="center">
  <form name="form1" action="content_att.php" method="post">
    <input type="hidden" name="dopost" value="save">
    <tr> 
      <td height="28" colspan="3" background='img/tbg.gif'>
       <table width="96%" border="0" cellspacing="1" cellpadding="1">
        <tr> 
          <td width="24%"><b>系统用户组管理</b> </td>
          <td width="76%" align="right"><b>
          	<a href="sys_group_add.php"><u>增加一个用户组</u></a>
          	|
            <a href="sys_admin_user.php"><u>管理系统用户</u></a>
          	</b>
          </td>
        </tr>
      </table>
      </td>
    </tr>
    <tr bgcolor="#FDFEE9" align="center" > 
      <td width="20%" height="24">Rank</td>
      <td width="45%">组名称</td>
      <td width="35%">管理</td>
    </tr>
    <?php 
	$dsql->SetQuery("Select rank,typename,system From #@__admintype");
	$dsql->Execute();
	while($row = $dsql->GetObject())
	{
	?>
   <tr align="center" bgcolor="#FFFFFF"> 
      <td height="24"> 
        <?php echo $row->rank?>
      </td>
      <td height="24">
      	<?php echo $row->typename?>
      </td>
      <td>
        <a href='sys_group_edit.php?rank=<?php echo $row->rank?>'>[权限设定]</a>
      	<a href='sys_admin_user.php?rank=<?php echo $row->rank?>'>[组用户]</a>
      <?php if($row->system==0){?><a href='sys_group_edit.php?dopost=del&rank=<?php echo $row->rank?>'>[删除组]</a><?php }?>
      </td>
    </tr>
    <?php 
	}
	?>
    <input type="hidden" name="idend" value="<?php echo $k?>">
    <tr> 
      <td height="24" colspan="3" bgcolor="#F8FCF1">&nbsp;</td>
    </tr>
  </form>
</table>
<?php 
$dsql->Close();
?>
</body>
</html>
