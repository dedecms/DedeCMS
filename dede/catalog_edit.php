<?
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost)) $dopost = "";
if(empty($ID)) $ID="0";
$ID = ereg_replace("[^0-9]","",$ID);

//检查权限许可
CheckPurview('t_Edit,t_AccEdit');
//检查栏目操作许可
CheckCatalog($ID,"你无权更改本栏目！");

$dsql = new DedeSql(false);

//----------------------------------
//保存改动 Action Save
//-----------------------------------
if($dopost=="save")
{
	 $description = Html2Text($description);
   $keywords = Html2Text($keywords);
   
   if($cfg_cmspath!="") $typedir = ereg_replace("^{$cfg_cmspath}","{cmspath}",$typedir);
   else if(!eregi("{cmspath}",$typedir)) $typedir = "{cmspath}".$typedir;
   
   $upquery = "
     Update #@__arctype set
     issend='$issend',
     sortrank='$sortrank',
     typename='$typename',
     typedir='$typedir',
     isdefault='$isdefault',
     defaultname='$defaultname',
     issend='$issend',
     channeltype='$channeltype',
     tempindex='$tempindex',
     templist='$templist',
     temparticle='$temparticle',
     tempone='$tempone',
     namerule='$namerule',
     namerule2='$namerule2',
     ispart='$ispart',
     corank='$corank',
     description='$description',
     keywords='$keywords',
     moresite='$moresite',
     siterefer='$siterefer',
     sitepath='$sitepath',
     siteurl='$siteurl',
     ishidden='$ishidden'
   where ID='$ID'";
   
   if(!$dsql->ExecuteNoneQuery($upquery)){
   	 ShowMsg("保存当前栏目更改时失败，请检查你的输入资料是否存在问题！","-1");
   	 exit();
   }
   
   //如果选择子栏目可投稿，更新顶级栏目为可投稿
   if($topID>0 && $issend==1){
   	 $dsql->ExecuteNoneQuery("Update #@__arctype set issend='$issend' where ID='$topID'; ");
   }
   
   //更改子栏目属性
   if(!empty($upnext))
   {
   	 require_once(dirname(__FILE__)."/../include/inc_typelink.php");
   	 $tl = new TypeLink($ID);
   	 $slinks = $tl->GetSunID($ID,'###',0);
   	 $slinks = str_replace("###.typeid","ID",$slinks);
   	 $upquery = "
       Update #@__arctype set
       issend='$issend',
       sortrank='$sortrank',
       defaultname='$defaultname',
       channeltype='$channeltype',
       tempindex='$tempindex',
       templist='$templist',
       temparticle='$temparticle',
       namerule='$namerule',
       namerule2='$namerule2',
       moresite='$moresite',
       siterefer='$siterefer',
       sitepath='$sitepath',
       siteurl='$siteurl',
       ishidden='$ishidden'
     where 1=1 And $slinks";
   
     if(!$dsql->ExecuteNoneQuery($upquery)){
   	   ShowMsg("更改当前栏目成功，但更改下级栏目属性时失败！","-1");
   	   exit();
     }
     $tl->Close();
     
   }
   //--------------------------
   $dsql->Close();
   ShowMsg("成功更改一个分类！","catalog_main.php");
   exit();
}//End Save Action


