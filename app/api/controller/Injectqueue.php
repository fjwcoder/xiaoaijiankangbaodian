<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | fjwcoder<fjwcoder@gmail.com>                           |
// +---------------------------------------------------------------------+
// | Repository |                   |
// +---------------------------------------------------------------------+

namespace app\api\controller;

use app\common\controller\ControllerBase;

/**
 * 接种点叫号排队序列
 * 
 * 
 * 
 */
class Injectqueue extends ApiBase
{
    
    /**
     * 接种点推送到的排队序列
     * add by fjw in 19.8.14
     */
    public function posInjectQueue(){

        return $this->apiReturn($this->logicInjectqueue->posInjectQueue($this->param)); 

    }


    /**
     * 小票二维码长连接生成短链接
     */
    public function urlToShort(){

        return $this->apiReturn($this->logicInjectqueue->urlToShort($this->param)); 
        // if(!isset($param['uc']) || empty($param['uc'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '唯一编码不可为空'];
        
        // if(!isset($param['no']) || empty($param['no'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '队列号码不可为空'];

        // $root = 'http://xiaoai.mamitianshi.com/';

        // $url = $root.'wechat/loginPlus?c=index&a=scanInjectQrcode&ts='.strtotime(date('Y-m-d', time())).'&uc='.$param['uc'].'&no='.$param['no'];
        // dump($url); die;
        // $wx_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3bfada96a932f9e1&redirect_uri='.urlencode($url).'&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
    }


}
