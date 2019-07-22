<?php
// +---------------------------------------------------------------------+
// | FJWCODER   | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | fjwcoder <fjwcoder@gmail.com>                          |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\common\service\wechat\driver;

use app\common\service\wechat\Driver;
use app\common\service\Wechat;

/**
 * 微信公众号服务驱动
 */
class Wxgzh extends Wechat implements Driver
{
    
    /**
     * 驱动基本信息
     */
    public function driverInfo()
    {
        
        return ['driver_name' => '微信公众号驱动', 'driver_class' => 'Wxgzh', 'driver_describe' => '微信公众号驱动', 'author' => 'fjwcoder', 'version' => '1.0'];
    }
    
    /**
     * 获取驱动参数
     */
    public function getDriverParam()
    {
        
        return ['wxid'=>'验证ID', 'appid' => '公众号APPID', 'appsecret' => '公众号APPSECRET', 
            'original_id' =>'公众号原始ID', 'token' => '验证token'];
    }

    /**
     * 获取配置信息
     */
    public function config()
    {
        
        return $this->driverConfig('Wxgzh');

    }
    
    /**
     * 微信公众号
     */
    public function wechat($param){
        $wechat_config = $this->config();
        
        if(!isset($param['wxid']) || $param['wxid'] !== $wechat_config['wxid'] ){
            return '验证错误';
        }
        // dump($wechat_config); die;
        $wechatObj = new wxgzh\Wechat($wechat_config);

        return $wechatObj->index();
        
        
    }

    /**
     * 自定义菜单
     */
    public function menu($param){

        $wechat_config = $this->config();
        // dump($wechat_config); die;
        if(!isset($param['wxid']) || $param['wxid'] != $wechat_config['wxid'] ){
            dump('验证错误'); die;
            // return '验证错误';
        }
        // dump($wechat_config); die;
        $wechatObj = new wxgzh\Wechat($wechat_config);

        return $wechatObj->menuDIY();
        
        
    }
    
    /**
     * 网页授权
     */
    public function webAuth($param){

        $wechat_config = $this->config();
        // dump($wechat_config); die;
        if(!isset($param['wxid']) || $param['wxid'] != $wechat_config['wxid'] ){
            dump('验证错误'); die;
            // return '验证错误';
        }
        // dump($wechat_config); die;
        $wechatObj = new wxgzh\Wechat($wechat_config);

        return $wechatObj->webAuth($param['code']);
        
        
    }

    /**
     * 获取微信access_token
     */
    public function access_token($param){
        $wechat_config = $this->config();
        // dump($wechat_config); die;
        if(!isset($param['wxid']) || $param['wxid'] != $wechat_config['wxid'] ){
            dump('验证错误'); die;
            // return '验证错误';
        }
        // dump($wechat_config); die;
        $wechatObj = new wxgzh\Wechat($wechat_config);

        return $wechatObj->access_token();
    }

    /**
     * 模板消息跳转
     */
    public function templateRedirectUrl($param){
        $wechat_config = $this->config();
        // dump($wechat_config); die;
        if(!isset($param['wxid']) || $param['wxid'] != $wechat_config['wxid'] ){
            dump('验证错误'); die;
            // return '验证错误';
        }
        // dump($wechat_config); die;
        $wechatObj = new wxgzh\Wechat($wechat_config);

        return $wechatObj->templateRedirectUrl($param['remind']);
    }

    /**
     * 发送模板消息
     */
    public function sendTemplateMsg(){
        $wechat_config = $this->config();
        // dump($wechat_config); die;
        if(!isset($param['wxid']) || $param['wxid'] != $wechat_config['wxid'] ){
            dump('验证错误'); die;
            // return '验证错误';
        }
        // dump($wechat_config); die;
        $wechatObj = new wxgzh\Wechat($wechat_config);

        return $wechatObj->sendTemplateMsg();
    }
    

   
}
