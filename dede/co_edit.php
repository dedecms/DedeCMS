<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('co_EditNote');
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if($nid=="") 
{
	ShowMsg("参数无效!","-1");	
	exit();
}
$dsql = new DedeSql(false);
$rowFirst = $dsql->GetOne("Select * from #@__conote where nid='$nid'");
$notename = $rowFirst['gathername'];
$notes = $rowFirst['noteinfo'];
$exrule = $rowFirst['typeid'];
$dsql->FreeResult();
$dtp = new DedeTagParse();
$dtp->SetNameSpace("dede","{","}");
$dtp2 = new DedeTagParse();
$dtp2->SetNameSpace("dede","{","}");
$dtp3 = new DedeTagParse();
$dtp3->SetNameSpace("dede","{","}");
$dtp->LoadString($notes);
foreach($dtp->CTags as $tid => $ctag)
{
	if($ctag->GetName()=="item")
	{
		$imgurl = $ctag->GetAtt("imgurl");
		$imgdir = $ctag->GetAtt("imgdir");
		$language = $ctag->GetAtt("language");
		$matchtype = $ctag->GetAtt("matchtype");
		$refurl = $ctag->GetAtt("refurl");
		$isref = $ctag->GetAtt("isref");
		$exptime = $ctag->GetAtt("exptime");
	}
	else if($ctag->GetName()=="list")
	{
		$sunnote = $ctag->GetInnerText();
		$dtp2->LoadString($sunnote);
		$source = $ctag->GetAtt('source');
		$sourcetype = $ctag->GetAtt('sourcetype');
		$varstart = $ctag->GetAtt('varstart');
		$varend = $ctag->GetAtt('varend');
		$urlTag = $dtp2->GetTagByName('url');
		$needTag = $dtp2->GetTagByName('need');
		$cannotTag = $dtp2->GetTagByName('cannot');
		$linkareaTag = $dtp2->GetTagByName('linkarea');
	}
	else if($ctag->GetName()=="art")
	{
		$sunnote = $ctag->GetInnerText();
		$dtp3->LoadString($sunnote);
		$sppageTag = $dtp3->GetTagByName('sppage');
  }
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>修改采集节点</title>
<link href="base.css" rel="stylesheet" type="text/css">
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
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <form name="form1" method="post" action="co_edit_action.php">
  	<input type='hidden' name='exrule' value='<?=$exrule?>'>
  	<input type='hidden' name='nid' value='<?=$nid?>'>
    <tr> 
      <td height="20" background='img/tbg.gif'> <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="47%" height="18"><b>修改采集节点：</b></td>
            <td width="53%" align="right"> <input type="button" name="b11" value="返回节点管理页" class="np2" style="width:110" onClick="location.href='co_main.php';"> 
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td bgcolor="#F2F6E5">
      	<table width="400" border="0" cellspacing="0" cellpadding="0">
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
            <td width="16%" height="24">节点名称：</td>
            <td width="26%"><input name="notename" type="text" id="notename" value="<?=$notename?>" style="width:150"></td>
            <td width="17%">页面编码：</td>
            <td width="41%"> <input type="radio" name="language" class="np" value="gb2312"<?if($language=='gb2312') echo " checked";?>>
              GB2312 
              <input type="radio" name="language" class="np" value="utf-8"<?if($language=='utf-8') echo " checked";?>>
              UTF8 
              <input type="radio" name="language" class="np" value="big5"<?if($language=='big5') echo " checked";?>>
              BIG5 </td>
          </tr>
          <tr> 
            <td height="24">图片相对网址： </td>
            <td><input name="imgurl" type="text" id="imgurl" style="width:150" value="<?=$imgurl?>"></td>
            <td>物理路径：</td>
            <td><input name="imgdir" type="text" id="imgdir2" style="width:150" value="<?=$imgdir?>"></td>
          </tr>
          <tr> 
            <td height="24">区域匹配模式： </td>
            <td><input type="radio" name="macthtype" class="np" value="regex"<?if($matchtype=="regex") echo " checked";?>>
              正则表达式 
              <input name="macthtype" type="radio" class="np" value="string"<?if($matchtype=="string"||$matchtype=="") echo " checked";?>>
              字符串 </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr bgcolor="#F0F2EE"> 
            <td height="24" colspan="4">以下选项仅在开启防盗链模式才需设定，如果目标网站没有防盗链功能，请不要开启，否则会降低采集速度。</td>
          </tr>
          <tr> 
            <td height="24">防盗链模式：</td>
            <td><input name="isref" type="radio" class="np" value="no"<?if($isref=="no"||$isref=="") echo " checked";?>>
              不开启 
              <input name="isref" type="radio" class="np" value="yes"<?if($isref=="yes") echo " checked";?>>
              开启</td>
            <td>资源下载超时时间：</td>
            <td><input name="exptime" type="text" id="exptime" value="<?=$exptime?>" size="8">
              秒</td>
          </tr>
          <tr> 
            <td height="24">引用网址：</td>
            <td colspan="3"><input name="refurl" type="text" id="refurl" size="45" value="<?=$refurl?>">
              （需加http://）</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td bgcolor="#F2F6E5">
      	<table width="400" border="0" cellspacing="0" cellpadding="0">
          <tr class="top" onClick="ShowHide('slist');" style="cursor:hand"> 
            <td width="26" align="center"><img src="img/file_tt.gif" width="7" height="8"></td>
            <td width="374">采集列表获取规则</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr id="slist" style="display:block"> 
      <td height="76" bgcolor="#FFFFFF">
      	<table width="98%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="18%" height="24">来源网址：</td>
            <td><input name="sourceurl" type="text" id="sourceurl" style="width:500" value="<?=$urlTag->GetAtt('value')?>"> 
            </td>
          </tr>
          <tr> 
            <td height="24">来源属性：</td>
            <td>
            	<input name="sourcetype" type="radio" class="np" value="list"<?if($sourcetype=="list"||$sourcetype=="") echo " checked";?>>
              文章列表网址 
              <input type="radio" name="sourcetype" class="np" value="archives"<?if($sourcetype=="archives") echo " checked";?>>
              文章网址（仅适用于手工指定网址的情況） </td>
          </tr>
          <tr> 
            <td height="24">分页变量起始值：</td>
            <td> <input name="varstart" type="text" id="varstart" size="15" value="<?=$varstart?>">
              　　变量结束值： 
              <input name="varend" type="text" id="varend" size="15" value="<?=$varend?>">
              　表示 [var:分页] 的范围） </td>
          </tr>
          <tr> 
            <td height="24">来源规则：</td>
            <td> <input name="source" type="radio" id="radio" value="var" onClick="ShowHide('surls');" class="np"<?if($source=="var") echo " checked";?>>
              序列网址（用 [var:分页] 表示序列变量） 
              <input name="source" type="radio" id="source" value="app" class="np" onClick="ShowHide('surls');"<?if($source=="app") echo " checked";?>>
              手工指定(列表/文章)网址 </td>
          </tr>
          <tr align="center" id="surls"> 
            <td height="143" colspan="2">
            	<table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td height="24">手工指定网址：（每行一条网址，不能在手工指定网址中使用变量）</td>
                </tr>
                <tr> 
                  <td align="center">
                  	<textarea name="sourceurls" id="sourceurls" style="width:100%;height:120"><?=$urlTag->GetInnerText()?></textarea> 
                  </td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td height="24">文章网址需包含：</td>
            <td><input name="need" type="text" id="need" size="15" value="<?=$needTag->GetInnerText()?>">
              　网址不能包含： 
              <input name="cannot" type="text" id="cannot" size="15" value="<?=$cannotTag->GetInnerText()?>">
              　(正则)</td>
          </tr>
          <tr> 
            <td height="24">链接区域规则：<br>
              变量：<br>
              [var:区域]</td>
            <td> <textarea name="linkarea" cols="50" rows="5" id="linkarea"><?=$linkareaTag->GetInnerText()?></textarea> 
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
            <td colspan="2"><textarea name="sppage" rows="3" id="sppage" style="width:90%"><?=$sppageTag->GetInnerText()?></textarea></td>
            <td width="48%"> <input name="sptype" type="radio" value="full" class="np"<?if($sppageTag->GetAtt('sptype')==""||$sppageTag->GetAtt('sptype')=="full") echo " checked"?>>
              全部列出的分页列表<br/> <input type="radio" name="sptype" class="np" value="next"<?if($sppageTag->GetAtt('sptype')=="next") echo " checked"?>>
              上下页形式或不完整的分页列表</td>
          </tr>
          <tr> 
            <td height="24" colspan="4"><strong> 　字段设置说明：</strong><br/>
              　1、规则：在匹配区域规则中，规则一般为“<font color="#FF0000">起始无重复HTML[var:内容]结尾无重复HTML</font>”，内容过滤规则才是正则表达式。<br/>
              　2、变量：如果你的字段值使用的不是变量，而是指定的其它值，则导出时直接使用该值，并且采集时不会分析该项目。<br>
              　3、过滤规则：如果有多个规则，请用{dede:teim}规则一{/dede:trim}换行{dede:teim}规则二{/dede:trim}...表示</td>
          </tr>
          <?
          $s=0;
          //$dtp->LoadString($notes);
          foreach($dtp3->CTags as $k => $ctag)
          {
          	if($ctag->GetName()=='note')
          	{
          		$s++;
          		$dtp->LoadString($ctag->GetInnerText());
          		$smatch = "";
          		$sfunction = "";
          		$strim = "";
          		$sfield = $ctag->GetAtt('field');
          		$svalue = $ctag->GetAtt('value');
              $sisunit = $ctag->GetAtt('isunit');
              $sisdown = $ctag->GetAtt('isdown');
              $comment = $ctag->GetAtt('comment');
          		foreach($dtp->CTags as $n => $ntag)
          		{
          			$tname = $ntag->GetName();
          			if($tname=='match') $smatch = $ntag->GetInnerText();
          			else if($tname=='function') $sfunction = $ntag->GetInnerText();
          			else if($tname=='trim') $strim .= "{dede:trim}".$ntag->GetInnerText()."{/dede:trim}\r\n";
          		}
          ?>
          <tr bgcolor="#EBEFD1"> 
            <td height="24">
            	&nbsp;
            <b><?=$comment?></b>
            <input type="hidden" name="comment<?=$s?>" id="comment<?=$s?>" value="<?=$comment?>">
            </td>
            <td width="23%"> <input name="field<?=$s?>" type="text" id="field<?=$s?>" value="<?=$sfield?>" size="22"></td>
            <td width="11%">字段值：</td>
            <td>
            	<input name="value<?=$s?>" type="text" id="value<?=$s?>" value="<?=$svalue?>" size="25">
            </td>
          </tr>
          <tr> 
            <td height="24" colspan="4">
            	<table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="18%" height="80">匹配区域：</td>
                  <td height="20" colspan="2">
                  	<textarea name="match<?=$s?>" rows="4" id="match<?=$s?>" style="width:90%"><?=$smatch?></textarea>
                  </td>
                </tr>
                <tr> 
                  <td height="63">过滤规则：</td>
                  <td height="63"> <textarea name="trim<?=$s?>" cols="20" rows="3" id="trim<?=$s?>" style="width:90%"><?=$strim?></textarea> 
                  </td>
                  <td height="63"> <input name="isunit<?=$s?>" type="checkbox" id="isunit<?=$s?>" value="1" class="np"<?if($sisunit=='1') echo " checked";?>>
                    分页内容字段（规则中只允许单一的该类型字段）<br/> <input name="isdown<?=$s?>" type="checkbox" id="isdown<?=$s?>" value="1" class="np"<?if($sisdown=='1') echo " checked";?>>
                    下载字段里的多媒体资源 </td>
                </tr>
                <tr> 
                  <td width="18%" height="60">自定义处理接口：</td>
                  <td width="42%" height="20"><textarea name="function<?=$s?>" cols="20" rows="3" id="function<?=$s?>" style="width:90%"><?=$sfunction?></textarea> 
                  </td>
                  <td width="40%" height="20">
                    函数或程序的变量<br>
                    @body 表示原始网页 @litpic 缩略图<br>
                    @me 表示当前标记值和最终结果
                    </td>
                </tr>
              </table>
              </td>
          </tr>
          <?
           }
             }
          ?>
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