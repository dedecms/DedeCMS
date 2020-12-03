<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
$aid = ereg_replace("[^0-9]","",$aid);
$channelid="1";
$dsql = new DedeSql(false);
//读取归档信息
//------------------------------
$arcQuery = "Select 
#@__channeltype.typename as channelname,
#@__arcrank.membername as rankname,
#@__archives.* 
From #@__archives
left join #@__channeltype on #@__channeltype.ID=#@__archives.channel 
left join #@__arcrank on #@__arcrank.rank=#@__archives.arcrank
where #@__archives.ID='$aid'";
$addQuery = "Select * From #@__addonarticle where aid='$aid'";

$dsql->SetQuery($arcQuery);
$arcRow = $dsql->GetOne($arcQuery);
if(!is_array($arcRow)){
	$dsql->Close();
	ShowMsg("读取档案基本信息出错!","-1");
	exit();
}

$addRow = $dsql->GetOne($addQuery);
if(!is_array($addRow)) $body = "";
else $body = $addRow["body"];

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>更改文章</title>
<style type="text/css">
<!--
body { background-image: url(img/allbg.gif); }
-->
</style>
<link href="base.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../include/dedeajax.js"></script>
<script language='javascript' src='main.js'></script>
<script language="javascript">
<!--
function checkSubmit()
{
   if(document.form1.title.value==""){
	 alert("文章标题不能为空！");
	 document.form1.title.focus();
	 return false;
  }
}
-->
</script>
</head>
<body topmargin="8">
<form name="form1" action="article_eidt_action.php" enctype="multipart/form-data" method="post" onSubmit="return checkSubmit();">
<input type="hidden" name="channelid" value="<?php echo $channelid?>">
<input type="hidden" name="ID" value="<?php echo $aid?>">
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="4%" height="30"><IMG height=14 src="img/book1.gif" width=20> 
        &nbsp;</td>
      <td width="85%"><a href="catalog_do.php?cid=<?php echo $arcRow['typeid']?>&channelid=<?php echo $channelid?>&dopost=listArchives"><u>文章列表</u></a>&gt;&gt;更改文章</td>
      <td width="10%">&nbsp; <a href="catalog_main.php">[<u>栏目管理</u>]</a> </td>
      <td width="1%">&nbsp;</td>
    </tr>
  </table>
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" id="head1" class="htable">
    <tr> 
      <td colspan="2"> 
        <table width="168" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="84" height="24" align="center" background="img/itemnote1.gif">&nbsp;常规参数&nbsp;</td>
            <td width="84" align="center">&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><td height="2"></td></tr>
