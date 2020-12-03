<?
require_once(dirname(__FILE__)."/config.php");
if(empty($ID)) $ID = 0;
if(empty($listtype)) $listtype="";
if(empty($dopost)) $dopost = "";
if(empty($channelid)) $channelid = 1;
$ID = ereg_replace("[^0-9]","",$ID);

if($ID==0){ CheckPurview('t_New'); }
else{
	CheckPurview('t_AccNew');
	CheckCatalog($ID,"你无权在本栏目下创建子类！");
}

$dsql = new DedeSql(false);
if(empty($myrow)) $myrow = "";
$issend = 0;
$corank = 0;
//--------------------------
//获取从父目录继承的默认参数
//--------------------------
if($dopost=="" && $ID>0)
{
  $myrow = $dsql->GetOne("Select #@__arctype.*,#@__channeltype.typename as ctypename From #@__arctype left join #@__channeltype on #@__channeltype.ID=#@__arctype.channeltype where #@__arctype.ID=$ID");
	$issennd = $myrow['issend'];
	$corank = $myrow['corank'];
	$topID = $myrow['topID'];
	$issend = $myrow['issend'];
	$corank = $myrow['corank'];
	if($topID>0)
	{
	  	$toprow = $dsql->GetOne("Select moresite,siterefer,sitepath,siteurl From #@__arctype where ID=$topID");
	  	foreach($toprow as $k=>$v){
	  		if(!ereg("[0-9]",$k)) $myrow[$k] = $v;
	  	}
	}
	$typedir = $myrow['typedir'];
}
else if($dopost=="save")
{
   if(empty($reID)) $reID = 0;
   if(empty($upinyin)) $upinyin = 0;
   $description = Html2Text($description);
   $keywords = Html2Text($keywords);
   
   //栏目的参照目录
   if($reID==0 && $moresite==1) $nextdir = "/";
   else{
     if($referpath=="cmspath") $nextdir = "{cmspath}";
     else if($referpath=="basepath") $nextdir = "";
     else $nextdir = $nextdir;
   }
   //用拼音命名
   if($upinyin==1 || ($typedir=="" && $sitepath=="")) $typedir = GetPinyin($typename);
   
   $typedir = $nextdir."/".$typedir;
   
   $typedir = ereg_replace("/{1,}","/",$typedir);
   
   if($referpath=="basepath" && $siteurl!="") $typedir = "";
   
   //检测二级网址
   if($siteurl!=""){
      $siteurl = ereg_replace("/$","",$siteurl);
      if(!eregi("http://",$siteurl)){
      	$dsql->Close();
   	    ShowMsg("你绑定的二级域名无效，请用(http://host)的形式！","-1");
   	    exit();
      }
      if(eregi($cfg_basehost,$siteurl)){
      	$dsql->Close();
   	    ShowMsg("你绑定的二级域名与当前站点是同一个域，不需要绑定！","-1");
   	    exit();
      }
   }
   
   //创建目录
   $true_typedir = str_replace("{cmspath}",$cfg_cmspath,$typedir);
   $true_typedir = ereg_replace("/{1,}","/",$true_typedir);
   if(!CreateDir($true_typedir,$siterefer,$sitepath))
   {
   	  $dsql->Close();
   	  ShowMsg("创建目录 {$true_typedir} 失败，请检查你的路径是否存在问题！","-1");
   	  exit();
   }
   
   $in_query = "insert into #@__arctype(
    reID,sortrank,typename,typedir,isdefault,defaultname,issend,channeltype,
    tempindex,templist,temparticle,tempone,modname,namerule,namerule2,
    ispart,corank,description,keywords,moresite,siterefer,sitepath,siteurl,ishidden)Values(
    '$reID','$sortrank','$typename','$typedir','$isdefault','$defaultname','$issend','$channeltype',
    '$tempindex','$templist','$temparticle','$tempone','default','$namerule','$namerule2',
    '$ispart','$corank','$description','$keywords','$moresite','$siterefer','$sitepath','$siteurl','$ishidden')";
   
   $dsql->SetQuery($in_query);
   if(!$dsql->ExecuteNoneQuery($in_query))
   {
   	 $dsql->Close();
   	 ShowMsg("保存目录数据时失败，请检查你的输入资料是否存在问题！","-1");
   	 exit();
   }
   $dsql->Close();
   ShowMsg("成功创建一个分类！","catalog_main.php");
   exit();
   
}//End dopost==save

