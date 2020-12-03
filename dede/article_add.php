<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
$channelid="1";
if(empty($cid)) $cid = 0;
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
<link rel="stylesheet" type="text/css" media="all" href="../include/calendar/calendar-win2k-1.css" title="win2k-1" />
<script type="text/javascript" src="../include/calendar/calendar.js"></script>
<script type="text/javascript" src="../include/calendar/calendar-cn.js"></script>
<script language="javascript">
<!--
function showHide(objname)
{
   var obj = document.getElementById(objname);
   if(obj.style.display=="none") obj.style.display = "block";
	 else obj.style.display="none";
}
function checkSubmit()
{
   if(document.form1.title.value==""){
	 alert("文章标题不能为空！");
	 document.form1.title.focus();
	 return false;
  }
  if(document.form1.seltypeid.value==0&&document.form1.typeid.value==0){
	   alert("请选择档案的主类别！");
	   return false;
  }
}
function ShowColor(){
	var fcolor=showModalDialog("img/color.htm?ok",false,"dialogWidth:106px;dialogHeight:110px;status:0;dialogTop:"+(window.event.clientY+120)+";dialogLeft:"+(window.event.clientX));
	if(fcolor!=null && fcolor!="undefined") document.form1.color.value = fcolor;
}

function SeePic(img,f)
{
   if ( f.value != "" ) { img.src = f.value; }
}

