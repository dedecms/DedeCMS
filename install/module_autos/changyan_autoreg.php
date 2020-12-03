<?php
class Changyan_autoreg
{
    var $errmsg='';
    function run($step=0)
    {
        global $dsql,$update_sqls,$cfg_db_language,$cfg_webname;
        if(!file_exists(DEDEINC.'/helpers/changyan.helper.php'))
        {
            $this->errmsg = '未成功安装畅言模块文件';
            return FALSE;
        }
        helper('changyan');
        if( !$dsql->IsTable("#@__plus_changyan_setting") )
        {
            $this->errmsg = '未成功初始化畅言模块所需数据库';
            return FALSE;
        }
        
        if (empty($version)) $version = '0.0.1';
        if (version_compare($version, CHANGYAN_VER, '<')) {
            $mysql_version = $dsql->GetVersion(TRUE);
            
            foreach ($update_sqls as $ver => $sqls) {
                if (version_compare($ver, $version,'<')) {
                    continue;
                }
                foreach ($sqls as $sql) {
                    $sql = preg_replace("#ENGINE=MyISAM#i", 'TYPE=MyISAM', $sql);
                    $sql41tmp = 'ENGINE=MyISAM DEFAULT CHARSET='.$cfg_db_language;
                    
                    if($mysql_version >= 4.1)
                    {
                        $sql = preg_replace("#TYPE=MyISAM#i", $sql41tmp, $sql);
                    }
                    $dsql->ExecuteNoneQuery($sql);
                }
                changyan_set_setting('version', $ver);
                $version=changyan_get_setting('version');
            }
            $isv_app_key = changyan_get_isv_app_key();
        }
        
        $db_user = changyan_get_setting('user');
        if(!empty($db_user))
        {
            $this->errmsg = '已经初始化畅言账号，无需再进行初始化';
            return FALSE;
        }

        $sign=changyan_gen_sign(CHANGYAN_CLIENT_ID);
        $url = $_SERVER['SERVER_NAME'];
        $isv_name = cn_substr($cfg_webname,20);
        $paramsArr=array(
            'client_id'=>CHANGYAN_CLIENT_ID, 
            'isv_name'=>changyan_autoCharset($isv_name), 
            'url'=>'http://'.$url, 
            'sign'=>$sign);

        $rs=changyan_http_send(CHANGYAN_API_AUTOREG,0,$paramsArr);
        $result=json_decode($rs,TRUE);
        
        if($result['status']==0)
        {
            // 保存appid,id信息
            changyan_set_setting('user', $result['user']);
            changyan_set_setting('appid', $result['appid']);
            changyan_set_setting('id', $result['id']);
            changyan_set_setting('isv_app_key', $result['isv_app_key']);
            changyan_set_setting('isv_id', $result['isv_id']);
            changyan_clearcache();
            $passwd = changyan_mchStrCode($result['passwd'], 'ENCODE');
            changyan_set_setting('pwd', $passwd);
            return TRUE;
        } else {
            if($step > 3)
            {
                $this->errmsg = '无法成功初始化畅言模块';
                return FALSE;
            }
            $step++;
            return $this->run($step);
        }
    }
}
?>