<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | fjwcoder<fjwcoder@gmail.com>                           |
// +---------------------------------------------------------------------+
// |  |                      |
// +---------------------------------------------------------------------+

namespace app\api\controller;

/**
 * PROJECT_妈咪v2  公共基础接口控制器
 * 1. 用户登录
 * 2. 用户注册
 * 3. 用户修改密码
 * 4. 短信验证码
 */
class Common extends ApiBase
{

    /**
     * 短信验证码
     */
    public function sendSms(){

        return $this->apiReturn($this->logicCommon->sendSms($this->param));

    }
    
    /**
     * APP前台用户登录接口
     */
    public function userLogin()
    {

        return $this->apiReturn($this->logicCommon->userlogin($this->param));
    }

    /**
     * APP前台用户注册接口
     */
    public function userRegist()
    {

        return $this->apiReturn($this->logicCommon->userRegist($this->param));
    
    }

    


    /**
     * 微信小程序用户注册
     */
    public function wxappLogin(){

        return $this->apiReturn($this->logicCommon->wxappLogin($this->param));

    }




    /**
     * 前台台修改密码接口
     */
    // public function userChangePassword()
    // {
        
    //     return $this->apiReturn($this->logicCommon->userChangePassword($this->param));

    // }
    
// ******************************************************************   
    /**
     * 后台登录接口
     * 
     */
    public function memberLogin()
    {
        
        return $this->apiReturn($this->logicCommon->memberLogin($this->param));
    }
    
    /**
     * 后台修改密码接口
     */
    public function memberChangePassword()
    {
        
        return $this->apiReturn($this->logicCommon->changePassword($this->param));
    }
    
    // /**
    //  * 友情链接
    //  */
    // public function getBlogrollList()
    // {
        
    //     return $this->apiReturn($this->logicCommon->getBlogrollList($this->param));
    // }

    
}
