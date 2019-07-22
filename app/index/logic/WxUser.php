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
class WxUser extends IndexBase
{
    public function getWxUserInfo($where = []){
        return $this->modelWxUser->getInfo($where);
    }

    // public function menu($param = []){
    //     // dump($param); die;
    //     return $this->serviceWechat->driverWxgzh->menu($param);
    // }
}
