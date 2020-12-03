<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<script language='javascript'>
function ShowHide(objname)
{
   var obj = document.getElementById(objname);
   if(obj.style.display=="none") obj.style.display = "block";
	 else obj.style.display="none";
}

function ShowItem(objname)
{
 	var obj = document.getElementById(objname);
 	obj.style.display = "block";
}

function AddMyField(mfnum)
{
var  sitem = "";
var enddd = 0;
if(mfnum=="") 
{
	alert("增加字段数目不能为空！");
	return;
}
enddd = parseInt(mfnum) + 4;
//alert(enddd);
for(i=6;i<=enddd;i++)
{
  sitem += "<table width=98% border=0 cellspacing=0 cellpadding=0>";
  
  sitem += "<tr><td colspan=3>";
  
  sitem += "<table width=100% border=0 cellspacing=0 cellpadding=0><tr>";
  sitem += "<td height=24 bgcolor=#F7FDE1 width='18%'><b>字段"+i+"</b>—字段名：</td>";
  sitem += "<td bgcolor=#F7FDE1 width='32%'> <input name='field"+i+"' type='text' id='field"+i+"' value='' size='15'></td>";
  sitem += "<td bgcolor=#F7FDE1 width='18%'>字段值：</td>";
  sitem += "<td bgcolor=#F7FDE1 width='32%'> <input name='value"+i+"' type=text id='value"+i+"' value='[var:内容]' size='15'></td>";
  sitem += "</tr></table>";
  
  sitem += "</td></tr>";
  
  sitem += "<tr> ";
  sitem += "<td width=18% height=80>匹配区域：<br/>变量：<br/>[var:内容]</td>";
  sitem += "<td width=82% colspan=2><textarea name='match"+i+"' cols='20' rows='4' id='match"+i+"' style='width:90%'></textarea></td>";
  sitem += "</tr>";
  
  sitem += "<tr> ";
  sitem += "<td width=18%>过滤规则：</td>";
  sitem += "<td width=42%><textarea name='trim"+i+"' cols='20' rows='3' id='trim"+i+"' style='width:90%'></textarea></td>";
  sitem += "<td width=40%>";
  //sitem += "<input name='isunit"+i+"' type='checkbox' value='1' class='np'>如果内容分多页，合并字段内容<br/>";
  sitem += "<input name='isdown"+i+"' type='checkbox' value='1' class='np'>下载字段里的多媒体资源";
  sitem += "</td>";
  sitem += "</tr>";
  
  sitem += "</table>";
}
document.all.myfield.innerHTML = sitem;
}
</script>
<title>新增采集节点</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <form name="form1" method="post" action="action_co_add.php">
    <tr> 
      <td height="20" background='img/tbg.gif'> <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="47%" height="18"><b>新增采集节点：</b></td>
            <td width="53%" align="right"> <input type="button" name="b11" value="返回节点管理页" class="np2" style="width:110" onClick="location.href='co_main.php';"> 
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td bgcolor="#F2F6E5"> <table width="400" border="0" cellspacing="0" cellpadding="0">
          <tr class="top" onClick="ShowHide('sitem');" style="cursor:hand"> 
            <td width="26" align="center"><img src="img/file_tt.gif" width="7" height="8"></td>
            <td width="374">节点基本信息<a name="d1"></a></td>
          </tr>
        </table></td>
    </tr>
    <tr id="sitem" style="display:block"> 
      <td height="87" bgcolor="#FFFFFF"><table width="98%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="18%" height="24">节点名称：</td>
            <td width="32%"><input name="notename" type="text" id="notename" style="width:150"></td>
            <td width="18%">页面编码：</td>
            <td width="32%"> <input type="radio" name="language" class="np" value="gb2312" checked>
              GB2312 
              <input type="radio" name="language" class="np" value="utf-8">
              UTF8 
              <input type="radio" name="language" class="np" value="big5">
              BIG5 </td>
          </tr>
          <tr> 
            <td height="24"> 图片相对网址： </td>
            <?
		$aburl = "";
		$curl = GetCurUrl();
		$curls = explode("/",$curl);
		for($i=0;$i<count($curls)-2;$i++){
			if($i!=0) $aburl.= "/".$curls[$i];
		}
		$aburl .= "/upimg";
          ?>
            <td><input name="imgurl" type="text" id="imgurl" style="width:150" value="<?=$aburl?>"></td>
            <td>物理路径：</td>
            <td><input name="imgdir" type="text" id="imgdir" style="width:150" value="../upimg"></td>
          </tr>
          <tr> 
            <td height="24">导出分类ID：</td>
            <td colspan="3"> 
              <?
       if(empty($cid)) $cid="0";
       $tl = new TypeLink($cid);
       $typeOptions = $tl->GetOptionArray($cid,$cuserLogin->getUserChannel(),1);
       echo "<select name='typeid' style='width:200'>\r\n";
       if($cid=="0") echo "<option value='0' selected>请选择分类...</option>\r\n";
       echo $typeOptions;
       echo "</select>";
	$tl->Close();
		?>
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td bgcolor="#F2F6E5"> <table width="400" border="0" cellspacing="0" cellpadding="0">
          <tr class="top" onClick="ShowHide('slist');" style="cursor:hand"> 
            <td width="26" align="center"><img src="img/file_tt.gif" width="7" height="8"></td>
            <td width="374">采集列表获取规则</td>
          </tr>
        </table></td>
    </tr>
    <tr id="slist" style="display:block"> 
      <td height="76" bgcolor="#FFFFFF"><table width="98%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="18%" height="24">来源网址：</td>
            <td><input name="sourceurl" type="text" id="sourceurl" style="width:300" value="http://"> 
            </td>
          </tr>
          <tr> 
            <td height="24">来源属性：</td>
            <td> <input name="sourcetype" type="radio" class="np" value="list" checked>
              含有文章网址的列表 
              <input type="radio" name="sourcetype" class="np" value="archives">
              指定文章的URL </td>
          </tr>
          <tr> 
            <td height="24">来源规则：</td>
            <td><input name="source" type="radio" class="np" id="radio" value="single" checked>
              单一网址 
              <input name="source" type="radio" id="radio" value="var" class="np">
              序列网址（用 [var:分页] 表示序列变量） 
              <input name="source" type="radio" id="source" value="app" class="np" onClick="ShowHide('surls');">
              手工指定网址</td>
          </tr>
          <tr> 
            <td height="24">分页变量起始值：</td>
            <td> <input name="varstart" type="text" id="varstart" size="15">
              　　变量结束值： 
              <input name="varend" type="text" id="varend" size="15">
              　表示 [var:分页] 的范围） </td>
          </tr>
          <tr> 
            <td height="24">文章网址需包含：</td>
            <td> <input name="need" type="text" id="cannot" size="15">
              　网址不能包含： 
              <input name="cannot" type="text" id="cannot" size="15">
              　(正则)</td>
          </tr>
          <tr align="center" id="surls" style="display:none"> 
            <td height="143" colspan="2"> <table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td height="24">手工指定网址：（每行一条网址，不能在手工指定网址中使用变量）</td>
                </tr>
                <tr> 
                  <td align="center"> <textarea name="sourceurls" id="textarea" style="width:100%;height:120"></textarea> 
                  </td>
                </tr>
              </table></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td bgcolor="#F2F6E5"> <table width="400" border="0" cellspacing="0" cellpadding="0">
          <tr class="top" onClick="ShowHide('sart');" style="cursor:hand"> 
            <td width="26" align="center"><img src="img/file_tt.gif" width="7" height="8"></td>
            <td width="374">网页内容获取规则<a name="d2"></a></td>
          </tr>
        </table></td>
    </tr>
    <tr id="sart" style="display:block"> 
      <td height="113" bgcolor="#FFFFFF"><table width="98%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="18%" height="60">分页匹配规则：<br/>
              可用变量：<br/>
              [var:分页区域]</td>
            <td colspan="2"><textarea name="sppage" rows="3" id="sppage" style="width:90%"></textarea></td>
            <td width="48%"> <input name="sptype" type="radio" value="full" class="np" checked>
              全部列出形式<br/> <input type="radio" name="sptype" class="np" value="next">
              上下页形式 </td>
          </tr>
          <tr> 
            <td height="24" colspan="4"><strong> 　设置说明：</strong><br/>
              　１、规则：在匹配区域规则中，规则一般为“<font color="#FF0000">起始无重复HTML[var:内容]结尾无重复HTML</font>”，匹配区域规则不是正则表达式，内容过滤规则才是正则表达式。<br/>
              　２、变量：如果你的字段值使用的不是变量，，而是指定的其它值，则导出时直接使用该值，并且采集时不会分析该项目。</td>
          </tr>
          <tr> 
            <td height="24" bgcolor="#F7FDE1"><b>文章标题</b>—字段名：</td>
            <td width="23%" bgcolor="#F7FDE1"> <input name="field1" type="text" id="field2" value="title" size="15"></td>
            <td width="11%" bgcolor="#F7FDE1">字段值：</td>
            <td bgcolor="#F7FDE1"> <input name="value1" type="text" id="value1" value="[var:内容]" size="15"></td>
          </tr>
          <tr> 
            <td height="24" colspan="4"><table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="18%" height="60">匹配区域：<br/>
                    变量：<br/>
                    [var:内容] <br/> </td>
                  <td height="71" colspan="2"> <textarea name="match1" rows="3" id="textarea2" style="width:90%"><title>[var:内容]</title></textarea></td>
                </tr>
                <tr> 
                  <td height="30">过滤规则：</td>
                  <td width="42%" height="20"> <textarea name="trim1" cols="20" rows="2" id="textarea4" style="width:90%"></textarea> 
                  </td>
                  <td width="40%" height="20">&nbsp;</td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td height="24" bgcolor="#F7FDE1"><strong>文章来源</strong>—字段名：</td>
            <td bgcolor="#F7FDE1"><input name="field2" type="text" id="field2" value="source" size="15"></td>
            <td bgcolor="#F7FDE1">字段值：</td>
            <td bgcolor="#F7FDE1"><input name="value2" type="text" id="value2" value="[var:内容]" size="15"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24" colspan="4"><table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="18%" height="60">匹配区域：<br/>
                    变量：<br/>
                    [var:内容] <br/> </td>
                  <td height="71" colspan="2"> <textarea name="match2" rows="3" id="textarea5" style="width:90%"></textarea></td>
                </tr>
                <tr> 
                  <td height="30">过滤规则：</td>
                  <td width="42%" height="20"> <textarea name="trim2" cols="20" rows="2" id="textarea6" style="width:90%"></textarea> 
                  </td>
                  <td width="40%" height="20">&nbsp;</td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td height="24" bgcolor="#F7FDE1"><strong>文章作者</strong>—字段名：</td>
            <td bgcolor="#F7FDE1"><input name="field3" type="text" id="field3" value="writer" size="15"></td>
            <td bgcolor="#F7FDE1">字段值：</td>
            <td bgcolor="#F7FDE1"><input name="value3" type="text" id="value3" value="[var:内容]" size="15"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24" colspan="4"><table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="18%" height="60">匹配区域：<br/>
                    变量：<br/>
                    [var:内容] <br/> </td>
                  <td height="71" colspan="2"> <textarea name="match3" rows="3" id="textarea7" style="width:90%"></textarea></td>
                </tr>
                <tr> 
                  <td height="30">过滤规则：</td>
                  <td width="42%" height="20"> <textarea name="trim3" cols="20" rows="2" id="textarea8" style="width:90%"></textarea> 
                  </td>
                  <td width="40%" height="20">&nbsp;</td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td height="24" bgcolor="#F7FDE1"><strong>发布时间</strong>—字段名：</td>
            <td bgcolor="#F7FDE1"><input name="field4" type="text" id="field4" value="pubdate" size="15"></td>
            <td bgcolor="#F7FDE1">字段值：</td>
            <td bgcolor="#F7FDE1"><input name="value4" type="text" id="value4" value="[var:内容]" size="15"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="24" colspan="4"><table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="18%" height="60">匹配区域：<br/>
                    变量：<br/>
                    [var:内容] <br/> </td>
                  <td height="71" colspan="2"> <textarea name="match4" rows="3" id="textarea9" style="width:90%"></textarea></td>
                </tr>
                <tr> 
                  <td height="30">过滤规则：</td>
                  <td width="42%" height="20"> <textarea name="trim4" cols="20" rows="2" id="textarea10" style="width:90%"></textarea> 
                  </td>
                  <td width="40%" height="20">&nbsp;</td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td height="24" bgcolor="#F7FDE1"><b>文章内容</b>—字段名：</td>
            <td bgcolor="#F7FDE1"> <input name="field5" type="text" id="field5" value="body" size="15"></td>
            <td bgcolor="#F7FDE1">字段值：</td>
            <td bgcolor="#F7FDE1"> <input name="value5" type="text" id="value5" value="[var:内容]" size="15"></td>
          </tr>
          <tr> 
            <td height="24" colspan="4"><table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="18%" height="80">匹配区域：<br/>
                    变量：<br/>
                    [var:内容] </td>
                  <td height="20" colspan="2"><textarea name="match5" rows="4" id="textarea3" style="width:90%"></textarea></td>
                </tr>
                <tr> 
                  <td width="18%" height="30">过滤规则：</td>
                  <td width="42%" height="20"> <textarea name="trim5" cols="20" rows="3" id="trim5" style="width:90%"></textarea> 
                  </td>
                  <td width="40%" height="20"> <input name="isunit5" type="checkbox" id="isunit5" value="1" class="np" checked>
                    如果内容分多页，合并字段内容<br/> <input name="isdown5" type="checkbox" id="isdown5" value="1" class="np" checked>
                    下载字段里的多媒体资源 </td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td height="24" colspan="4" bgcolor="#F0F1EB"> <input type="button" name="b1" value="增加其它字段" class="np" onClick="AddMyField(document.form1.mfnum.value);">
              数量： 
              <input type="input" name="mfnum" value="" size="10"> </td>
          </tr>
          <tr> 
            <td colspan="4"> <span id='myfield'> </span> </td>
          </tr>
        </table></td>
    </tr>
	<tr> 
      <td height="52" align="center" bgcolor="#FFFFFF"> 
        <input type="submit" name="b12" value="保存节点" class="coolbg" style="width:80">
      </td>
    </tr>
    <tr> 
      <td height="24" bgcolor="#EBF9D9">&nbsp; </td>
    </tr>
  </form>
</table>
</body>
</html>