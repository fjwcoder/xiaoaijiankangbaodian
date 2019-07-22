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

namespace app\common\logic;

use app\api\error\CodeBase;
use app\api\error\Common as CommonError;
use think\Db;
/**
 * 保险逻辑
 */
class Compensate extends LogicBase
{

/** * **********保险理赔信息start************************************************************************** * */

    /**
     * create by fjw in 19.3.29
     * 生成保险订单
     */
    public function CompensateOrderCreate($param = []){
        // 检查该保险订单是否存在未处理的理赔记录

        $exist_where = ['insurance_order_id'=>$param['insurance_order_id'], 'status'=>1];

        $compensate_order = $this->modelCompensateOrder->getInfo($exist_where, true);

        if($compensate_order){
            return CommonError::$compensateOrderExist;
        }



        $decoded_user_token = $param['decoded_user_token'];

        // dump($decoded_user_token); die;
        
        // 生成理赔订单号
        $order_id = setOrderID(); //
        // 查询保险信息
        // $insurance = $this->modelInsurance->getInfo(['insurance_id'=>$param['insurance_id'], 'status'=>1]);
        // 存入订单表
        $order_data = [
            'insurance_order_id'=>$param['insurance_order_id'],
            'compensate_order_id'=>$order_id,
            'user_id'=>intval($decoded_user_token->user_id),

            'id_card_front'=>$param['id_card_front'],
            'id_card_back'=>$param['id_card_back'],
            'bank_card'=>$param['bank_card'],
            'check_policy'=>$param['check_policy'],
            'bank_card_number'=>$param['bank_card_number'],
            'user_id_card'=>$param['user_id_card'],
            'step'=>0, // 默认是生成订单
            'create_time'=>time()
        ];



        // 生成订单
        if($this->modelCompensateOrder->setInfo($order_data)){
            return true;
        }else{
            return CommonError::$compensateOrderCreateFail;
        }


        // $success = true;
        // $result_code = CodeBase::$success;

        // Db::startTrans();
        // try{
        //     // 插入order表
        //     if($this->modelInsuranceOrder->setInfo($order_data)){
        //         $policy_data['oid'] = $this->modelInsuranceOrder->getLastInsID();
        //         if($this->modelInsuranceOrderPolicy->setInfo($policy_data)){
        //             Db::commit();
        //         }else{
        //             Db::rollback();
        //             $success = false;
        //             $result_code = CommonError::$insuranceOrderCreateFail; 
        //         }
        //     }else{
        //         Db::rollback();
        //         $success = false;
        //         $result_code = CommonError::$insuranceOrderCreateFail; 
        //     }
        // }catch(\Exception $e){
        //     dump($e);
        //     Db::rollback();
        //     $success = false;
        //     $result_code = CommonError::$insuranceOrderCreateFail; 
        // }
        
        // if(!$success){
        //     return $result_code;
        // }


    }


    /**
     * create by fjw in 19.3.29
     * 获取保险订单列表
     */
    public function getInsuranceOrderList($param = []){

        $decoded_user_token = $param['decoded_user_token'];

        $this->modelInsuranceOrder->alias('o');

        $this->modelInsuranceOrder->join = [
            [SYS_DB_PREFIX . 'insurance i', 'o.insurance_id = i.insurance_id'],
            [SYS_DB_PREFIX . 'insurance_order_policy p', 'o.id = p.oid']
        ];


        // 筛选查询字段 o.user_id, o.baby_id, o.insurance_id, 
        $field = '
                o.id, o.order_id, o.pay_money, o.pay_limit, o.step, o.status, o.create_time, o.gy_policy_id,

                i.name as insurance_name, i.description as insurance_description, i.headimgurl as insurance_img, 

                p.user_name, p.baby_name, p.relationship_to_baby
         ';
// dump($field); die;
        return $this->modelInsuranceOrder->getList(['o.status'=>1, 'o.user_id'=>$decoded_user_token->user_id], $field, 'o.id desc', false);
        // dump($param); die;

    }

