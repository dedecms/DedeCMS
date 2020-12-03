<?php
require_once(dirname(__FILE__).'/config.php');
CheckRank(0,0);
$myurl = $cfg_basehost.$cfg_member_dir.'/index.php?uid='.$cfg_ml->M_LoginID;
$moneycards = '';
$membertypes = '';
$dsql->SetQuery("Select * From #@__moneycard_type ");
$dsql->Execute();
while($row = $dsql->GetObject())
{
	$row->money = sprintf("%01.2f", $row->money);
	$moneycards .= "<tr>
	<td><input type='radio' name='pid' value='{$row->tid}'></td>
	<td><strong>{$row->pname}</strong></td>
	<td>{$row->num}</td>
	<td>{$row->money}</td>
	</tr>
	";
}
$dsql->SetQuery("Select #@__member_type.*,#@__arcrank.membername,#@__arcrank.money as cm From #@__member_type left join #@__arcrank on #@__arcrank.rank = #@__member_type.rank ");
$dsql->Execute();
while($row = $dsql->GetObject())
{
	$row->money = sprintf("%01.2f", $row->money); 
	$membertypes .= "<tr>
	<td><input type='radio' name='pid' value='{$row->aid}'></td>
	<td><strong>{$row->pname}</strong></td>
	<td>{$row->membername}</td>
	<td>{$row->exptime}</td>
	<td>{$row->money}</td>
	</tr>
	";
}
$tpl = new DedeTemplate();
$tpl->LoadTemplate(DEDEMEMBER.'/templets/buy.htm');
$tpl->Display();
?>