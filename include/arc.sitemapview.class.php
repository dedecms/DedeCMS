<?php   if(!defined('DEDEINC')) exit("DedeCMS Error: Request Error!");
/**
 * Sitemap视图类
 *
 * @version        $Id: arc.sitemapview.class.php 1 15:51 2010年7月7日 $
 * @package        DedeCMS.Libraries
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */

/**
 * Sitemap视图类
 *
 * @package          SitemapView
 * @subpackage       DedeCMS.Libraries
 * @link             http://www.dedecms.com
 */
class SitemapView
{
    var $dtp;

    /**
     *  php5构造函数
     *
     * @access    public
     * @param     int  $aid  内容ID
     * @return    string
     */
    function __construct($tplfile)
    {
        $this->dtp = new DedeTagParse();
        $this->dtp->refObj = $this;
        $this->dtp->SetNameSpace("dede","{","}");

        $this->dtp->LoadTemplate($tplfile);
        $this->ParseTemplet();
    }

    //php4构造函数
    function SitemapView($tplfile)
    {
        $this->__construct($tplfile);
    }

    /**
     *  显示内容
     *
     * @access    public
     * @return    void
     */
    function Display()
    {
        $this->dtp->Display();
    }

    /**
     *  获取内容
     *
     * @access    public
     * @return    void
     */
    function GetResult()
    {
        return $this->dtp->GetResult();
    }

    /**
     *  保存结果为文件
     *
     * @access    public
     * @return    void
     */
    function SaveToHtml($filename)
    {
        $this->dtp->SaveTo($filename);
    }

    /**
     *  解析模板里的标签
     *
     * @access    public
     * @return    string
     */
    function ParseTemplet()
    {
        $GLOBALS['envs']['likeid'] = "default";
        MakeOneTag($this->dtp,$this);
    }

    //关闭所占用的资源
    function Close()
    {
    }
}//End Class