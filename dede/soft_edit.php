<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
$aid = ereg_replace("[^0-9]","",$aid);
$channelid="3";
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
$addQuery = "Select * From #@__addonsoft where aid='$aid'";

$dsql->SetQuery($arcQuery);
$arcRow = $dsql->GetOne($arcQuery);
if(!is_array($arcRow)){
	$dsql->Close();
	ShowMsg("读取档案基本信息出错!","-1");
	exit();
}

$addRow = $dsql->GetOne($addQuery);

if(!is_array($addRow))
{
	$addRow["filetype"] = "";
  $addRow["language"] = "";
  $addRow["softtype"] = "";
  $addRow["accredit"] = "";
  $addRow["softrank"] = 3;
  $addRow["officialUrl"] = 400;
  $addRow["officialDemo"] = "";
  $addRow["softsize"] = 400;
  $addRow["softlinks"] = "";
  $addRow["introduce"] = "";
}

$newRowStart = 1;
$nForm = "";
if($addRow["softlinks"]!="")
{
	$dtp = new DedeTagParse();
  $dtp->LoadSource($addRow["softlinks"]);
  if(is_array($dtp->CTags))
  {
    foreach($dtp->CTags as $ctag){
      if($ctag->GetName()=="link"){
      	$nForm .= "
      	软件地址".$newRowStart."：<input type='text' name='softurl".$newRowStart."' style='width:280' value='".trim($ctag->GetInnerText())."'>
        服务器名称：<input type='text' name='servermsg".$newRowStart."' value='".$ctag->GetAtt("text")."' style='width:150'>
        <br/>";
        $newRowStart++;
      }
    }
  }
  $dtp->Clear();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>编辑软件</title>
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
	   alert("软件名称不能为空！");
	   document.form1.title.focus();
	   return false;
  }
}
function ShowColor(){
	var fcolor=showModalDialog("img/color.htm?ok",false,"dialogWidth:106px;dialogHeight:110px;status:0;dialogTop:"+(window.event.clientY+120)+";dialogLeft:"+(window.event.clientX));
	if(fcolor!=null && fcolor!="undefined") document.form1.color.value = fcolor;
}

function SeePic(img,f)
{
   if ( f != "" ) { img.src = f; }
}

