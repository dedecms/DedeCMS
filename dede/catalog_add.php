<?
require_once(dirname(__FILE__)."/config.php");
if(empty($ID)) $ID="0";
if(empty($listtype)) $listtype="";
if(empty($dopost)) $dopost = "";
if(empty($channeltype)) $channeltype = "";
if(empty($issend)) $issend="0";
$ID = ereg_replace("[^0-9]","",$ID);
$dsql = new DedeSql(false);
//--------------------------
//获取从父目录继承的默认参数
//--------------------------
if($listtype!="all" && $dopost=="")
{
	$dsql->SetQuery("Select #@__arctype.*,#@__channeltype.typename as ctypename From #@__arctype left join #@__channeltype on #@__channeltype.ID=#@__arctype.channeltype where #@__arctype.ID=$ID");
	$row = $dsql->GetOne();
	$typedir = $row['typedir'];
	$channeltype=$row['channeltype'];
	$channelname=$row['ctypename'];
	$issend = $row['issend'];
}
//---------------------------------------------
//保存事件响应，注解里的函数公为了方便在UltraEdit中显示
//---------------------------------------------
/*---------------------
function action_save();
----------------------*/
if($dopost=="save")
{
   if(empty($reID)) $reID = 0;
   $description = Html2Text($description);
   $keywords = Html2Text($keywords);
   
   //处理文章保存目录
   if(empty($isnext)) $isnext = 0;
   if($isnext==0) $nextdir = "/";
   if($typedir=="" && $ispart!=2) $typedir = GetPinyin($typename);
   else $typedir = str_replace("\\","/",$typedir);
   $typedir = $nextdir.ereg_replace("^/","",$typedir);
   
   if(!CreateDir($typedir))
   {
   	  $dsql->Close();
   	  ShowMsg("创建目录 $fullpath 失败，请检查你的路径是否存在问题！","-1");
   	  exit();
   }
   
   $in_query = "insert into #@__arctype(
   reID,sortrank,typename,typedir,isdefault,defaultname,issend,channeltype,
   tempindex,templist,temparticle,tempone,modname,namerule,namerule2,
   ispart,description,keywords)Values(
   '$reID','$sortrank','$typename','$typedir','$isdefault','$defaultname','$issend','$channeltype',
   '$tempindex','$templist','$temparticle','$tempone','default','$namerule','$namerule2',
   '$ispart','$description','$keywords')";
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
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>栏目管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language="javascript">
	function SelectTemplets(fname)
  {
     var posLeft = window.event.clientY-200;
     var posTop = window.event.clientX-300;
     window.open("../include/dialog/select_templets.php?f="+fname, "poptempWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
  }
</script>
</head>
<body background='img/allbg.gif' leftmargin='15' topmargin='10'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr> 
    <td height="19" background='img/tbg.gif'><a href="catalog_main.php"><u>栏目管理</u></a>&gt;&gt;增加栏目</td>
  </tr>
  <tr> 
    <td height="95" align="center" bgcolor="#FFFFFF">
    <table width="98%" border="0" cellspacing="0" cellpadding="0">
        <form name="form1" action="catalog_add.php" method="post">
          <input type="hidden" name="dopost" value="save">
          <input type="hidden" name="reID" value="<?if(!empty($ID)) echo $ID;?>">
          <tr> 
            <td height="26" width="120">是否支持投稿：</td>
            <td><input type='radio' name='issend' value='0' class='np' <?if($issend=="0") echo " checked";?>>
              不支持 &nbsp;&nbsp; <input type='radio' name='issend' value='1' class='np' <?if($issend=="1") echo " checked";?>>
              支持 </td>
          </tr>
          <tr> 
            <td height="26">栏目名称：</td>
            <td><input name="typename" type="text" id="typename" size="30"></td>
          </tr>
          <tr> 
            <td height="26"> 排列顺序： </td>
            <td><input name="sortrank" size="6" type="text" value="50">（由低 -&gt; 高） </td>
          </tr>
          <tr> 
            <td height="26">上级目录：</td>
            <td> 
              <?
            $pardir = $cfg_arcdir."/";
            if(!empty($typedir)) $pardir = $typedir."/";
            echo $pardir;
            ?>
              <input name="nextdir" type="hidden" id="nextdir" value="<?=$pardir?>"> 
            </td>
          </tr>
          <tr> 
            <td height="26">文件保存目录：</td>
            <td> <input name="typedir" type="text" id="typedir"> <input name="isnext" type="checkbox" id="isnext" class="np" value="1" checked>
              在上级目录建立 
              <input name="upinyin" type="checkbox" id="upinyin" class="np" value="1" checked>
              使用拼音 </td>
          </tr>
          <tr> 
            <td height="26"> 内容类型： &nbsp; </td>
            <td> <select name="channeltype" style="width:200px">
                <?
            if(empty($channeltype)) $channeltype="0";
            $dsql->SetQuery("select * from #@__channeltype where ID!=$channeltype And ID>-1 And isshow=1 order by ID");
            $dsql->Execute();
            if($listtype!="all")
            	echo "    <option value='$channeltype'>$channelname</option>\r\n";
            else
            {
            	while($row=$dsql->GetObject())
            	{
            		echo "    <option value='".$row->ID."'>".$row->typename."(cid=".$row->nid.")</option>\r\n";
            	}
            }
            ?>
              </select> </td>
          </tr>
          <tr> 
            <td height="26">列表页选项：</td>
            <td>
              <input type='radio' name='isdefault' value='1' class='np' checked>
              链接到默认页
              <input type='radio' name='isdefault' value='0' class='np'>
              链接到列表第一页
              <input type='radio' name='isdefault' value='-1' class='np'>
              列表使用动态页
             </td>
          </tr>
          <tr> 
            <td height="26">默认页的名称： </td>
            <td><input name="defaultname" type="text" value="index.html"></td>
          </tr>
          <tr> 
            <td height="26">栏目属性：</td>
            <td>
            	<input name="ispart" type="radio" id="radio" value="0" class='np' checked>
              传统的列表形式
              <input name="ispart" type="radio" id="radio" value="1" class='np'>
              使用封面模板 
              <input name="ispart" type="radio" id="radio" value="2" class='np'>
              使用用单独页面作为栏目
            </td>
          </tr>
          <tr> 
            <td height="26">模板变量：</td>
            <td>
            {tid}表示栏目ID，{cid}表示栏目的'名字ID'(内容类型的“(cid=***)”里的英文)
            <br/>
            模板文件的默认位置是放在模板目录 "cms安装目录<?=$cfg_templets_dir ?>" 内。
            </td>
          </tr>
          <tr> 
            <td height="26">封面模板：</td>
            <td>
            	<input name="tempindex" type="text" value="default/index_{cid}.htm" style="width:300">
            	<input type="button" name="set1" value="浏览..." style="width:60" onClick="SelectTemplets('form1.tempindex');">
            </td>
          </tr>
          <tr> 
            <td height="26">单独页面模板：</td>
            <td>
            	<input name="tempone" type="text" value="" style="width:300">
            	<input type="button" name="set2" value="浏览..." style="width:60" onClick="SelectTemplets('form1.tempone');">
            </td>
          </tr>
          <tr> 
            <td height="26">列表模板：</td>
            <td>
            	<input name="templist" type="text" value="default/list_{cid}.htm" style="width:300">
            	<input type="button" name="set3" value="浏览..." style="width:60" onClick="SelectTemplets('form1.templist');">
            </td>
          </tr>
          <tr> 
            <td height="26">文章模板：</td>
            <td>
            	<input name="temparticle" type="text" value="default/article_{cid}.htm" style="width:300">
              <input type="button" name="set4" value="浏览..." style="width:60" onClick="SelectTemplets('form1.temparticle');">
            </td>
          </tr>
          <tr> 
            <td height="26" colspan="2"> (Y、M、D为年月日，YMD的组合不允许含有“.”，文章名的{aid}可换为{pinyin}表示“拼音+文章ID”) 
            </td>
          </tr>
          <tr> 
            <td height="26">文章命名规则：</td>
            <td>{typedir}/ 
              <input name="namerule" type="text" id="namerule" value="{Y}/{M}{D}/{aid}.html" size="30"> 
            </td>
          </tr>
          <tr> 
            <td height="26">列表命名规则：</td>
            <td>{typedir}/ 
              <input name="namerule2" type="text" id="namerule2" value="list_{page}.html" size="30"></td>
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
          <tr> 
            <td height="50"></td>
            <td>
            	<input type="button" name="Submit" value=" 提交 " onClick="javascript:if(document.form1.typename.value!='') document.form1.submit();"> 
              　　
              <input type="button" name="Submit2" value=" 返回 " onClick="javascript:location.href='catalog_main.php';"> 
            </td>
          </tr>
          <tr> 
            <td height="20" colspan="2">&nbsp;</td>
          </tr>
        </form>
      </table></td>
  </tr>
</table>
<?
$dsql->Close();
?>
</body>
</html>
