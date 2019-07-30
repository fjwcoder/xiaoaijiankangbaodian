<?php

namespace app\admin\logic;
/**
 * FeverActivity逻辑层
 */
class FeverActivity extends AdminBase
{

    /**
     * 获取活动列表
     */
    public function getActivity($where = [], $field = '', $order = '', $paginate = false)
    {

        $this->modelFeverActivity->alias('a');

        $this->modelFeverActivity->join = [
            [SYS_DB_PREFIX."wx_user b", "a.user_id = b.user_id", "left"],
        ];
        
        $field = 'a.xiaoai_click, a.xiaoai_click_time, a.mami_click, a.mami_click_time, a.is_finished, a.finish_time, a.status, b.user_id, b.wx_openid, b.nickname, b.sex, b.headimgurl, b.unionid, b.city, b.province, b.country';

        $list = $this->modelFeverActivity->getList($where, $field, $order, $paginate);

        return $list;

    }

}