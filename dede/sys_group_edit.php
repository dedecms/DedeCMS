<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Group');
$dsql = new DedeSql(false);
if(empty($dopost)) $dopost = "";
if($dopost=='save')
{
	if($rank==10){
		ShowMsg("超级管理员的权限不允许更改!","sys_group.php");
	  $dsql->Close();
	  exit();
	}
	$purview = "";
	if(is_array($purviews)){
	  foreach($purviews as $p){
		  $purview .= "$p ";
	  }
	  $purview = trim($purview);
  }
	$dsql->ExecuteNoneQuery("Update #@__admintype set typename='$typename',purviews='$purview' where rank='$rank'");
	$dsql->Close();
	ShowMsg("成功更改用户组的权限!","sys_group.php");
	exit();
}
else if($dopost=='del')
{
	$dsql->ExecuteNoneQuery("Delete From #@__admintype where rank='$rank' And system='0';");
  ShowMsg("成功删除一个用户组!","sys_group.php");
	$dsql->Close();
	exit();
}
$groupRanks = Array();
$groupSet = $dsql->GetOne("Select * From #@__admintype where rank='".$rank."'");
$groupRanks = explode(' ',$groupSet['purviews']);

//检查是否已经有此权限
function CRank($n){
	global $groupRanks;
	if(in_array($n,$groupRanks)) return ' checked';
	else  return '';
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>组权限设置</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<center>
<table width="98%" border="0" cellpadding="2" cellspacing="1" bgcolor="#666666" align="center">
<form name='form1' action='sys_group_edit.php' method='post'> 
<input type='hidden' name='dopost' value='save'>
  <tr>
    <td height="23" background="img/tbg.gif"><b><a href='sys_group.php'>系统用户组管理</a>&gt;&gt;更改用户组：</b></td>
</tr>
  <tr> 
    <td valign="top" bgcolor="#FFFFFF" align="center"> 
      <table width="98%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="9%" height="30">组名称：</td>
          <td width="91%"> <input name="typename" type="text" id="typename" value="<?=$groupSet['typename']?>"> 
          </td>
        </tr>
        <tr> 
          <td width="9%" height="30">级别值：</td>
          <td width="91%">
          	<input name="rank" type="hidden" id="rank" value="<?=$groupSet['rank']?>">
          	<?=$groupSet['rank']?>
          </td>
        </tr>
        <?
        $start = 0;
        $k = 0;
        $gouplists = file(dirname(__FILE__).'/inc/grouplist.txt');
        foreach($gouplists as $line)
        {
        	$line = trim($line);
        	if($line=="") continue;
        	if(ereg("^>>",$line))
        	{
        		if($start>0) echo "        	 </td></tr>\r\n";
        		$start++;
        		$lhead = "
        	 <tr> 
           <td height='25' colspan='2' bgcolor='#F9FAF3'>{$start}、".str_replace('>>','',$line)."</td></tr>
           <tr><td height='25' colspan='2'>
        		"; 
        		echo $lhead;
        	}
        	else if(ereg("^>",$line))
        	{
        		$ls = explode('>',$line);
        		$tag = $ls[1];
        		$tagname = str_replace('[br]','<br>',$ls[2]);
        		echo "          	<input name='purviews[]' type='checkbox' class='np' id='purviews$k' value='$tag'".CRank($tag).">$tagname\r\n";
        	  $k++;
        	}
        }
        $start++;
        ?>
        </td>
        </tr>
        <tr> 
           <td height='25' colspan='2' bgcolor='#F9FAF3'><?=$start?>、插件权限</td>
         </tr>
        <tr>
        <td height='25' colspan='2'>
         <?
         $l = 0;
         $dsql->SetQuery('Select plusname From #@__plus');
         $dsql->Execute();
         while($row=$dsql->GetObject()){
         	 echo "          	<input name='purviews[]' type='checkbox' class='np' id='purviews$k' value='plus_{$row->plusname}'".CRank("plus_{$row->plusname}").">{$row->plusname}\r\n";
        	 $k++;
        	 $l++;
        	 if($l%4==0) echo "<br>";
         }
         ?>  	
        </td>
        </tr>
        <tr> 
          <td height="50" align="center">&nbsp;</td>
          <td height="50"><input  class="np" name="imageField" type="image" src="img/button_save.gif" width="60" height="22" border="0">
          </td>
        </tr>
      </table>
    </td>
</tr>
</form>
</table>
</center>
<?
$dsql->Close();
?>
</body>
</html>