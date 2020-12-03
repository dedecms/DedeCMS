<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typeunit_menu.php");
$userChannel = $cuserLogin->getUserChannel();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>类别管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<style>
.coolbg2 {
border: 1px solid #000000;
background-color: #F2F5E9;
height:18px
}
.bline {border-bottom: 1px solid #BCBCBC;background-color:#F0F4F1;}
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
   if(obj.style.display=="none") obj.style.display = "block";
	 else obj.style.display="none";
}
</script>
</head>
<base target="main">
<body bgcolor="#B5D185" leftmargin="0" topmargin="0" target="main">
<div style='font-size:2pt'>&nbsp;</div>
<table width='100%' border='0' cellspacing='0' cellpadding='2'>
  <tr bgcolor='#FFFFFF'>
  <td width='2%'><img src='img/dedeexplode.gif' width='11' height='11'></td>
  <td background='img/itemcomenu2.gif'>
  <a href='index_menu.php' target='_self'>全部管理项</a>
  </td>
  </tr>
</table>
<div style='font-size:2pt'>&nbsp;</div>
<table width='100%' border='0' cellspacing='0' cellpadding='2'>
  <tr bgcolor='#FFFFFF'>
  <td width='2%'><img src='img/dedeexplode.gif' width='11' height='11'></td>
  <td background='img/itemcomenu.gif'>
  <a href='catalog_main.php'>网站栏目管理</a>
  </td>
  </tr>
</table>
<div style='font-size:2pt'>&nbsp;</div>
<?
if(empty($opendir)) $opendir=-1;
if($userChannel>0) $opendir=$userChannel;
$tu = new TypeUnit();
$tu->ListAllType($userChannel,$opendir);
$tu->Close();
?>
</body>
</html>