function SelectImage(fname,vlist)
{
   var posLeft = window.event.clientY-100;
   var posTop = window.event.clientX-400;
   window.open("../include/dialog/select_images.php?f="+fname+"&imgstick="+vlist, "popUpImagesWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
}

function SelectKeywords(f)
{
	var posLeft = window.event.clientY-100;
  var posTop = window.event.clientX-500;
  window.open("article_keywords_select.php?f="+f, "popUpkwWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=450,left="+posLeft+", top="+posTop);
}

-->
</script>
</head>
<body topmargin="8">
<form name="form1" action="action_article_save.php" enctype="multipart/form-data" method="post" onSubmit="return checkSubmit();">
<input type="hidden" name="channelid" value="<?=$channelid?>">
<table width="98%"  border="0" align="center" cellpadding="1" cellspacing="1">
  <tr> 
    <td width="100%" height="26" colspan="2" background="img/tbg.gif" style="border:solid 1px #666666">
	  <table width="500" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="25" align="center">
          	<img src="img/dedeexplode.gif" width="11" height="11" border="0" onClick="showHide('infotable')" style="cursor: hand;">
          	</td>
          <td width="85"><strong>通用参数：</strong></td>
          <td width="390">
          <a href="catalog_do.php?cid=<?=$cid?>&channelid=<?=$channelid?>&dopost=listArchives">[<u>文章列表</u>]</a>
          &nbsp;
          <a href="catalog_main.php">[<u>栏目管理</u>]</a>
          </td>
        </tr>
      </table>
      </td>
  </tr>
</table>
<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><td height="2"></td></tr>
</table>
  <table width="98%"  border="0" align="center" cellpadding="2" cellspacing="2" id="infotable">
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">文章标题：</td>
            <td width="224"><input name="title" type="text" id="title" style="width:200"></td>
            <td width="73">附加参数：</td>
            <td width="223"> <input name="iscommend" type="checkbox" id="iscommend" value="11" class="np">
              推荐 
              <input name="ispic" type="checkbox" id="ispic" value="2" class="np" onClick="showHide('pictable');";>
              图片 
              <input name="isbold" type="checkbox" id="isbold" value="5" class="np">
              加粗</td>
          </tr>
        </table></td>
    </tr>
    <tr id="pictable" style="display:none"> 
      <td height="24" colspan="4" class="bline"> <table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="78" height="81">上传略略图：</td>
            <td width="337"> <table width="100%" border="0" cellspacing="1" cellpadding="1">
                <tr> 
                  <td height="30"> <input name="litpic" type="file" id="litpic" style="width:280" onChange="SeePic(document.picview,document.form1.litpic);"> 
                  </td>
                </tr>
                <tr> 
                  <td height="30"> <input name="picname" type="text" id="picname" style="width:154"> 
                    <input type="button" name="Submit" value="在网站内选择" style="width:120" onClick="SelectImage('form1.picname','small');"> 
                  </td>
                </tr>
              </table></td>
            <td width="185" align="center"><img src="img/pview.gif" width="150" id="picview" name="picview"></td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">文章来源：</td>
            <td width="224"> <input name="source" type="text" id="source" style="width:200"> 
            </td>
            <td width="63">作　者：</td>
            <td width="159"> <input name="writer" type="text" id="writer" style="width:120"> 
            </td>
            <td width="74" align="center"> 
              <!--input name="selwriter" type="button" id="selwriter" value="选择"-->
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">文章排序：</td>
            <td width="224"> <select name="sortup" id="sortup" style="width:150">
                <option value="0" selected>默认排序</option>
                <option value="7">置顶一周</option>
                <option value="30">置顶一个月</option>
                <option value="90">置顶三个月</option>
                <option value="180">置顶半年</option>
                <option value="360">置顶一年</option>
              </select> </td>
            <td width="63">标题颜色：</td>
            <td width="159"> <input name="color" type="text" id="color" style="width:120"> 
            </td>
            <td width="74" align="center"><input name="modcolor" type="button" id="modcolor" value="选取" onClick="ShowColor()"></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">阅读权限：</td>
            <td width="224"> <select name="arcrank" id="arcrank" style="width:150">
                <?
              $urank = $cuserLogin->getUserRank();
              $dsql = new DedeSql(false);
              $dsql->SetQuery("Select * from #@__arcrank where adminrank<='$urank'");
              $dsql->Execute();
              while($row = $dsql->GetObject())
              {
              	echo "     <option value='".$row->rank."'>".$row->membername."</option>\r\n";
              }
              $dsql->Close();
              ?>
              </select> </td>
            <td width="63">发布选项：</td>
            <td><input name="ishtml" type="radio" class="np" value="1" checked>
              生成HTML 
              <input type="radio" name="ishtml" class="np" value="0">
              仅动态浏览</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="70" colspan="4" class="bline"> 
        <table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80" height="51">文章摘要：</td>
            <td width="224"> <textarea name="description" rows="3" id="description" style="width:200"></textarea> 
            </td>
            <td width="63">关键字：</td>
            <td width="159"> <textarea name="keywords" rows="3" id="keywords" style="width:150"></textarea> 
            </td>
            <td width="74" align="center"> <input name="autokey" type="checkbox" onClick="showHide('keywords');"; class="np" id="autokey" value="1">
              自动 <br/>
              用空格分开<br/> <input type="button" name="Submit" value="浏览..." style="width:56;height:20" onClick="SelectKeywords('form1.keywords');"> 
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">发布时间：</td>
            <td width="286"> 
              <?
			$nowtime = GetDateTimeMk(time());
			echo "<input name=\"pubdate\" value=\"$nowtime\" type=\"text\" id=\"pubdate\" style=\"width:200\">";
			echo "<input name=\"selPubtime\" type=\"button\" id=\"selkeyword\" value=\"选择\" onClick=\"showCalendar('pubdate', '%Y-%m-%d %H:%M:00', '24');\">";
			?>
            </td>
            <td width="82" align="center">消费点数：</td>
            <td width="152"><input name="money" type="text" id="money" value="0" size="10"></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td width="100%" height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">附加选项：</td>
            <td> <input name="remote" type="checkbox" class="np" id="remote" value="1" checked>
              下载远程图片和资源 
              <input name="dellink" type="checkbox" class="np" id="dellink" value="1">
              删除非站内链接</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">文章主栏目：</td>
            <td width="446"> 
              <?
           	$tl = new TypeLink($cid);
           	$typeOptions = $tl->GetOptionArray(0,$cuserLogin->getUserChannel(),$channelid);
           	if($cid>0)
           	{
           	   echo "<input type='hidden' name='seltypeid' value='$cid'>"; 
			         echo "<span id='type1info'>".$tl->GetPositionLink(false)."</span>"; 
            }else
            { 
			         echo "<input type='hidden' name='seltypeid' value='0'>"; 
			         echo "<select name='typeid' style='width:300'>\r\n";
               echo "<option value='0' selected>请选择主分类...</option>\r\n";
            	 echo $typeOptions;
            	 echo "</select>";
			      } 
			    ?>
            </td>
            <td width="74" align="center"> <input name='modtype' type='button' onClick="showHide('typeidSelectFrom');"; id='modtype3' value='更改'> 
            </td>
          </tr>
        </table></td>
    </tr>
    <tr id='typeidSelectFrom' style='display:none'> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">&nbsp;</td>
            <td> 
              <?
            if($cid>0){
              echo "<select name='typeid' style='width:300'>\r\n";
              echo "<option value='0' selected>请选择主分类...</option>\r\n";
              echo $typeOptions;
              echo "</select>";
            }
            ?>
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">文章副栏目：</td>
            <td width="446"> 
              <?
            echo "<select name='typeid2' style='width:300'>\r\n";
            echo "<option value='0' selected>请选择副分类...</option>\r\n";
            echo $typeOptions;
            echo "</select>";
            $tl->Close();
            ?>
            </td>
            <td width="74" align="center">&nbsp; </td>
          </tr>
        </table></td>
    </tr>
  </table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr><td height="6"></td></tr>
</table>
<table width="98%" border="0" align="center" cellpadding="1" cellspacing="0">
  <tr>
    <td><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="100%" height="26" colspan="2" background="img/tbg.gif" style="border:solid 1px #666666"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="5%" align="center">
                	<img src="img/dedeexplode.gif" width="11" height="11" border="0" onClick="showHide('bodytable')" style="cursor: hand;">
                </td>
                <td width="61%"><strong>文章内容：</strong></td>
                <td width="34%">&nbsp;</td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr><td height="2"></td></tr>
</table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" id="bodytable">
  <tr>
    <td>
      <?
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
</body>
</html>