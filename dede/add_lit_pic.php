<?
require_once("config.php");
require_once("inc_pic_resize.php");
$conn = connectMySql();
if(!ereg("\.(jpg|gif|png)$",$litpic_name))
{
	ShowMsg("你的图片格式不合法！","add_news_ok.php?artID=$artID");
	exit;
}
else if($litpic>200000)
{
	ShowMsg("你的图片太大，请控制小于200K！","add_news_ok.php?artID=$artID");
	exit;
}
else
{
	if($picw=="") $picw=200;
	if($pich=="") $pich=200;
	$imgUrl = $ddimg_dir."/".strftime("%Y%m%d",time());
	$imgPath = $base_dir.$imgUrl;
	if(!is_dir($imgPath)) @mkdir($imgPath,0777);
	
	$milliSecond = strftime("%H%M%S",time()).mt_rand(100,999);
	$rndFileName = $milliSecond;

	if(eregi("\.gif$",$litpic_name)) $shortName = ".gif";
	else if(eregi("\.png$",$litpic_name)) $shortName = ".png";
	else $shortName = ".jpg";
	
	$picname = $imgUrl."/".$rndFileName."lit".$shortName;
	
	pic_resize($litpic,$base_dir.$picname,$picw,$pich);
	
	@unlink($litpic);
	
	$inQuery = "update dede_art set isdd=1,picname='$picname' where ID=$artID";
	mysql_query($inQuery,$conn);
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>成功提示</title>
<link href="base.css" rel="stylesheet" type="text/css">
<script src="menu.js" language="JavaScript"></script>
<style>
.bt{border-left: 1px solid #FFFFFF; border-right: 1px solid #666666; border-top: 1px solid #FFFFFF; border-bottom: 1px solid #666666; background-color: #C0C0C0}
</style>
</head>
<body background="img/allbg.gif" leftmargin="6" topmargin="6">
<br>
<br>
<table width="409" border="0" cellpadding="1" cellspacing="1" bgcolor="#666666" align="center">
          <tr align="center"> 
            <td width="405" height="26" colspan="2"  background='img/tbg.gif'><strong>上传缩略图成功!</strong></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            
          
    <td height="85" colspan="2" align="center"> 
      <p>  <a href='add_news_view.php?typeid=<?=$typeid?>&typename=<?=$typename?>'>[<u>发表新文章</u>]</a> 
        &nbsp;&nbsp; <a href='list_news.php'>[<u>文章列表</u>]</a> </td>
          </tr>
      </table>
	  </body>
<?
echo mysql_error();
?>
</html>