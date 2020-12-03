<?php 
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
.tdrl{
border-left: 1px solid #788C47;
border-right: 1px solid #788C47;
}
.topitem{ cursor: hand; 
    background-image:url(img/mtbg1.gif);
    height:24px;
    width:98%;
    border-right: 1px solid #2FA1DB;
    border-left: 1px solid #2FA1DB;
    clear:left
}
.itemsct{
  border-right: 1px solid #2FA1DB;
  border-left: 1px solid #2FA1DB;
  background-color:#EEFAFE;
  margin-bottom:6px;
  width:98%;
}
.itemem{ 
    text-align:left;
    clear:left;
    border-bottom: 1px solid #2FA1DB;
    height:21px;
 }
.tdl{ float:left; margin-top:2px; margin-left:6px; margin-right:5px }
.tdr{ float:left; margin-top:2px }
.topl{ float:left;margin-left:6px;margin-right:3px; }
.topr{ padding-top:3px }
body {
  scrollbar-base-color:#8CC1FE;
  scrollbar-arrow-color:#FFFFFF;
  scrollbar-shadow-color:#6994C2
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
<body bgcolor="#86C1FF" leftmargin="0" topmargin="3">
<div align="center">
<?php 
GetMenus($cuserLogin->getUserRank());
?>
</div>
</body>
</html>