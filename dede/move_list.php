<?
require_once("config.php");
require_once("inc_typelink.php");
if(empty($typeid)) $typeid="";
if(empty($job)) $job="movelist";
if($typeid=="") exit();
list($ID,$typename)=split("`",$typeid);
$tl = new typeLink();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>移动列表</title>
<style type="text/css">
body {background-image: url(img/allbg.gif);}
</style>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body topmargin="8">
<table width="98%"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
  <tr>
    <td width="100%" height="24" colspan="2" background="img/tbg.gif">
    &nbsp;<a href="list_type.php"><u>频道管理</u></a>&gt;&gt;移动列表
    </td>
  </tr>
  <tr>
    <td height="200" colspan="2" valign="top" bgcolor="#FFFFFF"> 
      <form name="form1" method="get" action="move_listok.php">
	    <table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr> 
            <td colspan="2" height="12"></td>
          </tr>
          <tr> 
            <td height="25" colspan="2" bgcolor="#F2F8FB">
            &nbsp;移动目录时不会删除原来已创建的列表，移动后需重新对类目创建HTML。 
              <input name="typeid" type="hidden" id="typeid" value="<?=$ID?>">
              <input name="job" type="hidden" id="job" value="<?=$job?>">
              </td>
          </tr>
          <tr> 
            <td width="30%" height="25">你选择的类目是：</td>
            <td width="70%">
            <?
			echo "$typename($ID)";
            ?>
            </td>
          </tr>
          <tr> 
            <td height="30">你希望<?if($job=="movelist") echo "移动"; else echo "合并";?>到那个类目？</td>
            <td>
            <select name="gototype">
                <option value='0'>移动为顶级类目</option>
                <?
				if($cuserLogin->getUserChannel()<=0)
					$tl->GetOptionArray();
				else
					$tl->GetOptionArray(0,$cuserLogin->getUserChannel());
				?>
              </select>
            </td>
          </tr>
          <tr> 
            <td height="25" colspan="2" bgcolor="#F2F8FB">
            &nbsp;不允许从父级移动到子级目录，只允许子级到更高级或同级或不同父级的情况。
             </td>
          </tr>
          <tr> 
            <td height="74">&nbsp;</td>
            <td>
            <input type="submit" name="Submit" value="确定操作"> 　 
            <input name="Submit11" type="button" id="Submit11" value="-不理返回-" onClick="history.go(-1);">
            </td>
          </tr>
        </table>
	  </form>
	  </td>
  </tr>
</table>
</body>
</html>