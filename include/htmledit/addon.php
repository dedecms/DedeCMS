<?php 
require_once(dirname(__FILE__)."/../config_base.php");
?>
<HTML>
<HEAD>
<title>插入附件</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<style>
.td{font-size:10pt;}
</style>
<script language=javascript>
function TableOK(){
    var rurl,widthdd,heightdd,rvalue;
    rurl = document.theform.rurl.value;
    rvalue = "<table width='300'>";
    rvalue += "<tr><td height='30' width='20'>";
    rvalue += "<a href='"+rurl+"' target='_blank'><img src='<?php echo $cfg_plus_dir?>/img/addon.gif' border='0' align='center'></a>";
    rvalue += "</td><td>";
    rvalue += "<a href='"+ rurl +"' target='_blank'><u>"+ rurl +"</u></a>";
    rvalue += "</td></tr></table>";
    window.returnValue = rvalue;
    window.close();
}
function SelectAddon(fname)
{
   var posLeft = window.event.clientY-100;
   var posTop = window.event.clientX-400;
   window.open("../dialog/select_soft.php?f="+fname, "popUpSoftWin", "scrollbars=yes,resizable=yes,statebar=no,width=500,height=350,left="+posLeft+", top="+posTop);
}
</script>
<link href="base.css" rel="stylesheet" type="text/css">
</HEAD>
<body bgcolor="#EBF6CD" topmargin="8">
  <table border="0" width="98%" align="center">
  	<form name="theform">
    <tr> 
      <td align="right">网　址:</td>
      <td colspan="3">
      	<input name="rurl" type="text" id="rurl" style="width:300px" value="http://">
      	<input type="button" name="selmedia" class="binput" style="width:60px" value="浏览..." onClick="SelectAddon('theform.rurl')">
      </td>
    </tr>
    <tr height="50"> 
      <td align="right">&nbsp;</td>
      <td nowrap>&nbsp; </td>
      <td colspan="2" align="right" nowrap>
      	<input onclick="TableOK();" type="button" name="Submit2" value=" 确定 " class="binput"> 
        <input type="button" name="Submit" onclick="window.close();" value=" 取消 " class="binput"> 
      </td>
    </tr>
    </form>
  </table>
</body>
</HTML>

