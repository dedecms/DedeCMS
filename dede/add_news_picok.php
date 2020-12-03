<?
require("config.php");
require("inc_makeart.php");
require("inc_pic_resize.php");
$picname="";
$srcW = "";
$srcH = "";
//图片保存位置
$rndFileName = strftime("%H%M%S",time()).mt_rand(100,999);
if(!empty($litpic_name))
{
	if(!ereg("\.(jpg|gif|png)$",$litpic_name))
	{
		ShowMsg("你的图片格式不合法！","-1");
		exit;
	}
	//检查目录
	$imgUrl = $ddimg_dir."/".strftime("%Y%m%d",time());
	if(!is_dir($base_dir.$imgUrl)) @mkdir($base_dir.$imgUrl,0755);
	
	if(eregi("\.gif$",$litpic_name)) $shortName = ".gif";
	else if(eregi("\.png$",$litpic_name)) $shortName = ".png";
	else $shortName = ".jpg";
	
	$picname = $imgUrl."/".$rndFileName.$shortName;
	
	copy($litpic,$base_dir.$picname);
	@unlink($litpic);
}
//处理大图片
if(!empty($bigpic_name))
{
	if(!ereg("\.(jpg|gif|png)$",$bigpic_name))
	{
		ShowMsg("你的图片格式不合法！","-1");
		exit;
	}
	
	if(eregi("\.gif$",$bigpic_name)) $shortName = ".gif";
	else if(eregi("\.png$",$bigpic_name)) $shortName = ".png";
	else $shortName = ".jpg";
	
	//如果没有上传小图片，自动生成小图
	if($picname=="")
	{
		//检查目录
		$imgUrl = $ddimg_dir."/".strftime("%Y%m%d",time());
		if(!is_dir($base_dir.$imgUrl)) @mkdir($base_dir.$imgUrl,0755);
	
		$picname = $imgUrl."/".$rndFileName.$shortName;
		pic_resize($bigpic,$base_dir.$picname,$imgw,$imgh);
	}
	
	//检查目录
	$imgUrl = $img_dir."/".strftime("%Y%m%d",time());
	if(!is_dir($base_dir.$imgUrl)) @mkdir($base_dir.$imgUrl,0755);
	
	$bigpicname = $imgUrl."/".$rndFileName.$shortName;
	copy($bigpic,$base_dir.$bigpicname);
	$dataz = GetImageSize($bigpic,&$info);
	$srcW=$dataz[0];
	$srcH=$dataz[1];
	if($srcW>=760) $srcWY=760;
	else $srcWY=$srcW;
	@unlink($bigpic);
}
else
{
	if(!ereg("\.(jpg|gif|png)$",$bigpic_name))
	{
		ShowMsg("必须上传大图片！","-1");
		exit();
	}
}
$writer="$srcW x $srcH 像素";
$body =
"<table width=".($srcWY+4)."  border=0 align=center cellpadding=1 cellspacing=1 bgcolor=#BBC7AD>
<tr>
<td align=center bgcolor=#FFFFFF>
<a href=$bigpicname target=_blank><img border=0 width=$srcWY alt=\"$title\" src=$bigpicname></a>
</td>
</tr>
</table>
";
//发布产品
$conn = connectMySql();
//--处理接收的数据--------------------
$adminid=$cuserLogin->getUserID();
$title = cn_substr(trim($title),100);
$source = cn_substr(trim($source),50);
$msg = cn_substr(trim($msg),300);
//---文章发布时间--------
$stime = strftime("%Y-%m-%d",time());
//---插入到数据库的SQL语句-------------
//$body = addslashes($body);
$inQuery = "
INSERT INTO dede_art(typeid,title,source,writer,rank,
stime,picname,isdd,click,msg,redtitle,ismake,body,userid,spec)
 VALUES ('$typeid','$title','$source','$writer','0',
'$stime','$picname','1','0','$msg','0','0','$body','$adminid','0')";
mysql_query($inQuery,$conn);
$artID = mysql_insert_id($conn);
$typename = "";
if($artID!=0)
{
	$mr = new makeArt();
	$mr->makeArtDone($artID);
	$artfilename = $mr->artFileName;
	$rs = mysql_query("select modname,typename from dede_arttype where ID=$typeid",$conn);
	$row = mysql_fetch_array($rs);
	$typename = $row["typename"];
}
else
{
	echo "<script>alert('发布图片失败！原因是：".str_replace("'","\\'",mysql_error())."');history.go(-1);</script>";
	exit();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>成功提示</title>
<link href="base.css" rel="stylesheet" type="text/css">
<style>
.bt{border-left: 1px solid #FFFFFF; border-right: 1px solid #666666; border-top: 1px solid #FFFFFF; border-bottom: 1px solid #666666; background-color: #C0C0C0}
</style>
</head>
<body background="img/allbg.gif" leftmargin="6" topmargin="6">
<table width="409" border="0" cellpadding="1" cellspacing="1" bgcolor="#666666" align="center">
          <tr align="center"> 
            <td width="405" height="26" colspan="2" background='img/tbg.gif'><strong>图片上传成功!</strong></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            
          <td height="85" colspan="2" align="center"> 图片名称: 
            <?=$title?>
            
      <p> <a href='add_news_pic.php?typeid=<?=$typeid?>&typename=<?=$typename?>'>[<u>发布新文章</u>]</a> 
        &nbsp;&nbsp; <a href='list_news.php?arttoptype=<?=$typeid?>'>[<u>文章列表</u>]</a>&nbsp; <a href='<?=$artfilename;?>' target='_blank'>[<u>查看文章</u>]</a>&nbsp; 
    </td>
          </tr>
      </table>
</body>
</html>