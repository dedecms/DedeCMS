<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Data');
//获取系统存在的表信息
$otherTables = Array();
$dedeSysTables = Array();
$channelTables = Array();
$dsql = new DedeSql(false);
$dsql->SetQuery("Select addtable From #@__channeltype");
$dsql->Execute();
while($row = $dsql->GetObject()){
	$channelTables[] = $row->addtable;
}
$dsql->SetQuery("Show Tables");
$dsql->Execute('t');
while($row = $dsql->GetArray('t')){
	if(ereg("^{$cfg_dbprefix}",$row[0])||in_array($row[0],$channelTables))
	{  $dedeSysTables[] = $row[0];  }
	else{ $otherTables[] = $row[0]; }
}

function TjCount($tbname,$dsql){
   $row = $dsql->GetOne("Select count(*) as dd From $tbname");
   return $row['dd'];
}

$mysql_version = $dsql->GetVersion();
$mysql_versions = explode(".",trim($mysql_version));
$mysql_version = $mysql_versions[0].".".$mysql_versions[1];

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>数据库维护</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language="javascript" src="../include/dedeajax.js"></script>
<script language="javascript">
var myajax;
var newobj;
var posLeft = 200;
var posTop = 150;
function LoadUrl(surl){
  newobj = document.getElementById('_mydatainfo');
  if(!newobj){
  	newobj = document.createElement("DIV");
    newobj.id = '_mydatainfo';
    newobj.style.position='absolute';
    newobj.className = "dlg";
    newobj.style.top = posTop;
    newobj.style.left = posLeft;
    document.body.appendChild(newobj);
  }else{
  	newobj.style.display = "block";
  }
  myajax = new DedeAjax(newobj);
  myajax.SendGet("sys_sql_query_lit.php?"+surl);
}
function HideObj(objname){
   var obj = document.getElementById(objname);
   obj.style.display = "none";
}

//获得选中文件的数据表

function getCheckboxItem(){
	 var myform = document.form1;
	 var allSel="";
	 if(myform.tables.value) return myform.tables.value;
	 for(i=0;i<myform.tables.length;i++)
	 {
		 if(myform.tables[i].checked){
			 if(allSel=="")
				 allSel=myform.tables[i].value;
			 else
				 allSel=allSel+","+myform.tables[i].value;
		 }
	 }
	 return allSel;	
}

//反选
function ReSel(){
	var myform = document.form1;
	for(i=0;i<myform.tables.length;i++){
		if(myform.tables[i].checked) myform.tables[i].checked = false;
		else myform.tables[i].checked = true;
	}
}

//全选
function SelAll(){
	var myform = document.form1;
	for(i=0;i<myform.tables.length;i++){
		myform.tables[i].checked = true;
	}
}

//取消
function NoneSel(){
	var myform = document.form1;
	for(i=0;i<myform.tables.length;i++){
		myform.tables[i].checked = false;
	}
}

function checkSubmit()
{
	var myform = document.form1;
	myform.tablearr.value = getCheckboxItem();
	return true;
}

