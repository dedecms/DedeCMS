<?
//----------------
$nowurl = $PHP_SELF;
$qstr = getenv("QUERY_STRING");
if($qstr!="") $nowurl.="?".$qstr;
setcookie("ENV_GOBACK_URL","$nowurl",time()+36000);
//--------------------------------------------------------------------
require("config.php");
require("inc_listpage.php");
$conn = connectMySql();
$pagesize = 30;
$j=0;
if(!isset($page)) $page="";
if(!isset($keyword)) $keyword="";
if(!isset($total)) $total="";
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>论坛管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="99%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <form>
  <tr>
    <td height="19" bgcolor="#FFFFFF"> &nbsp;<b>论坛管理</b> &nbsp;关键字：<input type='text' size='20' name='keyword'>&nbsp;<input type='submit' name='sb' value=' 确定 '> &nbsp; <a href='bbs_type.php'><u><b>论坛栏目管理</b></u></a> &nbsp;<a href='/rap' target='_blank'><u><b>查看论坛</b></u></a></td>
</tr>
</form>
<tr>
    <td height="215" bgcolor="#FFFFFF" valign="top">
    <?
if($total == 0){
         if($page==0) $page=1;
         $querystring = "Select ID from bbs where reID=0 And (username like '%$keyword%' Or title like '%$keyword%')";
         $result = mysql_query($querystring,$conn);
         $total = mysql_num_rows($result);
}
$pre = $page-1;
$start = $pre*$pagesize;
$query = "Select ID,username,title,username,re,sdtime,counter,pic,face From bbs where reID=0 And (username like '%$keyword%' Or title like '%$keyword%') order by ID desc limit 0,$pagesize";
$result = mysql_query($query,$conn);
?>
<table width='100%' border='0' cellpadding='0' cellspacing='0'>
   <tr bgcolor='#698AB6'> 
      <td width='40%'>&nbsp;标&nbsp;&nbsp;题：</td>
      <td width='9%' align='center'>作&nbsp;者</td>
      <td width='5%' align='center'>回复</td>
      <td width='5%' align='center'>点击</td>
      <td width='5%' align='center'>贴图</td>
      <td width='20%' align='center'>发表时间</td>
      <td width='16%' align='center'>处理</td>
    </tr>
    <?
    $i = 0;
    while($row=mysql_fetch_object($result))
    {
    $ID = $row->ID;
    $title = $row->title;
    $username = $row->username;
    $re = $row->re;
    $face = $row->face;
    $sdtime = $row->sdtime;
    $counter = $row->counter;
    $pic = $row->pic;
    if($pic==0) $img="<img src='/rap/img/folder.gif'>";
    else $img="<img src='/rap/img/hotfolder.gif'>";
    if($i==0)
    {
    	$bgcolor = " bgcolor='#EEF2F7'";
        $i=1;
    }
    else
    {
    	$bgcolor = " bgcolor='#FFFFFF'";
    	$i=0;
    }
    $j++;		
    $line = "
    <tr$bgcolor height='25'> 
      <td>&nbsp;<img src='/rap/ico/$face.gif' width='16' height='16'>&nbsp;<a href='/rap/view.php?ID=$ID' target='_blank'>$title</a></td>
      <td align='center'>$username</td>
      <td align='center'>$re</td>
      <td align='center'>$counter</td>
      <td align='center'>$img</td>
      <td align='center'>$sdtime</td>
      <td width='16%' align='center'><a href='bbs_d.php?ID=$ID'>[删除]</a></td>
    </tr>";
    echo $line;
     }
   ?>
    <tr> 
      <td colspan='7' height='30' align='right'>
      &nbsp;&nbsp;<? listpage("list_bbs.php",$total,$page,$pagesize,"&keyword=$keyword"); ?>
      </td>
    </tr>
</table>    
    </td>
</tr>
</table>
</body>
</html>