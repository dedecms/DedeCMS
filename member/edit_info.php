<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
$dsql=new DedeSql();
$row=$dsql->GetOne("select  * from #@__member where ID='{$cfg_ml->M_ID}'");
$rowper=$dsql->GetOne("select  * from #@__member_perinfo where id='{$cfg_ml->M_ID}'");
if(!is_array($rowper)){
	$inquery = "
	  INSERT INTO `#@__member_perinfo` (`id`, `uname` , `sex` , `birthday` , `weight` ,
	 `height` , `job` , `province` , `city` , `myinfo` , 
	 `mybb` , `tel` , `oicq` , `homepage` , `showaddr` ,
	  `address` , `spacestyle` , `listnum` , `fullinfo` , `scores` ) 
    VALUES ('{$cfg_ml->M_ID}','".addslashes($row['uname'])."', '1', '0000-00-00', '',
    '', '', '1', '0', '' ,
    '', '' , '' , '' ,'0',
     '', '', '20', '' , '0');
	";
	$rs = $dsql->ExecuteNoneQuery($inquery);
	if(!$rs){
		$dsql->Close();
		ShowMsg("系统错误，你无法更改个人资料！","javascript:;");
		exit();
	}
}
$rowper=$dsql->GetOne("select * from #@__member_perinfo where id='{$cfg_ml->M_ID}'");
require_once(dirname(__FILE__)."/templets/edit_info.htm");
?>