</table>
  <table width="98%"  border="0" align="center" cellpadding="2" cellspacing="2" id="needset">
    <tr> 
      <td width="400%" height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;文章标题：</td>
            <td width="250"> <input name="title" type="text" id="title" style="width:230" value="<?php echo $arcRow["title"]?>"> 
            </td>
            <td width="90">附加参数：</td>
            <td> <input name="iscommend" type="checkbox" id="iscommend" value="11" class="np"<?php  if($arcRow["iscommend"]>10) echo " checked";?>>
              推荐 
              <input name="isbold" type="checkbox" id="isbold" value="5" class="np"<?php  if($arcRow["iscommend"]==5||$arcRow["iscommend"]==16) echo " checked";?>>
              加粗 
              <input name="isjump" onClick="ShowUrlTrEdit()" type="checkbox" id="isjump" value="1" class="np"<?php  echo $arcRow["redirecturl"]=="" ? "" : " checked";?>>
              跳转网址 </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline" id="redirecturltr" style="display:<?php  echo $arcRow["redirecturl"]=="" ? "none" : "block";?>"> 
        <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;跳转网址：</td>
            <td> <input name="redirecturl" type="text" id="redirecturl" style="width:300" value="<?php echo $arcRow["redirecturl"]?>"> 
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;简略标题：</td>
            <td width="250"> <input name="shorttitle" type="text" value="<?php echo $arcRow["shorttitle"]?>" id="shorttitle" style="width:150"> 
            </td>
            <td width="90">自定属性：</td>
            <td> <select name='arcatt' style='width:150'>
                <option value='0'>普通文档</option>
                <?php 
            	$dsql->SetQuery("Select * From #@__arcatt order by att asc");
            	$dsql->Execute();
            	while($trow = $dsql->GetObject())
            	{
            		if($arcRow["arcatt"]==$trow->att) echo "<option value='{$trow->att}' selected>{$trow->attname}</option>";
            		else echo "<option value='{$trow->att}'>{$trow->attname}</option>";
            	}
            	?>
              </select> </td>
          </tr>
        </table></td>
    </tr>
    <tr id="pictable"> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90" height="81"> &nbsp;缩 略 图：<br/> &nbsp;
              <input type='checkbox' class='np' name='ddisremote' value='1'>
              远程 </td>
            <td width="340"> <input name="picname" type="text" id="picname" style="width:220" value="<?php echo $arcRow["litpic"]?>"> 
              <input type="button" name="Submit" value="浏览..." style="width:60" onClick="SelectImage('form1.picname','');" class='nbt'> 
            </td>
            <td align="center"><img src="<?php if($arcRow["litpic"]!="") echo $arcRow["litpic"]; else echo "img/pview.gif";?>" width="150" height="100" id="picview" name="picview"> 
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;文章来源：</td>
            <td width="250"> <input name="source" type="text" id="source" style="width:160" value="<?php echo $arcRow["source"]?>" size="16"> 
              <input name="selsource" type="button" id="selsource" value="选择" class='nbt'> 
            </td>
            <td width="90">作　者：</td>
            <td> <input name="writer" type="text" id="writer" style="width:120" value="<?php echo $arcRow["writer"]?>"> 
              <input name="selwriter" type="button" id="selwriter" value="选择" class='nbt'> 
            </td>
          </tr>
        </table>
        <script language='javascript'>InitPage();</script> </td>
    </tr>
    <tr>
      <td height="24" colspan="4" class="bline"><table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;文章模板：</td>
            <td><input name="templet" type="text" id="templet" value="<?php echo $arcRow["templet"]?>" size="30"> 
              <input type="button" name="set3" value="浏览..." style="width:60" onClick="SelectTemplets('form1.templet');" class='nbt'>
              （如果模板为空，则使用文章所在栏目定义的模板） </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;文章排序：</td>
            <td width="250"> <select name="sortup" id="sortup" style="width:150">
                <?php 
                $subday = SubDay($arcRow["sortrank"],$arcRow["senddate"]);
                echo "<option value='0'>正常排序</option>\r\n";
                if($subday>0) echo "<option value='$subday' selected>置顶 $subday 天</option>\r\n";
                ?>
                <option value="7">置顶一周</option>
                <option value="30">置顶一个月</option>
                <option value="90">置顶三个月</option>
                <option value="180">置顶半年</option>
                <option value="360">置顶一年</option>
              </select> </td>
            <td width="90">标题颜色：</td>
            <td width="159"> <input name="color" type="text" id="color" style="width:120" value="<?php echo $arcRow["color"]?>"> 
            </td>
            <td> <input name="modcolor" type="button" id="modcolor" value="选取" onClick="ShowColor()"></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;阅读权限：</td>
            <td width="250"> <select name="arcrank" id="arcrank" style="width:150">
                <option value='<?php echo $arcRow["arcrank"]?>'> 
                <?php echo $arcRow["rankname"]?>
                </option>
                <?php 
              $urank = $cuserLogin->getUserRank();
              $dsql = new DedeSql(false);
              $dsql->SetQuery("Select * from #@__arcrank where adminrank<='$urank'");
              $dsql->Execute();
              while($row = $dsql->GetObject()){
              	echo "     <option value='".$row->rank."'>".$row->membername."</option>\r\n";
              }
              ?>
              </select> </td>
            <td width="90">发布选项：</td>
            <td> <input name="ishtml" type="radio" class="np" value="1"<?php if($arcRow["ismake"]!=-1) echo " checked";?>>
              生成HTML 
              <input type="radio" name="ishtml" class="np" value="0"<?php if($arcRow["ismake"]==-1) echo " checked";?>>
              仅动态浏览 </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="75" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90" height="51">&nbsp;文章摘要：</td>
            <td width="250"> <textarea name="description" rows="3" id="description" style="width:200"><?php echo $arcRow["description"]?></textarea> 
            </td>
            <td width="90">关键字：</td>
            <td> <textarea name="keywords" rows="3" id="keywords" style="width:200"><?php echo $arcRow["keywords"]?></textarea> 
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;录入时间：</td>
            <td> 
              <?php 
			         $addtime = GetDateTimeMk($arcRow["senddate"]);
			         echo "$addtime (标准排序和生成HTML名称的依据时间) <input type='hidden' name='senddate' value='".$arcRow["senddate"]."'>";
			        ?>
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;发布时间：</td>
            <td width="381"> 
              <?php 
			$nowtime = GetDateTimeMk($arcRow["pubdate"]);
			echo "<input name=\"pubdate\" value=\"$nowtime\" type=\"text\" id=\"pubdate\" style=\"width:200\">";
			?>
            </td>
            <td width="95" align="center">消费点数：</td>
            <td width="234"><input name="money" type="text" id="money" value="<?php echo $arcRow["money"]?>" size="10"></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">主分类：</td>
            <td width="400"> 
          <?php 
           	$dsql = new DedeSql(false);
           	$seltypeids = $dsql->GetOne("Select ID,typename From #@__arctype where ID='".$arcRow["typeid"]."' ");
			    if(is_array($seltypeids)){
			         echo GetTypeidSel('form1','typeid','selbt1',$arcRow["channel"],$seltypeids['ID'],$seltypeids['typename']);
			    }
			    ?>
            </td>
            <td> （只允许在白色选项的栏目中发布当前类型内容）</td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td height="24" colspan="4" bgcolor="#FFFFFF" class="bline2">
