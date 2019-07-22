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

namespace app\common\logic;

use app\api\error\Common as CommonError;
use think\Db;
/**
 * 
 */
class MemberApi extends LogicBase
{

    /**
     * create by fjw in 19.3.14
     * 
     */
    public function getInsuranceCheckingList($param = []){
        // dump('here'); die;
        // file_put_contents('a.txt', var_export($param, true));
        $decoded_user_token = $param['decoded_user_token'];
        // 验证该用户是否有权限
        if($decoded_user_token->id <= 0){

            return CommonError::$userNoAccess;

        }

        $this->modelInsuranceOrder->alias('o');

        $this->modelInsuranceOrder->join = [
            // [SYS_DB_PREFIX." i", "o.insurance_id=i.insurance_id", "left"],
            [SYS_DB_PREFIX."insurance i", "o.insurance_id=i.insurance_id", "left"],
            [SYS_DB_PREFIX."insurance_order_policy p", "o.id=p.oid", "left"],
            
        ];
        $where = [];
        // 1. 先设置步骤
        if(isset($param['step']) && $param['step'] != ''){ 
            $where['o.step'] = $param['step'];
        }
        // 2. 设置状态
        if(isset($param['status']) && $param['status'] != ''){ 
            $where['o.status'] = $param['status'];
        }
        // 3. 设置时间
        if(isset($param['begintime']) && $param['begintime'] != ''){ 
            $where['o.create_time'] = ['>=', strtotime($param['begintime'])];
        }
        if(isset($param['endtime']) && $param['endtime'] != ''){
            $where['o.create_time'] = ['<=', strtotime($param['endtime'])];
        }

// dump($where); die;
        $field = '
            o.id as oid, o.order_id, o.user_id, o.pay_money, o.pay_limit, o.gy_policy_id, o.step, o.status,

            i.name as insurance_name,

            p.user_name, p.user_id_card, p.user_mobile, p.user_sex, p.relationship_to_baby, 
            p.baby_name, p.baby_id_card, p.baby_age, p.baby_sex
        
        ';

        return $this->modelInsuranceOrder->getList($where, $field, 'o.id', isset($param['limit'])?$param['limit']:50);

    }

    /**
     * create by fjw in 19.3.14
     * 
     */
    public function getInsuranceCheckingDetail($param = []){

        $decoded_user_token = $param['decoded_user_token'];
        
        // 验证该用户是否有获取权限
        if($decoded_user_token->id <= 0){

            return CommonError::$userNoAccess;

        }

        $this->modelInsuranceOrder->alias('o');

        $this->modelInsuranceOrder->join = [
            
            [SYS_DB_PREFIX."insurance i", "o.insurance_id=i.insurance_id", "left"],
            [SYS_DB_PREFIX."insurance_order_policy p", "o.id=p.oid", "left"],
            [SYS_DB_PREFIX."user u", "o.user_id=u.id", "left"],
            
        ];

        // $where = ['o.status'=>1];
        $where = ['o.order_id'=>$param['order_id']];

        $field = '
            o.id as oid, o.order_id, o.user_id, o.pay_money, o.pay_limit, o.gy_policy_id, o.insurance_policy, o.create_time, o.pay_time,

            i.name as insurance_name, i.headimgurl, i.description, i.content, 

            p.user_name, p.user_id_card, p.user_id_card_begintime, p.user_id_card_endtime, p.user_sex, p.user_age, p.user_country, p.user_address, p.user_mobile, p.relationship_to_baby, 
            p.baby_name, p.baby_id_card, p.baby_id_card_begintime, p.baby_id_card_endtime, p.baby_sex, p.baby_age, p.baby_country, p.baby_address, p.relationship_to_user
        
        ';

        return $this->modelInsuranceOrder->getInfo($where, $field);

    }

    /**
     * create by fjw in 19.3.14
     * 
     */
    public function setInsuranceCheckResult($param = []){
        
        $decoded_user_token = $param['decoded_user_token'];
        
        // 需要查询该用户是否有审查修改权限
        if($decoded_user_token->id <= 0){

            return CommonError::$userNoAccess;

        }
        // dump($param);
        $where = ['id'=>$param['oid'], 'order_id'=>$param['order_id'], 'status'=>1, 'step'=>1];

        $up_info = [
            'step'=>2, 
            'status'=>$param['status'], 
            'remark'=>$param['check_reason'], 
            'check_user' => $decoded_user_token->nickname.'/'.$decoded_user_token->username,
            'check_time' => time(),
        ];
        
        if(!$this->modelInsuranceOrder->updateInfo($where, $up_info)){
            return CommonError::$returnFalse;
        }

    }

/**
 * **************************以上是保险列表审核，以下是理赔列表审核****************************** *******************************************************
 */

