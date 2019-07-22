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
 * PROJECT_妈咪v2 保险业务：
 * 
 * 
 * 填写保险信息
 * 生成订单
 * 订单预览
 * 
 * 获取保险详情
 * 获取保险列表
 * 获取保险种类列表
 */
class Insurance extends ApiBase
{
/** * ********** wxapp保险订单信息 start ************************************************************************** * */
    /**
     * create by fjw in 19.3.29
     * 获取保险订单列表
     */
    public function getInsuranceOrderList(){

        return $this->apiReturn($this->logicInsurance->getInsuranceOrderList($this->param)); 

    }

    /**
     * create by fjw in 19.3.29
     * 获取保险订单详情
     */
    public function getInsuranceOrderDetail(){

        return $this->apiReturn($this->logicInsurance->getInsuranceOrderDetail($this->param)); 

    }

    /**
     * create by fjw in 19.3.29
     * 保险订单生成
     * @param  
     * @param 
     * @param 
     */
    public function insuranceOrderCreate(){

        return $this->apiReturn($this->logicInsurance->insuranceOrderCreate($this->param)); 

    }

/** * ********** wxapp保险订单信息end************************************************************************** * */


    /**
     * create by fjw in 19.3.19
     *  填写保险详情
     * @param  
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     */
    public function editInsuranceDetail(){

        return $this->apiReturn($this->logicInsurance->editInsuranceDetail($this->param)); 

    }

    

    /**
     * create by fjw in 19.3.19
     * 保险订单预览
     * @param  
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     */
    
    public function orderPreview(){

        return $this->apiReturn($this->logicInsurance->orderPreview($this->param)); 

    }


/* ******以下为保险列表、保险详情、保险分类列表******************************************************************* ***************** */


    /**
     * create by fjw in 19.3.19
     * 微信小程序获取保险详情
     * @param insurance_id: 
     * @param 
     * @param 
     */
    public function wxappGetInsuranceInfo(){

        $where = ['a.status'=>1, 'a.insurance_id'=>$this->param['insurance_id']];

        return $this->apiReturn($this->logicInsurance->getInsuranceInfo($where)); 

    }

    /**
     * create by fjw in 19.3.19
     * 微信小程序获取保险列表
     * @param category_id: 保险种类id   该参数为0时，获取全部保险；为种类值时，只获取该种类的保险
     * @param key_words: 保险搜索的关键词
     * @param 
     * @param 
     */
    public function wxappGetInsuranceList(){

        $where = ['a.status'=>1];

        if(isset($this->param['category_id']) && $this->param['category_id'] != 0){ // 设置了按照种类搜索

            $where['a.category_id'] = $this->param['category_id'];

        }
        if(isset($this->param['key_words']) && !empty($this->param['key_words'])){
            
        }

        $field = 'a.insurance_id, a.category_id, a.name, a.price, a.headimgurl, a.description, a.status, c.name as category_name';
        $listObj = $this->logicInsurance->getInsuranceList($where, $field, 'a.insurance_id', false);

        return $this->apiReturn($listObj);


    
    }

    /**
     * create by fjw in 19.3.19
     * 微信小程序获取保险种类列表
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     */
    public function wxappGetInsuranceCategoryList(){

        return $this->apiReturn($this->logicInsurance->getInsuranceCategoryList([], true, 'id', false));

    }

    

}
