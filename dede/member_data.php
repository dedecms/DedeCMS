<?php 
require_once(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title> 数据转换与导入</title>
<script language='javascript' src='main.js'></script>
<script language='javascript' src='../include/dedeajax2.js'></script>
<link href="base.css" rel="stylesheet" type="text/css">
<script language='javascript'>
	function selDbset(){
		if($('db2').checked){
			$('dbinfo1').style.display = 'block';
			$('dbinfo2').style.display = 'none';
			$('dbhostc').style.display = 'none';
		}else if($('db3').checked){
			$('dbinfo1').style.display = 'block';
			$('dbinfo2').style.display = 'block';
			$('dbhostc').style.display = 'block';
		}else{
			$('dbinfo1').style.display = 'none';
			$('dbinfo2').style.display = 'none';
		}
	}
	function ShowChangeType(){
		if($('tg2').checked){
			$('ot1').checked = false;
			$('ot2').checked = true;
			$('ot3').checked = false;
			$('ot4').checked = false;
		}
		if($('tg3').checked){
			$('ot1').checked = false;
			$('ot2').checked = false;
			$('ot3').checked = false;
			$('ot4').checked = true;
		}
	}
	function SelectedTable(){
		var dbhost = $('dbhost').value;
		var dbname = $('dbname').value;
		var dbuser = $('dbuser').value;
		var dbpwd = $('dbpwd').value;
		var dbptype = 1;
		if($('db2').checked) dbptype = 2;
		else if($('db3').checked) dbptype = 3;
		var queryUrl = "member_data_action.php?action=gettables&dbptype="+dbptype+"&dbhost="+dbhost+"&dbname="+dbname+"&dbuser="+dbuser+"&dbpwd="+dbpwd;
		var myajax = new DedeAjax($('tblist'),true,true,'','x','...');
	  myajax.SendGet(queryUrl);
	}
	function ShowFields(){
		var dbhost = $('dbhost').value;
		var dbname = $('dbname').value;
		var dbuser = $('dbuser').value;
		var dbpwd = $('dbpwd').value;
		var dbptype = 1;
		var exptable = $('exptable').options[$('exptable').selectedIndex].value;
		if($('db2').checked) dbptype = 2;
		else if($('db3').checked) dbptype = 3;
		var queryUrl = "member_data_action.php?exptable="+exptable+"&action=getfields&dbptype="+dbptype+"&dbhost="+dbhost+"&dbname="+dbname+"&dbuser="+dbuser+"&dbpwd="+dbpwd;
		var myajax = new DedeAjax($('fields'),true,true,'','x','...');
	  myajax.SendGet(queryUrl);
	}
	function CheckSubmit(){
	   if(!$('tg1').checked && !$('tg2').checked){
	      alert("请选择数据源的密码类型！");
		  return false;
	   }
	   return true;
	}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#98CAEF" align="center">
  <form action="member_data_action.php" name="form1" method="get" target="stafrm" onSubmit="return CheckSubmit()">
  	<input type='hidden' name='action' value='savesetting'>
    <tr> 
      <td height="20" background='img/tbg.gif'><table width="98%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="30%">
            	<strong><a href="co_main.php"><u>会员管理</u></a> &gt;<A href="member_password.php" target="main">数据转换与导入</A>：</strong> </td>
            <td>&nbsp;</td>
        </tr>
      </table>
      </td>
    </tr>
    <tr> 
      <td bgcolor="#FFFFFF">
<table width="100%" border="0" cellpadding="2" cellspacing="2">
          <tr bgcolor="#FFFFFF"> 
            <td colspan="4" style="line-height:180%"><img src="img/help.gif" width="16" height="16">本向导入于导入并转换第三方的用户数据，本程序会强制清空现有的DedeCms会员数据，请小心使用。<br>
              如果你只想转换本地的数据的密码类型，请在[<a href="member_password.php"><u>密码类型变换</u></a>]的地方转换。</td>
          </tr>
          <tr bgcolor="#EFFAFE"> 
            <td width="18%">数据源密码类型：</td>
            <td width="82%" colspan="3">
            	<input type="radio" name="tgtype" id="tg1" class="np" onclick="ShowChangeType()" value="none">
              明文(不加密) 
              <input type="radio" name="tgtype" id="tg2" class="np" onclick="ShowChangeType()" value="md5">
              MD5加密
              <input type="radio" name="tgtype" id="tg3" class="np" onclick="ShowChangeType()" value="md5m16">
              MD5中间16位
              &nbsp;
              MD5长度： 
              <input name="tgmd5len" type="text" id="tgmd5len" size="10">
              （空或32表示全长度）
            </td>
          </tr>
          <tr> 
            <td height="66">数据源信息：</td>
            <td colspan="3"><table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td height="24">
                  	<input name="dbptype" type="radio" onclick="selDbset()" id="db1" class="np" value="1" checked>
                    使用与本系统相同的数据库主机、数据库名、用户、密码<br>
                    <input type="radio" name="dbptype" onclick="selDbset()" id="db2" class="np" value="2">
                    使用与本系统相同的数据库主机、用户、密码 <br>
                    <input type="radio" name="dbptype" onclick="selDbset()" id="db3" class="np" value="3">
                    指定新的登录信息 
                    </td>
                </tr>
                <tr> 
                  <td height="28" id="dbinfo1" style="display:none"> <span id='dbhostc' style='float:left;display:none'>数据库主机： 
                    <input name="dbhost" type="text" id="dbhost">
                    </span> <span id='dbnamec' style='float:left'>&nbsp;&nbsp;数据库名称： 
                    <input name="dbname" type="text" id="dbname">
                    </span> </td>
                </tr>
                <tr> 
                  <td height="28" id="dbinfo2" style="display:none"> 数据库用户： 
                    <input name="dbuser" type="text" id="dbuser"> &nbsp;&nbsp;数据库密码： 
                    <input name="dbpwd" type="text" id="dbpwd">
                  </td>
                </tr>
                <tr> 
                  <td height="32"> <span id='tblist'> 
                    <input type="button" name="Submit2" value="选择数据表" class="nbt" onclick="SelectedTable()">
                    </span> <span id='fields'></span> </td>
                </tr>
                <tr>
                  <td height="32">数据表编码：
                    <input type="radio" name="dbchar" value="gbk" class="np" checked>
                    GBK 
                    <input type="radio" name="dbchar" value="latin1" class="np">
                    Iatin1</td>
                </tr>
                <tr> 
                  <td height="32">用户名字段名： 
                    <input name="userfield" type="text" id="userfield" size="12">
                    密码字段名： 
                    <input name="pwdfield" type="text" id="pwdfield" size="12">
                    Email字段名： 
                    <input name="emailfield" type="text" id="emailfield" value="email" size="12"></td>
                </tr>
                <tr> 
                  <td height="32">用户昵称字段： 
                    <input name="unamefield" type="text" id="unamefield" size="10">
                    (空同用户名)　性别： 
                    <input name="sexfield" type="text" id="sexfield" value="sex" size="10">
                    性别转换：男 
                    <input name="sexman" type="text" id="sexman" size="4">
                    女 
                    <input name="sexwoman" type="text" id="sexwoman" size="4">
                    （填写代号码）</td>
                </tr>
              </table></td>
          </tr>
          <tr bgcolor="#EFFAFE"> 
            <td>转换为：</td>
            <td colspan="3">
            	<input type="radio" name="oldtype" onclick="ShowChangeType()" id="ot1" class="np" value="none"<?php  if($cfg_pwdtype=='none') echo ' checked';?>>
              明文(不加密) 
              <input type="radio" name="oldtype" onclick="ShowChangeType()" id="ot2" class="np" value="md5"<?php  if($cfg_pwdtype=='md5') echo ' checked';?>>
              MD5加密 
              <input type="radio" name="oldtype" onclick="ShowChangeType()" id="ot4" class="np" value="md5m16"<?php  if($cfg_pwdtype=='md5m16') echo ' checked';?>>
              MD5中间16位
              <input type="radio" name="oldtype" onclick="ShowChangeType()" id="ot3" class="np" value="dd"<?php  if($cfg_pwdtype=='dd') echo ' checked';?>>
              Dede算法
           </td>
          </tr>
          <tr> 
            <td>MD5长度：</td>
            <td><input name="oldmd5len" type="text" id="oldmd5len2" value="<?php echo $cfg_md5len?>"> 
            </td>
            <td>Dede密钥：</td>
            <td><input name="oldsign" type="text" id="oldsign2" value="<?php echo $cfg_ddsign?>"></td>
          </tr>
          <tr> 
            <td height="29">安全确认码：</td>
            <td colspan="3"><table width="300"  border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="90"><input type="text" name="validate" style="width:80;height:20"></td>
                  <td><img src='../include/vdimgck.php' width='50' height='20'></td>
                </tr>
              </table> </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td height="31" bgcolor="#F8FBFB" align="center">
	  <input type="submit" name="Submit" value="开始导入并转换数据" class="nbt"> 
      </td>
    </tr>
  </form>
  <tr bgcolor="#E5F9FF"> 
    <td height="20"> <table width="100%">
        <tr> 
          <td width="74%"><strong>结果：</strong></td>
          <td width="26%" align="right"> <script language='javascript'>
            	function ResizeDiv(obj,ty)
            	{
            		if(ty=="+") document.all[obj].style.pixelHeight += 50;
            		else if(document.all[obj].style.pixelHeight>80) document.all[obj].style.pixelHeight = document.all[obj].style.pixelHeight - 50;
            	}
            	</script>
            [<a href='#' onClick="ResizeDiv('mdv','+');">增大</a>] [<a href='#' onClick="ResizeDiv('mdv','-');">缩小</a>] 
          </td>
        </tr>
      </table></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td id="mtd"> <div id='mdv' style='width:100%;height:100;'> 
        <iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
      </div>
      <script language="JavaScript">
	  document.all.mdv.style.pixelHeight = screen.height - 420;
	  </script> </td>
  </tr>
</table>
</body>
</html>
