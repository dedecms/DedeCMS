<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/inc/inc_archives_all.php");
$aid = ereg_replace("[^0-9]","",$aid);
if($aid=="")
{
	ShowMsg("你没指定文档ID，不允许访问本页面！","-1");
	exit();
}
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

$dsql->SetQuery($arcQuery);
$arcRow = $dsql->GetOne($arcQuery);
if(!is_array($arcRow)){
	$dsql->Close();
	ShowMsg("读取档案基本信息出错!","-1");
	exit();
}
//----------------------------
$query = "Select * From #@__channeltype where ID='".$arcRow['channel']."'";
$cInfos = $dsql->GetOne($query);
if(!is_array($cInfos)){
	$dsql->Close();
	ShowMsg("读取频道配置信息出错!","-1");
	exit();
}
$channelid = $arcRow['channel'];
$addtable = $cInfos['addtable'];
//-----------------------
$addQuery = "Select * From ".$cInfos['addtable']." where aid='$aid'";
$addRow = $dsql->GetOne($addQuery);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>更改文档</title>
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
	 alert("文档标题不能为空！");
	 return false;
  }
  if(document.form1.seltypeid.value==0&&document.form1.typeid.value==0){
	   alert("请选择档案的主类别！");
	   return false;
  }
}
-->
</script>
</head>
<body topmargin="8">
<form name="form1" action="archives_edit_action.php" enctype="multipart/form-data" method="post" onSubmit="return checkSubmit();">
  <input type="hidden" name="channelid" value="<?php echo $channelid?>">
  <input type="hidden" name="ID" value="<?php echo $aid?>">
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="4%" height="30"><IMG height=14 src="img/book1.gif" width=20></td>
      <td width="85%"><a href="catalog_do.php?cid=<?php echo $arcRow['typeid']?>&dopost=listArchives"><u>文档列表</u></a><a href="catalog_do.php?cid=<?php echo $arcRow["typeid"]?>&dopost=listArchives"></a>&gt;&gt;更改文档</td>
      <td width="10%">&nbsp; <a href="catalog_main.php">[<u>栏目管理</u>]</a> </td>
      <td width="1%">&nbsp;</td>
    </tr>
  </table>
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" id="head1" class="htable">
    <tr> 
      <td colspan="2">
      	<table border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="84" height="24" align="center" background="img/itemnote1.gif">&nbsp;常规内容&nbsp;</td>
            <td width="84" align="center" background="img/itemnote2.gif"><a href="#" onClick="ShowItem2()"><u>附加参数</u></a>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" id="head2" style="border-bottom:1px solid #CCCCCC;display:none">
    <tr> 
      <td colspan="2">
      	<table height="24" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="84" align="center" background="img/itemnote2.gif"><a href="#" onClick="ShowItem1()"><u>常规内容</u></a>&nbsp;</td>
            <td width="84" align="center" background="img/itemnote1.gif">附加参数&nbsp;</td>
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
      <td width="400%" height="24" colspan="4" class="bline">
        <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;文档标题：</td>
            <td width="240">
           <input name="title" type="text" id="title" style="width:200" value="<?php echo $arcRow["title"]?>">
            </td>
            <td width="90">附加参数：</td>
            <td> 
              <input name="iscommend" type="checkbox" id="iscommend" value="11" class="np"<?php  if($arcRow["iscommend"]>10) echo " checked";?>>
              推荐 
              <input name="isbold" type="checkbox" id="isbold" value="5" class="np"<?php  if($arcRow["iscommend"]==5||$arcRow["iscommend"]==16) echo " checked";?>>
              加粗
              <input name="isjump" type="checkbox" onClick="ShowUrlTrEdit()" id="isjump" value="1" class="np"<?php  echo $arcRow["redirecturl"]=="" ? "" : " checked";?>>
              跳转网址
              <input name="ispic" type="checkbox" id="ispic" value="1" onClick="ShowPicTr()" class="np"<?php  echo $arcRow["litpic"]=="" ? "" : " checked";?>>
              图
            </td>
          </tr>
        </table>
        </td>
    </tr>
    <tr>
      <td height="24" colspan="4" class="bline" id="redirecturltr" style="display:<?php  echo $arcRow["redirecturl"]=="" ? "none" : "block";?>">
	   <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;跳转网址：</td>
            <td>
            	<input name="redirecturl" type="text" id="redirecturl" style="width:300" value="<?php echo $arcRow["redirecturl"]?>"> 
              (外部网址需加 "http://" ，本站的用 "/" 表示根网址)
            </td>
          </tr>
       </table>
	 </td>
    </tr>
    <tr id="pictable" style="display:<?php  echo $arcRow["litpic"]=="" ? "none" : "block";?>"> 
      <td height="24" colspan="4" class="bline">
	  <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="135" height="81"> &nbsp;缩 略 图：<br/> &nbsp; <input type='checkbox' class='np' name='ddisremote' value='1' id='ddisremote' onClick="CkRemote('ddisremote','litpic')">
              远程图片 <br> </td>
            <td width="464">
            	<table width="100%" border="0" cellspacing="1" cellpadding="1">
                <tr> 
                  <td height="30"> <input name="picname" type="text" id="picname" style="width:250" value="<?php echo $arcRow["litpic"]; ?>"> 
                    <input type="button" name="Submit2" value="在网站内选择" style="width:120" onClick="SelectImage('form1.picname','small');" class='nbt'> 
                  </td>
                </tr>
              </table></td>
            <td width="201" align="center"><img src="<?php  echo ($arcRow["litpic"]=="" ? "img/pview.gif" : $arcRow["litpic"]);?>" width="150" id="picview" name="picview"></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;主栏目：</td>
            <td width="400">
            <?php 
           	$dsql = new DedeSql(false);
           	$seltypeids = $dsql->GetOne("Select ID,typename From #@__arctype where ID='".$arcRow["typeid"]."' ");
			    if(is_array($seltypeids)){
			         echo GetTypeidSel('form1','typeid','selbt1',$arcRow["channel"],$seltypeids['ID'],$seltypeids['typename']);
			    }
			    ?>
            </td>
            <td>（只允许在白色选项的栏目中发布当前类型内容）</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" bgcolor="#FFFFFF" class="bline2"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;副栏目：</td>
            <td> 
              <?php 
			$seltypeids = $dsql->GetOne("Select ID,typename From #@__arctype where ID='".$arcRow["typeid2"]."' ");
			if(is_array($seltypeids)){
			   echo GetTypeidSel('form1','typeid2','selbt2',$arcRow["channel"],$seltypeids['ID'],$seltypeids['typename']);
			}else{
			   echo GetTypeidSel('form1','typeid2','selbt2',$arcRow["channel"],0,'请选择...');
			}
            ?>
            </td>
          </tr>
        </table></td>
    </tr>
  <tr>
    <td style="padding:0px">
        <?php 
        $dtp = new DedeTagParse();
	      $dtp->SetNameSpace("field","<",">");
        $dtp->LoadSource($cInfos['fieldset']);
        $dede_addonfields = "";
        if(is_array($dtp->CTags))
        {
        	foreach($dtp->CTags as $tid=>$ctag)
			    {
        		 if($dede_addonfields=="") $dede_addonfields = $ctag->GetName().",".$ctag->GetAtt('type');
        		 else $dede_addonfields .= ";".$ctag->GetName().",".$ctag->GetAtt('type');
          	    //echo GetFormItem($ctag);
          	 echo GetFormItemValue($ctag,$addRow[$ctag->GetName()]);
           }
           echo "<input type='hidden' name='dede_addtablename' value=\"".$addtable."\">\r\n";
           echo "<input type='hidden' name='dede_addonfields' value=\"".$dede_addonfields."\">\r\n";
         }
         ?>
    </td>
  </tr>
  <tr>
      <td height="24" class="bline">
	  <table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;附加选项：</td>
            <td> <input name="remote" type="checkbox" class="np" id="remote" value="1" checked>
              下载远程图片和资源 
              <input name="dellink" type="checkbox" class="np" id="dellink" value="1">
              删除非站内链接</td>
          </tr>
        </table></td>
    </tr>
 </table>
 
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" id="adset" style="display:none">
  <tr> 
      <td height="24" colspan="4" class="bline">
	  <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;简略标题：</td>
            <td width="250"> 
            	<input name="shorttitle" type="text" value="<?php echo $arcRow["shorttitle"]?>" id="shorttitle" style="width:200">
            </td>
            <td width="90">自定义属性：</td>
            <td> 
            	<select name='arcatt' style='width:150'>
                <option value='0'>普通文档</option>
                <?php 
            	$dsql->SetQuery("Select * From #@__arcatt order by att asc");
            	$dsql->Execute();
            	while($trow = $dsql->GetObject())
            	{
            		echo "<option value='{$trow->att}'>{$trow->attname}(att={$trow->att})</option>";
            	}
            	?>
              </select>
             </td>
          </tr>
        </table></td>
    </tr>
  <tr> 
      <td height="24" colspan="4" class="bline">
	  <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;录入时间：</td>
            <td width="241"> 
              <?php 
			$nowtime = GetDateTimeMk($arcRow["senddate"]);
			echo "<input name=\"senddate\" value=\"$nowtime\" type=\"text\" id=\"senddate\" style=\"width:200\">";
			?> 
			      </td>
            <td width="88">消费点数：</td>
            <td width="381">
            <input name="money" type="text" id="money" value="<?php echo $arcRow["money"]?>" size="10">
            </td>
          </tr>
        </table></td>
    </tr>
	<tr> 
      <td height="24" colspan="4" class="bline">
	  <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">&nbsp;发布时间：</td>
            <td width="241"> 
              <?php 
			$nowtime = GetDateTimeMk($arcRow["pubdate"]);
			echo "<input name=\"pubdate\" value=\"$nowtime\" type=\"text\" id=\"pubdate\" style=\"width:200\">";
			?> 
			      </td>
            <td width="88">&nbsp;</td>
            <td width="381">&nbsp;</td>
          </tr>
        </table></td>
    </tr>
	<tr> 
      <td height="24" colspan="4" class="bline">
      	<table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">文档来源：</td>
            <td width="240"> <input name="source" type="text" id="source" style="width:160" value="<?php echo $arcRow["source"]?>" size="16">
              <input name="selsource" type="button" id="selsource" value="选择" class='nbt'></td>
            <td width="90">作　者：</td>
            <td> 
              <input name="writer" type="text" id="writer" style="width:120" value="<?php echo $arcRow["writer"]?>"> 
              <input name="selwriter" type="button" id="selwriter" value="选择" class='nbt'> 
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline">
      	<table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">文档排序：</td>
            <td width="240"> <select name="sortup" id="sortup" style="width:150">
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
            <td> <input name="color" type="text" id="color" style="width:120" value="<?php echo $arcRow["color"]?>"> 
              <input name="modcolor" type="button" id="modcolor" value="选取" onClick="ShowColor()" class='nbt'> 
            </td>
          </tr>
        </table>
        </td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline">
      	<table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">阅读权限：</td>
            <td width="240"> 
              <select name="arcrank" id="arcrank" style="width:150">
                <option value='<?php echo $arcRow["arcrank"]?>'>
                <?php echo $arcRow["rankname"]?>
                </option>
                <?php 
              $urank = $cuserLogin->getUserRank();
              $dsql->SetQuery("Select * from #@__arcrank where adminrank<='$urank'");
              $dsql->Execute();
              while($row = $dsql->GetObject())
              {
              	echo "     <option value='".$row->rank."'>".$row->membername."</option>\r\n";
              }
              ?>
              </select>
            </td>
            <td width="90">发布选项：</td>
            <td> <input name="ishtml" type="radio" class="np" value="1"<?php if($arcRow["ismake"]!=-1) echo " checked";?>>
              生成HTML 
              <input type="radio" name="ishtml" class="np" value="0"<?php if($arcRow["ismake"]==-1) echo " checked";?>>
              仅动态浏览 </td>
          </tr>
        </table>
      </td>
    </tr>
	<tr>
	  <td height="76" colspan="4" class="bline">
	  <table width="800" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="90" height="51">文档摘要：</td>
          <td width="446"><textarea name="description" rows="3" id="description" style="width:80%"><?php echo $arcRow["description"]; ?></textarea></td>
          <td>&nbsp;</td>
        </tr>
      </table></td>
    </tr>
	<tr>
	  <td height="76" colspan="4" class="bline"><table width="800" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="90" height="51">&nbsp;关键字：</td>
          <td width="446"><textarea name="keywords" rows="3" id="keywords" style="width:80%"><?php echo $arcRow["keywords"]; ?></textarea></td>
          <td>用空格分开<br/>
              <input type="button" name="Submit" value="浏览..." style="width:56;height:20" onclick="SelectKeywords('form1.keywords');" class='nbt' /></td>
        </tr>
      </table></td>
    </tr>
</table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
      <td height="56" valign="top"><table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr> 
            <td width="17%">&nbsp;</td>
            <td width="83%">
			<table width="214" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="115" height="36"><input name="imageField" type="image" src="img/button_ok.gif" width="60" height="22" border="0" class="np"></td>
                  <td width="99"> <img src="img/button_reset.gif" width="60" height="22" border="0" onClick="location.reload();" style="cursor:hand"> 
                  </td>
                </tr>
              </table></td>
          </tr>
        </table> </td>
  </tr>
</table>
</form>
<script language='javascript'>if($Nav()!="IE") ShowObj('adset');</script>
<?php 
$dsql->Close();
?>
</body>
</html>