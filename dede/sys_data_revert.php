<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Data');
$bkdir = dirname(__FILE__)."/".$cfg_backup_dir;
$filelists = Array();
$dh = dir($bkdir);
$structfile = "没找到数据结构文件";
while($filename=$dh->read()){
	if(!ereg('txt$',$filename)) continue;
	if(ereg('tables_struct',$filename)) $structfile = $filename;
	else if( filesize("$bkdir/$filename") >0 ) $filelists[] = $filename;
}
$dh->Close();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>数据库维护--数据还原</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language="javascript">
//获得选中文件的数据表
function getCheckboxItem(){
	 var myform = document.form1;
	 var allSel="";
	 if(myform.bakfile.value) return myform.bakfile.value;
	 for(i=0;i<myform.bakfile.length;i++)
	 {
		 if(myform.bakfile[i].checked){
			 if(allSel=="")
				 allSel=myform.bakfile[i].value;
			 else
				 allSel=allSel+","+myform.bakfile[i].value;
		 }
	 }
	 return allSel;	
}
//反选
function ReSel(){
	var myform = document.form1;
	for(i=0;i<myform.bakfile.length;i++){
		if(myform.bakfile[i].checked) myform.bakfile[i].checked = false;
		else myform.bakfile[i].checked = true;
	}
}
//全选
function SelAll(){
	var myform = document.form1;
	for(i=0;i<myform.bakfile.length;i++){
		myform.bakfile[i].checked = true;
	}
}
//取消
function NoneSel(){
	var myform = document.form1;
	for(i=0;i<myform.bakfile.length;i++){
		myform.bakfile[i].checked = false;
	}
}
//
function checkSubmit()
{
	var myform = document.form1;
	myform.bakfiles.value = getCheckboxItem();
	return true;
}

</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="99%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr> 
    <td height="19" colspan="4" background="img/tbg.gif" bgcolor="#E7E7E7"> <table width="96%" border="0" cellspacing="1" cellpadding="1">
        <tr> 
          <td width="24%"><strong>数据还原</strong></td>
          <td width="76%" align="right"> <b><a href="sys_data.php"><u>数据备份</u></a></b> 
            | <b><a href="sys_sql_query.php"><u>SQL命令行工具</u></a></b> </td>
        </tr>
      </table></td>
  </tr>
  <form name="form1" onSubmit="checkSubmit()" action="sys_data_done.php?dopost=redat" method="post" target="stafrm">
    <input type='hidden' name='bakfiles' value=''>
    <tr bgcolor="#F7F8ED"> 
      <td height="24" colspan="4" valign="top">
      	<strong>发现的备份文件：</strong>
        <? if(count($filelists)==0) echo " 没找到任何备份文件... "; ?>
      </td>
    </tr>
    <?
    for($i=0;$i<count($filelists);$i++)
    {
    	echo "<tr  bgcolor='#FFFFFF' align='center' height='24'>\r\n";
      $mtd = "<td width='10%'>
<input name='bakfile' id='bakfile' type='checkbox' class='np' value='".$filelists[$i]."' checked> 
</td>
<td width='40%'>".$filelists[$i]."</td>";
      echo $mtd;
      if(isset($filelists[$i+1])){
      	$i++;
      	$mtd = "<td width='10%'>
<input name='bakfile' id='bakfile' type='checkbox' class='np' value='".$filelists[$i]."' checked> 
</td>
<td width='40%'>".$filelists[$i]."</td>";
        echo $mtd;
      }else{
      	echo "<td></td><td></td>";
      }
      echo "</tr>\r\n";
    }
    ?>
    <tr align="center" bgcolor="#FDFDEA"> 
      <td height="24" colspan="4"> &nbsp; 
        <input name="b1" type="button" id="b1" onclick="SelAll()" value="全选"> 
        &nbsp; <input name="b2" type="button" id="b2" onclick="ReSel()" value="反选"> 
        &nbsp; <input name="b3" type="button" id="b3" onclick="NoneSel()" value="取消">
        &nbsp; </td>
    </tr>
	<tr bgcolor="#F7F8ED"> 
      <td height="24" colspan="4" valign="top"><strong>附加参数：</strong></td>
    </tr>
    <tr  bgcolor="#FFFFFF"> 
      <td height="24" colspan="4"> 
        <input name="structfile" type="checkbox" class="np" id="structfile" value="<?=$structfile?>" checked>
        还原表结构信息(<?=$structfile?>) 
        <input name="delfile" type="checkbox" class="np" id="delfile" value="1">
        还原后删除备份文件 </td>
    </tr>
    <tr bgcolor="#E3F4BB"> 
      <td height="33" colspan="4">
      	 &nbsp; <input type="submit" name="Submit" value="开始还原数据">
      </td>
    </tr>
  </form>
  <tr bgcolor="#F7F8ED"> 
    <td height="24" colspan="4"><strong>进行状态： </strong></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td height="180" colspan="4"> <iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe> 
    </td>
  </tr>
</table>
</body>
</html>