//读取频道模型信息
if(is_array($myrow)) $channelid = $myrow['channeltype'];
else $channelid = 1;
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
foreach($channelArray as $k=>$arr)
{
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
  var dfstyle = document.getElementsByName('dfstyle');
  var dfstyleValue = dfstyle[0].value;
  tempindex[0].value = dfstyleValue+"/index_"+sevvalue+".htm";
  templist[0].value = dfstyleValue+"/list_"+sevvalue+".htm";
  temparticle[0].value = dfstyleValue+"/article_"+sevvalue+".htm";
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

function CheckPathSet()
{
	var surl = document.getElementById("siteurl");
	var sreid = document.getElementById("reID");
	var mysel = document.getElementById("truepath3");
	if(surl.value!=""){
		if(sreid.value=="0" || sreid.value==""){
			mysel.checked = true;
		}
	}
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
  <form name="form1" action="catalog_add.php" method="post" onSubmit="return checkSubmit();">
  <input type="hidden" name="dopost" value="save">
  <input type="hidden" name="reID" id="reID" value="<?=$ID;?>">
  <?
  if($listtype!="all")
  {
  	echo "<input type='hidden' name='moresite' value='{$myrow['moresite']}'>\r\n";
  	echo "<input type='hidden' name='siterefer' value='{$myrow['siterefer']}'>\r\n";
  	echo "<input type='hidden' name='sitepath' value='{$myrow['sitepath']}'>\r\n";
  	echo "<input type='hidden' name='siteurl' value='{$myrow['siteurl']}'>\r\n";
  }
  ?>
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
            <td class='bline'>
            	<input type='radio' name='issend' value='0' class='np' <?if($issend=="0") echo " checked";?>>
              不支持&nbsp;
              <input type='radio' name='issend' value='1' class='np' <?if($issend=="1") echo " checked";?>>
              支持
             </td>
          </tr>
          <tr> 
            <td width="120" class='bline' height="26">是否隐藏栏目：</td>
            <td class='bline'>
            	<input type='radio' name='ishidden' value='0' class='np' checked>
              显示　&nbsp;
              <input type='radio' name='ishidden' value='1' class='np'>
              隐藏
             </td>
          </tr>
          <tr> 
            <td class='bline' height="26">栏目名称：</td>
            <td class='bline'><input name="typename" type="text" id="typename" size="30"></td>
          </tr>
          <tr> 
            <td class='bline' height="26"> 排列顺序： </td>
            <td class='bline'><input name="sortrank" size="6" type="text" value="50">
              （由低 -&gt; 高） </td>
          </tr>
          <tr> 
            <td class='bline' height="26">浏览权限：</td>
            <td class='bline'>
            	<select name="corank" id="corank" style="width:100">
                <?
              $dsql->SetQuery("Select * from #@__arcrank where rank >= 0");
              $dsql->Execute();
              while($row = $dsql->GetObject())
              {
              	if($corank==$row->rank) echo "<option value='".$row->rank."' selected>".$row->membername."</option>\r\n";
				        else
				        {
				        	//如果上级目录的corank>0，下级必须继承
				        	if($corank==0)
				        	{ echo "<option value='".$row->rank."'>".$row->membername."</option>\r\n"; }
				        }
              }
              ?>
              </select>
              (仅限制栏目里的文档浏览权限) </td>
          </tr>
          <tr> 
            <td class='bline' height="26">上级目录：</td>
            <td class='bline'> 
              <?
            $pardir = "{cmspath}".$cfg_arcdir;
            if(!empty($typedir)) $pardir = $typedir."/";
            $pardir = ereg_replace("/{1,}","/",$pardir);
            echo $pardir;
            ?>
              <input name="nextdir" type="hidden" id="nextdir" value="<?=$pardir?>"> 
            </td>
          </tr>
          <tr> 
            <td class='bline' height="26">文件保存目录：</td>
            <td class='bline'>
            	<table width="500" border="0" cellspacing="1" cellpadding="1">
                <tr> 
                  <td width="200">
                  	<input name="typedir" type="text" id="typedir" style="width:300">
                  </td>
                  <td width="300">
                  <input name="upinyin" type="checkbox" id="upinyin" class="np" value="1" onClick="CheckTypeDir()">
                  拼音
                  </td>
                </tr>
              </table>
              </td>
          </tr>
          <tr>
            <td class='bline' height="26">目录相对位置：</td>
            <td class='bline'>
			        <input name="referpath" type="radio" id="truepath1" class="np" value="parent" checked>
              上级目录 
              <?
              if($moresite==0){
              ?>
              <input name="referpath" type="radio" id="truepath2" class="np" value="cmspath">
              CMS根目录 
              <? } ?>
              <input name="referpath" type="radio" id="truepath3" class="np" value="basepath">
              站点根目录
              </td>
          </tr>
          <tr> 
            <td class='bline' height="26">内容模型： &nbsp; </td>
            <td class='bline'> 
           <select name="channeltype" id="channeltype" style="width:200px" onChange="ParTemplet(this)">
            <?    
            foreach($channelArray as $k=>$arr)
            {
            	if($k==$channelid) echo "    <option value='{$k}' selected>{$arr['typename']}|{$arr['nid']}</option>\r\n";
              else  echo "    <option value='{$k}'>{$arr['typename']}|{$arr['nid']}</option>\r\n";
            }
            ?>
              </select> 
            </td>
          </tr>
          <tr> 
            <td class='bline' height="26">栏目列表选项：</td>
            <td class='bline'> <input type='radio' name='isdefault' value='1' class='np' checked>
              链接到默认页 
              <input type='radio' name='isdefault' value='0' class='np'>
              链接到列表第一页 
              <input type='radio' name='isdefault' value='-1' class='np'>
              使用动态页 </td>
          </tr>
          <tr> 
            <td class='bline' height="26">默认页的名称： </td>
            <td class='bline'><input name="defaultname" type="text" value="index.html"></td>
          </tr>
          <tr> 
            <td height="26" class='bline'>栏目属性：</td>
            <td class='bline'>
            	<input name="ispart" type="radio" id="radio" value="0" class='np' checked>
              最终列表栏目（允许在本栏目发布文档，并生成文档列表） <br>
              <input name="ispart" type="radio" id="radio" value="1" class='np'>
              频道封面（栏目本身不允许发布文档） <br>
              <input name="ispart" type="radio" id="radio" value="2" class='np'>
              单独页面（栏目本身不允许发布文档）
             </td>
          </tr>
        </table>
	    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="display:none" id="adset">
          <?
          if($listtype=="all")
          {
          ?>
          <tr> 
            <td class='bline' width="120" height="24">多站点支持：</td>
            <td class='bline'> <input name="moresite" type="radio"  class="np" value="0" checked>
              不启用 
              <input type="radio" name="moresite" class="np" value="1">
              启用 </td>
          </tr>
          <tr> 
            <td height="24" bgcolor="#F3F7EA">说明：</td>
            <td bgcolor="#F3F7EA">绑名绑定仅需要在顶级栏目设定，下级栏目的目录和域名将相对于顶级栏目。</td>
          </tr>
          <tr>
            <td class='bline' height="24">绑定域名：</td>
            <td class='bline'>
            	<input name="siteurl" type="text" id="siteurl" size="35" onChange="CheckPathSet();">
              (需加 http://，一级或二级域名的根网址)
            </td>
          </tr>
          <tr> 
            <td class='bline' height="24">站点根目录：</td>
            <td class='bline'>
            	<input name="sitepath" type="text" id="sitepath" size="35"> 
              <input name="siterefer" type="radio" class="np" id="truepath" value="1" checked>
              相对于主站点根目录 
              <input name="siterefer" type="radio" id="truepath" class="np" value="2">
              绝对路径
            </td>
          </tr>
          <?
          }
          ?>
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
              <input type='hidden' value='{style}' name='dfstyle'> 
            </td>
          </tr>
          <tr> 
            <td height="26">封面模板：</td>
            <td> <input name="tempindex" type="text" value="{style}/index_<?=$nid?>.htm" style="width:300"> 
              <input type="button" name="set1" value="浏览..." style="width:60" onClick="SelectTemplets('form1.tempindex');"> 
              <img src="img/help.gif" alt="帮助" width="16" height="16" border="0" style="cursor:hand" onclick="ShowHide('helpvar1')"> 
            </td>
          </tr>
          <tr> 
            <td height="26">列表模板：</td>
            <td> <input name="templist" type="text" value="{style}/list_<?=$nid?>.htm" style="width:300"> 
              <input type="button" name="set3" value="浏览..." style="width:60" onClick="SelectTemplets('form1.templist');"> 
            </td>
          </tr>
          <tr> 
            <td height="26">文章模板：</td>
            <td><input name="temparticle" type="text" value="{style}/article_<?=$nid?>.htm" style="width:300"> 
              <input type="button" name="set4" value="浏览..." style="width:60" onClick="SelectTemplets('form1.temparticle');"> 
            </td>
          </tr>
          <tr> 
            <td height="26">单独页面模板：</td>
            <td><input name="tempone" type="text" value="" style="width:300"> 
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
            <td> <input name="namerule" type="text" id="namerule" value="{typedir}/{Y}{M}{D}/{aid}.html" size="40"> 
              <img src="img/help.gif" alt="帮助" width="16" height="16" border="0" style="cursor:hand" onclick="ShowHide('helpvar2')"> 
            </td>
          </tr>
          <tr id='helpvar3' style='display:none'> 
            <td height="24" bgcolor="#F3F7EA">支持变量： </td>
            <td bgcolor="#F3F7EA">{page} 列表的页码</td>
          </tr>
          <tr> 
            <td height="26">列表命名规则：</td>
            <td>
              <input name="namerule2" type="text" id="namerule2" value="{typedir}/list_{tid}_{page}.html" size="40"> 
              <img src="img/help.gif" alt="帮助" width="16" height="16" border="0" style="cursor:hand" onclick="ShowHide('helpvar3')"></td>
          </tr>
          <tr> 
            <td height="65">关键字：</td>
            <td> <textarea name="keywords" cols="40" rows="3" id="keywords"></textarea> 
            </td>
          </tr>
          <tr> 
            <td height="65">栏目描述：</td>
            <td height="65"> <textarea name="description" cols="40" rows="3" id="textarea2"></textarea></td>
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
