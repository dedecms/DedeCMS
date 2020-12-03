<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select rank From #@__member_arctype where memberid='".$cfg_ml->M_ID."' order by rank desc");
if(!is_array($row)) $nrank = 1;
else $nrank = $row[0]+1;
require_once(dirname(__FILE__)."/templets/archives_type.htm");
$dsql->Close();
?>