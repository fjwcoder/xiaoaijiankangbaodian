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

        // $this->param = [
        //     'c' =>'pregnant', 'a'=>'check', 'code'=>'001YhNuV0M9Iq12JP9yV0XkOuV0YhNui',
        //     'state'=>1
        // ];
        $res = $this->logicWechat->login($this->param);
        // dump($res); die;
        return $this->redirect($res['url'], $res['param']);
    }

    /**
     * 自定义菜单
     */
    public function menuDIY(){
        $this->logicWechat->menu($this->param);
    }

}
