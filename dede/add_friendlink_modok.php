<?
require("config.php");
//url,fwebname,logo,logoimg,msg,email,typeid
$dtime = strftime("%Y-%m-%d %H:%M:%S",time());
if(!empty($logoimg_name))
{
	$names = split("\.",$logoimg_name);
	$shortname = ".".$names[count($names)-1];
	$filename = strftime("%Y%m%d%H%M%S",time()).mt_rand(1000,9999).$shortname;
	$imgurl = $imgview_dir."/flink";
	if(!is_dir($base_dir.$imgurl)) @mkdir($base_dir.$imgurl,0777);
	$imgurl = $imgurl."/".$filename;
	copy($logoimg,$base_dir.$imgurl) or die("复制文件到:".$base_dir.$imgurl."失败");
	@unlink($logoimg);
}
else 
	$imgurl = $logo;
$query = "update dede_flink set url='$url',webname='$fwebname',logo='$imgurl',msg='$msg',email='$email',typeid=$typeid,dtime='$dtime',ischeck=$ischeck where ID=$ID";
$conn = connectMySql();
mysql_query($query,$conn);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>成功增加更改链接</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body>
<br>
<table width="400"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#B6B6B6">
  <tr>
    <td width="100%" height="24" colspan="2" align="center" background="img/tbg.gif"><strong>成功更改一个链接</strong></td>
  </tr>
  <tr>
    <td height="123" colspan="2" align="center" bgcolor="#FFFFFF"><table width="80%" border="0" cellspacing="1" cellpadding="1">
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td align="center"><a href="add_friendlink.php">&lt;&lt;返回管理页&gt;&gt;</a>　<a href="add_friendlink_mod.php?ID=<?=$ID?>">&lt;&lt;查看链接更改&gt;&gt;</a> 
            <a href="add_friendlink_form.php">&lt;&lt;增加链接&gt;&gt;</a></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>