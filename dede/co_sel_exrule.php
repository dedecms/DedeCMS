<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('co_AddNote');
if(empty($dopost)) $dopost = "";
$dsql = new DedeSql(false);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>选择数据规则模型</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <form name="form1" action="co_add.php" method="get">
    <tr> 
      <td width="100%" height="28" colspan="3" background='img/tbg.gif'> <strong>选择数据规则模型</strong> 
      </td>
    </tr>
    <tr> 
      <td height="34" colspan="3" bgcolor="#FFFFFF">
	  <select name="exrule" id="exrule" style="width:200px">
      <?
	  $dsql->SetQuery("Select aid,rulename From #@__co_exrule order by aid asc");
	  $dsql->Execute();
	  while($row = $dsql->GetObject())
	  {
	     echo "<option value='{$row->aid}'>{$row->rulename}</option>";
	  }
	  ?>
	  </select>
	  </td>
    </tr>
    <tr> 
      <td height="24" colspan="3" bgcolor="#F8FCF1"> 
        <input name="imageField" class="np" type="image" src="img/button_ok.gif" width="60" height="22" border="0"> 
      </td>
    </tr>
  </form>
</table>
<?
$dsql->Close();
?>
</body>
</html>
