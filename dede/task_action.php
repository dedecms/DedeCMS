<?php
//------------------------------------
//计划任务操作部份
//返回值标准
// 正常操作：返回下一个网址
// 执行某操作结束：返回 end
// 页面数据库类错误： 返回 error
// 用户验证不通过： 返回 task_config.php 里的相关值
// 其它错误：如服务器停止错误导致404或程序出错导致500错误等客户端会自动识别，并隔一段时间后重新尝试。
//------------------------------------
require_once(dirname(__FILE__)."/task_config.php");
if(empty($action)) $action = '';
$dsql = new DedeSql(false);
/*---------------
优化数据库
function _opdb(){  }
-----------------*/
if($action=='opdb')
{
	$dsql->Close();
	exit();
}
/*---------------
获取关键字
function _getkw(){  }
-----------------*/
else if($action == 'getkw')
{
	$dsql->Close();
	exit();
}
/*---------------
更新当天文档
function _mkday(){  }
-----------------*/
else if($action=='')
{
	$dsql->Close();
	exit();
}
/*---------------
更新所有HTML
function _mkall(){  }
-----------------*/
else if($action=='')
{
	$dsql->Close();
	exit();
}
/*---------------
更新相关ID
function _uplikeid(){  }
-----------------*/
else if($action=='uplikeid')
{
	$dsql->Close();
	exit();
}
/*---------------
自动采集
function _autoCt(){  }
-----------------*/
else if($action=='autoct')
{
	$dsql->Close();
	exit();
}
?>