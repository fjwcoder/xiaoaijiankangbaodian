<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | fjwcoder<fjwcoder@gmail.com>                           |
// +---------------------------------------------------------------------+
// | Repository |   |
// +---------------------------------------------------------------------+

namespace app\index\logic;


/**
 * Index基础逻辑
 */
class Wechat extends IndexBase
{
    public function index($param = []){
        // dump($param); die;
        return $this->serviceWechat->driverWxgzh->wechat($param);
    }

    /**
     * 微信网页授权登录
     */
    public function login($param = []){
        $user_id = user_is_login();
        // $redirect = ['controller'=>$param['c'], 'action'=>$param['a']];
        if($user_id < 0){
            $user = $this->modelWxUser->getInfo(['user_id'=>$user_id]);

        }else{
            $wxcode = input('get.code', '', 'htmlspecialchars,trim');
            if(empty($wxcode)){
                return ['url'=>'index/errorPage', 'param'=>['content'=>'access error']]; 
            }
            $param['wxid'] = 5;
            $response = $this->serviceWechat->driverWxgzh->webAuth(['wxid'=>$param['wxid'], 'code'=>$wxcode]);
            /**
             * 
             */
            if($response['status']){
                $openid = $response['data']['wx_openid'];
                // $openid = $data['wx_openid'];
            }else{
                return ['url'=>'index/errorPage', 'param'=>['content'=>'wechat access error']]; 
            }
    
            $user = $this->modelWxUser->getInfo(['wx_openid'=>$openid]);

            $auth = ['user_id' => $user['user_id'], TIME_UT_NAME => TIME_NOW];

            session('user_info', $user);
            session('user_auth', $auth);
            session('user_auth_sign', data_auth_sign($auth));
        }
        
        
        return ['url'=> $param['c'].'/'.$param['a'], 'param'=>['openid'=>$user['wx_openid']]]; 
        // return $this->redirect('/pregnant/check', ['openid'=>$user['wx_openid']]);

    }

    public function menu($param = []){
        // dump($param); die;
        return $this->serviceWechat->driverWxgzh->menu($param);
    }
}
