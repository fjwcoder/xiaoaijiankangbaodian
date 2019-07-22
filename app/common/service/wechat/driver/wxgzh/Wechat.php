<?php

namespace app\common\service\wechat\driver\wxgzh;
use think\Db;
class Wechat{

    public $wechat_config = array(
        // appid
        'appid' =>'',
        'appsecret' =>'',
        'original_id'=>'', // 
        'token'=>''
    );
    
    private $siteroot = 'http://xiaoai.fjwcoder.com/index.php/';

    
    

    public function __construct($param = []){
        $this->wechat_config['appid'] = $param['appid'];
        $this->wechat_config['appsecret'] = $param['appsecret'];
        $this->wechat_config['original_id'] = $param['original_id'];
        $this->wechat_config['token'] = $param['token'];

    }

    public function index(){
        if(!isset($_GET['echostr'])){
			$this -> responseMsg();
		}else{
			$this -> valid();//验证key
		}
    }
    /**
     * ########################################################################
     *  信息验证模块 create by fjw in 18.5.30
     * ########################################################################
     */
    public function valid()
    {
        $echoStr = $_GET['echostr'];
        if($this->checkSignature()){//调用验证签名checkSignature函数
        	echo $echoStr;
        	exit;
        }
    }

    private function checkSignature()
	{
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
		$tmpArr = array($this->wechat_config['token'], $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
    }


    /**
     * ########################################################################
     *  响应公众号事件/信息 create by fjw in 18.5.30
     * ########################################################################
     */
    public function responseMsg()
	{
        $postStr = file_get_contents('php://input');
        // file_put_contents('responsemsg.txt', $postStr);
        // $postStr = '<xml><ToUserName><![CDATA[gh_93c427bbf52e]]></ToUserName>
        //     <FromUserName><![CDATA[o20RC1RcDMBYPdwPkfP9dCXkJz0g]]></FromUserName>
        //     <CreateTime>1557732229</CreateTime>
        //     <MsgType><![CDATA[event]]></MsgType>
        //     <Event><![CDATA[subscribe]]></Event>
        //     <EventKey><![CDATA[]]></EventKey>
        //     </xml>';
		if (!empty($postStr))
		{
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$RX_TYPE = trim($postObj -> MsgType);
			switch($RX_TYPE)
			{
                case 'event':
                    $resultStr = $this -> handleEvent($postObj);
				break;
				case 'text':
					$resultStr = $this -> handleText($postObj);
				break;
				default:
					$resultStr = 'Unknow msg type: '.$RX_TYPE;
				break;
			}
			echo $resultStr;
		}else{
			echo "no user's post data";
		}
    }
    

/**
     * ########################################################################
     *  响应公众号 “事件消息” 方法 create by fjw in 18.5.30
     * ########################################################################
     */
    public function handleEvent($object){

        $openid = strval($object->FromUserName);
        // $registerObj = new Regist();
        $access_token = $this->access_token();
        $content = "";
        switch ($object->Event){
            case "subscribe": // ok by fjw in 18.6.2
                $wx_user_info_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
                $response = httpsGet($wx_user_info_url); 

                $user_info = json_decode($response, true);
                    // dump($user_info); die;
                if($user_info){
                    $subscribe = $this->wxSubscribe($user_info);
                    $content .= $subscribe['msg'];

                }
                // unset($user_info['subscribe_scene'], $user_info['qr_scene'], $user_info['qr_scene_str']);
                // $regist = $registerObj->subscribe($user_info, $pid); 
                // if($regist['status']){ // 发送模板消息
                //     if(isset($regist['type'])){
                //         switch($regist['type']){
                //             case 'new':
                //                 $data = ['openid'=>$openid, 
                //                     'first'=>$regist['first'],
                //                     'keyword1'=>$user_info['nickname'], 
                //                     'keyword2'=>$regist['name'],
                //                     'keyword3'=>$regist['name'],
                //                     'keyword4'=>date('Y-m-d H:i:s', time()),
                //                     'remark'=>$regist['remark']];
                //                 $this->sendTemplate('registTemplate', $data);
                //                 return true;
                //             break;
                //             case 'old':
                //                 $content .= $regist['first'].$regist['remark'];
                //             break;
                //             default:
                //                 $content .= 'data error';
                //             break;
                //         }
                //     }else{
                //         $content .= $regist['first'];
                //     }
                    
                // }else{ // 发送文本消息
                //     $content .= $regist['first'];
                // }
            break;
            case 'unsubscribe': // 2018.9.13 增加 取消关注 by fjw
                Db::name('wx_user') -> where(['wx_openid'=>$openid]) -> update(['subscribe'=>2]);
            break;
            case "CLICK":
                switch($object->EventKey){
                    case "xiaoaiyouxuan": // 我的推广， 完成 in 18.6.26
                        $content .= '提供宝宝的日常用品和营养品';
                    break;
                    case 'erbaotijian': // 每日签到, 完成 in 18.6.26
                        $content .= '宝宝儿保的体检提醒和预约';
                    break;
                    case 'yimiaobaoxian': // 每日签到, 完成 in 18.6.26
                        $content .= '宝宝接种疫苗的保险';
                    break;
                    case 'yimiaojiezhong': // 每日签到, 完成 in 18.6.26
                        $content .= '宝宝疫苗的接种提醒和预约';
                    break;
                    case 'baobaoxinxi': // 每日签到, 完成 in 18.6.26
                        $content .= '能够记录宝宝的各项信息';
                    break;
                    case 'jiezhongxinxi': // 每日签到, 完成 in 18.6.26
                        $content .= '记录宝宝疫苗的各项接种信息';
                    break;
                    case 'murujiance': // 每日签到, 完成 in 18.6.26
                        $content .= '检测母乳成分的各项营养指标';
                    break;
                    case 'kangtijiance': // 每日签到, 完成 in 18.6.26
                        $content .= '检测宝宝疫苗接种后的抗体情况';
                    break;
                    case 'tianshifuwu': // 每日签到, 完成 in 18.6.26
                        $content .= '能够提供客服性质的功能，解决宝宝成长期间的各项问题';
                    break;
                    default: 
                        $content .= " unknown ";
                    break;
                }
            break;
            case "VIEW":
                $content .= "跳转链接 ".$object->EventKey;
            break;
            case "SCAN": 
                $content .= "扫描场景 ";//.$object->EventKey;
            break;
            case "LOCATION":
                $content .= "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
            break;
            case "scancode_waitmsg":
                $content .= 'scancode_waitmsg';
            break;
            case "scancode_push": // 收货可以用这个，或者直接微信扫码
                $content .= "扫码推事件";
            break;
            case "pic_sysphoto":
                $content .= "系统拍照";
            break;
            case "pic_weixin":
                $content .= "相册发图：数量 ".$object->SendPicsInfo->Count;
            break;
            case "pic_photo_or_album":
                $content .= "拍照或者相册：数量 ".$object->SendPicsInfo->Count;
            break;
            case "location_select":
                $content .= "发送位置：标签 ".$object->SendLocationInfo->Label;
            break;
            default:
                $content .= "receive a new event: ".$object->Event;
            break;
        }

        $result = $this->transmitText($object, $content);
        return $result;
    }

    public function access_token(){

        $access_token = Db::name('wx_config') -> where(['name'=>'wx_access_token']) -> find();
        $access_token = json_decode($access_token['value'], true);
        if($access_token['end_time'] > time()){
            return $access_token['access_token'];
        }else{
            $request_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->wechat_config['appid'].'&secret='.$this->wechat_config['appsecret'];
            $response = httpsGet($request_url);
            $response = json_decode($response, true);
            $data = ['access_token'=>$response['access_token'], 'end_time'=>time()+7100];
            Db::name('wx_config') -> where(['name'=>'wx_access_token']) -> update(['value'=>json_encode($data)]);
            return $data['access_token'];
        }
        
    }

    /**
     * create by fjw in 19.6.28
     * 
     * 
     */
    public function wxSubscribe($param = []){
        // 1. 检查是否存在
        $user = Db::name('wx_user') -> where(['wx_openid'=>$param['openid']]) -> find();
        if($user){
            $data = ['wx_subscribe'=>1, 'wx_subscribe_time'=>$param['subscribe_time']];
            $result = Db::name('wx_user') -> where(['wx_openid'=>$param['openid']]) -> update($data);
            return ['status'=>true, 'code'=>2, 'msg'=>'欢迎回来~'];
        }else{
            $data = [
                'nickname'=>$param['nickname'],
                'sex'=>$param['sex'],
                'headimgurl'=>$param['headimgurl'],
                'unionid'=>isset($param['unionid'])?$param['unionid']:'',
                'wx_subscribe'=>$param['subscribe'],
                'wx_openid'=>$param['openid'],
                'wx_language'=>$param['language'],
                'city'=>$param['city'],
                'province'=>$param['province'],
                'country'=>$param['country'],
                
                'wx_subscribe_time'=>$param['subscribe_time'],
                'wx_subscribe_scene'=>$param['subscribe_scene'],
                'wx_qr_scene'=>$param['qr_scene'],
                'wx_qr_scene_str'=>$param['qr_scene_str'],
    
    
                'wx_groupid'=>$param['groupid'],
                'wx_tagid_list'=>$param['tagid_list']
    
            ];
            Db::startTrans();
            $result = Db::name('wx_user')  -> insert($data);
            if($result){
                $lastID = Db::name('wx_user') ->getLastInsID();
                $result = Db::name('wx_user') -> where(['wx_openid'=>$param['openid']]) -> update(['user_id'=>$lastID]);
                Db::commit();
                return ['status'=>true, 'code'=>1, 'msg'=>'欢迎来到小爱健康宝典'];
            }else{
                Db::rollback();
                return ['status'=>false, 'msg'=>'获取信息失败，请重新关注公众号~'];
            }
            
        }

        
        
    }

    /**
     * ########################################################################
     *  回复文本消息 create by fjw in 18.5.30
     * ########################################################################
     */
    private function transmitText($object, $content)
    {
        if (!isset($content) || empty($content)){
            return "";
        }
        
        $xmlTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[%s]]></Content>
			       </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);

