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

namespace app\api\logic;

use app\api\error\Common as CommonError;

/**
 * 附加功能
 */
class Vaccine extends ApiBase
{

    /**
     * create by fjw in 19.3.14
     * 疫苗预约记录
     */
    public function getVaccineAppointmentRecord($where=[],$field = 'a.yu_id, a.yu_time, a.yu_endtime, a.type, a.qr_url, a.yu_num, v.ym_name, j.jz_name',$order='a.yu_id desc',$paginate = false){

        $this->modelVaccineAppointment->alias('a');

        $this->modelVaccineAppointment->join = [

            [SYS_DB_PREFIX."vaccine v", "a.ym_id = v.ym_id", "left"],
            [SYS_DB_PREFIX."jiezhong j", " a.yu_jz_id = j.jz_id", "left"],
            
        ];

        return $this->modelVaccineAppointment->getList($where, $field, $order, $paginate);

    }


    
}
