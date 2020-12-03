<?
require(dirname(__FILE__)."/config.php");
if(empty($dopost)) $dopost = "";
SetPageRank(5);
//////////////////////////////////////////
if($dopost=="save")
{
	require_once(dirname(__FILE__)."/../include/inc_arcpart_view.php");
	$uptime = time();
	$body = str_replace('&quot;','\\"',$body);
	$filename = ereg_replace("^/","",$nfilename);
	
	$inQuery = "
	 Insert Into #@__sgpage(title,ismake,filename,uptime,body)
	 Values('$title','$ismake','$filename','$uptime','$body');
	";
	$dsql = new DedeSql(false);
	$dsql->SetQuery($inQuery);
	if(!$dsql->ExecuteNoneQuery())
	{
		$dsql->Close();
		ShowMsg("增加页面失败，请检查长相是否有问题！","-1");
	  exit();
	}
	$dsql->Close();
	
	$filename = $cfg_basedir.$cfg_cmspath."/".$filename;
	
	if($ismake==1)
	{
	  $pv = new PartView();
    $pv->SetTemplet(stripslashes($body),"string");
    $pv->SaveToHtml($filename);
    $pv->Close();
  }
  else
  {
  	$fp = fopen($filename,"w") or die("创建：{$filename} 失败，可能是没有权限！");
  	fwrite($fp,stripslashes($body));
  	fclose($fp);
  }
	ShowMsg("成功增加一个页面！","templets_one.php");
	exit();
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>增加自定义页面</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language="javascript">
function checkSubmit()
{
	if(document.form1.title.value=="")
	{
		alert("页面名称不能为空！");
		document.form1.title.focus();
		return false;
	}
	if(document.form1.nfilename.value=="")
	{
		alert("文件名不能为空！");
		document.form1.nfilename.focus();
		return false;
	}
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
<tr>
    <td height="19" background="img/tbg.gif">
    	<b><a href="templets_one.php"><u>单独页面管理</u></a></b>&gt;&gt;增加新页面
    </td>
</tr>
<tr>
    <td height="200" bgcolor="#FFFFFF" valign="top">
	<table width="100%" border="0" cellspacing="4" cellpadding="2">
        <form action="templets_one_add.php" method="post" name="form1" onSubmit="return checkSubmit()">
          <input type='hidden' name='dopost' value='save'>
          <tr> 
            <td width="15%" height="24" align="center">页面名称：</td>
            <td> 
              <input name="title" type="text" id="title" size="30"></td>
          </tr>
          <tr> 
            <td height="24" align="center" bgcolor="#F3FBEC">生成文件名：</td>
            <td bgcolor="#F3FBEC"><input name="nfilename" type="text" id="nfilename" value="newfile.html" size="30">
              （相对于CMS安装目录）</td>
          </tr>
          <tr> 
            <td height="24" align="center">是否编译：</td>
            <td><input name="ismake" type="radio" value="1" checked>
              含模板标记，要编译 
              <input type="radio" name="ismake" value="0">
              不含模板标记，不需要编译</td>
          </tr>
          <tr> 
            <td height="24" colspan="2" bgcolor="#F3FBEC">文件内容：</td>
          </tr>
          <tr> 
            <td height="80" colspan="2" align="center"> 
              <?
	GetEditor("body","","500","Default","print","true");
	?>
            </td>
          </tr>
          <tr> 
            <td height="53" align="center">&nbsp;</td>
            <td><input name="imageField" type="image" src="img/button_ok.gif" width="60" height="22" border="0"></td>
          </tr>
        </form>
      </table>
	 </td>
</tr>
</table>
</body>
</html>