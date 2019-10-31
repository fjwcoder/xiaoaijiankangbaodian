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

namespace app\api\logic;

use app\api\error\Common as CommonError;
use think\Db;
/**
 * 
 */
class Injectqueue extends ApiBase
{

    /**
     * 接种点推送到的排队序列
     * add by fjw in 19.8.14
     */
    public function posInjectQueue($param){

        // file_put_contents('posInjectQueue.txt', var_export($param, true));

        if(!isset($param['uc']) || empty($param['uc'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '机器唯一编码不可为空'];
        
        if(!isset($param['ql']) || empty($param['ql'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '接种点队列信息不可为空'];
        
        // if(!is_json($param['ql'])) return [API_CODE_NAME => 40003, API_MSG_NAME => '队列信息格式错误'];

        $time = date('Y-m-d H:i:s', time());

        
        // 拼凑数据，进行插入操作
        $data = [
            'unique_code'=>$param['uc'],
            'mac_address'=>isset($param['mac_address'])?$param['mac_address']:$param['uc'],
            'queue_list'=>$param['ql'],
            'create_time'=>$time,
            'status'=>1
        ];
// return $data;
        $add = $this->modelInjectQueueList->setInfo($data);

        if($add){

            Db::name('inject_queue_list') -> where('create_time < "'.$time.'" and unique_code = "'.$param['uc'].'"') -> update(['status'=>2]);

        }

        return $add;

    }

    /**
     * 小票二维码长连接生成短链接
     */
    public function urlToShort($param = []){

        if(!isset($param['uc']) || empty($param['uc'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '机器唯一编码不可为空'];
        
        if(!isset($param['no']) || empty($param['no'])) return [API_CODE_NAME => 40001, API_MSG_NAME => '接种点队列号码不可为空'];

        $root = 'http://xiaoai.mamitianshi.com/';

        $url = $root.'wechat/loginPlus?c=index&a=scanInjectQrcode&ts='.strtotime(date('Y-m-d', time())).'&uc='.$param['uc'].'&no='.$param['no'];

        $wx_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3bfada96a932f9e1&redirect_uri='.urlencode($url).'&response_type=code&scope=snsapi_base&state=1#wechat_redirect';

        $response = $this->serviceWechat->driverWxgzh->urlToShort(['url'=>$wx_url]);

        return $response;

    }

    
}
