<?php
/**
 * <strong style="color:red;">TypechoCloudflareCache</strong>
 *
 * @package TypechoCloudflareCache
 * @author PP
 * @version 1.0.0
 * @dependence 1.0-*
 * @link https://www.520495.xyz
 *
 */
 
//如果需要显示php错误打开这两行注释，问题修复后必须关闭！
error_reporting(E_ALL);
ini_set("display_errors", 1);

class CacheCf_Plugin implements Typecho_Plugin_Interface{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return string
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Contents_Post_Edit')->finishPublish = array('CacheCf_Plugin', 'cleanCache');
        Typecho_Plugin::factory('Widget_Contents_Page_Edit')->finishPublish = array('CacheCf_Plugin', 'cleanCache');
    }
    public static function deactivate(){}
 
    public static function config(Typecho_Widget_Helper_Form $form) {
        $zoneid = new Typecho_Widget_Helper_Form_Element_Text('zoneid', null, null, _t('ZoneID'), '请登录Cloudflare获取');
        $form->addInput($zoneid);
        $xauthemail = new Typecho_Widget_Helper_Form_Element_Text('xemail', null, null, _t('X-Auth-Email'), '请登录Cloudflare获取');
        $form->addInput($xauthemail);
        $xauthkey = new Typecho_Widget_Helper_Form_Element_Text('xkey', null, null, _t('X-Auth-Key'), '请登录Cloudflare获取');
        $form->addInput($xauthkey);
    }
 
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
 
    public static function render() {}
    public static function cleanCache(){
        $options = Helper::options();
        $zoneid=$options->plugin("zoneid")->zoneid;
        $xemail=$options->plugin("CacheCf")->xemail;
        $xkey=$options->plugin("CacheCf")->xkey;

        $curl=curl_init();
        $cfPureCacheApi='https://api.cloudflare.com/client/v4/zones/{$zoneid}/purge_cache';
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_URL,$cfPureCacheApi);
        $requestHeader = array(
            'X-Auth-Email: '.$xemail.'',
            'X-Auth-Key: '.$xkey.'',
            'Content-Type: application/json'
        );
        $data = json_encode(array("hosts" => array("www.520495.xyz")));
        curl_setopt($ch_query, CURLOPT_HTTPHEADER, $requestHeader);
        curl_setopt($ch_purge, CURLOPT_POST, true);
        curl_setopt($ch_purge, CURLOPT_POSTFIELDS, $data);
        $requestResult=curl_exec($curl);
        curl_close($curl);
        
        
    }
}