<table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">副分类：</td>
            <td width="400"><?php 
			$seltypeids = $dsql->GetOne("Select ID,typename From #@__arctype where ID='".$arcRow["typeid2"]."' ");
			if(is_array($seltypeids)){
			   echo GetTypeidSel('form1','typeid2','selbt2',$arcRow["channel"],$seltypeids['ID'],$seltypeids['typename']);
			}else{
			   echo GetTypeidSel('form1','typeid2','selbt2',$arcRow["channel"],0,'请选择...');
			}
            ?></td>
            <td>&nbsp; </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td height="24" colspan="4" bgcolor="#FFFFFF">&nbsp; </td>
    </tr>
  </table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr><td height="6"></td></tr>
</table>
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" id="head2" style="border-bottom:1px solid #CCCCCC;">
    <tr> 
      <td colspan="2"> <table width="168" height="24" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="84" align="center" background="img/itemnote1.gif">文章内容</td>
            <td width="84" align="center">&nbsp;</td>
          </tr>
        </table></td>
    </tr>
  </table>
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr><td height="2"></td></tr>
</table>
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" id="adset">
    <tr> 
      <td width="100%" height="24" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;附加选项：</td>
            <td>
            	<input name="remote" type="checkbox" class="np" id="remote" value="1" checked>
              下载远程图片和资源
              <input name="autolitpic" type="checkbox" class="np" id="autolitpic" value="1" checked>
              提取第一个图片为缩略图
            </td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td height="24" bgcolor="#FFFFFF">&nbsp;文章内容：</td>
    </tr>
    <tr> 
      <td> 
        <?php 
	GetEditor("body",$body,450);
	?>
      </td>
    </tr>
  </table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td height="56">
	<table width="100%" border="0" cellspacing="1" cellpadding="1">
        <tr> 
          <td width="17%">&nbsp;</td>
          <td width="83%"><table width="214" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="115"><input name="imageField" type="image" src="img/button_ok.gif" width="60" height="22" border="0"></td>
                <td width="99">
                	<img src="img/button_reset.gif" width="60" height="22" border="0" onClick="location.reload();" style="cursor:hand">
                </td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
</form>
<?php 
$dsql->Close();
?>
</body>
</html>