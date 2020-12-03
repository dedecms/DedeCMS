<?php
if(!defined('DEDEINC'))
{
	exit('forbidden');
}
require_once DEDEINC.'/dedetag.class.php';
require_once DEDEINC.'/customfields.func.php';

class diyform
{
	var $diyid;
	var $db;
	var $info;
	var $name;
	var $table;
	var $public;
	var $listTemplate;
	var $viewTemplate;
	var $postTemplate;

	function diyform($diyid){
		$this->__construct($diyid);
	}
	function __construct($diyid){
		$this->diyid = $diyid;
		$this->db = $GLOBALS['dsql'];
		$query = "select * from #@__diyforms where diyid='{$diyid}'";
		$diyinfo = $this->db->getone($query);
		if(!is_array($diyinfo))
		{
			showMsg('参数不正确，该自定义表单不存在','javascript:;');
			exit();
		}
		$this->info = $diyinfo['info'];
		$this->name = $diyinfo['name'];
		$this->table = $diyinfo['table'];
		$this->public = $diyinfo['public'];
		$this->listTemplate = $diyinfo['listtemplate'] != '' && file_exists(DEDEINC.'/../templets/plus/'.$diyinfo['listtemplate']) ? $diyinfo['listtemplate'] : 'list_diyform.htm';
		$this->viewTemplate = $diyinfo['viewtemplate'] != '' && file_exists(DEDEINC.'/../templets/plus/'.$diyinfo['viewtemplate']) ? $diyinfo['viewtemplate'] : 'view_diyform.htm';;
		$this->postTemplate = $diyinfo['posttemplate'] != '' && file_exists(DEDEINC.'/../templets/plus/'.$diyinfo['posttemplate']) ? $diyinfo['posttemplate'] : 'post_diyform.htm';;
	}//end func __construct()

	function getForm($type = 'post', $value = '', $admintype='member')
	{
		global $cfg_cookie_encode;
		$dtp = new DedeTagParse();
		$dtp->SetNameSpace("field","<",">");
		$dtp->LoadSource($this->info);
		$formstring = '';
		$formfields = '';
		$func = $type == 'post' ? 'GetFormItem' : 'GetFormItemValue';
		if(is_array($dtp->CTags))
		{
			foreach($dtp->CTags as $tagid=>$tag)
			{
				if($tag->GetAtt('autofield'))
				{
					if($type == 'post')
					{
						$formstring .= $func($tag,$admintype);
					}
					else
					{
						$formstring .= $func($tag,htmlspecialchars($value[$tag->GetName()],ENT_QUOTES),$admintype);
					}
					$formfields .= $formfields == '' ? $tag->GetName().','.$tag->GetAtt('type') : ';'.$tag->GetName().','.$tag->GetAtt('type');
				}
			}
		}

		$formstring .= "<input type=\"hidden\" name=\"dede_fields\" value=\"".$formfields."\" />\n";
		$formstring .= "<input type=\"hidden\" name=\"dede_fieldshash\" value=\"".md5($formfields.$cfg_cookie_encode)."\" />";
		return $formstring;
	}//end func getForm

	function getFieldList()
	{
		$dtp = new DedeTagParse();
		$dtp->SetNameSpace("field","<",">");
		$dtp->LoadSource($this->info);
		$fields = array();
		if(is_array($dtp->CTags))
		{
			foreach($dtp->CTags as $tagid=>$tag)
			{
				$fields[$tag->GetName()] = array($tag->GetAtt('itemname'), $tag->GetAtt('type'));
			}
		}
		return $fields;
	}//end func getFieldList()

}//end class diyform()

?>