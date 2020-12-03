<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_type_tree.php");
if(empty($c)) $c = 0;
if(empty($opall)) $opall=false;
else $opall = true;
$userChannel = $cuserLogin->getUserChannel();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title>栏目选择</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language="javascript" src="../include/dedeajax2.js"></script>
<script language="javascript">
function LoadSuns(ctid,tid,c)
{
   if($DE(ctid).innerHTML.length < 10){
      var myajax = new DedeAjax($DE(ctid),true,true,'','没子栏目','...');
      myajax.SendGet('catalog_do.php?opall=<?php echo $opall?>&dopost=GetSunListsTree&c='+c+'&cid='+tid);
   }else{
   	 if(document.all) showHide(ctid);
   }
}
function showHide(objname)
{
   if($DE(objname).style.display=="none") $DE(objname).style.display = "block";
   else $DE(objname).style.display="none";
   return false;
}
function ReSel(ctid,cname){
	
	if($DE('selid'+ctid).checked){
		window.opener.document.<?php echo $f; ?>.<?php echo $v; ?>.value=ctid;
		window.opener.document.<?php echo $f?>.<?php echo $bt?>.value=cname;
	  if(document.all) window.opener=true;
    window.close();
	}
}
</script>
<style>
div,dd{ margin:0px; padding:0px }
.dlf { margin-right:3px; margin-left:6px; margin-top:2px; float:left }
.dlr { float:left }
.topcc{ margin-top:5px }
.suncc{ margin-bottom:3px }
dl{ clear:left; margin:0px; padding:0px }
.sunct{  }
#items1{ border-bottom: 1px solid #3885AC;
         border-left: 1px solid #2FA1DB;
         border-right: 1px solid #2FA1DB;
}
.sunlist{ width:100%; padding-left:0px; margin:0px; clear:left } 
.tdborder{
border-left: 1px solid #43938B;
border-right: 1px solid #43938B;
border-bottom: 1px solid #43938B;
}
.tdline-left{
border-bottom: 1px solid #656363;
border-left: 1px solid #788C47;
}
.tdline-right{
border-bottom: 1px solid #656363;
border-right: 1px solid #788C47;
}
.tdrl{
border-left: 1px solid #788C47;
border-right: 1px solid #788C47;
}
.top{cursor: hand;}
body {
scrollbar-base-color:#bae87c;
scrollbar-arrow-color:#FFFFFF;
scrollbar-shadow-color:#c1ea8b
}
</style>
</head>
<base target="main">
<body leftmargin="0" bgcolor="#007400" topmargin="3" target="main">
<table width='98%' border='0' align='center' cellpadding='0' cellspacing='0'>
  <tr> 
    <td height='24' background='img/mtbg1.gif'  style='border-left: 1px solid #2FA1DB; border-right: 1px solid #2FA1DB;'>
		　<strong>√请在要选择的栏目打勾</strong>
	  <input type='checkbox' name='nsel' id='selid0' class='np' onClick="ReSel(0,'请选择...')">不限栏目
	</td>
  </tr>
  <tr bgcolor='#EEFAFE'> 
    <td align='center' bgcolor="#eefef0" id='items1'> 
<?php 
$tu = new TypeTree($userChannel);
$tu->ListAllType(0,$opall,$c);
$tu->Close();
?>    </td>
  </tr>
</table>
</body>
</html>