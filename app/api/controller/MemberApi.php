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
 * PROJECT_妈咪v2 后台用户接口
 * 
 * 
 * 
 */
class MemberApi extends ApiBase
{

    // eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJNQU1JIEpXVCIsImlhdCI6MTU1NDA4MjkxOSwiZXhwIjozMTA4MTY1ODM4LCJhdWQiOiJNQU1JIiwic3ViIjoiTUFNSSIsImRhdGEiOnsiaWQiOjEsIm5pY2tuYW1lIjoiYWRtaW4iLCJ1c2VybmFtZSI6ImFkbWluIn19.D9ZFIv0IQ7Ez4J8xOA87LkQat_pphB3zrgK9WZJKqdQ
    
    /**
     * create by fjw in 19.3.18
     * 获取数据统计
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     */
    
    
    
    /**
     * create by fjw in 19.3.18
     * 获取需要审查的保险订单列表
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     */
    public function getInsuranceCheckingList(){
        
        return $this->apiReturn($this->logicMemberApi->getInsuranceCheckingList($this->param));

    }

    /**
     * create by fjw in 19.3.18
     * 设置保险订单的审查结果
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     */
    public function setInsuranceCheckResult(){

        return $this->apiReturn($this->logicMemberApi->setInsuranceCheckResult($this->param));

    }

    /**
     * create by fjw in 19.3.18
     * 获取保单的详细信息
     * @param 
     * @param 
     */
    public function getInsuranceCheckingDetail(){
 

        return $this->apiReturn($this->logicMemberApi->getInsuranceCheckingDetail($this->param));

    }

    /**
     * create by fjw in 19.3.18
     * 获取需要审查的理赔订单列表
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     */
    public function getCompensateCheckingList(){
        
        return $this->apiReturn($this->logicMemberApi->getCompensateCheckingList($this->param));

    }

    /**
     * create by fjw in 19.3.18
     * 设置理赔订单的审查结果
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     * @param 
     */
    public function setCompensateCheckResult(){

        return $this->apiReturn($this->logicMemberApi->setCompensateCheckResult($this->param));

    }

    /**
     * create by fjw in 19.3.18
     * 获取理赔订单的详细信息
     * @param 
     * @param 
     */
    public function getCompensateCheckingDetail(){
 

        return $this->apiReturn($this->logicMemberApi->getCompensateCheckingDetail($this->param));

    }




}
