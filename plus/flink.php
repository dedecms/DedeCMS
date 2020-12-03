<?
require(dirname(__FILE__)."/../include/config_base.php");
if(empty($dopost)) $dopost="";
$dsql = new DedeSql(false);
if($dopost=="save")
{
  if(empty($dopost)) $dopost="";
  $dtime = strftime("%Y-%m-%d %H:%M:%S",time());
  $query = "Insert Into #@__flink(sortrank,url,webname,logo,msg,email,typeid,dtime,ischeck) 
  Values('50','$url','$webname','$logo','$msg','$email','$typeid','$dtime','0')";
  $dsql->SetQuery($query);
  $dsql->ExecuteNoneQuery();
  ShowMsg("成功增加一个链接，但需要审核后才能显示!","",1);
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>所有链接</title>
<link href="../base.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style2 {
	color: #CC0000;
	font-size: 11pt;
}
-->
</style>
</head>
<body>
<table width="650" border="0" align="center" cellspacing="2">
  <tr> 
    <td><img src="img/flink.gif" width="320" height="46"></td>
  </tr>
  <tr> 
    <td bgcolor="#CCCC99" height="6"></td>
  </tr>
  <tr> 
    <td bgcolor="#DEEFE2">
    	 友情链接：
    	 <a href='/'>[返回主页]</a>
    	 <a href='flink-add.php'>[申请链接]</a> 
    </td>
  </tr>
  <tr> 
    <td>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="3"></td>
        </tr>
        <tr> 
          <td height="100" align="center" valign="top">
		  <?
		  $row = 180;
		  $col = 6;
		  $titlelen = 50;
		  $tdwidth = (100/6)."%";
		  $dsql->SetQuery("Select * from #@__flink where ischeck>0 order by sortrank asc");
		  $dsql->Execute();
		$revalue = "<table width='100%' border='0' cellspacing='1' cellpadding='1' bgcolor='#F5F5F5'>\r\n";
		for($i=1;$i<=$row;$i++)
		{
			$revalue.="<tr bgcolor='#FFFFFF' height='20'>\r\n";
			for($j=1;$j<=$col;$j++)
			{
				if($dbrow=$dsql->GetObject())
				{
					$wtitle = cn_substr($dbrow->webname,$titlelen);
					if($dbrow->logo=="")
						$link = " <a href='".$dbrow->url."' target='_blank'>$wtitle</a>";
					else
						$link = " <a href='".$dbrow->url."' target='_blank'><img src='".$dbrow->logo."' width='88' height='31' border='0' alt='$wtitle'></a>";
					$revalue.="<td width='$tdwidth'>$link</td>\r\n";
				}
				else
				{
					$revalue.="<td></td>\r\n";
				}
			}
			$revalue.="</tr>\r\n";
			if(!$dbrow) break;
		}
		$revalue .= "</table>";
		echo  $revalue;
		$dsql->Close();
		  ?>
			</td>
        </tr>
        <tr> 
          <td height="3"></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td bgcolor="#CCCC99" height="6"></td>
  </tr>
  <tr> 
    <td align="center" height="30">
    <a href="http://www.dedecms.com" target="_blank">Power by DedeCms 织梦内容管理系统</a>
    </td>
  </tr>
</table>
	</td>
  </tr>
  <tr>
    <td height="20"> </td>
  </tr>
</table>
</body>
</html>
