<?php 
require_once(dirname(__FILE__)."/config_space.php");
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
$myurl = $cfg_basehost.$cfg_member_dir."/?".$cfg_ml->M_LoginID.'/';
$myurl = "<a href='$myurl'><u>$myurl</u></a>";
$dsql = new DedeSql(false);
$minfos = $dsql->GetOne("Select c1,c2,c3,guestbook,spaceshow,pageshow From #@__member where ID='".$cfg_ml->M_ID."'; ");
if($cfg_ml->M_utype == 0)
{
		$minfos['totaluse'] = GetUserSpace($cfg_ml->M_ID,$dsql);
		$minfos['totaluse'] = number_format($minfos['totaluse']/1024/1024,2);
		if($cfg_mb_max>0) $ddsize = ceil( ($minfos['totaluse']/$cfg_mb_max) * 100 );
		else $ddsize = 0;
		require_once(dirname(__FILE__)."/templets/control.htm");
}else{
		$minfos['totaluse'] = GetUserSpace($cfg_ml->M_ID,$dsql);
		$minfos['totaluse'] = number_format($minfos['totaluse']/1024/1024,2);
		if($cfg_mb_max>0) $ddsize = ceil( ($minfos['totaluse']/$cfg_mb_max) * 100 );
		else $ddsize = 0;
		require_once(dirname(__FILE__)."/templets/comuser.htm");
}
exit();
?>