$dsql->SetQuery("Select #@__arctype.*,#@__channeltype.typename as ctypename From #@__arctype left join #@__channeltype on #@__channeltype.ID=#@__arctype.channeltype where #@__arctype.ID=$ID");
$myrow = $dsql->GetOne();
$topID = $myrow['topID'];
if($topID>0)
{
	$toprow = $dsql->GetOne("Select moresite,siterefer,sitepath,siteurl From #@__arctype where ID=$topID");
	foreach($toprow as $k=>$v){
	  if(!ereg("[0-9]",$k)) $myrow[$k] = $v;
	}
}
//读取频道模型信息
$channelid = $myrow['channeltype'];
$row = $dsql->GetOne("select * from #@__channeltype where ID='$channelid'");
$nid = $row['nid'];
//读取所有模型资料
$dsql->SetQuery("select * from #@__channeltype where ID>-1 And isshow=1 order by ID");
$dsql->Execute();
while($row=$dsql->GetObject())
{
  $channelArray[$row->ID]['typename'] = $row->typename;
  $channelArray[$row->ID]['nid'] = $row->nid;
}
//父栏目是否为二级站点
if(!empty($myrow['moresite'])){
	 $moresite = $myrow['moresite'];
}else{
	 $moresite = 0;
}

if($myrow['topID']==0){
	PutCookie('lastCid',$ID,3600*24,"/");
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>栏目管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language="javascript">
var channelArray = new Array();
<?    
$i = 0;
foreach($channelArray as $k=>$arr){
  echo "channelArray[$k] = \"{$arr['nid']}\";\r\n";
}
?>
	
function SelectTemplets(fname){
   var posLeft = window.event.clientY-200;
   var posTop = window.event.clientX-300;
   window.open("../include/dialog/select_templets.php?f="+fname, "poptempWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
}
  
function ShowHide(objname){
  var obj = document.getElementById(objname);
  if(obj.style.display == "block" || obj.style.display == "")
	   obj.style.display = "none";
  else
	   obj.style.display = "block";
}
  
function ShowObj(objname){
   var obj = document.getElementById(objname);
	 obj.style.display = "block";
}
  
function HideObj(objname){
  var obj = document.getElementById(objname);
	obj.style.display = "none";
}
  
function ShowItem1(){
  ShowObj('head1'); ShowObj('needset'); HideObj('head2'); HideObj('adset');
}
  
function ShowItem2(){
  ShowObj('head2'); ShowObj('adset'); HideObj('head1'); HideObj('needset');
}
  
function CheckTypeDir(){
  var upinyin = document.getElementById('upinyin');
  var tpobj = document.getElementById('typedir');
  if(upinyin.checked) tpobj.style.display = "none";
  else tpobj.style.display = "block";
}
  
function ParTemplet(obj)
{
  var sevvalue = channelArray[obj.value];
  var tempindex = document.getElementsByName('tempindex');
  var templist = document.getElementsByName('templist');
  var temparticle = document.getElementsByName('temparticle');
  //var dfstyle = document.getElementsByName('dfstyle');
  //var dfstyleValue = dfstyle[0].value;
  tempindex[0].value = "{style}/index_"+sevvalue+".htm";
  templist[0].value = "{style}/list_"+sevvalue+".htm";
  temparticle[0].value = "{style}/article_"+sevvalue+".htm";
}
  
function checkSubmit()
{
   if(document.form1.typename.value==""){
		  alert("栏目名称不能为空！");
		  document.form1.typename.focus();
		  return false;
	 }
	 return true;
}
</script>
</head>
<body leftmargin='15' topmargin='10'>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#111111" style="BORDER-COLLAPSE: collapse">
  <tr> 
    <td width="100%" height="20" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="30"><IMG height=14 src="img/book1.gif" width=20> &nbsp;<a href="catalog_main.php"><u>栏目管理</u></a>&gt;&gt;增加栏目</td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td width="100%" height="1" background="img/sp_bg.gif"></td>
  </tr>
</table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr><td height="10"></td></tr>
  <tr>
  <form name="form1" action="catalog_edit.php" method="post" onSubmit="return checkSubmit();">
  <input type="hidden" name="dopost" value="save">
  <input type="hidden" name="ID" value="<?=$ID?>">
  <input type="hidden" name="topID" value="<?=$myrow['topID']?>">
  <td height="95" align="center" bgcolor="#FFFFFF">
	<table width="100%" border="0" cellspacing="0" id="head1" cellpadding="0" style="border-bottom:1px solid #CCCCCC">
     <tr> 
       <td colspan="2" bgcolor="#FFFFFF">
<table width="168" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="84" height="24" align="center" background="img/itemnote1.gif">&nbsp;常规选项&nbsp;</td>
                  <td width="84" align="center" background="img/itemnote2.gif"><a href="#" onClick="ShowItem2()"><u>高级选项</u></a>&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
        </table> 
        <table width="100%" border="0" cellspacing="0" id="head2" cellpadding="0" style="border-bottom:1px solid #CCCCCC;display:none">
          <tr>
            <td colspan="2" bgcolor="#FFFFFF">
<table width="168" height="24" border="0" cellpadding="0" cellspacing="0">
                <tr> 
                  <td width="84" align="center" background="img/itemnote2.gif" bgcolor="#F2F7DF"><a href="#" onClick="ShowItem1()"><u>常规选项</u></a>&nbsp;</td>
                  <td width="84" align="center" background="img/itemnote1.gif">高级选项&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
       </table>
	    <table width="100%" border="0"  id="needset" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="120" class='bline' height="26">是否支持投稿：</td>
            <td class='bline'> <input type='radio' name='issend' value='0' class='np' <?if($myrow['issend']=="0") echo " checked";?>>
              不支持&nbsp; <input type='radio' name='issend' value='1' class='np' <?if($myrow['issend']=="1") echo " checked";?>>
              支持 </td>
          </tr>
          <tr> 
            <td width="120" class='bline' height="26">是否隐藏栏目：</td>
            <td class='bline'> <input type='radio' name='ishidden' value='0' class='np'<?if($myrow['ishidden']=="0") echo " checked";?>>
              显示　&nbsp; <input type='radio' name='ishidden' value='1' class='np'<?if($myrow['ishidden']=="1") echo " checked";?>>
              隐藏 </td>
          </tr>
          <tr> 
            <td class='bline' height="26">栏目名称：</td>
            <td class='bline'><input name="typename" type="text" id="typename" size="30" value="<?=$myrow['typename']?>"></td>
          </tr>
          <tr> 
            <td class='bline' height="26"> 排列顺序： </td>
            <td class='bline'> <input name="sortrank" size="6" type="text" value="<?=$myrow['sortrank']?>">
              （由低 -&gt; 高） </td>
          </tr>
          <tr> 
            <td class='bline' height="26">浏览权限：</td>
            <td class='bline'> <select name="corank" id="corank" style="width:100">
                <?
              $dsql->SetQuery("Select * from #@__arcrank where rank >= 0");
              $dsql->Execute();
              while($row = $dsql->GetObject())
              {
              	if($myrow['corank']==$row->rank)
              	  echo "<option value='".$row->rank."' selected>".$row->membername."</option>\r\n";
				        else
				          echo "<option value='".$row->rank."'>".$row->membername."</option>\r\n";
              }
              ?>
              </select>
              (仅限制栏目里的文档浏览权限) </td>
          </tr>
          <tr> 
            <td class='bline' height="26">文件保存目录：</td>
            <td class='bline'> <input name="typedir" type="text" id="typedir" value="<?=$myrow['typedir']?>" style="width:300"> 
            </td>
          </tr>
          <tr> 
            <td class='bline' height="26">内容模型： &nbsp; </td>
            <td class='bline'> <select name="channeltype" id="channeltype" style="width:200px" onChange="ParTemplet(this)">
                <?    
            foreach($channelArray as $k=>$arr)
            {
            	if($k==$channelid) echo "    <option value='{$k}' selected>{$arr['typename']}|{$arr['nid']}</option>\r\n";
              else  echo "    <option value='{$k}'>{$arr['typename']}|{$arr['nid']}</option>\r\n";
            }
            ?>
              </select> </td>
          </tr>
          <tr> 
            <td class='bline' height="26">栏目列表选项：</td>
            <td class='bline'> <input type='radio' name='isdefault' value='1' class='np'<? if($myrow['isdefault']==1) echo" checked";?>>
              链接到默认页 
              <input type='radio' name='isdefault' value='0' class='np'<? if($myrow['isdefault']==0) echo" checked";?>>
              链接到列表第一页 
              <input type='radio' name='isdefault' value='-1' class='np'<? if($myrow['isdefault']==-1) echo" checked";?>>
              使用动态页 </td>
          </tr>
          <tr> 
            <td class='bline' height="26">默认页的名称： </td>
            <td class='bline'><input name="defaultname" type="text" value="<?=$myrow['defaultname']?>"></td>
          </tr>
          <tr> 
            <td height="26" class='bline'>栏目属性：</td>
            <td class='bline'> <input name="ispart" type="radio" id="radio" value="0" class='np'<? if($myrow['ispart']==0) echo" checked";?>>
              最终列表栏目（允许在本栏目发布文档，并生成文档列表）<br> <input name="ispart" type="radio" id="radio2" value="1" class='np'<? if($myrow['ispart']==1) echo" checked";?>>
              频道封面（栏目本身不允许发布文档）<br> <input name="ispart" type="radio" id="radio3" value="2" class='np'<? if($myrow['ispart']==2) echo" checked";?>>
              单独页面（栏目本身不允许发布文档） </td>
          </tr>
        </table>
	    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="display:none" id="adset">
          <tr> 
            <td class='bline' width="120" height="24">多站点支持：</td>
            <td class='bline'> <input name="moresite" type="radio"  class="np" value="0"<? if($myrow['moresite']==0) echo" checked";?>>
              不启用 
              <input type="radio" name="moresite" class="np" value="1"<? if($myrow['moresite']==1) echo" checked";?>>
              启用 </td>
          </tr>
          <tr> 
            <td height="24" bgcolor="#F3F7EA">说明：</td>
            <td bgcolor="#F3F7EA">绑名绑定仅需要在顶级栏目设定，下级栏目更改无效。</td>
          </tr>
          <tr> 
            <td class='bline' height="24">绑定域名：</td>
            <td class='bline'> <input name="siteurl" type="text" id="siteurl" size="35" value="<?=$myrow['siteurl']?>">
              (需加 http://，一级或二级域名的根网址) </td>
          </tr>
          <tr> 
            <td class='bline' height="24">站点根目录：</td>
            <td class='bline'> <input name="sitepath" type="text" id="sitepath" size="35" value="<?=$myrow['sitepath']?>"> 
              <input name="siterefer" type="radio" id="siterefer1" class="np" value="1"<? if($myrow['siterefer']==1) echo" checked";?>>
              相对于当前站点根目录 
              <input name="siterefer" type="radio" id="siterefer2" class="np" value="2"<? if($myrow['siterefer']==2) echo" checked";?>>
              绝对路径 </td>
          </tr>
          <tr id='helpvar1' style='display:none'> 
            <td height="24" bgcolor="#F3F7EA">支持变量： </td>
            <td bgcolor="#F3F7EA"> {tid}表示栏目ID，<br>
              {cid}表示频道模型的'名字ID' <font color='#888888'> （ 
              <?
              foreach($channelArray as $k=>$arr)
              {
            	   echo "{$arr['typename']}({$arr['nid']})、";
              }
             ?>
              ） </font> <br/>
              模板文件的默认位置是放在模板目录 "cms安装目录 
              <?=$cfg_templets_dir ?>
              " 内。 
              <input type='hidden' value='{style}' name='dfstyle'> </td>
          </tr>
          <tr> 
            <td height="26">封面模板：</td>
            <td> <input name="tempindex" type="text" value="<?=$myrow['tempindex']?>" style="width:300"> 
              <input type="button" name="set1" value="浏览..." style="width:60" onClick="SelectTemplets('form1.tempindex');"> 
              <img src="img/help.gif" alt="帮助" width="16" height="16" border="0" style="cursor:hand" onclick="ShowHide('helpvar1')"> 
            </td>
          </tr>
          <tr> 
            <td height="26">列表模板：</td>
            <td> <input name="templist" type="text" value="<?=$myrow['templist']?>" style="width:300"> 
              <input type="button" name="set3" value="浏览..." style="width:60" onClick="SelectTemplets('form1.templist');"> 
            </td>
          </tr>
          <tr> 
            <td height="26">文章模板：</td>
            <td><input name="temparticle" type="text" value="<?=$myrow['temparticle']?>" style="width:300"> 
              <input type="button" name="set4" value="浏览..." style="width:60" onClick="SelectTemplets('form1.temparticle');"> 
            </td>
          </tr>
          <tr> 
            <td height="26">单独页面模板：</td>
            <td><input name="tempone" type="text" value="<?=$myrow['tempone']?>" style="width:300"> 
              <input type="button" name="set2" value="浏览..." style="width:60" onClick="SelectTemplets('form1.tempone');"> 
            </td>
          </tr>
          <tr id='helpvar2' style='display:none'> 
            <td height="24" bgcolor="#F3F7EA">支持变量： </td>
            <td height="24" bgcolor="#F3F7EA"> {Y}、{M}、{D} 年月日<br/>
              {timestamp} INT类型的UNIX时间戳<br/>
              {aid} 文章ID<br/>
              {pinyin} 拼音+文章ID<br/>
              {py} 拼音部首+文章ID<br/>
              {typedir} 栏目目录 <br/>
              {cc} 日期+ID混编后用转换为适合的字母 <br/>
              </td>
          </tr>
          <tr> 
            <td height="26">文章命名规则：</td>
            <td> <input name="namerule" type="text" id="namerule" value="<?=$myrow['namerule']?>" size="40"> 
              <img src="img/help.gif" alt="帮助" width="16" height="16" border="0" style="cursor:hand" onclick="ShowHide('helpvar2')"> 
            </td>
          </tr>
          <tr id='helpvar3' style='display:none'> 
            <td height="24" bgcolor="#F3F7EA">支持变量： </td>
            <td bgcolor="#F3F7EA">{page} 列表的页码</td>
          </tr>
          <tr> 
            <td height="26">列表命名规则：</td>
            <td> <input name="namerule2" type="text" id="namerule2" value="<?=$myrow['namerule2']?>" size="40"> 
              <img src="img/help.gif" alt="帮助" width="16" height="16" border="0" style="cursor:hand" onclick="ShowHide('helpvar3')"></td>
          </tr>
          <tr> 
            <td height="65">关键字：</td>
            <td> <textarea name="keywords" cols="40" rows="3" id="keywords"><?=$myrow['keywords']?></textarea> 
            </td>
          </tr>
          <tr>
            <td height="65">栏目描述：</td>
            <td height="65"><textarea name="description" cols="40" rows="3" id="textarea"><?=$myrow['description']?></textarea></td>
          </tr>
          <tr> 
            <td height="45">继承选项：</td>
            <td> 
              <input name="upnext" type="checkbox" id="upnext" value="1" class="np">
              同时更改下级栏目的浏览权限、内容类型、模板风格、命名规则等通用属性
            </td>
          </tr>
        </table>
          <table width="98%" border="0" cellspacing="0" cellpadding="0">
		       <tr> 
            <td width="1%" height="50"></td>
            <td width="99%" valign="bottom">
            <input name="imageField" type="image" src="img/button_ok.gif" width="60" height="22" border="0" class="np">
            &nbsp;&nbsp;&nbsp;
            <a href="catalog_main.php"><img src="img/button_back.gif" width="60" height="22" border="0"></a>
			    </td>
          </tr>
        </table></td>
	  </form>
  </tr>
</table>
<?
$dsql->Close();
?>
</body>
</html>
