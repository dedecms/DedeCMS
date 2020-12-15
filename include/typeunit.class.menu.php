<?php if (!defined('DEDEINC')) {
    exit("Request Error!");
}

/**
 * 栏目单元,主要用户管理后台管理菜单处
 *
 * @version        $Id: typeunit.class.menu.php 1 15:21 2010年7月5日 $
 * @package        DedeCMS.Libraries
 * @founder        IT柏拉图, https: //weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once DEDEDATA . "/cache/inc_catalog_base.inc";

/**
 * 栏目单元,主要用户管理后台管理菜单处
 *
 * @package          TypeUnit
 * @subpackage       DedeCMS.Libraries
 * @link             http://www.dedecms.com
 */
class TypeUnit
{
    public $dsql;
    public $aChannels;
    public $isAdminAll;

    //php5构造函数
    public function __construct($catlogs = '')
    {
        global $cfg_Cs;
        $this->dsql = $GLOBALS['dsql'];
        $this->aChannels = array();
        $this->isAdminAll = false;
        if (!empty($catlogs) && $catlogs != '-1') {
            $this->aChannels = explode(',', $catlogs);
            foreach ($this->aChannels as $cid) {
                if ($cfg_Cs[$cid][0] == 0) {
                    $this->dsql->SetQuery("Select id,ispart From `#@__arctype` where reid=$cid");
                    $this->dsql->Execute();
                    while ($row = $this->dsql->GetObject()) {
                        //if($row->ispart==1)
                        $this->aChannels[] = $row->id;
                    }
                }
            }
        } else {
            $this->isAdminAll = true;
        }
    }

    public function TypeUnit($catlogs = '')
    {
        $this->__construct($catlogs);
    }

    //清理类
    public function Close()
    {
    }

   
} //End Class
