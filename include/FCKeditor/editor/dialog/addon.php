<?php 
require_once(dirname(__FILE__)."/../../../config_base.php");
?>
<HTML>
	
<HEAD>
<title>插入附件</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
.td{font-size:10pt;}
</style>
<script language=javascript>
var oEditor	= window.parent.InnerDialogLoaded() ;
var oDOM		= oEditor.FCK.EditorDocument ;
var FCK = oEditor.FCK;

function TableOK(){
    var rurl,widthdd,heightdd,rvalue,rurlname;
    rurlname = form1.rurl.value;
    rurl = encodeURI(form1.rurl.value);
    rvalue = "<table width='300'>";
    rvalue += "<tr><td height='30' width='20'>";
    rvalue += "<a href='"+rurl+"' target='_blank'><img src='<?php echo $cfg_plus_dir?>/img/addon.gif' border='0' align='center'></a>";
    rvalue += "</td><td>";
    rvalue += "<a href='"+ rurl +"' target='_blank'><u>"+ rurlname +"</u></a>";
    rvalue += "</td></tr></table>";
    if(document.all) oDOM.selection.createRange().pasteHTML(rvalue);
    else FCK.InsertHtml(rvalue);
    window.close();
}
 
function SelectAddon(fname)
{
   if(document.all){
     var posLeft = window.event.clientY-100;
     var posTop = window.event.clientX-400;
   }
   else{
     var posLeft = 100;
     var posTop = 100;
   }
   window.open("../../../dialog/select_soft.php?f="+fname, "popUpSoftWin", "scrollbars=yes,resizable=yes,statebar=no,width=500,height=350,left="+posLeft+", top="+posTop);
}
</script>
<link href="base.css" rel="stylesheet" type="text/css">
</HEAD>
<body bgcolor="#EBF6CD" topmargin="8">
  <form name="form1" id="form1">
  	<table border="0" width="98%" align="center">
    <tr> 
      <td align="right">网　址:</td>
      <td colspan="3">
      	<input name="rurl" type="text" id="rurl" style="width:300px" value="http://">
      	<input type="button" name="selmedia" class="binput" style="width:60px" value="浏览..." onClick="SelectAddon('form1.rurl')">
      </td>
    </tr>
    <tr height="50"> 
      <td align="right">&nbsp;</td>
      <td nowrap>&nbsp; </td>
      <td colspan="2" align="right" nowrap>
      	<input onclick="TableOK();" type="button" name="Submit2" value=" 确定 " class="binput"> 
      </td>
    </tr>
  </table>
  </form>
</body>
</HTML>
