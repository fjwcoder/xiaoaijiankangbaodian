<?php
/**
 * Common 控制器
 * by fqm in 19.10.17
 */

namespace app\index\controller;

class Common extends IndexBase
{



    /**
     * 发送短信
     */
    public function sendSms(){

        return $this->logicCommon->sendSms($this->param);
    }


}