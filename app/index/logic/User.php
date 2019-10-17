<?php
/**
 * User 模型
 * by fqm in 19.10.17
 */

namespace app\index\logic;

class User extends IndexBase
{


    /**
     * 绑定手机号
     * by fqm in 19.10.17
     */
    public function bindMobile($param = [])
    {

        if(empty($param['mobile'])) return ['code'=>400,'msg'=>'请填写手机号'];
        if(empty($param['code'])) return ['code'=>400,'msg'=>'请填写验证码'];
        if(empty($param['userId'])) return ['code'=>400,'msg'=>'请刷新页面后重试'];

        $where = [
            'code'=>$param['code'],
            'mobile'=>$param['mobile'],
            'uid'=>$param['userId'],
            'remark'=>1
        ];

        $codeInfo = $this->modelVerifyCode->getInfo($where);

        if($codeInfo){
            if(time() > $codeInfo['end_time']){
                return ['code'=>400,'msg'=>'验证码已过期'];
            }else{
                $this->modelVerifyCode->updateInfo($where,['remark'=>0]);

                $this->modelWxUser->updateInfo(['user_id'=>$param['userId']],['mobile'=>$param['mobile'],'is_vip'=>2]);
                
                return ['code'=>200,'msg'=>'恭喜您获得一次免费母乳检测的机会'];
            }
        }else{
            return ['code'=>400,'msg'=>'验证码错误'];
        }

    }
}