<?
require("config.php");
require("inc_makeart.php");

if(isset($isdd[0])) $isdd = $isdd[0];
else $isdd=0;

//发布文章
if(empty($artID))
{
	$conn = connectMySql();
	//--处理接收的数据--------------------
	$title = trim(cn_substr($title,100));
	$source = trim(cn_substr($source,50));
	$writer = trim(cn_substr($writer,50));
	$msg = cn_substr($shortmsg,500);
	
	if(!empty($stime))
		$stime = trim($stime);
	else
	{
		$stime = strftime("%Y-%m-%d",time());
	}
	
	$ishtml = $ishtml;
	$body = str_replace($base_url,"",$artbody);
	
	if(isset($redtitle[0])) $redtitle = $redtitle[0];
	else $redtitle=0;
	
	$adminid=$cuserLogin->getUserID();
	//---文章发布时间--------
	$dtime = strftime("%Y-%m-%d %H:%M:%S",time());
	
	
	//获取文章body中的远程图片
	if(!empty($saveremoteimg))
	{
		$body = stripslashes($body);
		$img_array = array();
		preg_match_all("/(src|SRC)=[\"|'| ]{0,}(http:\/\/(.*)\.(gif|jpg|jpeg|bmp|png))/isU",$body,$img_array);
		$img_array = array_unique($img_array[2]);
		set_time_limit(0);
		$imgUrl = $img_dir."/".strftime("%Y%m%d",time());
		$imgPath = $base_dir.$imgUrl;
		$milliSecond = strftime("%H%M%S",time());
		if(!is_dir($imgPath)) @mkdir($imgPath,0777);
		foreach($img_array as $key =>$value)
		{
			$value = trim($value);
			$get_file = @file_get_contents($value);
			$rndFileName = $imgPath."/".$milliSecond.$key.".".substr($value,-3,3);
			$fileurl = $imgUrl."/".$milliSecond.$key.".".substr($value,-3,3);
			if($get_file)
			{
				$fp = @fopen($rndFileName,"w");
				@fwrite($fp,$get_file);
				@fclose($fp);
			}
			$body = ereg_replace($value,$fileurl,$body);
		}
		$body = addslashes($body);
	}
	
	//---插入到数据库的SQL语句-------------
	$inQuery = "
INSERT INTO dede_art(typeid,title,source,writer,rank,
stime,isdd,click,msg,redtitle,ismake,body,userid,spec)
 VALUES ('$typeid','$title','$source','$writer','$rank',
'$stime','0','0','$msg','$redtitle','0','$body','$adminid','0')";
	mysql_query($inQuery,$conn);
	$artID = mysql_insert_id($conn);
	if($rank==0)
	{
		$mr = new makeArt();
		$mr->makeArtDone($artID);
	}
	$rs = mysql_query("select typename from dede_arttype where ID=$typeid",$conn);
	$row = mysql_fetch_object($rs);
	$typename = $row->typename;
}
//设置缩略图
else
{
	$conn = connectMySql();
	$isdd="1";
	if(!isset($typeid))
	{
		$rs = mysql_query("select dede_art.typeid,dede_arttype.typename from dede_art left join dede_arttype on dede_arttype.ID=dede_art.typeid where dede_art.ID=$artID",$conn);
		$row = mysql_fetch_object($rs);
		$typename = $row->typename;
		$typeid = $row->typeid;
	}
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
 <?
	  if($isdd=="1")
	  {
	  ?>
<table width="411" border="0" cellpadding="1" cellspacing="1" bgcolor="#666666" align="center">
  <tr align="center"> 
    <td width="407" height="26" colspan="2" align="center" background='img/tbg.gif'><strong>上传缩略图片</strong></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td height="141" colspan="2" align="center"> 
	  <table width="90%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="49">&nbsp;&nbsp;&nbsp;&nbsp;你选择的文章是图片新闻，所以你必须上传一张缩略图以便以后制作图片新闻列表或相关专题中选用（最佳比例W：H--1:1）：</td>
        </tr>
        <form action="add_lit_pic.php" name="form1" method="post" enctype="multipart/form-data">
          <input type="hidden" name="artID" value="<?=$artID?>">
          <input type="hidden" name="typeid" value="<?=$typeid?>">
          <input type="hidden" name="typename" value="<?=$typename?>">
		  <tr> 
            <td height="38">
            &nbsp; 图片宽度:<input type="text" name="picw" value="200" size="4">
            高度:<input type="text" name="pich" value="200" size="4">
            </td>
           </tr>
		  <tr> 
            <td height="38">&nbsp; 
              <input type="file" name="litpic"> &nbsp;&nbsp; <input type="submit" name="Submit" value="提交"></td>
          </tr>
        </form>
        <tr>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;如果你不想上传缩略图，请选择如下操作：</td>
        </tr>
        <tr> 
          <td height="43">&nbsp;&nbsp;&nbsp;&nbsp; 
            <a href='add_news_view.php?typeid=<?=$typeid?>&typename=<?=$typename?>'>[<u>发表新文章</u>]</a> &nbsp;&nbsp; <a href='list_news.php'>[<u>文章列表</u>]</a>&nbsp; 
            <a href='<?=$art_php_dir?>/viewart.php?artID=<?=$artID?>' target='_blank'>[<u>查看文章</u>]</a>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
<br>
<?
}
else
{
?>
<table width="409" border="0" cellpadding="1" cellspacing="1" bgcolor="#666666" align="center">
          <tr align="center"> 
            <td width="405" height="26" colspan="2" background='img/tbg.gif'><strong>文章发布成功!</strong></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            
          <td height="85" colspan="2" align="center"> 标题: 
            <?=$title?>
            
      <p> <a href='add_news_view.php?typeid=<?=$typeid?>&typename=<?=$typename?>'>[<u>发表新文章</u>]</a> 
        &nbsp;&nbsp; <a href='list_news.php'>[<u>文章列表</u>]</a>&nbsp; <a href='<?=$art_php_dir?>/viewart.php?artID=<?=$artID?>' target='_blank'>[<u>查看文章</u>]</a>&nbsp; 
    </td>
          </tr>
      </table>
	  <?
	  }
	  ?>
</body>
<?
echo mysql_error();
?>
</html>