<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: story.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:24 $
 */

require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(dirname(__FILE__).'./include/story.view.class.php');
$id = intval($id);
if(empty($id))
{
	ParamError();
}
$bv = new BookView($id,'content');

//检测是否收费图书
$freenum = $bv->Fields['freenum'];
if($freenum > -1)
{
	require_once(DEDEINC."/memberlogin.class.php");
	$ml = new MemberLogin();
	if($ml->M_MbType < $cfg_book_freerank)
	{
		$row = $bv->dsql->GetOne("Select chapnum From #@__story_chapter where id='{$bv->Fields['chapterid']}' ");
		$chapnum = $row['chapnum'];
		$member_err = '';

		//确定当前内容属于收费章节
		if($chapnum > $freenum)
		{
			if( empty($ml->M_ID) )
			{
				$member_err = "NoLogin";
			}
			else
			{
				$row = $bv->dsql->GetOne("Select * From #@__story_viphistory where mid='{$ml->M_ID}' ");
				if(!is_array($row) && $ml->M_Money < $cfg_book_money)
				{
					$member_err = "NoEnoughMoney";
				}
			}

			//权限错误
			if($member_err!='')
			{
				$row = $bv->dsql->GetOne("Select membername From #@__arcrank where rank = '$cfg_book_freerank' ");
				if(!is_array($row))
				{
					$membername = '';
				}
				else
				{
					$membername = $row['membername'];
				}
				require_once(DEDEROOT.'/book/templets/'.$cfg_df_style.'/book_member_err.htm');
				$bv->Close();
				exit();
			}

			//扣点
			else
			{
				$rs = $bv->dsql->ExecuteNoneQuery("Insert Into #@__story_viphistory(cid,uid) Values('{$id}','{$ml->M_ID}') ");
				if($rs)
				{
					$bv->dsql->ExecuteNoneQuery("Update #@__member Set money=money-{$cfg_book_money} where id='{$ml->M_ID}' ");
				}
			}
		}
	}
}
$bv->Display();
$bv->Close();
?>