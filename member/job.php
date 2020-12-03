<?php

$jobid = intval($jobid);

if($jobid < 1){
		ShowMsg("id错误！","-1");
		exit();
}

$jobinfo = $dsql->getone("select * from #@__jobs where id=$jobid");
//print_r($jobinfo);

$jobinfo['pubdate'] = GetDateMk($jobinfo['pubdate']);
$jobinfo['endtime'] = GetDateMk($jobinfo['endtime']);
if($jobinfo['salaries'] == 0){
	$jobinfo['salaries'] = '面议';
}

include(dirname(__FILE__)."/templets/company/company_job_view.htm");
?>