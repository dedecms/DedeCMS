<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Passport');
if(!function_exists('file_get_contents')){
	ShowMsg("你的系统不支持函数：file_get_contents<br><br> 不能使用 Dede 通行证接口！","javascript:;");
	exit();
}
if(empty($action)) $action = '';
if($action=='save'){
	$dsql = new DedeSql(false);
	$dsql->ExecuteNoneQuery("Delete From #@__syspassport ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_isopen','cfg_pp_isopen'); ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_regurl','cfg_pp_regurl'); ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_loginurl','cfg_pp_loginurl'); ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_exiturl','cfg_pp_exiturl'); ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_editsafeurl','cfg_pp_editsafeurl'); ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_name','cfg_pp_name'); ");
	$dsql->ExecuteNoneQuery("Insert into #@__syspassport Values('$pp_indexurl','cfg_pp_indexurl'); ");
	$dsql->Close();
	$fp = fopen(dirname(__FILE__)."/../include/config_passport.php","w") or die("写入文件 ../include/config_passport.php 失败!");
	fwrite($fp,'<'.'?php ');
	fwrite($fp,"\r\n");
	foreach($GLOBALS as $k=>$v){
		if(ereg('^pp_',$k)){
			$v = str_replace("'","`",stripslashes($v));
			fwrite($fp,'$cfg_'.$k." = '".$v."';\r\n");
		}
	}
	fwrite($fp,'?'.'>');
	fclose($fp);
	ShowMsg("成功更改通行证设置！","sys_passport.php");
	exit();
}
$dsql = new DedeSql(false);
$dsql->SetQuery("Select * From #@__syspassport ");
$dsql->Execute();
while($row = $dsql->GetArray()){ $$row['varname'] = $row['value']; }
$dsql->Close();
?>
<script language='javascript' src='main.js'></script>
<script language="JavaScript">
var basehost = "<?php echo $cfg_basehost?>";

templets = new Array();

templets[0]  = '__dz55;论坛;/bbs;/bbs/logging.php?action=login;';
templets[0] += '/bbs/logging.php?action=logout;/bbs/register.php;/bbs/memcp.php?action=profile';

templets[1]  = '__pw53;论坛;/bbs;/bbs/login.php;';
templets[1] += '/bbs/login.php?action=quit;/bbs/register.php;/bbs/profile.php?action=modify';

templets[2]  = '__dvbbs71;论坛;/bbs;/bbs/login.asp;';
templets[2] += '/bbs/logout.asp;/bbs/reg.asp;/bbs/modifyadd.asp';

function ShowTemplet(sysname){
   var tmppos = 0;
   for(var i=0;i<templets.length;i++){
     if(templets[i].indexOf(sysname)>0){ tmppos = i; break; }
   }
   stemplets = templets[tmppos].split(';');
   $Obj('pp_name').value = stemplets[1];
   $Obj('pp_indexurl').value = basehost+stemplets[2];
   $Obj('pp_loginurl').value = basehost+stemplets[3];
   $Obj('pp_exiturl').value = basehost+stemplets[4];
   $Obj('pp_regurl').value = basehost+stemplets[5];
   $Obj('pp_editsafeurl').value = basehost+stemplets[6];
}

</script>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>通行证设置</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#98CAEF">
  <form action="sys_passport.php" method="post">
    <input type="hidden" name="action" value="save">
    <tr> 
      <td height="26" colspan="2" bgcolor="#FFFFFF" background="img/tbg.gif"> 
        <b>DedeCms系统配置参数</b> - <strong>通行证设置</strong>(反向整合接口) </td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="30" colspan="2"><img src="img/help.gif" width="16" height="16">
      	启用了通行证后，会员注册、登录、退出、更改密码将不使用Dedecms的会员系统，如果官方没有提供整某系统的接口文件，请参考本系统的通行证接口的说明文档，在第三方系统的这几个功能模块中加入调用本系统提供的远程API接口即可，Dedecms的反向接口允许论坛与主站在不同的服务器，但必须用相同的主域名，即是：bbs.abc.com 和 www.abc.com 这样的关系。
      </td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td width="24%" height="30">是否启用通行证：<br> </td>
      <td width="76%">
      	<input name="pp_isopen" type="radio" value="1" class="np"<?php  if($cfg_pp_isopen==1) echo " checked"; ?>>
        启用 
        <input name="pp_isopen" type="radio"  class="np" value="0"<?php  if($cfg_pp_isopen==0) echo " checked"; ?>>
        不启用 </td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="30">通行证密钥：</td>
      <td>
      	<?php echo $cfg_cookie_encode?>
        <br>
      （此参数即是系统参数里的cookie加密变量，在被整合系统的接口文件中需要使用与此参数一致的密码）
      </td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="30">常用系统：</td>
      <td> 
	   ·<a href="#" onClick="ShowTemplet('_dz55')"><u>DISCUZ5.5</u></a>
	   ·<a href="#" onClick="ShowTemplet('_pw53')"><u>PHPWIND5.3/PHPWIND4.3.2</u></a> 
	   ·<a href="#" onClick="ShowTemplet('_dvbbs71')"><u>DVBBS-ASP 7.1</u></a>
      </td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="30">整合系统名称：</td>
      <td>
      	<input name="pp_name" type="text" id="pp_name" style="width:60%" value="<?php echo $cfg_pp_name?>">
      </td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="30">整合系统的主页：</td>
      <td>
      	<input name="pp_indexurl" type="text" id="pp_indexurl" style="width:60%" value="<?php echo $cfg_pp_indexurl?>">
      </td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="30">会员注册网址：</td>
      <td>
      	<input name="pp_regurl" type="text" id="pp_regurl" style="width:60%" value="<?php echo $cfg_pp_regurl?>">
      </td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="30">会员登陆网址：</td>
      <td>
      	<input name="pp_loginurl" type="text" id="pp_loginurl" style="width:60%" value="<?php echo $cfg_pp_loginurl?>">
      </td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="30">会员退出网址：</td>
      <td>
      	<input name="pp_exiturl" type="text" id="pp_exiturl" style="width:60%" value="<?php echo $cfg_pp_exiturl?>">
      </td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="30">更改密码网址：</td>
      <td>
      	<input name="pp_editsafeurl" type="text" id="pp_editsafeurl" style="width:60%" value="<?php echo $cfg_pp_editsafeurl?>"> 
      </td>
    </tr>
    <tr bgcolor="#CCEDFD"> 
      <td height="37" colspan="2"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="15%">&nbsp;</td>
            <td width="85%">
            	<input type="submit" name="Submit" value="保存设置" class="nbt">
              &nbsp;
              <input type="reset" name="Submit2" value="重置" class="nbt">
             </td>
          </tr>
        </table></td>
    </tr>
  </form>
</table>
</body>
</html>
