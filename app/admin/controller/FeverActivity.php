<?php
/*
 * @Descripttion: 活动记录控制器
 * @Author: fqm
 * @Date: 2019-07-30 08:35:17
 */

namespace app\admin\controller;

/**
 * FeverActivity控制器
 */
class FeverActivity extends AdminBase
{
    /**
     * 活动记录列表
     */
    public function activityList()
    {
        return $this->fetch('list');
    }

    /**
     * 获取活动记录
     */
    public function getActivity()
    {
        $where = [];

        $list = $this->logicFeverActivity->getActivity($where, true, '', 15);

        return $list;

    }



}
