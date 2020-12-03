<?
require_once(dirname(__FILE__)."/config.php");
if(!isset($dopost)) $dopost = "";
if($dopost=='save'){
	 $ConfigFile = dirname(__FILE__)."/../include/config_passport.php";
	 $vars = array('cfg_pp_need','cfg_pp_encode','cfg_pp_login','cfg_pp_exit','cfg_pp_reg');
 $configstr = "";
 foreach($vars as $v){
 	 ${$v} = str_replace("'","",${$v});
 	 $configstr .= "\${$v} = '".str_replace("'","",stripslashes(${'edit___'.$v}))."';\r\n";
 }
 $configstr = '<'.'?'."\r\n".$configstr.'?'.'>';
 $fp = fopen($ConfigFile,"w") or die("写入文件 $ConfigFile 失败，请检查权限！");
 fwrite($fp,$configstr);
 fclose($fp);
 echo "<script>alert('修改通行证配置成功！');window.location='sys_passport.php?".time()."';</script>\r\n";
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>通行证设置</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="2" cellspacing="1" bgcolor="#666666" align="center">
<tr>
<td height="23" background="img/tbg.gif"> &nbsp;<b>通行证设置：</b> </td>
</tr>
<tr> 
<td bgcolor="#FFFFFF">
<table width="100%" border="0" cellspacing="1" cellpadding="1">
<form action="sys_passport.php" method="post" name="form1">
<input type="hidden" name="dopost" value="save">
<tr align="center" bgcolor="#E6F7CA" height="25"> 
<td width="25%">参数说明</td>
<td width="75%">参数值</td>
</tr>
<tr align="center" height="25" bgcolor="#F4FCDC"> 
<td bgcolor="#FFFFFF"> 是否使用通行证： </td>
<td align="left" bgcolor="#FFFFFF">
	<input type='radio' class='np' name='edit___cfg_pp_need' value='是'<?if($cfg_pp_need=='是') echo " checked";?>>
是 
<input type='radio' class='np' name='edit___cfg_pp_need' value='否'<?if($cfg_pp_need=='否') echo " checked";?>>
否 </td>
</tr>
<tr align="center" height="25" bgcolor="#F4FCDC"> 
<td> 通行证加密码： </td>
<td align="left">
	<?
	if($cfg_pp_encode=='') $cfg_pp_encode = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(1000,9999).chr(mt_rand(ord('A'),ord('Z')));
	?>
	<input type='text' name='edit___cfg_pp_encode' id='edit___cfg_pp_encode' style='width:80%' value='<?=$cfg_pp_encode?>'>
</td>
</tr>
<tr align="center" height="25" bgcolor="#FFFFFF"> 
<td> 登陆通行证网址： </td>
<td align="left"> 
<input type='text' name='edit___cfg_pp_login' id='edit___cfg_pp_login' style='width:80%' value='<?=$cfg_pp_login?>'></td>
</tr>
<tr align="center" height="25" bgcolor="#F4FCDC">
<td>退出通行证网址： </td>
<td align="left"><input name="edit___cfg_pp_exit" type='text' id="edit___cfg_pp_exit" style='width:80%' value='<?=$cfg_pp_exit?>'></td>
</tr>
<tr align="center" height="25" bgcolor="#FFFFFF"> 
<td>注册通行证帐号网址：</td>
<td align="left"> 
<input type='text' name='edit___cfg_pp_reg' id='edit___cfg_pp_reg' style='width:80%' value='<?=$cfg_pp_reg?>'> 
</td>
</tr>
<tr bgcolor="#F3FFDD"> 
<td height="50" colspan="2"> <table width="98%" border="0" cellspacing="1" cellpadding="1">
<tr> 
<td width="11%">&nbsp;</td>
<td width="11%"><input name="imageField" type="image" src="img/button_ok.gif" width="60" height="22" border="0" class="np"></td>
<td width="78%"><img src="img/button_reset.gif" width="60" height="22" style="cursor:hand" onclick="document.form1.reset()"></td>
</tr>
</table></td>
</tr>
</form>
</table>
</td>
</tr>
</table>
<center>
</center>
</body>
</html>