    /**
     * create by fjw in 19.3.14
     * 
     */
    public function getCompensateCheckingList($param = []){
        // dump($param); die;
        // file_put_contents('a.txt', var_export($param, true));
        $decoded_user_token = $param['decoded_user_token'];
        // 验证该用户是否有权限
        if($decoded_user_token->id <= 0){

            return CommonError::$userNoAccess;

        }

        $this->modelCompensateOrder->alias('c');

        $this->modelCompensateOrder->join = [
            [SYS_DB_PREFIX."insurance_order_policy p", "c.order_policy_id=p.id", "left"],
            [SYS_DB_PREFIX."insurance_order o", "c.insurance_order_id=o.order_id", "left"],
            [SYS_DB_PREFIX."insurance i", "o.insurance_id=i.insurance_id", "left"],
            
        ];
        $where = [];
        // 1. 先设置步骤
        if(isset($param['step']) && $param['step'] != ''){ 
            $where['c.step'] = $param['step'];
        }
        // 2. 设置状态
        if(isset($param['status']) && $param['status'] != ''){ 
            $where['c.status'] = $param['status'];
        }
        // 3. 设置时间
        if(isset($param['begintim']) && $param['begintime'] != ''){ 
            $where['c.create_time'] = ['>=', strtotime($param['begintime'])];
        }
        if(isset($param['endtime']) && $param['endtime'] != ''){
            $where['c.create_time'] = ['<=', strtotime($param['endtime'])];
        }

// o.id as oid, o.order_id, o.user_id, o.pay_money, o.pay_limit, o.gy_policy_id, o.step, o.status,
// i.name as insurance_name,
        $field = '
            c.compensate_order_id, c.user_id, c.gy_policy_id, c.step, c.status, c.create_time, 
            c.insurance_order_id, c.check_time,

            i.name as insurance_name,
            o.pay_money, o.pay_limit, 

            p.user_name, p.user_id_card, p.user_mobile, p.user_sex, p.relationship_to_baby, 
            p.baby_name, p.baby_id_card, p.baby_age, p.baby_sex
        
        ';

        return $this->modelCompensateOrder->getList($where, $field, 'c.id', isset($param['limit'])?$param['limit']:50);

    }

    /**
     * create by fjw in 19.3.14
     * 
     */
    public function getCompensateCheckingDetail($param = []){

        $decoded_user_token = $param['decoded_user_token'];
        
        // 验证该用户是否有获取权限
        if($decoded_user_token->id <= 0){

            return CommonError::$userNoAccess;

        }

        $this->modelCompensateOrder->alias('c');

        $this->modelCompensateOrder->join = [
            [SYS_DB_PREFIX."insurance_order_policy p", "c.order_policy_id=p.id", "left"],
            [SYS_DB_PREFIX."insurance_order o", "c.insurance_order_id=o.order_id", "left"],
            [SYS_DB_PREFIX."insurance i", "o.insurance_id=i.insurance_id", "left"],
        ];

        $where = ['c.compensate_order_id'=>$param['order_id']];

        $field = '
            c.insurance_order_id, c.compensate_order_id, c.user_id, c.gy_policy_id, 
            c.id_card_front, c.id_card_back, c.bank_card, c.check_policy, c.bank_card_number,
            c.user_id_card, c.step, c.status, c.reason, c.remark, c.create_time, c.check_time,
            
            i.name as insurance_name, i.headimgurl, i.description, i.content, 
            o.pay_money, o.pay_limit, 

            p.user_name, p.user_id_card, p.user_id_card_begintime, p.user_id_card_endtime, p.user_sex, p.user_age, p.user_country, p.user_address, p.user_mobile, p.relationship_to_baby, 
            p.baby_name, p.baby_id_card, p.baby_id_card_begintime, p.baby_id_card_endtime, p.baby_sex, p.baby_age, p.baby_country, p.baby_address, p.relationship_to_user
        
        ';

        return $this->modelCompensateOrder->getInfo($where, $field);

    }

    /**
     * create by fjw in 19.3.14
     * 
     */
    public function setCompensateCheckResult($param = []){
        
        $decoded_user_token = $param['decoded_user_token'];
        
        // 需要查询该用户是否有审查修改权限
        if($decoded_user_token->id <= 0){

            return CommonError::$userNoAccess;

        }
        // dump($param);
        $where = [
            // 'id'=>$param['oid'], 
            'compensate_order_id'=>$param['order_id'], 'status'=>1, 'step'=>0];

        $up_info = [
            'step'=>1, 
            'status'=>$param['status'], 
            'reason'=>$param['check_reason'], 
            'check_user' => $decoded_user_token->nickname.'/'.$decoded_user_token->username,
            'check_time' => time(),
        ];
        
        if(!$this->modelCompensateOrder->updateInfo($where, $up_info)){
            return CommonError::$returnFalse;
        }

    }


    
}
