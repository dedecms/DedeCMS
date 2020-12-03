<?
require("config.php");
require("inc_makeart.php");
require("inc_pic_resize.php");
$conn = connectMySql();
//typeid,title,source,stime,softrank,language,softsize,
//opensystem,softtype,msg,body,
//litpic,uploadsoft,
//addr1,addr2,addr3,addr4,addr5
$isdd=1;
$title = cn_substr(trim($title),100);
$source = trim(cn_substr($source,50));
$msg = cn_substr($msg,500);
//处理上传的图片和文件---------------------
if(empty($litpic_name)) $picname=$mod_dir."/defdd.gif";
else
{
	if(!ereg("\.(jpg|gif|png)$",$litpic_name))
	{
		ShowMsg("你的图片格式不合法！","-1");
		exit();
	}
	else
	{
		$imgUrl = $ddimg_dir."/".strftime("%Y%m%d",time());
		if(!is_dir($base_dir.$imgUrl)) @mkdir($base_dir.$imgUrl,0755);
		$rndFileName = strftime("%H%M%S",time()).mt_rand(100,999);
		
		if(eregi("\.gif$",$litpic_name)) $shortName = ".gif";
		else if(eregi("\.png$",$litpic_name)) $shortName = ".png";
		else $shortName = ".jpg";
		
		$picname = $imgUrl."/".$rndFileName.$shortName;
		pic_resize($litpic,$base_dir.$picname,200,200);
		@unlink($litpic);
	}
}

//上传软件
if(empty($uploadsoft_name)) $softfilename="#";
else
{
	$softUrl = $soft_dir."/".strftime("%Y%m",time());
	$softPath = $base_dir.$softUrl;
	if(!is_dir($softPath)) @mkdir($softPath,0755);
	$milliSecond = strftime("%d%H%M%S",time()).mt_rand(100,999);
	$rndFileName = $milliSecond;
	$names = split("\.",$uploadsoft_name);
	$shortname = ".".$names[count($names)-1];
	$softfilename = $softUrl."/".$rndFileName.$shortname;
	copy($uploadsoft,$base_dir.$softfilename);
}
//处理soft链接列表
$addlist = "";
if($softfilename!="#") $addlist.="<a href=\"$art_php_dir/download.php?artID=#aid#&goto=".ereg_replace("=$","",base64_encode("/dd?goto=".$softfilename))."\" target=_blank>[本地下载]</a> \r\n";
for($i=1;$i<=5;$i++)
{
	if($i==1) $cnnum="一";
	if($i==2) $cnnum="二";
	if($i==3) $cnnum="三";
	if($i==4) $cnnum="四";
	if($i==5) $cnnum="五";
	$softarr = trim(${"addr".$i});
	if($softarr!=""&&$softarr!="http://")
		$addlist.="<a href=\"$art_php_dir/download.php?artID=#aid#&goto=".ereg_replace("=$","",base64_encode("/dd?goto=".$softarr))."\" target=_blank>[下载地址".$cnnum."]</a> \r\n";
	
}
//处理body---------------------------------
$body = cn_substr(htmlspecialchars(trim($body)),20000);
$body = str_replace("\r","",$body);
$body = str_replace("\n","<br>\r\n",$body);
$body = str_replace("  ","&nbsp;&nbsp;",$body);
$rs = mysql_query("select modname,typename from dede_arttype where ID=$typeid",$conn);
$row = mysql_fetch_array($rs);
$modname = $row["modname"];
$typename = $row["typename"];
$bodymodfile = $base_dir.$mod_dir."/$modname/3/向导.htm";
$fp = fopen($bodymodfile,"r");
	$bodymod = fread($fp,filesize($bodymodfile));
fclose($fp);
$bodys = split("~",$bodymod);
$bn = count($bodys);
$okbody = "";
for($i=0;$i<$bn;$i++)
{
	if($i%2==1)
	{if(!empty(${$bodys[$i]})) $okbody.=${$bodys[$i]};}
	else
       $okbody.=$bodys[$i];
}
unset($bodymod);
unset($bodys);
unset($body);
////////////////////////////
$stime = trim($stime);
if($stime=="") $stime = strftime("%Y-%m-%d",time());
$adminid=$cuserLogin->getUserID();
$dtime = strftime("%Y-%m-%d %H:%M:%S",time());
//---插入到数据库的SQL语句-------------
$inQuery = "
INSERT INTO dede_art(typeid,title,source,writer,rank,picname,
stime,isdd,click,msg,redtitle,ismake,body,userid,spec)
 VALUES ('$typeid','$title','$source','',0,'$picname',
'$stime',1,0,'$msg','0','0','$okbody','$adminid','0')";
mysql_query($inQuery,$conn);
$artID = mysql_insert_id($conn);
$artfilename="";
if($artID!=0)
{
	$okbody = str_replace("#aid#",$artID,$okbody);
	mysql_query("update dede_art set body='$okbody' where ID=$artID",$conn);
	//创建静态文件
	$mr = new makeArt();
	$mr->makeArtDone($artID);
	$artfilename = $mr->artFileName;
}
else
{
	echo "<script>alert('发布软件失败！');history.go(-1);</script>";
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
            <td width="405" height="26" colspan="2" background='img/tbg.gif'><strong>软件发布成功!</strong></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            
          <td height="85" colspan="2" align="center"> 软件名称: 
            <?=$title?>
            
      <p> <a href='add_news_soft.php?typeid=<?=$typeid?>&typename=<?=$typename?>'>[<u>发布新软件</u>]</a> 
        &nbsp;&nbsp; <a href='list_news.php?arttoptype=<?=$typeid?>'>[<u>软件列表</u>]</a>&nbsp; <a href='<?=$artfilename;?>' target='_blank'>[<u>查看软件</u>]</a>&nbsp; 
    </td>
          </tr>
      </table>
</body>
</html>