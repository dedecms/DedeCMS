<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('co_AddNote');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if(empty($action)) $action = "";
if(empty($exrule)) $exrule = "";

if($action=="select"){
	require_once(dirname(__FILE__)."/templets/co_sel_exrule.htm");
	ClearAllLink();
	exit();
}

if($exrule==""){
	ShowMsg("请先选择一个导入规则！","co_sel_exrule.php");
	exit();
}

require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
$dsql = new DedeSql(false);
if(empty($extype))
{
  $row = $dsql->GetOne("Select * From #@__co_exrule where aid='$exrule'");
}else
{
	$row = $dsql->GetOne("Select * From #@__co_exrule where channelid='$channelid'");
	//如果不存在某频道的规则，系统自动生成一个规则
	if(!is_array($row))
	{
		$cinfos = $dsql->GetOne("Select * From #@__channeltype where ID='$channelid'",MYSQL_ASSOC);
		$maintable = ($cinfos['maintable']=='' ? '#@__archives' : $cinfos['maintable'] );
		$addtable = $cinfos['addtable'];
		$tablesinfo = ($addtable=='' ? $maintable : $maintable.','.$addtable);
    $dtp = new DedeTagParse();
    $dtp->SetNameSpace("field","<",">");
    $dtp->LoadString($cinfos['fieldset']);
    $exRule = "
{dede:note 
  rulename='{$cinfos['typename']}模型'
  etype='当前系统'
  tablename='{$tablesinfo}'
  autofield='ID'
  synfield='aid'
  channelid='{$cinfos['ID']}'
/}
{dede:field name='typeid' comment='栏目ID' intable='{$maintable}' source='value'}{tid}{/dede:field}
{dede:field name='arcrank' comment='文档权限' intable='{$maintable}' source='value'}{rank}{/dede:field}
{dede:field name='channel' comment='频道类型' intable='{$maintable}' source='value'}{cid}{/dede:field}
{dede:field name='typeid' comment='栏目ID' intable='{$addtable}' source='value'}{tid}{/dede:field}
{dede:field name='adminID' comment='管理员ID' intable='{$maintable}' source='value'}{admin}{/dede:field}
{dede:field name='sortrank' comment='排序级别' intable='{$maintable}' source='value'}{senddate}{/dede:field}
{dede:field name='senddate' comment='录入时间' intable='{$maintable}' source='value'}{senddate}{/dede:field}
{dede:field name='source' comment='来源' intable='{$maintable}' source='value'}{source}{/dede:field}
{dede:field name='pubdate' comment='发布时间' intable='{$maintable}' source='function'} @me = (@me=='' ? time() : GetMkTime(@me));{/dede:field}
{dede:field name='litpic' comment='缩略图' intable='{$maintable}' source='function'}@me = @litpic;{/dede:field}
{dede:field name='title' comment='标题' intable='{$maintable}' source='export'}{/dede:field}
{dede:field name='writer' comment='作者' intable='{$maintable}' source='export'}{/dede:field}
";

    if(is_array($dtp->CTags))
    {
    	foreach($dtp->CTags as $tagid=>$ctag)
    	{
    		 $action = '';
    		 if($ctag->GetAtt('notsend')==1) continue;
    		 $ctype = $ctag->GetAtt('type');
    		 if($ctype=='int'||$ctype=='float'){
    		 	 $action = "@me = ((\$str = preg_replace(\"/[^0-9\.\-]/is\",\"\",@me))=='' ? '0' : \$str);";
    		 }else if($ctype=='softlinks'){
    		 	 $action = "@me = TurnLinkTag(@me);";
    		 }else if($ctype=='img'){
    		 	 $action = "@me = TurnImageTag(@me);";
    		 }
    		 $exRule .= "{dede:field name='".$ctag->GetName()."' comment='".$ctag->GetAtt('itemname')."' intable='".$addtable."' source='export'}{$action}{/dede:field}\r\n";
    	}
    }
    $row['ruleset'] = $exRule;
	 $exRule = addslashes($exRule);
	 $ntime = time();
	 $query = "
	Insert Into `#@__co_exrule`(channelid,rulename,etype,dtime,ruleset)
	Values('$channelid','{$cinfos['typename']}模型','当前系统','".time()."','$exRule')
	";
	 $dsql->ExecuteNoneQuery($query);
	 $gerr = $dsql->GetError();
	 $row['aid'] = $exrule = $dsql->GetLastID();
	 if($row['aid']<1){
		 ClearAllLink();
		 ShowMsg("生成规则错误，无法进行操作！".$gerr,"javascript:;");
		 exit();
	 }
	 $row['channelid'] = $channelid;
	 $row['rulename'] = "{$cinfos['typename']}模型";
	 $row['etype'] = "当前系统";
	 $row['dtime'] = $ntime;
	}
}
if(empty($exrule)) $exrule = $row['aid'];
if(empty($exrule)){
	ClearAllLink();
  ShowMsg("读取规则错误，无法继续操作！","javascript:;");
	exit();
}
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
require_once(dirname(__FILE__)."/templets/co_add.htm");
ClearAllLink();
?>