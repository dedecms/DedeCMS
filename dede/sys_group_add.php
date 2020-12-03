<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Group');
if(!empty($dopost)){
   $dsql = new DedeSql(false);
   $row = $dsql->GetOne("Select * From #@__admintype where rank='".$rankid."'");
   if(is_array($row)){
   	  ShowMsg("你所创建的组别的级别值已存在，不允许重复!","-1");
	    $dsql->Close();
	    exit();
   }
   $AllPurviews = "";
   if(is_array($purviews)){
   	 foreach($purviews as $pur){
   	 	 $AllPurviews = $pur.' ';
   	 }
   	 $AllPurviews = trim($AllPurviews);
   }
   $dsql->ExecuteNoneQuery("INSERT INTO #@__admintype(rank,typename,system,purviews) VALUES ('$rankid','$groupname', 0, '$AllPurviews');");
   ShowMsg("成功创建一个新的用户组!","sys_group.php");
	 $dsql->Close();
	 exit();
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
<table width="98%" border="0" cellpadding="2" cellspacing="1" bgcolor="#98CAEF" align="center">
<form name='form1' action='sys_group_add.php' method='post'> 
<input type='hidden' name='dopost' value='save'>
  <tr>
    <td height="23" background="img/tbg.gif"><b><a href='sys_group.php'>系统用户组管理</a>&gt;&gt;增加用户组：</b></td>
</tr>
  <tr> 
    <td valign="top" bgcolor="#FFFFFF" align="center"> 
      <table width="98%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="9%" height="30">组名称：</td>
          <td width="91%"> <input name="groupname" type="text" id="groupname"> 
          </td>
        </tr>
        <tr> 
          <td width="9%" height="30">级别值：</td>
          <td width="91%"> <input name="rankid" type="text" id="rankid" size="6">
            （数字，系统已占用的级别值：
            <?php 
          	$dsql = new DedeSql(false);
          	$dsql->SetQuery("Select rank From #@__admintype");
          	$dsql->Execute();
          	while($row = $dsql->GetObject()) echo '<font color=red>'.$row->rank.'</font>、';
          	$dsql->Close();
          	?>
            ） </td>
        </tr>
        <?php 
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
        		echo "          	<input name='purviews[]' type='checkbox' class='np' id='purviews$k' value='$tag'>$tagname\r\n";
        	  $k++;
        	}
        }
        $start++;
        ?>
        </td>
        </tr>
        <tr> 
           <td height='25' colspan='2' bgcolor='#F9FAF3'><?php echo $start?>、插件权限</td>
         </tr>
        <tr>
        <td height='25' colspan='2'>
         <?php 
         $l = 0;
         $dsql = new DedeSql(false);
         $dsql->SetQuery('Select plusname From #@__plus');
         $dsql->Execute();
         while($row=$dsql->GetObject()){
         	 echo "          	<input name='purviews[]' type='checkbox' class='np' id='purviews$k' value='plus_{$row->plusname}'>{$row->plusname}\r\n";
        	 $k++;
        	 $l++;
        	 if($l%4==0) echo "<br>";
         }
         $dsql->Close();
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
</body>
</html>