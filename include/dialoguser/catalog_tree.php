<?php
require_once(dirname(__FILE__)."/../inc_type_tree_member.php");
if(!isset($dopost)) $dopost = '';
if(!isset($c)) $c = 0;
$opall = (empty($opall) ? false : true);
$issend = (empty($issend) ? 1 : $issend);
$channelid = (empty($channelid) ? 0 : $channelid);
//载入子栏目
if($dopost=='GetSunListsTree'){
	header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
	header("Content-Type: text/html; charset=utf-8");
	PutCookie('lastCidTree',$cid,3600*24,"/");
	$tu = new TypeTreeMember();
	$tu->dsql = new DedeSql(false);
	$tu->LogicListAllSunType($cid,"　",$opall,$issend,$channelid);
  $tu->Close();
  exit();
}

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title>栏目选择</title>
<link href='<?php echo $cfg_phpurl?>/base2.css' rel='stylesheet' type='text/css'>
<script language="javascript" src="<?php echo $cfg_mainsite.$cfg_cmspath?>/include/dedeajax2.js"></script>
<script language="javascript">
function LoadSuns(ctid,tid,channelid)
{
   if($DE(ctid).innerHTML.length < 10){
      var myajax = new DedeAjax($DE(ctid),true,true,'','没子栏目','...');
      myajax.SendGet('catalog_tree.php?opall=<?php echo $opall; ?>&issend=<?php echo $issend; ?>&dopost=GetSunListsTree&channelid='+channelid+'&cid='+tid);
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
		window.opener.document.<?php echo $f?>.<?php echo $v?>.value=ctid;
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
         padding-left:8px;
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
scrollbar-base-color:#8CC1FE;
scrollbar-arrow-color:#FFFFFF;
scrollbar-shadow-color:#6994C2
}
</style>
</head>
<base target="main">
<body leftmargin="0" bgcolor="#86C1FF" topmargin="3" target="main">
<table width='98%' border='0' align='center' cellpadding='0' cellspacing='0'>
  <tr>
    <td height='24' background='<?php echo $cfg_phpurl?>/img/mtbg1.gif'  style='border-left: 1px solid #2FA1DB; border-right: 1px solid #2FA1DB;'>
		　<strong>√请在要选择的栏目打勾</strong>
	  <input type='checkbox' name='nsel' id='selid0' class='np' onClick="ReSel(0,'请选择...')">不限栏目
	</td>
  </tr>
  <tr bgcolor='#EEFAFE'>
    <td id='items1'>
<?php
$tu = new TypeTreeMember();
$tu->ListAllType($c,$issend,$opall,$channelid);
$tu->Close();
?>    </td>
  </tr>
</table>
</body>
</html>