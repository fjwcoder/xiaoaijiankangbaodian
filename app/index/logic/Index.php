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
class Index extends IndexBase
{

    /**
     * 扫描取号小票上的二维码，查看排队人数
     * @param $ts 时间戳
     * @param $unique_code
     * @param $mac_address
     * @param $number 小票上的号码
     */
    public function scanInjectQrcode($param = []){

        $param = request()->param();
        $param['openid'] = '123';

        $openid = $param['openid'];
        $timestamp = $param['ts'];
        $unique_code = $param['uc'];
        $number = $param['no'];
        
        // $this->modelInjectQueueList->get



    }
}