    /**
     * create by fjw in 19.3.29
     * 获取保险订单详情
     */
    public function getInsuranceOrderDetail($param = []){

        $decoded_user_token = $param['decoded_user_token'];
        
        $this->modelInsuranceOrder->alias('o');

        $this->modelInsuranceOrder->join = [
            [SYS_DB_PREFIX . 'insurance i', 'o.insurance_id = i.insurance_id'],
            [SYS_DB_PREFIX . 'insurance_order_policy p', 'o.id = p.oid']
        ];
        $where = ['o.id'=>$param['oid'], 'o.order_id'=>$param['order_id'], 'o.status'=>1];

        $field = '
                o.id, o.order_id as insurance_order_id, o.pay_money, o.pay_limit, o.step, o.status, o.create_time, o.gy_policy_id,

                i.insurance_id, i.name as insurance_name, i.description as insurance_description, i.headimgurl as insurance_img, 

                p.*
         ';

        return $this->modelInsuranceOrder->getInfo($where, $field);

    }
    



























/** * **********保险订单信息end************************************************************************** * */
    
    /**
     * 保险分类编辑
     */
    public function insuranceCategoryEdit($data = [])
    {
        
        $validate_result = $this->validateInsuranceCategory->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateInsuranceCategory->getError()];
        }
        
        $url = url('insuranceCategoryList');
        
        $result = $this->modelInsuranceCategory->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '保险分类' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelInsuranceCategory->getError()];
    }
    
    /**
     * 获取保险列表
     */
    public function getInsuranceList($where = [], $field = 'a.*,c.name as category_name', $order = '', $paginate=false)
    {
        
        $this->modelInsurance->alias('a');
        
        $join = [
                    [SYS_DB_PREFIX . 'insurance_category c', 'a.category_id = c.id'],
                ];
        
        $where['a.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        
        $this->modelInsurance->join = $join;
        
        return $this->modelInsurance->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 获取保险列表搜索条件
     */
    public function getWhere($data = [])
    {
        
        $where = [];
        
        !empty($data['search_data']) && $where['a.name|a.describe'] = ['like', '%'.$data['search_data'].'%'];
        
        return $where;
    }
    
    /**
     * 保险信息编辑
     */
    public function insuranceEdit($data = [])
    {
        
        // $validate_result = $this->validateInsurance->scene('edit')->check($data);
        
        // if (!$validate_result) {
            
        //     return [RESULT_ERROR, $this->validateInsurance->getError()];
        // }
        
        $url = url('insuranceList');
        
        // empty($data['id']) && $data['member_id'] = MEMBER_ID;

        $result = $this->modelInsurance->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '保险' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '保险操作成功', $url] : [RESULT_ERROR, $this->modelInsurance->getError()];
    }

    /**
     * 获取保险信息
     */
    public function getInsuranceInfo($where = [], $field = 'a.*,c.name as category_name')
    {
        
        $this->modelInsurance->alias('a');
        
        $join = [
 
                    [SYS_DB_PREFIX . 'insurance_category c', 'a.category_id = c.id'],
                ];
        
        // $where['a.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        // $where = [];
        $this->modelInsurance->join = $join;
        
        return $this->modelInsurance->getInfo($where, $field);
    }
    
    /**
     * 获取分类信息
     */
    public function getInsuranceCategoryInfo($where = [], $field = true)
    {
        
        return $this->modelInsuranceCategory->getInfo($where, $field);
    }
    
    /**
     * 获取保险分类列表
     */
    public function getInsuranceCategoryList($where = [], $field = true, $order = '', $paginate = 0)
    {
        
        return $this->modelInsuranceCategory->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 保险分类删除
     */
    public function insuranceCategoryDel($where = [])
    {
        
        $result = $this->modelInsuranceCategory->deleteInfo($where);
        
        $result && action_log('删除', '保险分类删除，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '保险分类删除成功'] : [RESULT_ERROR, $this->modelInsuranceCategory->getError()];
    }
    
    /**
     * 保险删除
     */
    public function insuranceDel($where = [])
    {
        
        $result = $this->modelInsurance->deleteInfo($where);
        
        $result && action_log('删除', '保险删除，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '保险删除成功'] : [RESULT_ERROR, $this->modelInsurance->getError()];
    }
}