        return $result;
    }

    /**
     * ########################################################################
     *  发送模板消息 create by fjw in 18.5.30
     * ########################################################################
     */
    public function templateRedirectUrl($remind){
        $redirect_url = $this->siteroot;

        $yunqijiance_url = $redirect_url.'wechat/login?c=pregnant&a=check';
        $erbaotijian_url = $redirect_url.'wechat/login?c=kids&a=kidslist';
        $jiezhongtixing_url = $redirect_url.'wechat/login?c=kids&a=kidslist';
        $murujiance_url = $redirect_url.'wechat/login?c=pregnant&a=breastmilkcheck';

        switch($remind){
            case 'pregnant':
                return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wechat_config['appid'].'&redirect_uri='.urlencode($yunqijiance_url).'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect'; //$redirect_url.'pregnant/check',
            break;
            case 'kids':
                return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wechat_config['appid'].'&redirect_uri='.urlencode($erbaotijian_url).'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect'; //$redirect_url.'pregnant/check',
            break;
            case 'vaccine':
                return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wechat_config['appid'].'&redirect_uri='.urlencode($jiezhongtixing_url).'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect'; //$redirect_url.'pregnant/check',
            break;
            default: return '';
        }
    }
    // public function sendTemplateMsg(){
    //     // 诊疗计划提醒
    //     $template_id = 'OPENTM201438585';
    //     // http请求方式: POST
    //     $send_url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->access_token();
    //     return ['template_id'=>$template_id, 'send_url'=>$send_url];

        
        
    // }

    

    /**
     * 网页授权 add by fjw in 19.5.20
     */
    public function webAuth($code){
        // 1. 获取access_token 和 openid
        $web_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wechat_config['appid'].'&secret='.$this->wechat_config['appsecret'].'&code='.$code.'&grant_type=authorization_code';
        $open_res = httpsGet($web_url); //则本步骤中获取到网页授权access_token的同时，也获取到了openid，snsapi_base式的网页授权流程即到此为止。
        $open_arr = json_decode($open_res, true);
        if(!isset($open_arr['access_token']) || !isset($open_arr['openid'])){
            return ['status'=>false, 'msg'=>'openid 获取失败'];
        }
        $write_status = '';
        // 2. 查询wx_openid 是否存在
        $user = Db::name('wx_user') -> where(['wx_openid'=>$open_arr['openid']]) -> find();
        if(empty($user)){ // 用户不存在
            $info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$open_arr['access_token'].'&openid='.$open_arr['openid'].'&lang=zh_CN';
            $info_res = httpsGet($info_url);
            $info_arr = json_decode($info_res, true);
            $write_status = 'insert';

        }else{ // 用户存在
            if(!isset($user['unionid']) || $user['unionid'] == ''){ // 用户unionid不存在
                $info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$open_arr['access_token'].'&openid='.$open_arr['openid'].'&lang=zh_CN';
                $info_res = httpsGet($info_url);
                $info_arr = json_decode($info_res, true);
                $write_status = 'update';
            }else{ // 存在unionid
                return ['status'=>true, 'data'=>$user];
            }
            
        }
        // file_put_contents('info_arr.txt', var_export($info_arr, true)); 
        // 需要 添加 or 更新
        if(isset($info_arr['openid'])){
            $data = [
                'wx_openid'=>$info_arr['openid'],
                'nickname'=>$info_arr['nickname'],
                'sex'=>$info_arr['sex'],
                'headimgurl'=>$info_arr['headimgurl'],
                'unionid'=>$info_arr['unionid'],
                "province"=>$info_arr["province"],
                "city"=>$info_arr["city"],
                "country"=>$info_arr["country"],
                'wx_language'=>'zh_CN'
            ];

            switch($write_status){
                case 'insert':
                    $data['wx_subscribe'] = 1;
                    $data['wx_subscribe_time'] = time();
                    Db::startTrans();
                    $result = Db::name('wx_user')  -> insert($data);
                    if($result){
                        $lastID = Db::name('wx_user') ->getLastInsID();
                        $result = Db::name('wx_user') -> where(['wx_openid'=>$data['wx_openid']]) -> update(['user_id'=>$lastID]);
                        Db::commit();
                        return ['status'=>true, 'data'=>$data];
                    }else{
                        Db::rollback();
                        return ['status'=>false, 'msg'=>'获取信息失败，请重试~'];
                    }
                break;
                case 'update':
                    $result = Db::name('wx_user') -> where(['wx_openid'=>$data['wx_openid']]) -> update($data);
                    if($result){
                        return ['status'=>true, 'data'=>$data];
                    }else{
                        return ['status'=>false, 'msg'=>'获取信息失败，请重试~'];
                    }
                break;
                default: break;
            }
        }else{
            return ['status'=>false, 'msg'=>'获取用户信息失败'];
        }




        
        // /**
        //  * change by fjw in 19.7.22 : 
        //  */
        // if(isset($open_arr['access_token']) && isset($open_arr['openid'])){
        //     // $info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$open_arr['access_token'].'&openid='.$open_arr['openid'].'&lang=zh_CN';
        //     // $info_res = httpsGet($info_url);
        //     // $info_arr = json_decode($info_res, true);
        //     if(isset($info_arr['openid'])){
        //         $data = [
        //             'nickname'=>$info_arr['nickname'],
        //             'headimgurl'=>$info_arr['headimgurl'],
        //             'unionid'=>$info_arr['unionid']
        //         ];
        //         $update = Db::name('wx_user') -> where(['wx_openid'=>$info_arr['openid']]) -> update($data);
                
        //     }
        //     return isset($info_arr['openid'])?['status'=>true, 'data'=>$info_arr]:['status'=>false, 'msg'=>'用户信息获取失败'];
        // }else{
        //     return ['status'=>false, 'msg'=>'openid 获取失败'];
        // }
        
        
        
    
    }


    public function menuDIY(){

        $redirect_url = $this->siteroot;
        
        $yunqijiance_url = $redirect_url.'wechat/login?c=pregnant&a=check';
        $erbaotijian_url = $redirect_url.'wechat/login?c=kids&a=kidslist';
        $jiezhongtixing_url = $redirect_url.'wechat/login?c=kids&a=kidslist';
        $murujiance_url = $redirect_url.'wechat/login?c=pregnant&a=breastmilkcheck';

        $fever_activity_url = $redirect_url.'wechat/login?c=activity&a=subscribeFeverActivity';

        $menu = [
            'button'=>[
                [
                    'name'=>'天使守护',
                    'sub_button'=>[
                        [
                            'type'=>'view', 'name'=>'孕检提醒',
                            'url'=> 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wechat_config['appid'].'&redirect_uri='.urlencode($yunqijiance_url).'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect', //$redirect_url.'pregnant/check',
                        ],
                        [
                            'type'=>'view', 'name'=>'接种提醒',
                            'url'=> 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wechat_config['appid'].'&redirect_uri='.urlencode($jiezhongtixing_url).'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect', //$redirect_url.'pregnant/check',
                        ],
                        
                        [
                            'type'=>'view', 'name'=>'儿保体检',
                            'url'=> 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wechat_config['appid'].'&redirect_uri='.urlencode($erbaotijian_url).'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect', //$redirect_url.'pregnant/check',
                        ],
                    ]
                ],
                [
                    'name'=>'健康天地',
                    'sub_button'=>[
                        [
                            'type'=>'view', 'name'=>'宝妈配方',
                            'url'=> $redirect_url.'article/category?cid=5',
                        ],
                        [
                            'type'=>'view', 'name'=>'天使配方',
                            'url'=> $redirect_url.'article/category?cid=1',
                        ],
                        [
                            'type'=>'view', 'name'=>'母乳检测',
                            'url'=> 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wechat_config['appid'].'&redirect_uri='.urlencode($murujiance_url).'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect', //$redirect_url.'pregnant/check',
                        ],
                        [
                            'type'=>'view', 'name'=>'疫苗保险',
                            'url'=> $redirect_url.'vaccine/insurance'
                        ],
                        [
                            'type'=>'view', 'name'=>'抗体检测',
                            'url'=> $redirect_url.'vaccine/antibodycheck'
                        ],
                    ]
                ],
                [
                    'name'=>'妈咪助手',
                    'sub_button'=>[
                        [
                            'type'=>'view','name'=>'健康知识',
                            'url'=> $redirect_url.'article/category?cid=2',
                        ],
                        [
                            'type'=>'view','name'=>'参加活动',
                            'url'=> 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wechat_config['appid'].'&redirect_uri='.urlencode($fever_activity_url).'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect', //$redirect_url.'pregnant/check',
                        ]
                    ]
                ]
                
            ]
        ];
        // dump(json_encode($menu, JSON_UNESCAPED_UNICODE)); die;
        $wx_menu_url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->access_token();
        $response = httpsPost($wx_menu_url, json_encode($menu, JSON_UNESCAPED_UNICODE));
        dump($response); die;


    }

}

