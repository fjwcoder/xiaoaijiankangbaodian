<?php
/**
 * Common 逻辑层
 */

namespace app\index\logic;

class Common extends IndexBase
{


    /**
     * 发送短信
     */
    public function sendSms($param = [])
    {

        if(empty($param['mobile'])) return ['code'=>400,'msg'=>'请输入手机号'];
        if(empty($param['userId'])) return ['code'=>400,'msg'=>'请刷新页面后重试'];

        $where = [
            'mobile'=>$param['mobile'],
            'uid'=>$param['userId'],
            'end_time'=>['>',time()],
            'remark'=>1
        ];

        $old_code = $this->modelVerifyCode->getInfo($where);

        if($old_code){
            return ['code'=>400,'msg'=>'请勿重复发送验证码'];
        }

        $parameter['sign_name'] = '妈咪爱天使';
        $parameter['template_code'] = 'SMS_109680029';
        $parameter['phone_number'] = $param['mobile'];
        $code = rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9);

        $parameter['template_param'] = [
            'code' => $code,
            'product' => '信息验证码',
        ];

        $result = $this->serviceSms->driverAlidy->sendSms($parameter);
        // dump($result);
        $data = [
            'OutId'=>$parameter['template_code'],
            'uid'=>empty($param['userId']) ? 0 : $param['userId'],
            'mobile'=>$param['mobile'],
            'code'=>$code,
            'create_time'=>time(),
            'end_time'=>time() + 600,
            'remark'=>1,
        ];

        if($result){ // 发送成功
            // 把code存入数据库
            if($this->modelVerifyCode->setInfo($data)){
                return ['code'=>200,'msg'=>'发送成功'];
            }else{
                return ['code'=>400,'msg'=>'发送失败'];
            }

        }else{
            return ['code'=>400,'msg'=>'发送失败'];
        }
    }
}