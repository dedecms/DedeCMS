<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('temp_Other');
//根据条件生成标记
$attlist = "";
$attlist .= ' row='.$row;
$attlist .= ' titlelen='.$titlelen;
if($orderby!='senddate')  $attlist .= ' orderby='.$orderby;
if($order!='desc') $attlist .= ' order='.$order;
if($typeid>0) $attlist .= ' typeid='.$typeid;
if($channel>0) $attlist .= ' channel='.$channel;
if($att>0) $attlist .= ' att='.$att;
if($col>1) $attlist .= ' col='.$col;
if($subday>0) $attlist .= ' subday='.$subday;
if(!empty($types)){
	$attlist .= " type='";
	foreach($types as $v) $attlist .= $v.'.';
	$attlist .= "'";
}
$innertext = stripslashes($innertext);
if($keyword!="") $attlist .= " keyword='$keyword'";
$fulltag = "{dede:arclist$attlist}
$innertext
{/dede:arclist}\r\n";

if($dopost=='savetag')
{
	$dsql = new DedeSql(false);
	$fulltag = addslashes($fulltag);
	$tagname = "auto";
	$inQuery = "
	 Insert Into #@__mytag(typeid,tagname,timeset,starttime,endtime,normbody,expbody)
	 Values('0','$tagname','0','0','0','$fulltag','');
	";
	$dsql->ExecuteNoneQuery($inQuery);
	$id = $dsql->GetLastID();
	$dsql->ExecuteNoneQuery("Update #@__mytag set tagname='{$tagname}_{$id}' where aid='$id'");
	$dsql->Close();
	$fulltag = "{dede:mytag name='{$tagname}_{$id}' ismake='yes'/}";
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>智能标记向导</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body leftmargin='0' topmargin='10'>
<table width="99%" border="0" cellspacing="0" cellpadding="0">
  <form action="tag_test_action.php" method="post" name="f1" target="_blank">
  <tr> 
    <td align="center"> 
      <textarea name="partcode" cols="60" rows="6" id="partcode" style="width:90%;height:120"><?php echo $fulltag?></textarea> 
      <input type="submit" name="Submit" value="预览" class="np">
    </td>
  </tr>
  </form>
</table>
</body>
</html>