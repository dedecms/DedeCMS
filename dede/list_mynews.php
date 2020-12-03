<?
require("config.php");
require("inc_dedetag.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>查看站内新闻</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif"><strong>&nbsp;查看站内新闻&nbsp;</strong>[<a href="add_my_news.php"><u>新增站内新闻</u>]</a> [<a href="edit_mynews.php"><u>编辑站内新闻</u></a>] </td>
</tr>
<tr>
    <td height="127" align="center" bgcolor="#FFFFFF"><table width="98%"  border="0" cellspacing="4" cellpadding="2">
      <tr>
        <td height="163" valign="top">
		<?
		$datafile = $base_dir.$art_php_dir."/webnews/news.xml";
		if(!file_exists($datafile))
		{
			$fp = @fopen($datafile,"w") or die("无法创建文件：$datafile");
			fclose($fp);
		}
		$CDTag = new DedeTag();
		$ctp = new DedeTagParse();
		$ctp->SetNameSpace("mynews");
		$ctp->LoadTemplate($datafile);
		$i=1;
		if($ctp->Count!=-1)
		foreach($ctp->CTags as $CDTag)
		{
			echo $CDTag->GetAtt("title")."|<font color='blue'>";
			echo $CDTag->GetAtt("writer")."</font>\r\n";
			echo $CDTag->GetAtt("senddate")."<br>\r\n";
			echo "　　".$CDTag->InnerText;
			if($i!=$ctp->GetCount())
				echo "<hr size='1'>\r\n";
			$i++;
		}
		?>
		</td>
      </tr>
    </table></td>
</tr>
</table>
</body>
</html>