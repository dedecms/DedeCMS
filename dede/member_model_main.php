<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Type');
require_once(DEDEINC."/datalistcp.class.php");
require_once(DEDEINC."/common.func.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
function GetTotalMember($mtable=''){
	global $dsql;
    if($dsql->IsTable($mtable)){
        $row =$dsql->GetOne("SELECT COUNT(*) AS nums FROM {$mtable}");
        return empty($row['nums'])? "0" : $row['nums'];        
    }else{
        return '0';
    }
}



$sql = "Select `id`,`name`,`table`,`description`,`state`,`issystem` From #@__member_model order by id asc";
$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN."/templets/member_model_main.htm");
$dlist->SetSource($sql);
$dlist->display();
$dlist->Close();

?>