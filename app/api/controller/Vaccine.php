<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | fjwcoder<fjwcoder@gmail.com>                           |
// +---------------------------------------------------------------------+
// | Repository |                   |
// +---------------------------------------------------------------------+

namespace app\api\controller;

use app\common\controller\ControllerBase;

/**
 * PROJECT_妈咪v2 疫苗
 * 疫苗预约
 * 疫苗预约记录
 * 抗体检测记录
 */
class Vaccine extends ApiBase
{

    /**
     * create by fjw in 19.3.13
     * 疫苗预约
     * @param yu_time: 预约开始时间
     * @param yu_endtime: 预约结束时间
     * @param yu_jz_id: 预约接种点的id
     * @param ym_id: 疫苗id
     * @param us_id: 用户id
     * @param type: 预约类型 0全部， 1未失效 2已失效
     * @param qr_url: 预约二维码
     * @param yu_num: 
     */
    public function vaccineAppointment(){

    }

    /**
     * create by fjw in 19.3.14
     * 疫苗预约记录
     * @param user_id: 用户id
     */
    public function vaccineAppointmentRecord(){

        $decoded_user_token = $this->param['decoded_user_token'];

        $where = ['a.us_id'=>$decoded_user_token->user_id];

        if($this->param['appointment_type'] != 0){ // 0全部， 1未失效 2已失效

            $where['a.type'] = $this->param['appointment_type'];

        }

        return $this->apiReturn($this->logicVaccine->getVaccineAppointmentRecord($where));

    }

    /**
     * create by fjw in 19.3.14
     * 疫苗预约详情
     */
    public function vaccineAppointmentDetails(){

    }

    /**
     * create by fjw in 19.3.14
     * 抗体检测预约记录
     * @param user_id: 用户id
     */
    public function antibodyTestAppointmentRecord(){



        
        return $this->apiReturn($this->logicVaccine->antibodyTestAppointmentRecord($this->param));

    }


}
