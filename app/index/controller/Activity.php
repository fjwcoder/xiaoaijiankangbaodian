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

namespace app\index\controller;
use think\Db;
/**
 * 前端活动控制器
 */
class Activity extends IndexBase
{

    /**
     * 关注领取退热贴活动
     * 小爱健康平台
     */
    public function subscribeFeverActivity(){

        // 一、 处理用户信息数据
        $user_id = user_is_login();
        $openid = input('openid', '', 'htmlspecialchars,trim');

        if(empty($openid)){
            return $this->redirect('index/errorPage', ['content'=>'no user info, resubscribe please']);
        }
        $user = $this->logicWxUser->getWxUserInfo(['wx_openid'=>$openid, 'wx_id'=>$user_id]);
// dump($user); die;
        if(isset($user['unionid']) && $user['unionid'] != ''){ // 判断unionid是否存在
            $unionid = $user['unionid'];
        }else{
            return $this->redirect('index/errorPage', ['content'=>'no user info, resubscribe please']);
        }
        
        /**
         * 活动流程
         */
        $this->assign('gzh_name', 'mami');
        $this->assign('activity_id', 0);
        $activity = Db::name('fever_activity') -> where(['unionid'=>$unionid]) -> find();
        $this->assign('activity_id', isset($activity['id'])?$activity['id']:0);

        $this->assign('activity_info', $activity);

        if(empty($activity)){ 
            // 1. 表中没有活动记录，直接参与
            $data = [
                'user_id'=>$user_id, 'openid'=>$openid, 'unionid'=>$unionid,
                'xiaoai_click'=>1, 'xiaoai_click_time'=>intval(time())
            ];
            $insert = Db::name('fever_activity') -> insert($data);
            if($insert){

                $this->assign('fever_click', 1);

                return $this->fetch('fever_ok');
            }else{
                return $this->redirect('index/errorPage', ['content'=>'参加活动失败，请重试']);
            }

            
        }else{
            
            // 2. 活动表中已存在活动记录
            $deal = $this->dealTwoActivity($activity);
            if($deal['click'] == 0){
                $data = [
                    'user_id'=>$user_id, 'openid'=>$openid, 
                    'xiaoai_click'=>1, 'xiaoai_click_time'=>intval(time())
                ];
                $update = Db::name('fever_activity') -> where(['unionid'=>$unionid]) -> update($data);
                if($update){
                    $this->assign('fever_click', 1);
                    
                    return $this->fetch('fever_ok');
                }else{
                    return $this->redirect('index/errorPage', ['content'=>'参加活动失败，请重试']);
                }
            }
            if($deal['click'] == 1 && $activity['xiaoai_click'] == 0){
                $data = [
                    'user_id'=>$user_id, 'openid'=>$openid, 
                    'xiaoai_click'=>1, 'xiaoai_click_time'=>intval(time())
                ];
                $update = Db::name('fever_activity') -> where(['unionid'=>$unionid]) -> update($data);
                if($update){
                    $this->assign('fever_click', 2);

                    return $this->fetch('fever_ok');
                }else{
                    return $this->redirect('index/errorPage', ['content'=>'参加活动失败，请重试']);
                }
            }
            // $this->assign('activity_id', $activity['id']);
            $this->assign('fever_click', $deal['click']);
            return $this->fetch('fever_ok');

            
        }

    }

//     // 点击完成活动，领取到礼品
    public function finishFeverActivity(){
        
        $activity_id = input('aid', 0, 'intval');

        $activity = Db::name('fever_activity') -> where(['id'=>$activity_id]) -> find();

        $this->assign('activity_info', $activity);
// dump($activity); die; 
        if(empty($activity)){
            return $this->redirect('index/errorPage', ['content'=>'活动记录不存在']);
        }
        $this->assign('gzh_name', 'none');
        $this->assign('activity_id', 0);

        $deal = $this->dealTwoActivity($activity);
// dump($deal); die;
        if($deal['click'] < 2){
            return $this->redirect('index/errorPage', ['content'=>'尚未完成活动，不可领取']);
        }
        if($deal['click'] > 2){

            return $this->redirect('index/errorPage', ['content'=>'不可重复领取奖品']);
        }

        $user_id = $activity['user_id'];
        $user = $this->logicWxUser->getWxUserInfo(['user_id'=>$user_id]);
        if($user){
            $finish_data = [
                'is_finished'=>1, 'finish_time'=>intval(time())
            ];
            $update = Db::name('fever_activity') -> where(['id'=>$activity_id, 'mami_click'=>1, 'xiaoai_click'=>1, 'is_finished'=>0]) -> update($finish_data);
            if($update){
                $activity['is_finished'] = 1;
                $activity['finish_time'] = time();
                
                $this->assign('activity_info', $activity);
                $this->assign('fever_click', 3);

                return $this->fetch('fever_ok');
            }
        }else{
            return $this->redirect('index/errorPage', ['content'=>'用户信息错误，请重试']);
        }



    }




