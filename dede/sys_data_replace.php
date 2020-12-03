<?php 
require_once(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>数据批量替换</title>
<script language='javascript' src='main.js'></script>
<script language='javascript' src='../include/dedeajax2.js'></script>
<link href="base.css" rel="stylesheet" type="text/css">
<script language='javascript'>
	function ShowFields(){
		var exptable = $('exptable').options[$('exptable').selectedIndex].value;
		var queryUrl = "sys_data_replace_action.php?exptable="+exptable+"&action=getfields";
		var myajax = new DedeAjax($('fields'),true,true,'','x','...');
	    myajax.SendGet(queryUrl);
	}
	function CheckSubmit(){
	   if($('qfs1').checked && $('rpfield').value==""){
	      alert("你选择的操作为手工指定字段，但你并没指定！");
		  return false;
	   }
	   if($('rpstring').value==""){
	      alert("你没指定要替换的字符串！");
		  return false;
	   }
	   return true;
	}
	function pf(v){
	   $('rpfield').value = v;
	}
	function ShowHideFromItem(){
	   if($('qfs1').checked){
	     $('datasel').style.display = 'block';
	   }else{
	     $('datasel').style.display = 'none';
	   }
	}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#98CAEF" align="center">
  <form action="sys_data_replace_action.php" name="form1" method="post" target="stafrm" onSubmit="return CheckSubmit()">
  	<input type='hidden' name='action' value='apply'>
    <tr> 
      <td height="20" background='img/tbg.gif'><table width="98%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="30%">
            	<strong>&gt;数据库内容替换：</strong> </td>
            <td>&nbsp;</td>
        </tr>
      </table>
      </td>
    </tr>
    <tr> 
      <td bgcolor="#FFFFFF">
<table width="100%" border="0" cellpadding="2" cellspacing="2">
          <tr bgcolor="#FFFFFF"> 
            <td colspan="2" style="line-height:180%"><img src="img/help.gif" width="16" height="16">程序用于批量替换数据库中某字段的内容。</td>
          </tr>
          <tr> 
            <td width="15%" bgcolor="#EFFAFE">&nbsp;字段选项：</td>
            <td bgcolor="#EFFAFE"><input type="radio" name="quickfield" id="qfs1" onClick="ShowHideFromItem()" value="none" class="np" checked>
              手工指定要替换的字段 
              <input type="radio" name="quickfield" id="qfs2" onClick="ShowHideFromItem()" value="title" class="np">
              文档标题 
              <input type="radio" name="quickfield" id="qfs3" onClick="ShowHideFromItem()" value="body" class="np">
              文章内容 </td>
          </tr>
          <tr id='datasel'> 
            <td width="15%" height="66">&nbsp;选择数据表与字段：</td>
            <td> <table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td id="tables"> 
                    <?php 
	$dsql = new DedeSql(false);
	if(!$dsql->linkID){
		echo "<font color='red'>连接数据库失败！</font><br>";
		echo $qbutton;
		exit();
	}
	$dsql->SetQuery("Show Tables");
  $dsql->Execute('t');
  if($dsql->GetError()!=""){
  	echo "<font color='red'>找不到你所指定的数据库！ $dbname</font><br>";
		echo $qbutton;
  }
  echo "<select name='exptable' id='exptable' size='10' style='width:60%' onchange='ShowFields()'>\r\n";
  while($row = $dsql->GetArray('t')){
	  echo "<option value='{$row[0]}'>{$row[0]}</option>\r\n";
  }
  echo "</select>\r\n";
	$dsql->Close();
				  ?>
                  </td>
                </tr>
                <tr> 
                  <td id='fields'></td>
                </tr>
                <tr> 
                  <td height="28"> 要替换的字段： 
                    <input name="rpfield" type="text" id="rpfield"></td>
                </tr>
              </table></td>
          </tr>
          <tr bgcolor="#EFFAFE"> 
            <td bgcolor="#EFFAFE">&nbsp;替换方式：</td>
            <td bgcolor="#EFFAFE"> <input name="rptype" type="radio" class="np" id="ot1" value="replace" checked>
              普通替换 
              <input type="radio" name="rptype"  id="ot2" class="np" value="regex">
              正则表达式 主键字段：
              <input name="keyfield" type="text" id="keyfield" size="12">
              （正则模式必须指定）</td>
          </tr>
          <tr> 
            <td>&nbsp;被替换内容：</td>
            <td><textarea name="rpstring" id="rpstring" style="width:60%;height:50px"></textarea></td>
          </tr>
          <tr> 
            <td>&nbsp;替换为：</td>
            <td><textarea name="tostring" id="tostring" style="width:60%;height:50px"></textarea></td>
          </tr>
          <tr>
            <td height="29">&nbsp;替换条件：</td>
            <td><input name="condition" type="text" id="condition" style="width:45%">
              (空完全替换)</td>
          </tr>
          <tr> 
            <td height="29">&nbsp;安全确认码：</td>
            <td><table width="300"  border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="90"><input type="text" name="validate" style="width:80;height:20"></td>
                  <td><img src='../include/vdimgck.php' width='50' height='20'></td>
                </tr>
              </table></td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td height="31" bgcolor="#F8FBFB" align="center">
	  <input type="submit" name="Submit" value="开始替换数据" class="nbt"> 
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
	  document.all.mdv.style.pixelHeight = screen.height - 520;
	  </script> </td>
  </tr>
</table>
</body>
</html>
