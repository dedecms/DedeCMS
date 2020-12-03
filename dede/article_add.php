<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
$channelid="1";
if(empty($cid)) $cid = 0;
$dsql = new DedeSql(false);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>增加文章</title>
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
function SelectTemplets(fname){
   var posLeft = window.event.clientY-200;
   var posTop = window.event.clientX-300;
   window.open("../include/dialog/select_templets.php?f="+fname, "poptempWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
}

function checkSubmit()
{
  if(document.form1.title.value==""){
	   alert("文章标题不能为空！");
	   return false;
  }
  if(document.form1.typeid.value==0){
	   alert("请选择档案的主类别！");
	   return false;
  }
}
-->
</script>
</head>
<body topmargin="8">
<form name="form1" action="article_add_action.php" enctype="multipart/form-data" method="post" onSubmit="return checkSubmit()">
  <input type="hidden" name="channelid" value="<?php echo $channelid?>">
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="4%" height="30"><IMG height=14 src="img/book1.gif" width=20> &nbsp;</td>
      <td width="85%"><a href="catalog_do.php?cid=<?php echo $cid?>&channelid=<?php echo $channelid?>&dopost=listArchives"><u>文章列表</u></a>&gt;&gt;发布文章</td>
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
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;文章标题：</td>
            <td width="250"> <input name="title" type="text" id="title" style="width:230"> 
            </td>
            <td width="90">附加参数：</td>
            <td> <input name="iscommend" type="checkbox" id="iscommend" value="11" class="np">
              推荐 
              <input name="isbold" type="checkbox" id="isbold" value="5" class="np">
              加粗 
              <input name="isjump" type="checkbox" id="isjump" value="1" onClick="ShowUrlTr()" class="np">
              跳转网址</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline" id="redirecturltr" style="display:none"> 
        <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;跳转网址：</td>
            <td> <input name="redirecturl" type="text" id="redirecturl" style="width:300" value=""> 
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;简略标题：</td>
            <td width="250"> <input name="shorttitle" type="text" id="shorttitle" style="width:150"> 
            </td>
            <td width="90">自定义属性：</td>
            <td> <select name='arcatt' style='width:150'>
                <option value='0'>普通文档</option>
                <?php 
            	$dsql->SetQuery("Select * From #@__arcatt order by att asc");
            	$dsql->Execute();
            	while($trow = $dsql->GetObject())
            	{
            		echo "<option value='{$trow->att}'>{$trow->attname}(att={$trow->att})</option>";
            	}
            	?>
              </select> </td>
          </tr>
        </table></td>
    </tr>
    <tr id="pictable"> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="135" height="81"> &nbsp;缩 略 图：<br/> &nbsp; <input type='checkbox' class='np' name='ddisremote' value='1' id='ddisremote' onClick="CkRemote('ddisremote','litpic')">
              远程图片 <br> </td>
            <td width="464"><table width="100%" border="0" cellspacing="1" cellpadding="1">
                <tr> 
                  <td height="30"> 本地上传请点击“浏览”按钮 
                    <input name="litpic" type="file" id="litpic" class="np" style="width:200" onChange="SeePic(document.picview,document.form1.litpic);"> 
                  </td>
                </tr>
                <tr> 
                  <td height="30"> <input name="picname" type="text" id="picname" style="width:250"> 
                    <input type="button" name="Submit2" value="在网站内选择" style="width:120" onClick="SelectImage('form1.picname','small');" class='nbt'> 
                  </td>
                </tr>
              </table></td>
            <td width="201" align="center"><img src="img/pview.gif" width="150" id="picview" name="picview"></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;文章来源：</td>
            <td width="240"><input name="source" type="text" id="source" style="width:160" size="16"> 
              <input name="selsource" type="button" id="selsource" value="选择" class='nbt'></td>
            <td width="90">作　者：</td>
            <td> <input name="writer" type="text" id="writer" style="width:120"> 
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
            <td> <input name="templet" type="text" id="templet" size="30"> 
              <input type="button" name="set3" value="浏览..." style="width:60" onClick="SelectTemplets('form1.templet');" class='nbt'>
              　（如果模板为空，则使用文章所在栏目定义的模板） </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90" height="22">&nbsp;文章排序：</td>
            <td width="240"> <select name="sortup" id="sortup" style="width:150">
                <option value="0" selected>默认排序</option>
                <option value="7">置顶一周</option>
                <option value="30">置顶一个月</option>
                <option value="90">置顶三个月</option>
                <option value="180">置顶半年</option>
                <option value="360">置顶一年</option>
              </select> </td>
            <td width="90">标题颜色：</td>
            <td> <input name="color" type="text" id="color" style="width:120"> 
              <input name="modcolor" type="button" id="modcolor" value="选取" onClick="ShowColor()" class='nbt'> 
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;阅读权限：</td>
            <td width="240"> <select name="arcrank" id="arcrank" style="width:150">
                <?php 
              $urank = $cuserLogin->getUserRank();
              $dsql->SetQuery("Select * from #@__arcrank where adminrank<='$urank'");
              $dsql->Execute();
              while($row = $dsql->GetObject())
              {
              	echo "     <option value='".$row->rank."'>".$row->membername."</option>\r\n";
              }
              ?>
              </select> </td>
            <td width="90">发布选项：</td>
            <td> <input name="ishtml" type="radio" class="np" value="1" checked>
              生成HTML 
              <input type="radio" name="ishtml" class="np" value="0">
              仅动态浏览</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="70" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90" height="51">&nbsp;文章摘要：</td>
            <td width="240"> <textarea name="description" rows="3" id="description" style="width:200"></textarea> 
            </td>
            <td width="90">关键字：</td>
            <td width="234"> <textarea name="keywords" rows="3" id="keywords" style="width:200"></textarea> 
            </td>
            <td width="146" align="center"> <input name="autokey" type="checkbox" onClick="ShowHide('keywords');"; class="np" id="autokey" value="1">
              自动 <br/>
              用空格分开<br/> <input type="button" name="Submit" value="浏览..." style="width:56;height:20" onClick="SelectKeywords('form1.keywords');" class='nbt'> 
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;发布时间：</td>
            <td width="400"> 
              <?php 
			$nowtime = GetDateTimeMk(mytime());
			echo "<input name=\"pubdate\" value=\"$nowtime\" type=\"text\" id=\"pubdate\" style=\"width:200\">";
			?>
            </td>
            <td width="82" align="center">消费点数：</td>
            <td> <input name="money" type="text" id="money" value="0" size="10"> 
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;文章主栏目：</td>
            <td width="400"> 
            <?php 
           	if(empty($cid)) echo GetTypeidSel('form1','typeid','selbt1',$channelid);
           	else{
           	  $typeOptions = GetOptionList($cid,$cuserLogin->getUserChannel(),$channelid);
		          echo "<select name='typeid' style='width:300'>\r\n";
              echo "<option value='0'>请选择主分类...</option>\r\n";
              echo $typeOptions;
              echo "</select>";
            }
			      ?>
            </td>
            <td>（只允许在白色选项的栏目中发布当前类型内容）</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;文章副栏目：</td>
            <td> 
			  <?php echo GetTypeidSel('form1','typeid2','selbt2',$channelid)?>
			  </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="6" colspan="4"></td>
    </tr>
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
      <td width="100%" height="24" class="bline">
	  <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;附加选项：</td>
            <td>
            	<input name="remote" type="checkbox" class="np" id="remote" value="1" checked>
              下载远程图片和资源 
              <input name="dellink" type="checkbox" class="np" id="dellink" value="1">
              删除非站内链接
              <input name="autolitpic" type="checkbox" class="np" id="autolitpic" value="1" checked>
              提取第一个图片为缩略图
              </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td width="100%" height="24" class="bline">
	  <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;分页方式：</td>
            <td>
            	<input name="sptype" type="radio" class="np" value="hand"<?php if($cfg_arcautosp=='否') echo " checked"?>>
              手动分页 
              <input type="radio" name="sptype" value="auto" class="np"<?php if($cfg_arcautosp=='是') echo " checked"?>>
              自动分页　自动分页大小： 
              <input name="spsize" type="text" id="spsize" value="<?php echo $cfg_arcautosp_size?>" size="6">
              (K)
              </td>
          </tr>
          <tr> 
            <td width="90">&nbsp;</td>
            <td>（手动分页在需分的地方加上 <font color="#FF0000">#p#分页标题#e# </font>，编辑器里包含该按钮）</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" bgcolor="#FFFFFF">&nbsp;文章内容：</td>
    </tr>
    <tr> 
      <td> 
        <?php 
	GetEditor("body","",450);
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