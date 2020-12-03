<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('co_AddNote');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if(empty($action)) $action = "";
if(empty($exrule)) $exrule = "";

if($action=="select"){
	require_once(dirname(__FILE__)."/co_sel_exrule.php");
	exit();
}

if($exrule==""){
	ShowMsg("请先选择一个导入规则！","co_sel_exrule.php");
	exit();
}

require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__co_exrule where aid='$exrule'");
$dsql->Close();
$ruleset = $row['ruleset'];
$dtp = new DedeTagParse();
$dtp->LoadString($ruleset);
$noteid = 0;
if(is_array($dtp->CTags))
{
	foreach($dtp->CTags as $ctag){
		if($ctag->GetName()=='field') $noteid++;
		if($ctag->GetName()=='note') $noteinfos = $ctag;
	}
}
else
{
	ShowMsg("该规则不合法，无法进行生成采集配置!","-1");
	$dsql->Close();
	exit();
}

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

</script>
<title>新增采集节点</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <form name="form1" method="post" action="co_add_action.php">
  	<input type='hidden' name='exrule' value='<?=$exrule?>'>
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
      <td bgcolor="#FFFFFF">
<table width="98%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="18%" height="24">节点名称：</td>
            <td width="32%"><input name="notename" type="text" id="notename" style="width:200"></td>
            <td width="18%">页面编码：</td>
            <td width="32%"> <input type="radio" name="language" class="np" value="gb2312" checked>
              GB2312 
              <input type="radio" name="language" class="np" value="utf-8">
              UTF8 
              <input type="radio" name="language" class="np" value="big5">
              BIG5 </td>
          </tr>
          <tr> 
            <td height="24">图片相对网址： </td>
            <td> 
              <?
		$aburl = "";
		$curl = GetCurUrl();
		$curls = explode("/",$curl);
		for($i=0;$i<count($curls)-2;$i++){
			if($i!=0) $aburl .= "/".$curls[$i];
		}
		$aburl .= "/upimg";
          ?>
              <input name="imgurl" type="text" id="imgurl" style="width:200" value="<?=$aburl?>"></td>
            <td>物理路径：</td>
            <td><input name="imgdir" type="text" id="imgdir2" style="width:150" value="../upimg"></td>
          </tr>
          <tr> 
            <td height="24">区域匹配模式：</td>
            <td colspan="3"> <input type="radio" class="np" name="macthtype" value="regex">
              正则表达式 
              <input name="macthtype" class="np" type="radio" value="string" checked>
              字符串 </td>
          </tr>
          <tr bgcolor="#F0F2EE"> 
            <td height="24" colspan="4">以下选项仅在开启防盗链模式才需设定，如果目标网站没有防盗链功能，请不要开启，否则会降低采集速度。</td>
          </tr>
          <tr> 
            <td height="24">防盗链模式：</td>
            <td><input name="isref" type="radio" class="np" value="no" checked>
              不开启 
              <input name="isref" type="radio" class="np" value="yes">
              开启</td>
            <td>资源下载超时时间：</td>
            <td><input name="exptime" type="text" id="exptime" value="10" size="8">
              秒</td>
          </tr>
          <tr> 
            <td height="24">引用网址：</td>
            <td colspan="3"><input name="refurl" type="text" id="refurl" size="30">
              （一般为目标网站其中一个文章页的网址，需加http://）</td>
          </tr>
        </table>
      </td>
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
            <td><input name="sourceurl" type="text" id="sourceurl" style="width:500" value="http://"> 
            </td>
          </tr>
          <tr> 
            <td height="24">来源属性：</td>
            <td> <input name="sourcetype" type="radio" class="np" value="list" checked>
              文章列表网址 
              <input type="radio" name="sourcetype" class="np" value="archives">
              文章网址（仅适用于手工指定网址的情況） </td>
          </tr>
          <tr>
            <td height="24">分页变量起始值：</td>
            <td><input name="varstart" type="text" id="varstart2" size="15">
              　　变量结束值： 
              <input name="varend" type="text" id="varend2" size="15">
              　表示 [var:分页] 的范围） </td>
          </tr>
          <tr> 
            <td height="24">来源规则：</td>
            <td>
            	<input name="source" type="radio" id="radio" value="var" class="np" onClick="ShowHide('surls');" checked>
              序列网址（用 [var:分页] 表示序列变量） 
              <input name="source" type="radio" id="source" value="app" class="np" onClick="ShowHide('surls');">
              手工指定(列表/文章)网址
             </td>
          </tr>
          <tr align="center" id="surls" style="display:none"> 
            <td height="143" colspan="2">
            	<table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td height="24">手工指定网址：（每行一条网址，不能在手工指定网址中使用变量）</td>
                </tr>
                <tr> 
                  <td align="center">
                  	<textarea name="sourceurls" id="sourceurls" style="width:100%;height:120"><?=$urlTag?></textarea> 
                  </td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td height="24">文章网址需包含：</td>
            <td><input name="need" type="text" id="cannot" size="15">
              　网址不能包含： 
              <input name="cannot" type="text" id="cannot" size="15">
              　(正则)</td>
          </tr>
          <tr> 
            <td height="24">链接区域规则：<br>
              变量：<br>
              [var:区域]</td>
            <td> <textarea name="linkarea" cols="50" rows="5" id="linkarea">[var:区域]</textarea> 
            </td>
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
      <td height="113" valign="top" bgcolor="#FFFFFF"><table width="98%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="18%" height="60">分页匹配规则：<br/>
              可用变量：<br/>
              [var:分页区域]</td>
            <td colspan="2"><textarea name="sppage" rows="3" id="sppage" style="width:90%"></textarea></td>
            <td width="48%"> <input name="sptype" type="radio" value="full" class="np" checked>
              全部列出的分页列表<br/> <input type="radio" name="sptype" class="np" value="next">
              上下页形式或不完整的分页列表</td>
          </tr>
          <tr> 
            <td height="24" colspan="4"><strong> 　字段设置说明：</strong><br/>
              　１、规则：在匹配区域规则中，规则一般为“<font color="#FF0000">起始无重复HTML[var:内容]结尾无重复HTML</font>”，内容过滤规则才是正则表达式。<br/>
              　２、变量：如果你的字段值使用的不是变量，而是指定的其它值，则导出时直接使用该值，并且采集时不会分析该项目。<br>
              　３、过滤规则：如果有多个规则，请用{dede:teim}规则一{/dede:trim}换行{dede:teim}规则二{/dede:trim}...表示</td>
          </tr>
          <?
          if(is_array($dtp->CTags))
          {
	          $s = 0;
	          foreach($dtp->CTags as $ctag)
	          {
		           if($ctag->GetName()=='field')
		           {
		             if($ctag->GetAtt('source')=='value') continue;
		             
		             $tagv = "[var:内容]";
		             if($ctag->GetAtt('source')=='function') $fnv = $ctag->GetInnerText();
		             else $fnv = "";
		             
		             $cname = $ctag->GetAtt('name');
		             
		             if($ctag->GetAtt('intable')!="" 
		                  && $ctag->GetAtt('intable')!=$noteinfos->GetAtt('tablename') )
		             {
		             	  $cname = $ctag->GetAtt('intable').'.'.$cname;
		             }
		             $comment = $ctag->GetAtt('comment');
		             $s++;
          ?>
          <tr bgcolor="#EBEFD1"> 
            <td height="24">
            	&nbsp;
            <b><?=$comment?></b>
            <input type="hidden" name="comment<?=$s?>" id="comment<?=$s?>" value="<?=$comment?>">
            </td>
            <td width="23%"> <input name="field<?=$s?>" type="text" id="field<?=$s?>" value="<?=$cname?>" size="22"></td>
            <td width="11%">字段值：</td>
            <td>
            	<input name="value<?=$s?>" type="text" id="value<?=$s?>" value="<?=$tagv?>" size="25">
            </td>
          </tr>
          <tr> 
            <td height="24" colspan="4">
            	<table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="18%" height="80">匹配区域：</td>
                  <td height="20" colspan="2">
                  	<textarea name="match<?=$s?>" rows="4" id="match<?=$s?>" style="width:90%"></textarea>
                  </td>
                </tr>
                <tr> 
                  <td height="63">过滤规则：</td>
                  <td height="63"> <textarea name="trim<?=$s?>" cols="20" rows="3" id="trim<?=$s?>" style="width:90%"></textarea> 
                  </td>
                  <td height="63"> <input name="isunit<?=$s?>" type="checkbox" id="isunit<?=$s?>" value="1" class="np">
                    分页内容字段（规则中只允许单一的该类型字段）<br/> <input name="isdown<?=$s?>" type="checkbox" id="isdown<?=$s?>" value="1" class="np">
                    下载字段里的多媒体资源 </td>
                </tr>
                <tr> 
                  <td width="18%" height="60">自定义处理接口：</td>
                  <td width="42%" height="20"><textarea name="function<?=$s?>" cols="20" rows="3" id="function<?=$s?>" style="width:90%"><?=$fnv?></textarea> 
                  </td>
                  <td width="40%" height="20">函数或程序的变量<br>
                    @body 表示原始网页 @litpic 缩略图<br>
                    @me 表示当前标记值和最终结果</td>
                </tr>
              </table>
              </td>
          </tr>
          <? } } } ?>
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