    /**
     * create by fjw in 19.7.23
     * 处理两个公众号的关注情况
     * 
     */
    public function dealTwoActivity($activity){
        $click = 0;
        $gzh = [];
        // dump($activity); die;
        if($activity['xiaoai_click'] == 1){
            $click += $activity['xiaoai_click'];
            $gzh[] = 'xiaoai';
        }
        if($activity['mami_click'] == 1){
            $click += $activity['mami_click'];
            $gzh[] = 'mami';
        }

        $click += $activity['is_finished'];

        return ['click'=>$click, 'data'=>$gzh];
        
    }


    /**
     * =============以上是小爱平台的，下边是 妈咪天使平台的 ===============================================================================================================
     */
    public function mamiSubscribeFeverActivity(){
        // 冯 oPC1q6Aul52AWBud40-bxcoEhdRQ
        // 王 oPC1q6Fh6Lm4tydL2LH5kE7t222o
        $unionid = input('unionid', 'oPC1q6Aul52AWBud40-bxcoEhdRQ', 'htmlspecialchars,trim');

        $this->assign('gzh_name', 'xiaoai');
        
        $activity = Db::name('fever_activity') -> where(['unionid'=>$unionid]) -> find();
        $this->assign('activity_id', isset($activity['id'])?$activity['id']:0);
        
        $this->assign('activity_info', $activity);

        if(empty($activity)){ 
            // 1. 表中没有活动记录，直接参与
            $data = [
                'user_id'=>0, 'openid'=>'',
                'unionid'=>$unionid,
                'mami_click'=>1, 'mami_click_time'=>intval(time())
            ];

            $insert = Db::name('fever_activity') -> insert($data);
            if($insert){

                $this->assign('fever_click', 1);
                
                return $this->fetch('fever_ok');
            }else{
                return $this->redirect('index/errorPage', ['content'=>'参加活动失败，请重试']);
            }

            
        }else{
            
            // 2. 活动表中已存在活动记录
            $deal = $this->dealTwoActivity($activity);
            if($deal['click'] == 0){
                $data = [
                    'mami_click'=>1, 'mami_click_time'=>intval(time())
                ];
                $update = Db::name('fever_activity') -> where(['unionid'=>$unionid]) -> update($data);
                if($update){
                    $this->assign('fever_click', 1);

                    return $this->fetch('fever_ok');
                }else{
                    return $this->redirect('index/errorPage', ['content'=>'参加活动失败，请重试']);
                }
            }
            if($deal['click'] == 1 && $activity['mami_click'] == 0){
                $data = [
                    'mami_click'=>1, 'mami_click_time'=>intval(time())
                ];
                $update = Db::name('fever_activity') -> where(['unionid'=>$unionid]) -> update($data);
                if($update){
                    $this->assign('fever_click', 2);

                    return $this->fetch('fever_ok');
                }else{
                    return $this->redirect('index/errorPage', ['content'=>'参加活动失败，请重试']);
                }
            }

            // $this->assign('activity_id', $activity['id']);
            $this->assign('fever_click', $deal['click']);
            return $this->fetch('fever_ok');

            
        }
    }

    



}
