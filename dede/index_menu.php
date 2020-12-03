<?
require(dirname(__FILE__)."/config.php");
require(dirname(__FILE__)."/inc/inc_menu.php");
?>
<html>
<head>
<title>DedeCms menu</title>
<link rel="stylesheet" href="base.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=gb2312"></head>
<style>
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
scrollbar-base-color:#C0D586;
scrollbar-arrow-color:#FFFFFF;
scrollbar-shadow-color:DEEFC6
}
</style>
<script language="javascript">
function showHide(objname)
{
    var obj = document.getElementById(objname);
    if(obj.style.display == "none") obj.style.display = "block";
    else{ if(document.all) obj.style.display = "none"; }
}
</script>
<base target="main">
<body bgcolor="#B5D185" leftmargin="0" topmargin="0" target="main">
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
	<tr><td height='3'></td></tr>
</table>
<?
GetMenus($cuserLogin->getUserRank());
?>
<table width="120" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr><td height="6"></td></tr>
</table>
</body>
</html>