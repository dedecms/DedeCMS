<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>备份与还原</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language="javascript">
function selAll()
{
	for(i=0;i<document.formbak2.files.length;i++)
	{
		document.formbak2.files[i].checked=true;
	}
}
function selNone()
{
	for(i=0;i<document.formbak2.files.length;i++)
	{
		document.formbak2.files[i].checked=false;
	}
}
function GetSubmit()
{
	for(i=0;i<document.formbak2.files.length;i++)
	{
		if(document.formbak2.files[i].checked)
		{
			document.formbak2.refiles.value+="*"+document.formbak2.files[i].value;
		}
	}
	return true;
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="99%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif" bgcolor="#E7E7E7"> &nbsp;<b>数据备份</b></td>
</tr>
<tr>
    <td height="215" align="center" bgcolor="#FFFFFF"> 
      <table width="96%" border="0" cellspacing="1" cellpadding="0">
      <form name="form1" action="sys_bakdatnew_ok.php">
        <tr> 
          <td width="25%" bgcolor="#F1F2EC"><strong>备份特定数据：</strong></td>
          <td width="75%" bgcolor="#F1F2EC" align="right"> <input type="submit" name="Submit0" value=" " style="width:0"><input type="submit" name="Submit" value="确定备份"></td>
        </tr>    
          <tr> 
            <td height="45" align="right">请选择要备份的表：</td>
            <td>
            <select name="baktable" id="baktable">
			<option value='dede_art'>dede_art</option>
			<option value='dede_feedback'>dede_feedback</option>
			<option value='dede_member'>dede_member</option>
              </select> </td>
          </tr>
          <tr> 
            <td height="25" align="right">存放路径：</td>
            <td><?=$bak_dir?></td>
          </tr>
          <tr> 
            <td height="25" align="right">备份方式：</td>
            <td>
            <input type="radio" name="baktype" value="tid" class="np" checked>
            指定备份ID
            <input type="radio" name="baktype" value="fsize" class="np">
            指定备份文件大小
            <input type="radio" name="baktype" value="stime" class="np">
            指定起始时间
            </td>
          </tr>
          <tr> 
            <td height="25" align="right">指定ID：</td>
            <td>
            开始ID：
            <input type="input" name="tidstart" value="" style="width:50">
			结束ID：
            <input type="input" name="tidend" value="" style="width:50">
            </td>
          </tr>
          <tr> 
            <td height="25" align="right">备份文件大小：</td>
            <td>
            <input type="input" name="filesize" value="2" style="width:50"> (M)
            </td>
          </tr>
          <tr> 
            <td height="25" align="right">指定时间：</td>
            <td>
            开始时间：
            <input type="input" name="timestart" value="<?=strftime("%Y-%m-%d",time())?>" style="width:80">
			结束时间：
            <input type="input" name="timeend" value="<?=strftime("%Y-%m-%d",time())?>" style="width:80">
            (用 xxxx-xx-xx 格式)
            </td>
          </tr>
        </form>
        <tr align="center"> 
          <td colspan="2" bgcolor="#F1F2EC">数据备份说明</td>
        </tr>
        <tr> 
          <td height="32" colspan="2">　　数据按每个表名生成对应的txt文件，内容是SQL的Insert语句，前面加了~符号作行识别，如需在“执行MySQL命令”的操作中运行这些SQL语句，请先将~Insert 
            替换为： Insert，如果你要把网站进行平台转移，需要做的事情是：[1]备份数据；[2]把文件上传到新网站空间，重新运行setup.php，按原来的配置再安装一遍，然后还原数据即可。</td>
        </tr>
        <tr> 
          <td bgcolor="#F1F2EC" colspan="2">&nbsp;</td>
        </tr>
      </table></td>
</tr>
</table>
</body>
</html>