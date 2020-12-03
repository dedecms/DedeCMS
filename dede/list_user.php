<?
require("config.php");
require("inc_page_list.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+36000,"/");
$conn = connectMySql();
//----------------
$nowurl = $dedeNowurl;
//--------------------------------------------------------------------
//读取列表的相关参数
$pagesize=20;
if(!isset($keyword)) $keyword="";
else $keyword = trim($keyword);
if(!isset($pageurl)) $pageurl=$nowurl;
if(!isset($utype)) $utype="0";
else $utype = trim($utype);
if($utype=="0") $typesql = "";
if($utype=="1") $typesql = " And dede_member.isup>0";
if($utype=="2") $typesql = " And dede_member.rank>0";
$sql = "Select dede_member.ID,dede_member.userid,dede_member.pwd,dede_member.uname,dede_member.sex,dede_member.jointime,dede_member.logintime,dede_member.rank,dede_member.city,dede_member.isup,dede_aera.name as aera From dede_member left join dede_aera on dede_member.aera=dede_aera.ID where (dede_member.userid like '%$keyword%' Or dede_member.uname like '%$keyword%' Or dede_member.email like '%$keyword%') $typesql";
if(!isset($total_record))
{
      $rs=mysql_query("Select count(ID) as dd From dede_member where (dede_member.userid like '%$keyword%' Or dede_member.uname like '%$keyword%' Or dede_member.email like '%$keyword%') $typesql",$conn);
      $row=mysql_fetch_object($rs);
      $total_record = $row->dd;
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>会员管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script src='menu.js' language='JavaScript'></script>
</head>
<body background='img/allbg.gif' leftmargin='6' topmargin='6'>
<table width="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#666666">
  <tr> 
    <td height="26" colspan="9"  background='img/tbg.gif'>&nbsp;<b>会员管理</b></td>
  </tr>
  <form name="form1">
  <tr> 
    <td height="30" colspan="9" bgcolor="#FFFFFF">&nbsp;查找用户(仅搜昵称、用户名和Email)：
    <input type='text' style='width:100;height:18' name='keyword' value='<?=$keyword?>'>
    <input type="radio" name="utype" value="0" class="np"<?if($utype=="0") echo "checked";?>> 所有会员
    <input type="radio" name="utype" value="1" class="np"<?if($utype=="1") echo "checked";?>> 待升级
    <input type="radio" name="utype" value="2" class="np"<?if($utype=="2") echo "checked";?>> 大于普通
    <input type='submit' value='确定' style='width:50;height:18'></td>
  </tr>
  </form>
  <tr align="center" bgcolor="#F9F8F2"> 
    <td width="60" height="18">昵称</td>
    <td width="40">性别</td>
    <td width="60">地区</td>
    <td width="100">注册时间</td>
    <td width="100">登录时间</td>
    <td width="50">级别</td>
    <td width="80">状态</td>
    <td width="80">用户密码</td>
    <td width="100">操作</td>
  </tr>
  <?
        $sql.=" order by dede_member.logintime desc ".get_limit($pagesize);
        if($total_record!=0)
        {
        	$rs = mysql_query($sql,$conn);
        	while($row=mysql_fetch_object($rs))
        	{
        		$sex = $row->sex;
        		if($sex==1) $sex="男";
        		else $sex="女";
        		$rank = $row->rank;
        		$isup = $row->isup;
        		if($rank==0) $rankn="普通";
        		else
        		{
        			$rs2 = mysql_query("Select * from dede_membertype where rank='$rank'",$conn);
        			$row2 = mysql_fetch_array($rs2);
        			$rankn = cn_substr($row2[2],4);
        		}
        		if($isup==0) $isupn="正常";
        		else
        		{
        			$rs2 = mysql_query("Select * from dede_membertype where rank='$isup'",$conn);
        			$row2 = mysql_fetch_array($rs2);
        			$isupn = "申请".$row2[2];
        		}
        ?>
  <tr align="center" bgcolor="#FFFFFF" height="18"> 
    <td><a href='user_view.php?ID=<?=$row->ID?>'>
      <u><?=$row->uname?></u>
      </a></td>
    <td>
      <?=$sex?>
    </td>
    <td>
      <?=$row->aera.$row->city?>
    </td>
    <td>
      <?=$row->jointime?>
    </td>
    <td>
      <?=$row->logintime?>
    </td>
    <td>
      <?=$rankn?>
    </td>
    <td>
      <?=$isupn?>
    </td>
    <td>
      <?=$row->userid."|".$row->pwd?>
    </td>
    <td>
    <a href='user_del.php?ID=<?=$row->ID?>&user=<?=$row->uname?>'>[删除]</a>
    <a href='user_check.php?ID=<?=$row->ID?>&nowrank=<?=$rank?>'>[升级]</a></td>
  </tr>
  <?
               }
        }
        ?>
  <tr align="center" bgcolor="#F9F8F2"> 
    <td height="18" colspan="9"> 
      <?
          get_page_list($pageurl,$total_record,$pagesize);
          ?>
    </td>
  </tr>
</table>
</body>
</html>
