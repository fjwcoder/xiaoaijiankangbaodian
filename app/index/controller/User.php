<?php
/**
 * User 用户控制器
 * by fqm in 19.10.17
 */

namespace app\index\controller;

class User extends IndexBase
{


    /**
     * 用户中心
     * by fqm in 19.10.17
     */
    public function index()
    {

        $openid = input('openid', '', 'htmlspecialchars,trim');

        $user = $this->logicWxUser->getWxUserInfo(['wx_openid'=>$openid]);

        if(empty($user)){
            return $this->redirect('index/errorPage', ['content'=>'用户不存在，请关注微信公众号“小爱健康宝典”']);
        }

        $this->assign('user',$user);

        return $this->fetch('index');
    }

    /**
     * 成为vip，绑定手机号
     * by fqm in 19.10.17
     */
    public function bindMobile()
    {
        return $this->logicUser->bindMobile($this->param);
    }

    
}