</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="99%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr> 
    <td height="19" colspan="4" background="img/tbg.gif" bgcolor="#E7E7E7">
    	<table width="96%" border="0" cellspacing="1" cellpadding="1">
        <tr> 
          <td width="24%"><strong>数据库管理</strong></td>
          <td width="76%" align="right"> <b><a href="sys_data_revert.php"><u>数据还原</u></a></b> 
            | <b><a href="sys_sql_query.php"><u>SQL命令行工具</u></a></b> </td>
        </tr>
      </table>
    </td>
  </tr>
  <form name="form1" onSubmit="checkSubmit()" action="sys_data_done.php?dopost=bak" method="post" target="stafrm">
  <input type='hidden' name='tablearr' value=''>
  <tr bgcolor="#F7F8ED"> 
    <td height="24" colspan="4" valign="top"><strong>DedeCms默认系统表：</strong></td>
  </tr>
  <tr bgcolor="#F9FEE2"> 
    <td height="24" align="center" valign="top">选择</td>
    <td align="center" valign="top">表名</td>
    <td align="center" valign="top">记录数</td>
    <td align="center" valign="top">操作</td>
  </tr>
  <?  
  foreach($dedeSysTables as $t){ 
  ?>
  <tr align="center"  bgcolor="#FFFFFF"> 
    <td width="9%" height="24"> <input type="checkbox" name="tables" value="<?=$t?>" class="np" checked> 
    </td>
    <td width="41%" > 
      <?=$t?>
    </td>
    <td width="25%"> 
      <?=TjCount($t,$dsql)?>
    </td>
    <td width="25%">
    <a href="#" onClick="LoadUrl('dopost=opimize&tablename=<?=$t?>');">优化</a> |
    <a href="#" onClick="LoadUrl('dopost=repair&tablename=<?=$t?>');">修复</a> |
    <a href="#" onClick="LoadUrl('dopost=viewinfo&tablename=<?=$t?>');">结构</a>
    </td>
  </tr>
  <? } ?>
  <tr bgcolor="#F7F8ED"> 
    <td height="24" colspan="4" valign="top"><strong>其它数据表：</strong></td>
  </tr>
  <tr bgcolor="#F9FEE2" align="center"> 
    <td height="24">选择</td>
    <td bgcolor="#F9FEE2">表名</td>
    <td>记录数</td>
    <td>操作</td>
  </tr>
  <?  
  foreach($otherTables as $t){ 
  ?>
  <tr align="center"  bgcolor="#FFFFFF"> 
    <td width="9%" height="24"> <input type="checkbox" name="tables" value="<?=$t?>" class="np"> 
    </td>
    <td width="41%" > 
      <?=$t?>
    </td>
    <td width="25%"> 
      <?=TjCount($t,$dsql)?>
    </td>
    <td width="25%">
    <a href="#" onClick="LoadUrl('dopost=opimize&tablename=<?=$t?>');">优化</a> |
    <a href="#" onClick="LoadUrl('dopost=repair&tablename=<?=$t?>');">修复</a> |
    <a href="#" onClick="LoadUrl('dopost=viewinfo&tablename=<?=$t?>');">结构</a>
    </td>
  </tr>
  <? } ?>
    <tr bgcolor="#FDFDEA"> 
      <td height="24" colspan="4">&nbsp; 
        <input name="b1" type="button" id="b1" onclick="SelAll()" value="全选">
        &nbsp; 
        <input name="b2" type="button" id="b2" onclick="ReSel()" value="反选">
        &nbsp; 
        <input name="b3" type="button" id="b3" onclick="NoneSel()" value="取消">
      </td>
  </tr>
  <tr bgcolor="#F7F8ED"> 
    <td height="24" colspan="4"><strong>数据备份选项：</strong></td>
  </tr>
  <tr align="center" bgcolor="#FFFFFF"> 
    <td height="50" colspan="4"> <table width="90%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td height="30">当前数据库版本： <?=$mysql_version?></td>
          </tr>
          <tr> 
            <td height="30">
            	指定备份数据格式： 
              <input name="datatype" type="radio" class="np" value="4.0"<?if($mysql_version<4.1) echo " checked";?>>
              MySQL3.x/4.0.x 版本 
              <input type="radio" name="datatype" value="4.1" class="np"<?if($mysql_version>=4.1) echo " checked";?>>
              MySQL4.1.x/5.x 版本
              </td>
          </tr>
          <tr> 
            <td height="30">分卷大小： 
              <input name="fsize" type="text" id="fsize" value="1024" size="6">
              K&nbsp;， 
              <input name="isstruct" type="checkbox" class="np" id="isstruct" value="1" checked>
              备份表结构信息 <input type="submit" name="Submit" value="提交"></td>
          </tr>
        </table></td>
  </tr>
  </form>
  <tr bgcolor="#F7F8ED">
    <td height="24" colspan="4"><strong>进行状态：</strong></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td height="180" colspan="4">
	<iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
	</td>
  </tr>
</table>
<? $dsql->Close(); ?>
</body>
</html>