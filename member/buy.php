<?php 
require_once(dirname(__FILE__).'/config_space.php');
require_once(dirname(__FILE__).'/config.php');
CheckRank(0,0);
$myurl = $cfg_basehost.$cfg_member_dir.'/index.php?uid='.$cfg_ml->M_LoginID;
$myurl = "<a href='$myurl'><u>$myurl</u></a>";
$dsql = new DedeSql(false);
$moneycards = '';
$membertypes = '';
$dsql->SetQuery("Select * From #@__moneycard_type ");
$dsql->Execute();
while($row = $dsql->GetObject()){
	$moneycards .= "<tr align='center' bgcolor='#FFFFFF'> 
  <td><input type='radio' name='pid' value='{$row->tid}'></td>
  <td>{$row->pname}</td>
  <td>{$row->num}</td>
  <td>{$row->money}</td>
</tr>\r\n";
}
$dsql->SetQuery("Select #@__member_type.*,#@__arcrank.membername,#@__arcrank.money as cm From #@__member_type left join #@__arcrank on #@__arcrank.rank = #@__member_type.rank ");
$dsql->Execute();
while($row = $dsql->GetObject()){
	$membertypes .= "<tr align='center' bgcolor='#FFFFFF'> 
   <td><input type='radio' name='pid' value='{$row->aid}'></td>
   <td>{$row->pname}</td>
   <td>{$row->membername}</td>
   <td>{$row->exptime}</td>
   <td>{$row->cm}</td>
   <td>{$row->money}</td>
</tr>\r\n";
}
require_once(dirname(__FILE__).'/templets/buy.htm');
?>