function SelectSoft(fname)
{
   var posLeft = window.event.clientY-100;
   var posTop = window.event.clientX-400;
   window.open("../include/dialog/select_Soft.php?f="+fname, "popUpImagesWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
}

function MakeUpload()
{
   var startNum = <?=$newRowStart?>;
   var upfield = document.getElementById("uploadfield");
   var endNum =  document.form1.picnum.value;
   if(endNum>9) endNum = 9;
   upfield.innerHTML = "";
   for(startNum;startNum<=endNum;startNum++){
	   upfield.innerHTML += "软件地址"+startNum+"：<input type='text' name='softurl"+startNum+"' style='width:280' value='http://'> ";
	   upfield.innerHTML += " ";
	   upfield.innerHTML += "服务器名称：<input type='text' name='servermsg"+startNum+"' style='width:150'><br/>\r\n";
	 }
}

function SelectImage(fname,vlist)
{
   var posLeft = window.event.clientY-100;
   var posTop = window.event.clientX-400;
   window.open("../include/dialog/select_images.php?f="+fname+"&imgstick="+vlist, "popUpImagesWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
}

-->
</script>
</head>
<body topmargin="8">
<form name="form1" action="action_soft_edit_save.php" enctype="multipart/form-data" method="post" onSubmit="return checkSubmit();">
<input type="hidden" name="channelid" value="<?=$channelid?>">
<input type="hidden" name="ID" value="<?=$aid?>">
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
          <a href="catalog_do.php?cid=<?=$arcRow["typeid"]?>&dopost=listArchives">[<u>软件列表</u>]</a>
          &nbsp;
          <a href="catalog_main.php">[<u>频道管理</u>]</a>
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
      <td width="400%" height="24" colspan="4" class="bline">
      	<table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">软件名称：</td>
            <td width="224"><input name="title" type="text" id="title" style="width:200" value="<?=$arcRow["title"]?>"></td>
            <td width="73">附加参数：</td>
            <td width="223">
            	<input name="iscommend" type="checkbox" id="iscommend" value="11" class="np"<? if($arcRow["iscommend"]>10) echo " checked";?>>
              推荐 
              <input name="isbold" type="checkbox" id="isbold" value="5" class="np"<? if($arcRow["iscommend"]==5||$arcRow["iscommend"]==16) echo " checked";?>>
              加粗
            </td>
          </tr>
        </table>
       </td>
    </tr>
    <tr id="pictable"> 
      <td height="24" colspan="4" class="bline">
      	<table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="78" height="81">软件缩略图：</td>
            <td width="337">
              <input name="picname" type="text" id="picname" style="width:230" value="<?=$arcRow["litpic"]?>">
              <input type="button" name="Submit" value="浏览..." style="width:60" onClick="SelectImage('form1.picname','');"> 
            </td>
            <td width="185" align="center"><img src="<?if($arcRow["litpic"]!="") echo $arcRow["litpic"]; else echo "img/pview.gif";?>" width="150" height="100" id="picview" name="picview">
            </td>
          </tr>
        </table>
       </td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">软件来源：</td>
            <td width="224"> <input name="source" type="text" id="source" style="width:200" value="<?=$arcRow["source"]?>">  
            </td>
            <td width="63">软件作者：</td>
            <td width="159"><input name="writer" type="text" id="writer"  style="width:120"value="<?=$arcRow["writer"]?>"> 
            </td>
            <td width="74" align="center">&nbsp; </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">内容排序：</td>
            <td width="224">
            	<select name="sortup" id="sortup" style="width:150">
                <?
                $subday = SubDay($arcRow["sortrank"],$arcRow["senddate"]);
                echo "<option value='0'>正常排序</option>\r\n";
                if($subday>0) echo "<option value='$subday' selected>置顶 $subday 天</option>\r\n";
                ?>
                <option value="7">置顶一周</option>
                <option value="30">置顶一个月</option>
                <option value="90">置顶三个月</option>
                <option value="180">置顶半年</option>
                <option value="360">置顶一年</option>
              </select>
              </td>
            <td width="63">标题颜色：</td>
            <td width="159">
            	<input name="color" type="text" id="color" style="width:120" value="<?=$arcRow["color"]?>"> 
            </td>
            <td width="74" align="center">
            	<input name="modcolor" type="button" id="modcolor" value="选取" onClick="ShowColor()">
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline">
      	<table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">阅读权限：</td>
            <td width="224">
             <select name="arcrank" id="arcrank" style="width:150">
              <option value='<?=$arcRow["arcrank"]?>'><?=$arcRow["rankname"]?></option>
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
              </select>
             </td>
            <td width="63">发布选项：</td>
            <td>
            	<input name="ishtml" type="radio" class="np" value="1"<?if($arcRow["ismake"]!=-1) echo " checked";?>>
              生成HTML 
              <input type="radio" name="ishtml" class="np" value="0"<?if($arcRow["ismake"]==-1) echo " checked";?>>
              仅动态浏览
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="75" colspan="4" class="bline">
<table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80" height="51">简要说明：</td>
            <td width="224"> <textarea name="description" rows="3" id="description" style="width:200"><?=$arcRow["description"]?></textarea> 
            </td>
            <td width="63">关键字：</td>
            <td> <textarea name="keywords" rows="3" id="keywords" style="width:200"><?=$arcRow["keywords"]?></textarea> 
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline">
      	<table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">录入时间：</td>
            <td> 
              <?
			         $addtime = GetDateTimeMk($arcRow["senddate"]);
			         echo "$addtime (标准排序和生成HTML名称的依据时间) <input type='hidden' name='senddate' value='".$arcRow["senddate"]."'>";
			        ?>
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"> <table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">发布时间：</td>
            <td> 
              <?
			$nowtime = GetDateTimeMk($arcRow["pubdate"]);
			echo "<input name=\"pubdate\" value=\"$nowtime\" type=\"text\" id=\"pubdate\" style=\"width:200\">";
			echo "<input name=\"selPubtime\" type=\"button\" id=\"selkeyword\" value=\"选择\" onClick=\"showCalendar('pubdate', '%Y-%m-%d %H:%M:00', '24');\">";
			?>
            </td>
            <td width="82" align="center">消费点数：</td>
            <td width="152"><input name="money" type="text" id="money" value="<?=$arcRow["money"]?>" size="10"></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">主分类：</td>
            <td width="446"> 
              <?
           	$tl = new TypeLink($arcRow["typeid"]);
           	$typeOptions = $tl->GetOptionArray($arcRow["typeid"],$cuserLogin->getUserChannel(),$channelid);
           	echo "<select name='typeid' style='width:300'>\r\n";
            if($arcRow["typeid"]=="0") echo "<option value='0' selected>请选择主分类...</option>\r\n";
            echo $typeOptions;
            echo "</select>";
			    ?>
            </td>
            <td width="74" align="center">
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">副分类：</td>
            <td width="446"> 
            <?
            $tl->SetTypeID($arcRow["typeid2"]);
           	$typeOptions = $tl->GetOptionArray($arcRow["typeid2"],$cuserLogin->getUserChannel(),$channelid);
            echo "<select name='typeid2' style='width:300'>\r\n";
            if($arcRow["typeid2"]=="0") echo "<option value='0' selected>请选择副分类...</option>\r\n";
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
                	<img src="img/dedeexplode.gif" width="11" height="11" border="0" onClick="showHide('addtable')" style="cursor: hand;">
                </td>
                  <td width="61%"><strong>软件附加参数：</strong></td>
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
  <table width="98%"  border="0" align="center" cellpadding="2" cellspacing="2" id="addtable">
    <tr> 
      <td width="100%" height="24" colspan="4" class="bline"> <table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">文件类型：</td>
            <td width="220">
            	<select name="filetype" id="filetype" style="width:100">
                <?
                if($addRow["filetype"]!="") echo "<option value=\"".$addRow["filetype"]."\">".$addRow["filetype"]."</option>\r\n";
                ?>
                <option value=".exe">.exe</option>
                <option value=".zip">.zip</option>
                <option value=".rar">.rar</option>
                <option value=".iso">.iso</option>
                <option value=".gz">.gz</option>
                <option value="其它">其它</option>
              </select></td>
            <td width="80">界面语言：</td>
            <td width="220">
            	<select name="language" id="language" style="width:100">
                <?
                if($addRow["language"]!="") echo "<option value=\"".$addRow["language"]."\">".$addRow["language"]."</option>\r\n";
                ?>
                <option value="简体中文">简体中文</option>
                <option value="英文软件">英文软件</option>
                <option value="繁体中文">繁体中文</option>
                <option value="其它类型">其它类型</option>
              </select></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">软件类型：</td>
            <td width="220">
            	<select name="softtype" id="softtype" style="width:100">
                <?
                if($addRow["softtype"]!="") echo "<option value=\"".$addRow["softtype"]."\">".$addRow["softtype"]."</option>\r\n";
                ?>
                <option value="国产软件">国产软件</option>
                <option value="国外软件">国外软件</option>
                <option value="汉化补丁">汉化补丁</option>
              </select>
            </td>
            <td width="80">授权方式：</td>
            <td width="220">
            	<select name="accredit" id="accredit" style="width:100">
                <?
                if($addRow["accredit"]!="") echo "<option value=\"".$addRow["accredit"]."\">".$addRow["accredit"]."</option>\r\n";
                ?>
                <option value="共享软件">共享软件</option>
                <option value="免费软件">免费软件</option>
                <option value="开源软件">开源软件</option>
                <option value="商业软件">商业软件</option>
                <option value="破解软件">破解软件</option>
                <option value="游戏外挂">游戏外挂</option>
              </select>
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">运行环境：</td>
            <td width="220">
            	<input type='text' name='os' value='<?=$addRow["os"]?>' style='width:200'>
            </td>
            <td width="80">软件等级：</td>
            <td width="220">
            	<select name="softrank" id="softrank" style="width:100">
                 <?
                if($addRow["softrank"]!="") echo "<option value=\"".$addRow["softrank"]."\">".$addRow["softrank"]."星</option>\r\n";
                ?>
                <option value="1">一星</option>
                <option value="2">二星</option>
                <option value="3">三星 </option>
                <option value="4">四星</option>
                <option value="5">五星</option>
              </select>
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">官方网址：</td>
            <td width="220">
            	<input name="officialUrl" type="text" id="officialUrl" value="<?=$addRow["officialUrl"]?>">
            </td>
            <td width="80">程序演示：</td>
            <td width="220">
            	<input name="officialDemo" type="text" id="officialDemo" value="<?=$addRow["officialDemo"]?>">
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline">
      	<table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="80">软件大小：</td>
            <td width="220">
            	<input name="softsize" type="text" id="softsize" style="width:100"  value="<?=$addRow["softsize"]?>"> 
            </td>
            <td width="81">&nbsp;</td>
            <td width="219">&nbsp;</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" bgcolor="#F1F5F2" class="bline2"><strong>软件链接列表：</strong></td>
    </tr>
    <tr>
      <td height="24" colspan="4" class="bline"><table width="600" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="72">其它地址：</td>
            <td>
            	<input name="picnum" type="text" id="picnum" size="8" value="5"> 
              <input name='kkkup' type='button' id='kkkup2' value='增加数量' onClick="MakeUpload();">
              (最多为9个链接)
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline">
        <?
        echo $nForm;
	      echo "<span id='uploadfield'></span>";
	      ?>
      </td>
    </tr>
    <tr> 
      <td height="24" colspan="4" bgcolor="#F1F5F2" class="bline2"><strong>软件详细介绍：</strong></td>
    </tr>
    <tr> 
      <td height="100" colspan="4" class="bline"> 
        <?
	GetEditor("body",$addRow["introduce"],250,"Small");
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
                <td width="99"><img src="img/button_reset.gif" width="60" height="22" border="0" onClick="location.reload();" style="cursor:hand"></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
</form>
<?
$dsql->Close();
?>
</body>
</html>