<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>生成HTML</title>
<link href="base.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="main.js"></script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#98CAEF" align="center">
  <form name='form2' action='content_list.php' method="get" target='stafrm'>
   <input type='hidden' name='nullfield' value='ok'>
  </form>
  <form name="form1" action="makehtml_archives_action.php" method="get" target='stafrm'>
  <tr> 
    <td height="20" colspan="2" background='img/tbg.gif'>
    	<table width="98%" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="30%" height="18"><strong>更新文档HTML：</strong></td>
          <td width="70%" align="right">&nbsp; </td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td width="108" valign="top" bgcolor="#FFFFFF">选择栏目：</td>
    <td width="377" valign="top" bgcolor="#FFFFFF"><?php 
			$seltypeids = 0;
			if(!empty($cid)){
			  $dsql = new DedeSql(false);
			  $seltypeids = $dsql->GetOne("Select ID,typename From #@__arctype where ID='$cid' ");
			  $dsql->Close();
			}
			$opall=1;
			if(is_array($seltypeids)){
			   echo GetTypeidSel('form1','typeid','selbt1',0,$seltypeids['ID'],$seltypeids['typename']);
			}else{
			   echo GetTypeidSel('form1','typeid','selbt1',0,0,'请选择...');
			}
        ?></td>
  </tr>
  <tr>
    <td height="20" valign="top" bgcolor="#FFFFFF">起始ID：</td>
    <td height="20" valign="top" bgcolor="#FFFFFF"><input name="startid" type="text" id="startid" size="10">
      （空或0表示从头开始）</td>
  </tr>
  <tr> 
    <td height="20" valign="top" bgcolor="#FFFFFF">结束ID：</td>
    <td height="20" valign="top" bgcolor="#FFFFFF"><input name="endid" type="text" id="endid" size="10">
      （空或0表示直到结束ID） </td>
  </tr>
    <tr> 
      <td height="20" bgcolor="#FFFFFF">每页生成：</td>
      <td height="20" bgcolor="#FFFFFF"> <input name="pagesize" type="text" id="pagesize" value="20" size="8">
        个文件</td>
    </tr>
    <tr> 
      <td height="20" colspan="2" bgcolor="#F8FBFB" align="center">
      	<input name="b112" type="button" value="开始生成HTML" onClick="document.form1.submit();" style="width:100" class='nbt'>
        &nbsp;
        <input type="button" name="b113" value="查看所有文档" onClick="document.form2.submit();" style="width:100" class='nbt'> 
      </td>
    </tr>
  </form>
  <tr bgcolor="#E5F9FF"> 
    <td height="20" colspan="2"> <table width="100%">
        <tr> 
          <td width="74%">进行状态： </td>
          <td width="26%" align="right">
          	<script language='javascript'>
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
    <td colspan="2" id="mtd"> <div id='mdv' style='width:100%;height:100;'> 
        <iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
      </div>
      <script language="JavaScript">
	  document.all.mdv.style.pixelHeight = screen.height - 360;
	  </script> </td>
  </tr>
</table>
</body>
</html>
