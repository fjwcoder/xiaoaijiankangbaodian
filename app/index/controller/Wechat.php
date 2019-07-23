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

namespace app\index\controller;

/**
 * 
 */
class Wechat extends IndexBase
{
    /**
     * 微信
     */
    public function index(){
        $this->logicWechat->index($this->param);
    }

    /**
     * 微信网页授权登录
     */
    public function login(){

        $res = $this->logicWechat->login($this->param);
        return $this->redirect($res['url'], $res['param']);
    }

    /**
     * 自定义菜单
     */
    public function menuDIY(){
        $this->logicWechat->menu($this->param);
    }


    /**
     * 妈咪天使公众号网页授权
     */
    public function mamiLogin(){
        $res = $this->logicWechat->mamiLogin($this->param);
        return $this->redirect($res['url'], $res['param']);
    }

}
