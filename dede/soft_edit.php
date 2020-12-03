<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");
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
<script language='javascript' src='main.js'></script>
<script language="javascript">
<!--
function checkSubmit()
{
   if(document.form1.title.value==""){
	   alert("软件名称不能为空！");
	   document.form1.title.focus();
	   return false;
  }
}

function MakeUpload()
{
   var startNum = <?php echo $newRowStart?>;
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

-->
</script>
</head>
<body topmargin="8">
<form name="form1" action="soft_edit_action.php" enctype="multipart/form-data" method="post" onSubmit="return checkSubmit();">
<input type="hidden" name="channelid" value="<?php echo $channelid?>">
<input type="hidden" name="ID" value="<?php echo $aid?>">
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="4%" height="30"><IMG height=14 src="img/book1.gif" width=20> 
        &nbsp;</td>
      <td width="85%"><a href="catalog_do.php?cid=<?php echo $arcRow["typeid"]?>&dopost=listArchives"></a><a href="catalog_do.php?cid=<?php echo $arcRow["typeid"]?>&dopost=listArchives"><u>软件列表</u></a>&gt;&gt;更改软件</td>
      <td width="10%">&nbsp; <a href="catalog_main.php">[<u>栏目管理</u>]</a> </td>
      <td width="1%">&nbsp;</td>
    </tr>
  </table>
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" id="head1" class="htable">
    <tr> 
      <td colspan="2"> <table width="168" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="84" height="24" align="center" background="img/itemnote1.gif">&nbsp;常规参数&nbsp;</td>
            <td width="84" align="center" background="img/itemnote2.gif"><a href="#" onClick="ShowItem2()"><u>软件内容</u></a>&nbsp;</td>
          </tr>
        </table></td>
    </tr>
  </table>
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" id="head2" style="border-bottom:1px solid #CCCCCC;display:none">
    <tr> 
      <td colspan="2"> <table width="168" height="24" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="84" align="center" background="img/itemnote2.gif"><a href="#" onClick="ShowItem1()"><u>常规参数</u></a>&nbsp;</td>
            <td width="84" align="center" background="img/itemnote1.gif">软件内容&nbsp;</td>
          </tr>
        </table></td>
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
            <td width="90">软件名称：</td>
            <td width="240"><input name="title" type="text" id="title" style="width:200" value="<?php echo $arcRow["title"]?>"></td>
            <td width="90">附加参数：</td>
            <td> 
              <input name="iscommend" type="checkbox" id="iscommend" value="11" class="np"<?php  if($arcRow["iscommend"]>10) echo " checked";?>>
              推荐 
              <input name="isbold" type="checkbox" id="isbold" value="5" class="np"<?php  if($arcRow["iscommend"]==5||$arcRow["iscommend"]==16) echo " checked";?>>
              加粗
              <input name="isjump" onClick="ShowUrlTrEdit()" type="checkbox" id="isjump" value="1" class="np"<?php  echo $arcRow["redirecturl"]=="" ? "" : " checked";?>>
              跳转网址
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
            <td> <input name="redirecturl" type="text" id="redirecturl" style="width:300" value="<?php echo $arcRow["redirecturl"]?>"> 
            </td>
          </tr>
       </table>
	 </td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline">
      	<table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">简略标题：</td>
            <td width="240"><input name="shorttitle" type="text" value="<?php echo $arcRow["shorttitle"]?>" id="shorttitle" style="width:200"></td>
            <td width="90">自定属性：</td>
            <td> 
              <select name='arcatt' style='width:150'>
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
              </select>
            </td>
          </tr>
        </table>
        </td>
    </tr>
    <tr id="pictable"> 
      <td height="24" colspan="4" class="bline">
      	<table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90" height="81">
            	&nbsp;缩 略 图：<br/>
            	&nbsp;<input type='checkbox' class='np' name='ddisremote' value='1'>远程
            </td>
            <td width="340"> 
              <input name="picname" type="text" id="picname" style="width:230" value="<?php echo $arcRow["litpic"]?>">
              <input type="button" name="Submit" value="浏览..." style="width:60" onClick="SelectImage('form1.picname','');" class='nbt'>
            </td>
            <td align="center"><img src="<?php if($arcRow["litpic"]!="") echo $arcRow["litpic"]; else echo "img/pview.gif";?>" width="150" height="100" id="picview" name="picview"> 
            </td>
          </tr>
        </table>
       </td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">软件来源：</td>
            <td width="240"> <input name="source" type="text" id="source" style="width:200" value="<?php echo $arcRow["source"]?>">  
            </td>
            <td width="90">软件作者：</td>
            <td width="159"><input name="writer" type="text" id="writer"  style="width:120"value="<?php echo $arcRow["writer"]?>"> 
            </td>
            <td>&nbsp; </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">内容排序：</td>
            <td width="240">
            	<select name="sortup" id="sortup" style="width:150">
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
              </select>
              </td>
            <td width="90">标题颜色：</td>
            <td width="159">
            	<input name="color" type="text" id="color" style="width:120" value="<?php echo $arcRow["color"]?>"> 
            </td>
            <td> 
              <input name="modcolor" type="button" id="modcolor" value="选取" onClick="ShowColor()" class='nbt'>
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline">
      	<table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">阅读权限：</td>
            <td width="240">
             <select name="arcrank" id="arcrank" style="width:150">
              <option value='<?php echo $arcRow["arcrank"]?>'><?php echo $arcRow["rankname"]?></option>
                <?php 
              $urank = $cuserLogin->getUserRank();
              $dsql = new DedeSql(false);
              $dsql->SetQuery("Select * from #@__arcrank where adminrank<='$urank'");
              $dsql->Execute();
              while($row = $dsql->GetObject()){
              	echo "     <option value='".$row->rank."'>".$row->membername."</option>\r\n";
              }
              ?>
              </select>
             </td>
            <td width="63">发布选项：</td>
            <td>
            	<input name="ishtml" type="radio" class="np" value="1"<?php if($arcRow["ismake"]!=-1) echo " checked";?>>
              生成HTML 
              <input type="radio" name="ishtml" class="np" value="0"<?php if($arcRow["ismake"]==-1) echo " checked";?>>
              仅动态浏览
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="75" colspan="4" class="bline">
<table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90" height="51">简要说明：</td>
            <td width="240"> <textarea name="description" rows="3" id="description" style="width:200"><?php echo $arcRow["description"]?></textarea> 
            </td>
            <td width="90">关键字：</td>
            <td> <textarea name="keywords" rows="3" id="keywords" style="width:200"><?php echo $arcRow["keywords"]?></textarea> 
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline">
      	<table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">录入时间：</td>
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
            <td width="90">发布时间：</td>
            <td width="427"> 
              <?php 
			$nowtime = GetDateTimeMk($arcRow["pubdate"]);
			echo "<input name=\"pubdate\" value=\"$nowtime\" type=\"text\" id=\"pubdate\" style=\"width:200\">";
			?>
            </td>
            <td width="96" align="center">消费点数：</td>
            <td width="187">
<input name="money" type="text" id="money" value="<?php echo $arcRow["money"]?>" size="10">
            </td>
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
  </table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr><td height="2"></td></tr>
</table>
  <table width="98%"  border="0" align="center" cellpadding="2" cellspacing="2" style="display:none" id="adset">
    <tr> 
      <td width="100%" height="24" colspan="4" class="bline"> <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">文件类型：</td>
            <td width="240"> 
              <select name="filetype" id="filetype" style="width:100">
                <?php 
                if($addRow["filetype"]!="") echo "<option value=\"".$addRow["filetype"]."\">".$addRow["filetype"]."</option>\r\n";
                ?>
                <option value=".exe">.exe</option>
                <option value=".zip">.zip</option>
                <option value=".rar">.rar</option>
                <option value=".iso">.iso</option>
                <option value=".gz">.gz</option>
                <option value="其它">其它</option>
              </select>
            </td>
            <td width="90">界面语言：</td>
            <td> 
              <select name="language" id="language" style="width:100">
                <?php 
                if($addRow["language"]!="") echo "<option value=\"".$addRow["language"]."\">".$addRow["language"]."</option>\r\n";
                ?>
                <option value="简体中文">简体中文</option>
                <option value="英文软件">英文软件</option>
                <option value="繁体中文">繁体中文</option>
                <option value="其它类型">其它类型</option>
              </select>
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline"><table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">软件类型：</td>
            <td width="240"> 
              <select name="softtype" id="softtype" style="width:100">
                <?php 
                if($addRow["softtype"]!="") echo "<option value=\"".$addRow["softtype"]."\">".$addRow["softtype"]."</option>\r\n";
                ?>
                <option value="国产软件">国产软件</option>
                <option value="国外软件">国外软件</option>
                <option value="汉化补丁">汉化补丁</option>
              </select>
            </td>
            <td width="90">授权方式：</td>
            <td> 
              <select name="accredit" id="accredit" style="width:100">
                <?php 
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
      <td height="24" colspan="4" class="bline"><table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">运行环境：</td>
            <td width="240"> 
              <input type='text' name='os' value='<?php echo $addRow["os"]?>' style='width:200'>
            </td>
            <td width="90">软件等级：</td>
            <td> 
              <select name="softrank" id="softrank" style="width:100">
                 <?php 
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
      <td height="24" colspan="4" class="bline"><table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">官方网址：</td>
            <td width="240"> 
              <input name="officialUrl" type="text" id="officialUrl" value="<?php echo $addRow["officialUrl"]?>">
            </td>
            <td width="90">程序演示：</td>
            <td> 
              <input name="officialDemo" type="text" id="officialDemo" value="<?php echo $addRow["officialDemo"]?>">
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline">
      	<table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="90">软件大小：</td>
            <td width="240"> 
              <input name="softsize" type="text" id="softsize" style="width:100"  value="<?php echo $addRow["softsize"]?>">
            </td>
            <td width="90">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" bgcolor="#F1F5F2" class="bline2"><strong>软件链接列表：</strong></td>
    </tr>
    <tr>
      <td height="24" colspan="4" class="bline"><table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="72">其它地址：</td>
            <td>
            	<input name="picnum" type="text" id="picnum" size="8" value="5"> 
              <input name='kkkup' type='button' id='kkkup2' value='增加数量' onClick="MakeUpload();" class='nbt'>
              (最多为9个链接)
            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="24" colspan="4" class="bline">
        <?php 
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
        <?php 
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
<script language='javascript'>if($Nav()!="IE") ShowObj('adset');</script>
<?php 
$dsql->Close();
?>
</body>
</html>