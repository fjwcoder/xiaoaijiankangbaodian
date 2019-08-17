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
     * 微信网页授权登录增强版
     * add by fjw in 19.8.16
     * 区别是跳转参数进行了数组合并
     */
    public function loginPlus($param = []){
        // return ['url'=>'index/subscribePlease', 'param'=>['content'=>'请先关注公众号，小爱健康宝典']]; 
        $user_id = user_is_login();
        if($user_id > 0){
            $user = $this->modelWxUser->getInfo(['user_id'=>$user_id]);

        }else{
            $wxcode = input('get.code', '', 'htmlspecialchars,trim');
            if(empty($wxcode)){
                return ['url'=>'index/errorPage', 'param'=>['content'=>'未获取到微信登录秘钥']]; 
            }
            $param['wxid'] = 5;
            $response = $this->serviceWechat->driverWxgzh->webAuth(['wxid'=>$param['wxid'], 'code'=>$wxcode]);

            if($response['status']){
                $openid = $response['data']['wx_openid'];
            }else{
                return ['url'=>'index/subscribePlease', 'param'=>['content'=>'请先关注公众号，小爱健康宝典']]; 
            }

            // $openid = 'o20RC1RcDMBYPdwPkfP9dCXkJz0g';
    
            $user = $this->modelWxUser->getInfo(['wx_openid'=>$openid]);

            $auth = ['user_id' => $user['user_id'], TIME_UT_NAME => TIME_NOW];

            session('user_info', $user);
            session('user_auth', $auth);
            session('user_auth_sign', data_auth_sign($auth));
        }
        
        // dump(['url'=> $param['c'].'/'.$param['a'], 'param'=>array_merge($param, ['openid'=>$user['wx_openid']])]); die;
        return ['url'=> $param['c'].'/'.$param['a'], 'param'=>array_merge($param, ['openid'=>$user['wx_openid']])]; 
    }

    /**
     * 微信网页授权登录
     */
    public function login($param = []){
        $user_id = user_is_login();
        if($user_id > 0){
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

    }

    public function menu($param = []){
        // dump($param); die;
        return $this->serviceWechat->driverWxgzh->menu($param);
    }



    /**
     * 妈咪天使微信网页授权登录
     */
    public function mamiLogin($param = []){
        // 1. 获取code
        $wxcode = input('get.code', '', 'htmlspecialchars,trim');
        if(empty($wxcode)){
            return ['url'=>'index/errorPage', 'param'=>['content'=>'access error']]; 
        }

        // 2. 获取用户openid 和 access_token
        $web_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxfcb19a60a1a9523f&secret=b7774caf8dc09fd2df721a2286f41bdc&code='.$wxcode.'&grant_type=authorization_code';
        $open_res = httpsGet($web_url); //则本步骤中获取到网页授权access_token的同时，也获取到了openid，snsapi_base式的网页授权流程即到此为止。
        $open_arr = json_decode($open_res, true);
        if(!isset($open_arr['access_token']) || !isset($open_arr['openid'])){
            return ['url'=>'index/errorPage', 'param'=>['content'=>'openid error']]; 
            // return ['status'=>false, 'msg'=>'openid 获取失败'];
        }
        // 3. 获取用户的详细信息
        $info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$open_arr['access_token'].'&openid='.$open_arr['openid'].'&lang=zh_CN';
        $info_res = httpsGet($info_url);
        $info_arr = json_decode($info_res, true);
        // dump($info_arr); die;
        if(isset($info_arr['openid']) && isset($info_arr['unionid']) && $info_arr['unionid'] != ''){
            return ['url'=>'activity/mamiSubscribeFeverActivity', 'param'=>['unionid'=>$info_arr['unionid']]];
        }else{
            return ['url'=>'index/errorPage', 'param'=>['content'=>'userinfo error']]; 
        }



    }



}
